<?php

namespace Tests\Feature;

use App\Enums\DossierStatus;
use App\Jobs\ProcessDossierJob;
use App\Models\Dossier;
use App\Models\Pays;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Mockery\MockInterface;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Queue::fake();
    }

    public function test_checkout_redirects_to_stripe(): void
    {
        $pays = Pays::first();
        $dossier = Dossier::factory()->create([
            'status' => DossierStatus::DRAFT,
            'pays_id' => $pays->id
        ]);

        // MOCK ROBUSTE
        $this->mock(StripeClient::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();

            $sessionsMock = Mockery::mock();
            $sessionsMock->shouldIgnoreMissing();
            $sessionsMock->shouldReceive('create')
                ->andReturn((object) ['id' => 'cs_test_123', 'url' => 'https://checkout.stripe.com/pay/cs_test_123']);

            $checkoutMock = Mockery::mock();
            $checkoutMock->shouldIgnoreMissing();
            $checkoutMock->sessions = $sessionsMock;

            $mock->checkout = $checkoutMock;
            $mock->shouldReceive('__get')->with('checkout')->andReturn($checkoutMock);
        });

        // CORRECTION ICI : Ajout du header X-Inertia
        // Cela dit au contrôleur "Je suis une requête Inertia", donc il répondra 409
        $response = $this->post(
            route('stripe.checkout', $dossier), 
            [], 
            ['X-Inertia' => 'true']
        );

        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', 'https://checkout.stripe.com/pay/cs_test_123');
        
        $this->assertDatabaseHas('dossiers', [
            'id' => $dossier->id,
            'stripe_payment_id' => 'cs_test_123'
        ]);
    }

    public function test_success_page_validates_payment_fallback(): void
    {
        $pays = Pays::first();
        $dossier = Dossier::factory()->create([
            'status' => DossierStatus::DRAFT,
            'pays_id' => $pays->id
        ]);

        $this->mock(StripeClient::class, function (MockInterface $mock) {
            $mock->shouldIgnoreMissing();

            $sessionsMock = Mockery::mock();
            $sessionsMock->shouldIgnoreMissing();
            $sessionsMock->shouldReceive('retrieve')
                ->with('cs_test_valid')
                ->andReturn((object) [
                    'id' => 'cs_test_valid',
                    'payment_status' => 'paid',
                    'customer_details' => (object) ['email' => 'client@test.com']
                ]);

            $checkoutMock = Mockery::mock();
            $checkoutMock->shouldIgnoreMissing();
            $checkoutMock->sessions = $sessionsMock;

            $mock->checkout = $checkoutMock;
            $mock->shouldReceive('__get')->with('checkout')->andReturn($checkoutMock);
        });

        $response = $this->get(route('stripe.success', [
            'dossier' => $dossier->id, 
            'session_id' => 'cs_test_valid'
        ]));

        $response->assertOk();
        
        $this->assertDatabaseHas('dossiers', [
            'id' => $dossier->id,
            'status' => DossierStatus::PAID,
            'email' => 'client@test.com',
            'stripe_payment_id' => 'cs_test_valid'
        ]);

        Queue::assertPushed(ProcessDossierJob::class);
    }

    public function test_webhook_processes_valid_payment(): void
    {
        $pays = Pays::first();
        $dossier = Dossier::factory()->create([
            'status' => DossierStatus::DRAFT,
            'pays_id' => $pays->id
        ]);

        $payload = json_encode([
            'id' => 'evt_test_123',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_webhook',
                    'object' => 'checkout.session',
                    'metadata' => ['dossier_id' => $dossier->id],
                    'customer_details' => ['email' => 'webhook@test.com'],
                    'payment_status' => 'paid'
                ]
            ]
        ]);

        $secret = 'whsec_test_mock';
        config(['services.stripe.webhook.secret' => $secret]);
        
        $timestamp = time();
        $signedPayload = "$timestamp.$payload";
        $signature = hash_hmac('sha256', $signedPayload, $secret);
        $header = "t=$timestamp,v1=$signature";

        $response = $this->call(
            'POST',
            route('stripe.webhook'),
            [], 
            [], 
            [], 
            [
                'HTTP_Stripe-Signature' => $header,
                'CONTENT_TYPE' => 'application/json'
            ],
            $payload
        );

        $response->assertOk();

        $this->assertDatabaseHas('dossiers', [
            'id' => $dossier->id,
            'status' => DossierStatus::PAID,
            'email' => 'webhook@test.com'
        ]);

        Queue::assertPushed(ProcessDossierJob::class);
    }
}
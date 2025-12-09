<?php

namespace App\Http\Controllers;

use App\Enums\DossierStatus;
use App\Jobs\ProcessDossierJob;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook.secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe Webhook Signature Error');
            return response('Invalid signature', 400);
        }

        // On ne traite que le paiement réussi
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            
            $dossierId = $session->metadata->dossier_id ?? null;
            $email = $session->customer_details->email ?? null;

            if ($dossierId) {
                $dossier = Dossier::find($dossierId);

                if ($dossier) {
                    Log::info("💰 Paiement validé pour dossier {$dossier->id}. Email: {$email}");

                    // Mise à jour et lancement du Job
                    $dossier->update([
                        'status' => DossierStatus::PAID,
                        'email' => $email,
                        'expires_at' => now()->addHours(24),
                    ]);

                    ProcessDossierJob::dispatch($dossier);
                }
            }
        }

        return response('Received', 200);
    }
}
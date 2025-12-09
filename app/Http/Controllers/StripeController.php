<?php

namespace App\Http\Controllers;

use App\Enums\DossierStatus;
use App\Jobs\ProcessDossierJob;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Inertia\Inertia;

class StripeController extends Controller
{
    public function checkout(Dossier $dossier, StripeClient $stripe, Request $request)
    {
        if ($dossier->status === DossierStatus::PAID || $dossier->status === DossierStatus::COMPLETED) {
            return redirect()->route('dossiers.upload', $dossier);
        }

        // --- LOG JURIDIQUE : CONSENTEMENT ---
        $dossier->logs()->create([
            'action_type' => 'legal_consent',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'details' => "Acceptation CGU/CGV + Renoncement rétractation (Case cochée avant paiement)."
        ]);

        $session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Sécurisation Dossier Location',
                        'description' => 'Fusion, Filigrane et Sécurisation - Valide 24h',
                    ],
                    'unit_amount' => 490,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'metadata' => [
                'dossier_id' => $dossier->id,
            ],
            'success_url' => route('stripe.success', ['dossier' => $dossier->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('dossiers.upload', $dossier),
        ]);

        
        $dossier->update(['stripe_payment_id' => $session->id]);

        return Inertia::location($session->url);
    }

    public function success(Request $request, Dossier $dossier, StripeClient $stripe)
    {
        $sessionId = $request->query('session_id');

        if ($dossier->status === DossierStatus::DRAFT && $sessionId) {
            try {
                $session = $stripe->checkout->sessions->retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    Log::info("✅ FALLBACK SUCCESS: Paiement validé via redirection pour dossier {$dossier->id}");

                    $email = $session->customer_details->email ?? null;

                    $dossier->update([
                        'status' => DossierStatus::PAID,
                        'email' => $email,
                        'expires_at' => now()->addHours(24),
                        // CORRECTION : On utilise TA colonne
                        'stripe_payment_id' => $session->id, 
                    ]);

                    ProcessDossierJob::dispatch($dossier);
                }
            } catch (\Exception $e) {
                Log::error("⚠️ FALLBACK ERROR: Impossible de vérifier la session Stripe : " . $e->getMessage());
            }
        }

        $dossier->load('pays');
        
        return Inertia::render('Upload', [
            'dossier' => $dossier,
            'documents' => $dossier->documents,
            'documentTypes' => $dossier->pays->typesDocumentsPays,
            'paymentSuccess' => true
        ]);
    }
}
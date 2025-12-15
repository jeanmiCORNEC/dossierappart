<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
  <Head title="Politique de Confidentialité - DossierAppart" />

  <div class="min-h-screen bg-[#F9FAFB] font-sans text-gray-900">

    <!-- HEADER -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/">
          <img src="/storage/logoHeader.png" alt="DossierAppart" class="h-8 w-auto" />
        </a>

        <div class="flex items-center gap-2 text-gray-500 text-sm bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
          <Lock class="w-4 h-4 text-emerald-500" />
          <span class="font-medium">Connexion Sécurisée SSL</span>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-3xl mx-auto px-4 py-8 pb-32">
      <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-200">
        <h1 class="text-3xl font-display font-bold text-gray-900 mb-2 text-center">
          POLITIQUE DE CONFIDENTIALITÉ ET DE PROTECTION DES DONNÉES PERSONNELLES
        </h1>
        <p class="text-sm text-gray-500 mb-8 text-center">
          Dernière mise à jour : 13 Décembre 2025
        </p>

        <!-- L'ESSENTIEL EN 3 POINTS -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">L'ESSENTIEL EN 3 POINTS (RÉSUMÉ)</h2>
          <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
            <p class="font-semibold text-amber-700 mb-3">Pour votre sécurité, DossierAppart applique une politique stricte :</p>
            <div class="space-y-3">
              <p>
                <strong class="text-amber-800">🗑️ Suppression Automatique :</strong>
                <span class="text-amber-700"> Vos documents (sources et finaux) sont définitivement effacés de nos serveurs 24 heures après leur traitement. Nous ne sommes pas un espace de stockage.</span>
              </p>
              <p>
                <strong class="text-amber-800">🚫 Zéro Vente de Données :</strong>
                <span class="text-amber-700"> Vos informations ne sont ni vendues, ni louées, ni partagées à des tiers publicitaires.</span>
              </p>
              <p>
                <strong class="text-amber-800">🇪🇺 Hébergement en Europe :</strong>
                <span class="text-amber-700"> Vos fichiers sont traités sur des serveurs sécurisés situés en Allemagne (Union Européenne).</span>
              </p>
            </div>
          </div>
        </section>

        <!-- 1. RESPONSABLE DU TRAITEMENT -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">1. RESPONSABLE DU TRAITEMENT</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p class="mb-2"><strong>Cornec Jean-Michel EI</strong></p>
            <p class="mb-2">Auto-entrepreneur</p>
            <p class="mb-2">12 rue Chateaubriand, 13007 Marseille, France</p>
            <p class="mb-2">RCS Marseille : 882 931 959</p>
            <p>Email de contact DPO / Confidentialité : <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a></p>
          </div>
        </section>

        <!-- 2. NATURE DES DONNÉES COLLECTÉES -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">2. NATURE DES DONNÉES COLLECTÉES</h2>
          <div class="text-gray-700 space-y-4">
            <p><strong>Données fournies directement par l'utilisateur :</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Adresse email (pour la facturation et le suivi de commande)</li>
              <li>Documents personnels uploadés (pièces d'identité, justificatifs, etc.)</li>
            </ul>

            <p><strong>Données collectées automatiquement :</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Logs de connexion (IP, date/heure, navigateur)</li>
              <li>Cookies techniques nécessaires au fonctionnement du service</li>
            </ul>
          </div>
        </section>

        <!-- 3. FINALITÉS ET BASE LÉGALE -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">3. FINALITÉS ET BASE LÉGALE</h2>
          <div class="text-gray-700 space-y-4">
            <table class="w-full border-collapse border border-gray-300">
              <thead class="bg-gray-100">
                <tr>
                  <th class="border border-gray-300 px-4 py-2 text-left">Finalité</th>
                  <th class="border border-gray-300 px-4 py-2 text-left">Base Légale (RGPD)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="border border-gray-300 px-4 py-2">Fusion et sécurisation de documents</td>
                  <td class="border border-gray-300 px-4 py-2">Exécution du contrat (Art. 6.1.b)</td>
                </tr>
                <tr>
                  <td class="border border-gray-300 px-4 py-2">Facturation et paiement</td>
                  <td class="border border-gray-300 px-4 py-2">Exécution du contrat (Art. 6.1.b)</td>
                </tr>
                <tr>
                  <td class="border border-gray-300 px-4 py-2">Support technique</td>
                  <td class="border border-gray-300 px-4 py-2">Intérêt légitime (Art. 6.1.f)</td>
                </tr>
                <tr>
                  <td class="border border-gray-300 px-4 py-2">Sécurité et prévention de la fraude</td>
                  <td class="border border-gray-300 px-4 py-2">Intérêt légitime (Art. 6.1.f)</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <!-- 4. DURÉE DE CONSERVATION -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">4. DURÉE DE CONSERVATION (RÈGLES STRICTES)</h2>
          <div class="text-gray-700 space-y-4">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
              <p class="font-semibold text-red-700 mb-2">POLITIQUE DE SUPPRESSION AUTOMATIQUE</p>
              <ul class="list-disc list-inside space-y-1 text-red-700">
                <li><strong>Documents sources et finaux :</strong> 24h maximum après traitement</li>
                <li><strong>Adresse email :</strong> 30 jours après la commande (sauf obligation légale)</li>
                <li><strong>Logs de connexion :</strong> 90 jours maximum</li>
              </ul>
            </div>
            <p>
              Cette politique de suppression automatique garantit que vos données sensibles ne restent pas stockées inutilement sur nos serveurs.
            </p>
          </div>
        </section>

        <!-- 5. DESTINATAIRES DES DONNÉES -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">5. DESTINATAIRES DES DONNÉES</h2>
          <div class="text-gray-700 space-y-4">
            <p><strong>Destinataires internes :</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>L'équipe technique de DossierAppart (Cornec Jean-Michel EI)</li>
            </ul>

            <p><strong>Sous-traitants :</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li><strong>Stripe :</strong> Traitement des paiements (certifié PCI DSS)</li>
              <li><strong>Hetzner :</strong> Hébergement des serveurs (Allemagne, UE)</li>
            </ul>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mt-4">
              <p class="font-semibold text-blue-700 mb-2">AUCUNE PARTAGE PUBLICITAIRE</p>
              <p class="text-blue-700">
                Vos données ne sont jamais vendues, louées ou partagées à des fins publicitaires ou commerciales.
              </p>
            </div>
          </div>
        </section>

        <!-- 6. SÉCURITÉ DES DONNÉES -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">6. SÉCURITÉ DES DONNÉES</h2>
          <div class="text-gray-700 space-y-4">
            <p><strong>Mesures techniques :</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Chiffrement SSL/TLS pour toutes les transmissions</li>
              <li>Chiffrement des données au repos</li>
              <li>Accès limité et contrôlé aux serveurs</li>
              <li>Suppression automatique des données</li>
            </ul>

            <p><strong>Mesures organisationnelles :</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Équipe réduite (auto-entrepreneur)</li>
              <li>Aucun stockage permanent des documents</li>
              <li>Formation aux bonnes pratiques RGPD</li>
            </ul>
          </div>
        </section>

        <!-- 7. VOS DROITS RGPD -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">7. VOS DROITS RGPD</h2>
          <div class="text-gray-700 space-y-4">
            <p>Conformément au RGPD, vous disposez des droits suivants :</p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li><strong>Droit d'accès :</strong> Connaître les données vous concernant</li>
              <li><strong>Droit de rectification :</strong> Corriger des données inexactes</li>
              <li><strong>Droit à l'effacement :</strong> Faire supprimer vos données</li>
              <li><strong>Droit à la portabilité :</strong> Récupérer vos données</li>
              <li><strong>Droit d'opposition :</strong> Refuser certains traitements</li>
            </ul>
            <p class="mt-4">
              Pour exercer ces droits : <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a>
            </p>
          </div>
        </section>

        <!-- 8. COOKIES -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">8. COOKIES</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Nous n'utilisons que des cookies techniques strictement nécessaires au fonctionnement du service :
            </p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Cookies de session pour maintenir votre connexion</li>
              <li>Cookies de sécurité pour prévenir les attaques CSRF</li>
            </ul>
            <p>
              Aucun cookie publicitaire ou de tracking n'est utilisé.
            </p>
          </div>
        </section>

        <!-- 9. TRANSFERTS HORS UE -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">9. TRANSFERTS HORS UE</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Vos données sont exclusivement traitées et stockées en Union Européenne (Allemagne).
            </p>
            <p>
              Aucun transfert hors UE n'est effectué.
            </p>
          </div>
        </section>

        <!-- 10. CONTACT -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">10. CONTACT</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p>
              Pour toute question concernant cette politique ou vos données :
            </p>
            <p class="mt-2">
              <strong>Email :</strong> <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a>
            </p>
            <p class="mt-2">
              <strong>Adresse :</strong> 12 rue Chateaubriand, 13007 Marseille, France
            </p>
          </div>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

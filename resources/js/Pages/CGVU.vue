<script setup lang="ts">
import { Lock } from 'lucide-vue-next';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
  <div class="min-h-screen bg-[#F9FAFB] font-sans text-gray-900">
    <!-- HEADER -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/">
          <img src="/storage/logoHeader.png" alt="DossierAppart" class="h-8 w-auto" />
        </a>
        <div class="flex items-center gap-2 text-gray-500 text-sm bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
          <Lock class="w-4 h-4 text-emerald-500" />
          <span class="font-medium">Connexion Sécurisée SSL</span>
        </div>
      </div>
    </div>

    <!-- CONTENT -->
    <main class="max-w-3xl mx-auto px-4 py-8 pb-32">
      <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-200">
        <h1 class="text-3xl font-display font-bold text-gray-900 mb-2 text-center">
          CONDITIONS GÉNÉRALES DE VENTE ET D'UTILISATION
        </h1>
        <p class="text-center text-gray-500 mb-8">(CGVU)</p>
        <p class="text-sm text-gray-500 mb-8 text-center">
          Dernière mise à jour : 13 Décembre 2025
        </p>

        <!-- PRÉAMBULE -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">PRÉAMBULE</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Les présentes Conditions Générales (ci-après "CGVU") régissent l'utilisation du service DossierAppart et les ventes conclues entre :
            </p>
            <p>
              La société <strong>cornec jean-michel</strong>, Auto-entrepreneur, immatriculée au RCS de Marseille sous le numéro <strong>882931959</strong>, dont le siège social est situé au <strong>12 rue chateaubriand 13007 Marseille</strong>, ci-après dénommée "l'Éditeur" ou "DossierAppart".
            </p>
            <p>
              Toute personne physique agissant à titre particulier, ci-après dénommée "le Client" ou "l'Utilisateur", souhaitant utiliser les services payants proposés sur le site internet <strong>dossierappart.fr</strong>.
            </p>
          </div>
        </section>

        <!-- ARTICLE 1 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 1 : OBJET DU SERVICE</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              DossierAppart est un service SaaS (Software as a Service) de traitement documentaire automatisé.
            </p>
            <p>
              Le Service permet à l'Utilisateur de fusionner plusieurs documents personnels (fichiers images ou PDF) en un document unique au format PDF, sur lequel est apposé automatiquement un filigrane (watermark) mentionnant "DOSSIER POUR LOCATION UNIQUEMENT" (ou mention similaire).
            </p>
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded">
              <p class="font-semibold text-amber-700 mb-2">Avertissement important :</p>
              <p class="text-amber-700">
                L'Éditeur fournit un outil technique de sécurisation et de mise en forme. L'Éditeur ne garantit en aucun cas l'acceptation du dossier final par les agences immobilières ou propriétaires, ni l'obtention d'un logement.
              </p>
            </div>
          </div>
        </section>

        <!-- ARTICLE 2 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 2 : PRIX ET MODALITÉS DE PAIEMENT</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Les prix sont indiqués en Euros (€) toutes taxes comprises (TTC). Le tarif applicable est celui affiché sur le site au moment de la commande.
            </p>
            <p>
              Il s'agit d'un <strong>paiement unique à l'acte</strong> (One-off). Aucun abonnement récurrent n'est souscrit.
            </p>
            <p>
              Le règlement s'effectue immédiatement par carte bancaire via notre prestataire de paiement sécurisé <strong>Stripe</strong>. L'Éditeur ne conserve aucune donnée bancaire complète.
            </p>
          </div>
        </section>

        <!-- ARTICLE 3 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 3 : RENONCIATION AU DROIT DE RÉTRACTATION</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Le Service constitue la fourniture d'un contenu numérique non fourni sur un support matériel, dont l'exécution commence immédiatement après le paiement.
            </p>
            <p>
              Conformément aux dispositions de l'article <strong>L.221-28 13° du Code de la Consommation</strong>, le Client ne peut pas exercer son droit de rétractation pour ce type de service.
            </p>
            <p>
              En cochant la case "J'accepte les CGVU et renonce à mon droit de rétractation" avant le paiement, le Client :
            </p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Accepte expressément les présentes conditions</li>
              <li>Renonce expressément à son droit de rétractation</li>
              <li>Demande l'exécution immédiate du service</li>
            </ul>
          </div>
        </section>

        <!-- ARTICLE 4 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 4 : CONDITIONS D'UTILISATION</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              L'utilisation du Service est réservée aux personnes physiques majeures capables.
            </p>
            <p>
              Le Client s'engage à :
            </p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Fournir des documents exacts et licites</li>
              <li>Ne pas utiliser le service à des fins illégales</li>
              <li>Respecter les droits de propriété intellectuelle</li>
              <li>Ne pas tenter de contourner les mesures de sécurité</li>
            </ul>
          </div>
        </section>

        <!-- ARTICLE 5 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 5 : RESPONSABILITÉ</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              L'Éditeur met en œuvre tous les moyens raisonnables pour assurer la disponibilité et la sécurité du Service.
            </p>
            <p>
              Cependant, la responsabilité de l'Éditeur ne peut être engagée en cas de :
            </p>
            <ul class="list-disc list-inside ml-4 space-y-1">
              <li>Interruptions temporaires du service pour maintenance</li>
              <li>Pannes techniques indépendantes de sa volonté</li>
              <li>Utilisation frauduleuse du service par le Client</li>
              <li>Force majeure</li>
            </ul>
          </div>
        </section>

        <!-- ARTICLE 6 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 6 : PROPRIÉTÉ INTELLECTUELLE</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              L'ensemble du contenu du site (textes, images, logos, logiciels) est la propriété exclusive de l'Éditeur.
            </p>
            <p>
              Le Client ne dispose que d'un droit d'usage personnel et non-transférable du Service.
            </p>
          </div>
        </section>

        <!-- ARTICLE 7 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 7 : CONFIDENTIALITÉ ET DONNÉES PERSONNELLES</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Le traitement des données personnelles est décrit dans la <a href="/politique-confidentialite" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">Politique de Confidentialité</a>.
            </p>
            <p>
              Les documents sont supprimés automatiquement sous 24h maximum.
            </p>
          </div>
        </section>

        <!-- ARTICLE 8 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 8 : SUPPORT ET CONTACT</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Pour toute question ou assistance, le Client peut contacter l'Éditeur à : <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a>
            </p>
            <p>
              L'Éditeur s'efforce de répondre dans les meilleurs délais, généralement sous 24h ouvrées.
            </p>
          </div>
        </section>

        <!-- ARTICLE 9 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 9 : DONNÉES PERSONNELLES (RGPD)</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              L'Éditeur collecte et traite les données personnelles du Client (Email, Logs de connexion, Documents) dans le strict respect du <strong>Règlement Général sur la Protection des Données (RGPD)</strong>.
            </p>
            <p>
              Les documents sont supprimés automatiquement sous <strong>24h à 48h maximum</strong>.
            </p>
            <p>
              Pour plus de détails sur la gestion des données, le Client est invité à consulter la <a href="/politique-confidentialite" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">Politique de Confidentialité</a> du site.
            </p>
          </div>
        </section>

        <!-- ARTICLE 10 -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">ARTICLE 10 : LOI APPLICABLE ET LITIGES</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Les présentes CGVU sont soumises au <strong>Droit Français</strong>.
            </p>
            <p>
              En cas de litige, le Client est informé de la possibilité de recourir à un médiateur de la consommation. À défaut de résolution amiable, les tribunaux français seront seuls compétents.
            </p>
          </div>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

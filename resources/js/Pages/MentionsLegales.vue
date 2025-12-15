<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';
import AppFooter from '@/Components/AppFooter.vue';
</script>

<template>
  <Head title="Mentions Légales - DossierAppart" />

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
        <h1 class="text-3xl font-display font-bold text-gray-900 mb-8 text-center">
          Mentions Légales
        </h1>

        <!-- Éditeur du site -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">1. Éditeur du site</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="space-y-2">
              <p><strong>Raison sociale :</strong> Cornec Jean-Michel EI</p>
              <p><strong>Forme juridique :</strong> Entrepreneur Individuel (Auto-entrepreneur)</p>
              <p><strong>Adresse du siège social :</strong> 12 rue Chateaubriand, 13007 Marseille, France</p>
              <p><strong>Numéro RCS :</strong> 882 931 959 RCS Marseille</p>
              <p><strong>Numéro TVA Intracommunautaire :</strong> Non applicable (Auto-entrepreneur en franchise de TVA, article 293 B du CGI)</p>
              <p><strong>Email :</strong> <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a></p>
            </div>
          </div>
        </section>

        <!-- Directeur de la publication -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">2. Directeur de la publication</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p><strong>Nom :</strong> Jean-Michel Cornec</p>
            <p><strong>Contact :</strong> <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a></p>
          </div>
        </section>

        <!-- Hébergeur -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">3. Hébergeur</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <div class="space-y-2">
              <p><strong>Raison sociale :</strong> Hetzner Online GmbH</p>
              <p><strong>Adresse :</strong> Industriestr. 25, 91710 Gunzenhausen, Allemagne</p>
              <p><strong>Site web :</strong> <a href="https://www.hetzner.com" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">www.hetzner.com</a></p>
            </div>
          </div>
        </section>

        <!-- Propriété intellectuelle -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">4. Propriété intellectuelle</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              L'ensemble du contenu de ce site (textes, images, logos, marques, graphismes, logiciels)
              est la propriété exclusive de Cornec Jean-Michel EI, sauf mention contraire.
            </p>
            <p>
              Toute reproduction, représentation, modification ou distribution, totale ou partielle,
              sans autorisation expresse et préalable est strictement interdite.
            </p>
          </div>
        </section>

        <!-- Données personnelles -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">5. Données personnelles</h2>
          <div class="text-gray-700 space-y-4">
            <p>
              Le traitement des données personnelles est décrit dans notre <a href="/politique-confidentialite" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">Politique de Confidentialité</a>.
            </p>
            <p>
              Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos données. Contactez-nous à <a href="mailto:contact@dossierappart.fr" class="text-indigo-600 hover:text-indigo-800 underline hover:no-underline">contact@dossierappart.fr</a>.
            </p>
          </div>
        </section>

        <!-- Crédits -->
        <section class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">6. Crédits</h2>
          <div class="bg-gray-50 p-4 rounded-lg">
            <p><strong>Conception et développement :</strong> Cornec Jean-Michel EI</p>
          </div>
        </section>
      </div>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

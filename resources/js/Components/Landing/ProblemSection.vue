<script setup lang="ts">
</script>

<template>
    <section class="py-16 lg:py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-display font-bold text-slate-900 mb-4">
                    Pourquoi sécuriser votre dossier ?
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Envoyer vos documents par email ou messagerie sans protection vous expose au vol d'identité.
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 lg:gap-12 items-stretch">
                <!-- DANGER ZONE -->
                <div
                    class="relative group rounded-2xl p-8 bg-red-50 border-2 border-red-100 hover:border-red-200 transition-all duration-300">
                    <div
                        class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-red-500 text-white text-sm font-bold rounded-full uppercase tracking-wide">
                        ⚠️ DANGER
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-red-900 mb-2">Envoi classique</h3>
                        <p class="text-red-700/80 text-sm">Vos documents sont réutilisables par n'importe qui.</p>
                    </div>

                    <div
                        class="relative w-full aspect-[1.586] bg-white rounded-lg shadow-sm border border-red-100 overflow-hidden mb-6">
                        <!-- Image CNI AVANT -->
                        <img src="/storage/CNIAVANT.png" alt="Carte d'identité non protégée"
                            class="w-full h-full object-cover" />

                        <!-- Cross overlay -->
                        <div class="absolute inset-0 flex items-center justify-center bg-red-500/10">
                            <svg class="w-16 h-16 text-red-500 opacity-80" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>

                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-red-800">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Utilisable pour des arnaques</span>
                        </li>
                        <li class="flex items-center gap-3 text-red-800">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Modification possible (Photoshop)</span>
                        </li>
                        <li class="flex items-center gap-3 text-red-800">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Stockage inconnu</span>
                        </li>
                    </ul>
                </div>

                <!-- SAFE ZONE -->
                <div
                    class="relative group rounded-2xl p-8 bg-green-50 border-2 border-green-100 hover:border-green-200 transition-all duration-300 shadow-xl shadow-green-900/5">
                    <div
                        class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-green-500 text-white text-sm font-bold rounded-full uppercase tracking-wide shadow-lg shadow-green-500/30">
                        🛡️ SÉCURISÉ
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-xl font-bold text-green-900 mb-2">Avec DossierAppart</h3>
                        <p class="text-green-700/80 text-sm">Vos documents sont inutilisables pour autre chose.</p>
                    </div>

                    <div
                        class="relative w-full aspect-[1.586] bg-white rounded-lg shadow-md border border-green-100 overflow-hidden mb-6">
                        <!-- Image CNI APRES -->
                        <img src="/storage/CNIAPRES.png" alt="Carte d'identité sécurisée"
                            class="w-full h-full object-cover" />
                    </div>

                    <ul class="space-y-3">
                        <li class="flex items-center gap-3 text-green-800">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="font-medium">Filigrane complet & indélébile</span>
                        </li>
                        <li class="flex items-center gap-3 text-green-800">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="font-medium">Date & Motif incrustés</span>
                        </li>
                        <li class="flex items-center gap-3 text-green-800">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="font-medium">Données supprimées (24h)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>

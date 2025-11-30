<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import CountrySelector from '@/Components/CountrySelector.vue';

defineProps<{
    pays: Array<{
        id: number;
        code: string;
        nom: string;
        actif: boolean;
    }>;
}>();
</script>

<template>

    <Head title="DossierAppart - Votre dossier de location en 5 minutes" />

    <div
        class="min-h-screen bg-gradient-to-br from-gray-50 via-purple-50/30 to-indigo-50 dark:from-gray-900 dark:via-purple-900/10 dark:to-indigo-900/20">
        <!-- Header -->
        <header class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                    📋 DossierAppart
                </h1>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 py-12 sm:py-20">
            <div class="text-center mb-12 max-w-4xl">
                <h2
                    class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                    <span class="block">Votre dossier de location</span>
                    <span class="block text-indigo-600 dark:text-indigo-400 mt-2">en 5 minutes</span>
                </h2>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-700 dark:text-gray-300">
                    Créez un dossier complet et sécurisé pour vos candidatures locatives.
                    Simple, rapide, et conforme aux exigences des propriétaires.
                </p>
            </div>

            <!-- Country Selector -->
            <div class="w-full max-w-4xl">
                <CountrySelector :pays="pays" />
            </div>

            <!-- Features -->
            <div class="mt-20 grid grid-cols-1 gap-8 sm:grid-cols-3 text-center max-w-5xl">
                <div class="p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-5xl mb-4">🔒</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Sécurisé
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Vos documents sont protégés et filigranés automatiquement pour éviter toute réutilisation
                        frauduleuse.
                    </p>
                </div>

                <div class="p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-5xl mb-4">⚡</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Rapide
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Uploadez vos documents, payez 4.99€ et recevez votre PDF complet par email en quelques minutes.
                    </p>
                </div>

                <div class="p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow">
                    <div class="text-5xl mb-4">📱</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                        Mobile First
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Prenez vos documents en photo directement depuis votre smartphone, où que vous soyez.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center text-sm text-gray-500 dark:text-gray-400 mt-20">
            <p>&copy; {{ new Date().getFullYear() }} DossierAppart. Tous droits réservés.</p>
            <p class="mt-2">Vos données sont supprimées automatiquement après 30 jours.</p>
        </footer>
    </div>
</template>

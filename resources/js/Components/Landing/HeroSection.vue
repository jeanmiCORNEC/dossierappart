<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    pays: Array<{
        id: number;
        code: string;
        nom: string;
        actif: boolean;
    }>;
    selectedCountry: string;
}>();

const showModal = ref(false);
const selectedPaysCode = ref(props.selectedCountry);

const openModal = () => {
    showModal.value = true;
};

const startDossier = () => {
    router.post('/dossiers', { pays_code: selectedPaysCode.value });
};

const getFlag = (code: string) => {
    const flags: { [key: string]: string } = {
        FR: '🇫🇷',
        BE: '🇧🇪',
        CH: '🇨🇭',
    };
    return flags[code] || '🏳️';
};
</script>

<template>
    <section
        class="relative min-h-screen flex items-center bg-gradient-to-br from-slate-50 via-indigo-50/50 to-white pt-16 overflow-hidden">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute top-1/4 -left-1/4 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-1/4 w-96 h-96 bg-indigo-100 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Text Content -->
                <div class="text-center lg:text-left">
                    <!-- Trust Badge -->
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-success-light text-success border border-success/20 rounded-full text-sm font-medium mb-8">
                        <span>🔒</span>
                        <span>Données supprimées automatiquement sous 24h</span>
                    </div>

                    <!-- H1 -->
                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl font-display font-extrabold text-secondary leading-tight mb-6">
                        Ne laissez pas votre dossier de location devenir une
                        <span class="text-primary">usurpation d'identité</span>.
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-lg sm:text-xl text-slate-600 mb-8 max-w-xl mx-auto lg:mx-0">
                        Fusionnez vos pièces, ajoutez un filigrane <strong class="text-secondary">"DOSSIER POUR LOCATION
                            UNIQUEMENT"</strong> indélébile et obtenez un dossier PDF unique et professionnel en <strong
                            class="text-secondary">2 minutes</strong>.
                    </p>

                    <!-- CTA -->
                    <div id="start" class="flex flex-col items-center lg:items-start gap-2 mb-6">
                        <button @click="openModal"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 bg-primary hover:bg-primary-500 text-white text-lg font-bold rounded-xl shadow-lg shadow-primary/30 transition-all duration-200 hover:scale-105">
                            <span>Créer mon Dossier Sécurisé</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                        <p class="text-xs text-slate-500 italic">
                            Aucune inscription requise - Prêt en 2 min
                        </p>
                    </div>

                    <!-- Micro-copy -->
                    <p class="text-sm text-slate-500">
                        Pas de création de compte • Paiement unique
                    </p>

                    <!-- Trust Bar -->
                    <div class="mt-12 pt-8 border-t border-slate-200">
                        <p class="text-xs text-slate-400 uppercase tracking-wider mb-4">Sécurisé par</p>
                        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6 text-slate-400">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                                <span class="text-sm font-medium">Stripe</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                                </svg>
                                <span class="text-sm font-medium">SSL</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-lg">🇪🇺</span>
                                <span class="text-sm font-medium">RGPD</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z" />
                                </svg>
                                <span class="text-sm font-medium">Hetzner</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual Animation -->
                <div class="hidden lg:flex items-center justify-center">
                    <div class="relative">
                        <!-- Document Card -->
                        <div class="bg-white rounded-2xl p-8 shadow-xl border border-slate-100">
                            <div
                                class="w-72 h-44 bg-gradient-to-br from-slate-100 to-slate-200 rounded-xl flex items-center justify-center relative overflow-hidden">
                                <!-- Fake ID card content -->
                                <div class="text-center text-slate-400 text-sm">
                                    <div class="w-16 h-16 mx-auto mb-2 rounded-full bg-slate-300"></div>
                                    <div class="h-2 w-24 mx-auto bg-slate-300 rounded mb-1"></div>
                                    <div class="h-2 w-20 mx-auto bg-slate-300 rounded"></div>
                                </div>

                                <!-- Watermark Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div
                                        class="transform -rotate-30 text-success font-bold text-sm tracking-wide opacity-80 text-center leading-relaxed">
                                        DOSSIER POUR<br />LOCATION<br />UNIQUEMENT
                                    </div>
                                </div>
                            </div>

                            <!-- Status badge -->
                            <div class="mt-4 flex items-center justify-center gap-2 text-success">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                                </svg>
                                <span class="font-semibold">Filigrané & Protégé</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Country Selection Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click="showModal = false">
        <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4" @click.stop>
            <h2 class="text-2xl font-bold text-center mb-6">Pour quel pays constituez-vous ce dossier ?</h2>
            <div class="space-y-4">
                <button v-for="pays in props.pays" :key="pays.id" @click="selectedPaysCode = pays.code; startDossier()"
                    class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-gray-50 hover:bg-primary hover:text-white rounded-lg transition-colors text-lg font-medium">
                    <span class="text-2xl">{{ getFlag(pays.code) }}</span>
                    <span>{{ pays.nom }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.-rotate-30 {
    transform: rotate(-30deg);
}
</style>

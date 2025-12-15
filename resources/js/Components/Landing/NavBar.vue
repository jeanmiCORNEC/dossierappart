<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';

defineProps<{
    pays: Array<{
        id: number;
        code: string;
        nom: string;
        actif: boolean;
    }>;
}>();

const selectedCountry = defineModel<string>('selectedCountry', { default: 'FR' });

const startDossier = () => {
    router.post('/dossiers', { pays_code: selectedCountry.value });
};

const countryFlags: Record<string, string> = {
    FR: '🇫🇷',
    BE: '🇧🇪',
    CH: '🇨🇭',
};
</script>

<template>
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <Link href="/">
                        <img src="/storage/logoHeader.png" alt="DossierAppart" class="h-8 w-auto" />
                    </Link>
                </div>

                <!-- Right side -->
                <div class="flex items-center gap-4">
                    <!-- Country Selector (Dropdown discret) -->
                    <div class="relative">
                        <select v-model="selectedCountry"
                            class="appearance-none bg-slate-100 text-secondary text-sm border border-slate-200 rounded-lg px-3 py-2 pr-8 cursor-pointer hover:bg-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-primary/50">
                            <option v-for="p in pays" :key="p.id" :value="p.code" class="bg-white text-secondary">
                                {{ countryFlags[p.code] || '🌍' }} {{ p.nom }}
                            </option>
                        </select>
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- CTA Desktop -->
                    <button @click="startDossier"
                        class="hidden md:inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-500 text-white text-sm font-semibold rounded-lg transition-all duration-200 hover:scale-105">
                        Créer mon dossier
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

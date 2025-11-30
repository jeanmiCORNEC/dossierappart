<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Props {
    pays: Array<{
        id: number;
        code: string;
        nom: string;
        actif: boolean;
    }>;
}

const props = defineProps<Props>();

const form = useForm({
    pays_id: null as number | null,
});

const submit = () => {
    if (!form.pays_id) return;
    form.post(route('dossiers.store'));
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
            Où cherchez-vous un logement ?
        </h2>

        <form @submit.prevent="submit" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div v-for="p in pays" :key="p.id"
                    class="relative flex items-center p-4 border rounded-lg cursor-pointer transition-all duration-200"
                    :class="[
                        p.actif
                            ? (form.pays_id === p.id
                                ? 'border-indigo-500 ring-2 ring-indigo-200 dark:ring-indigo-900 bg-indigo-50 dark:bg-indigo-900/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700')
                            : 'opacity-50 cursor-not-allowed border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900'
                    ]" @click="p.actif && (form.pays_id = p.id)">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ p.nom }}
                            </span>
                            <span v-if="p.code === 'FR'" class="text-2xl">🇫🇷</span>
                            <span v-else-if="p.code === 'BE'" class="text-2xl">🇧🇪</span>
                            <span v-else-if="p.code === 'CH'" class="text-2xl">🇨🇭</span>
                        </div>
                        <p v-if="!p.actif" class="text-xs text-gray-500 mt-1">Bientôt disponible</p>
                    </div>

                    <!-- Checkmark icon -->
                    <div v-if="form.pays_id === p.id"
                        class="absolute top-2 right-2 text-indigo-600 dark:text-indigo-400">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div v-if="form.errors.pays_id" class="text-red-600 text-sm">
                {{ form.errors.pays_id }}
            </div>

            <div class="flex justify-end">
                <button type="submit" :disabled="!form.pays_id || form.processing"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <span v-if="form.processing">Création...</span>
                    <span v-else>Commencer mon dossier</span>
                </button>
            </div>
        </form>
    </div>
</template>

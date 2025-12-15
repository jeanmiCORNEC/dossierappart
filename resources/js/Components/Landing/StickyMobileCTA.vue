<script setup lang="ts">
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    paysCode?: string;
}>();

const startDossier = () => {
    router.post('/dossiers', { pays_code: props.paysCode || 'FR' });
};
</script>

<template>
    <div
        class="fixed bottom-0 left-0 w-full p-4 bg-white/95 backdrop-blur-md border-t border-slate-200 md:hidden z-50 safe-area-inset-bottom">
        <button @click="startDossier"
            class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-lg rounded-xl shadow-md hover:shadow-lg transition-all">
            <Lock class="w-5 h-5" />
            Sécuriser mon dossier (4,9€ TTC)
        </button>
    </div>
</template>

<style scoped>
.safe-area-inset-bottom {
    padding-bottom: max(1rem, env(safe-area-inset-bottom));
}
</style>

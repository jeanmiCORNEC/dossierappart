<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    documentType: {
        id: number;
        code: string;
        nom: string;
        description: string;
    };
    dossierId: string;
    existingDocument?: {
        id: number;
        original_filename: string;
    };
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);

const form = useForm({
    file: null as File | null,
    type_document_pays_id: props.documentType.id,
});

const handleDrop = (e: DragEvent) => {
    isDragging.value = false;
    const files = e.dataTransfer?.files;
    if (files && files.length > 0) {
        validateAndSetFile(files[0]);
    }
};

const handleFileSelect = (e: Event) => {
    const files = (e.target as HTMLInputElement).files;
    if (files && files.length > 0) {
        validateAndSetFile(files[0]);
    }
};

const validateAndSetFile = (file: File) => {
    // Basic validation (size < 10MB, type)
    if (file.size > 10 * 1024 * 1024) {
        alert('Le fichier est trop volumineux (max 10MB)');
        return;
    }
    
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
        alert('Format non supporté. Utilisez PDF, JPG ou PNG.');
        return;
    }

    form.file = file;
    submit();
};

const submit = () => {
    form.post(route('dossiers.uploadDocument', props.dossierId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('file');
            if (fileInput.value) fileInput.value.value = '';
        },
    });
};

const triggerFileInput = () => {
    fileInput.value?.click();
};
</script>

<template>
    <div 
        class="border-2 border-dashed rounded-lg p-6 transition-colors duration-200"
        :class="[
            isDragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600',
            existingDocument ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800' : 'hover:border-indigo-400'
        ]"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop"
    >
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center gap-2">
                    {{ documentType.nom }}
                    <span v-if="existingDocument" class="text-green-600 dark:text-green-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ documentType.description }}
                </p>
                
                <div v-if="existingDocument" class="mt-2 text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ existingDocument.original_filename }}
                </div>
            </div>

            <div class="ml-4 flex-shrink-0">
                <input
                    ref="fileInput"
                    type="file"
                    class="hidden"
                    accept=".pdf,.jpg,.jpeg,.png"
                    @change="handleFileSelect"
                />
                
                <button
                    v-if="!existingDocument"
                    type="button"
                    @click="triggerFileInput"
                    :disabled="form.processing"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                >
                    <span v-if="form.processing">Envoi...</span>
                    <span v-else>Choisir un fichier</span>
                </button>

                <button
                    v-else
                    type="button"
                    @click="triggerFileInput"
                    :disabled="form.processing"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500"
                >
                    Remplacer
                </button>
            </div>
        </div>
        
        <div v-if="form.errors.file" class="mt-2 text-sm text-red-600">
            {{ form.errors.file }}
        </div>
    </div>
</template>

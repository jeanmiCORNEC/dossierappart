<script setup lang="ts">
import { ref } from 'vue';

const emit = defineEmits<{
  fileSelected: [file: File];
}>();

const isDragging = ref(false);
const selectedFile = ref<File | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

const handleDrop = (e: DragEvent) => {
  isDragging.value = false;
  const files = e.dataTransfer?.files;
  if (files && files.length > 0) {
    setFile(files[0]);
  }
};

const handleFileSelect = (e: Event) => {
  const files = (e.target as HTMLInputElement).files;
  if (files && files.length > 0) {
    setFile(files[0]);
  }
};

const setFile = (file: File) => {
  selectedFile.value = file;
  emit('fileSelected', file);
};

const triggerFileInput = () => {
  fileInputRef.value?.click();
};

const triggerCamera = () => {
  if (fileInputRef.value) {
    fileInputRef.value.setAttribute('capture', 'environment');
    fileInputRef.value.click();
  }
};

// Expose reset method to parent
const resetDropZone = () => {
  selectedFile.value = null;
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
};

defineExpose({
  resetDropZone
});
</script>

<template>
  <div class="relative border-2 border-dashed rounded-xl p-12 transition-all duration-200" :class="[
    isDragging
      ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
      : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800',
    selectedFile ? 'border-green-500 dark:border-green-600' : ''
  ]" @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop">
    <input ref="fileInputRef" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png" @change="handleFileSelect" />

    <div class="text-center">
      <div v-if="!selectedFile" class="space-y-4">
        <div class="text-6xl">📄</div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          Glissez-déposez votre document
        </h3>
        <p class="text-gray-500 dark:text-gray-400">
          ou utilisez les boutons ci-dessous
        </p>

        <div class="flex justify-center gap-4 pt-4">
          <button type="button" @click="triggerCamera"
            class="md:hidden inline-flex items-center gap-2 px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Prendre une photo
          </button>

          <button type="button" @click="triggerFileInput"
            class="inline-flex items-center gap-2 px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Choisir un fichier
          </button>
        </div>

        <!-- Info pour les recto et verso -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-red-200 dark:border-red-800 rounded-lg">
          <div class="flex items-center justify-center gap-2 text-sm text-red-700 dark:text-red-300">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd" />
            </svg>
            <div class="text-center">
              <p class="font-medium">!!! Important !!!</p>
              <p class="text-xs mt-1">Uploadez le recto et le verso <strong>séparément</strong> (2 fichiers distincts)</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="space-y-4">
        <div class="text-6xl">✅</div>
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
          Fichier sélectionné
        </h3>
        <p class="text-gray-700 dark:text-gray-300 font-medium">
          {{ selectedFile.name }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          {{ (selectedFile.size / 1024 / 1024).toFixed(2) }} MB
        </p>
        <button type="button" @click="triggerFileInput"
          class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
          Changer de fichier
        </button>
      </div>
    </div>
  </div>
</template>

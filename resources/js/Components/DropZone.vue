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
  <div class="relative bg-white border-2 border-dashed rounded-xl p-8 transition-all duration-200 text-center" :class="[
    isDragging
      ? 'border-indigo-500 bg-indigo-50'
      : 'border-indigo-600 hover:bg-slate-50',
    selectedFile ? 'border-emerald-500 bg-emerald-50' : ''
  ]" @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop">
    <input ref="fileInputRef" type="file" class="hidden" accept=".pdf,.jpg,.jpeg,.png" @change="handleFileSelect" />

    <!-- STATE: NO FILE -->
    <div v-if="!selectedFile" class="space-y-6">
      <!-- Icon -->
      <div class="mx-auto w-16 h-16 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
      </div>

      <div>
        <h3 class="text-lg font-bold text-gray-900">
          Glissez votre fichier ici
        </h3>
        <p class="text-sm text-gray-500 mt-1">
          JPG, PNG ou PDF (Max 10 Mo)
        </p>
      </div>

      <!-- Actions Buttons -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-sm mx-auto">
        <!-- Camera Button -->
        <button type="button" @click="triggerCamera"
          class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-50 text-indigo-600 font-semibold rounded-lg hover:bg-blue-100 transition-colors md:hidden">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          Prendre une photo
        </button>

        <!-- File Button -->
        <button type="button" @click="triggerFileInput"
          class="col-span-1 sm:col-span-2 inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-50 text-indigo-600 font-semibold rounded-lg hover:bg-blue-100 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
          </svg>
          Choisir un fichier
        </button>
      </div>
    </div>

    <!-- STATE: FILE SELECTED -->
    <div v-else class="space-y-4">
      <div class="mx-auto w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>

      <div>
        <h3 class="text-lg font-bold text-gray-900 truncate max-w-xs mx-auto">
          {{ selectedFile.name }}
        </h3>
        <p class="text-sm text-gray-500 mt-1">
          {{ (selectedFile.size / 1024 / 1024).toFixed(2) }} MB
        </p>
      </div>

      <button type="button" @click="triggerFileInput"
        class="text-sm font-medium text-indigo-600 hover:text-indigo-800 underline">
        Changer de fichier
      </button>
    </div>
  </div>
</template>

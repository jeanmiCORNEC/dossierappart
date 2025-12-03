<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import DropZone from '@/Components/DropZone.vue';
import DocumentList from '@/Components/DocumentList.vue';
import ValidationModal from '@/Components/ValidationModal.vue';
import axios from 'axios';

interface Document {
  id: number;
  type_document_pays_id: number;
  original_filename: string;
  storage_path: string;
}

interface DocumentType {
  id: number;
  code: string;
  nom: string;
  libelle: string;
  description: string;
}

interface Props {
  dossier: {
    id: string;
    status: string;
    pays: {
      nom: string;
      code: string;
    };
  };
  documents: Document[];
  documentTypes: DocumentType[];
}

const props = defineProps<Props>();

const selectedFile = ref<File | null>(null);
const selectedTypeId = ref<number | null>(null);
const showModal = ref(false);
const dropZoneRef = ref<InstanceType<typeof DropZone> | null>(null);
const isProcessing = ref(false);
const downloadUrl = ref<string | null>(null);
const pollingInterval = ref<number | null>(null);

const form = useForm({
  file: null as File | null,
  type_document_pays_id: null as number | null,
});

const canAdd = computed(() => {
  return selectedFile.value !== null && selectedTypeId.value !== null;
});

const hasAllRequiredDocuments = computed(() => {
  return props.documents.length > 0;
});

// En mode dev, on ne fait pas de polling automatique
// L'utilisateur verra juste le message "Vous recevrez un email" après validation
// Pour tester le téléchargement, on peut manuellement aller sur /dossiers/{id}/download?token=...
onMounted(() => {
  if (props.dossier.status === 'completed') {
    checkStatus(); // Get the download URL only if already completed
  }
  // Si status = processing/paid, on ne fait rien - l'utilisateur recevra un email (en prod)
});

onUnmounted(() => {
  stopPolling();
});

const startPolling = () => {
  if (pollingInterval.value) return;

  pollingInterval.value = window.setInterval(checkStatus, 3000);
};

const stopPolling = () => {
  if (pollingInterval.value) {
    clearInterval(pollingInterval.value);
    pollingInterval.value = null;
  }
};

const checkStatus = async () => {
  try {
    const response = await axios.get(route('dossiers.status', props.dossier.id));
    const status = response.data.status;

    if (status === 'completed') {
      isProcessing.value = false;
      downloadUrl.value = response.data.download_url;
      stopPolling();
    } else if (status === 'failed') {
      isProcessing.value = false;
      stopPolling();
      alert('Une erreur est survenue lors de la génération du dossier.');
    }
  } catch (error) {
    console.error('Error polling status:', error);
  }
};

const handleFileSelected = (file: File) => {
  selectedFile.value = file;
};

const handleAdd = () => {
  if (!canAdd.value) return;

  form.file = selectedFile.value;
  form.type_document_pays_id = selectedTypeId.value;

  form.post(route('dossiers.uploadDocument', props.dossier.id), {
    preserveScroll: true,
    onSuccess: () => {
      selectedFile.value = null;
      selectedTypeId.value = null;
      form.reset();
      dropZoneRef.value?.resetDropZone();
    },
  });
};

const handleValidate = () => {
  showModal.value = true;
};

const handleConfirmPayment = () => {
  // TODO: Integration Stripe réelle
  // Soumettre le dossier - le backend le marquera en "processing" et lancera le job
  showModal.value = false;

  router.post(route('dossiers.submit', props.dossier.id), {}, {
    onError: () => {
      alert('Erreur lors de la soumission du dossier.');
    }
    // onSuccess: la page se recharge automatiquement avec le message de succès
  });
};
</script>

<template>

  <Head title="Ajouter vos documents" />

  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <Link href="/"
            class="inline-flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour
          </Link>

          <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
            📋 DossierAppart
          </h1>

          <div class="w-20"></div> <!-- Spacer for centering -->
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      <!-- Title -->
      <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
          Constituez votre dossier pour la {{ dossier.pays.nom }}
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Ajoutez vos documents un par un
        </p>
      </div>

      <!-- DropZone -->
      <DropZone ref="dropZoneRef" @file-selected="handleFileSelected" />

      <!-- Type Selector + Add Button -->
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm space-y-4">
        <div>
          <label for="document-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Type de document
          </label>
          <select id="document-type" v-model="selectedTypeId"
            class="block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option :value="null">-- Sélectionnez un type --</option>
            <option v-for="type in documentTypes" :key="type.id" :value="type.id">
              {{ type.libelle }}
            </option>
          </select>
          <p v-if="selectedTypeId" class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{documentTypes.find(t => t.id === selectedTypeId)?.description}}
          </p>
        </div>

        <div class="flex justify-end">
          <button type="button" @click="handleAdd" :disabled="!canAdd || form.processing"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
            <span v-if="form.processing">Ajout en cours...</span>
            <span v-else>Ajouter ce document</span>
          </button>
        </div>

        <div v-if="form.errors.file" class="text-red-600 text-sm">
          {{ form.errors.file }}
        </div>
      </div>

      <!-- Document List -->
      <DocumentList :documents="documents" :document-types="documentTypes" :dossier-id="dossier.id" />

      <!-- Action Buttons -->
      <div class="flex justify-end pt-4">

        <!-- State: Completed (Download) -->
        <a v-if="downloadUrl" :href="downloadUrl"
          class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transition-colors">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
          </svg>
          Télécharger mon dossier
        </a>

        <!-- State: Processing -->
        <button v-else-if="isProcessing" type="button" disabled
          class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-yellow-500 cursor-not-allowed shadow-lg opacity-75">
          <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
            viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
          </svg>
          Traitement en cours...
        </button>

        <!-- State: Draft (Validate) -->
        <button v-else type="button" @click="handleValidate" :disabled="!hasAllRequiredDocuments"
          class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-lg">
          Valider mon dossier
        </button>
      </div>
    </div>

    <!-- Validation Modal -->
    <ValidationModal :show="showModal" :has-all-documents="hasAllRequiredDocuments" @close="showModal = false"
      @confirm="handleConfirmPayment" />
  </div>
</template>

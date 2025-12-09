<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { CheckCircle, Mail, ArrowLeft, FileText, UploadCloud } from 'lucide-vue-next';
import DropZone from '@/Components/DropZone.vue';
import DocumentList from '@/Components/DocumentList.vue';
import ValidationModal from '@/Components/ValidationModal.vue';

// --- Interfaces TypeScript ---
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
  paymentSuccess?: boolean;
}

const props = defineProps<Props>();

// --- État UI ---
const selectedFile = ref<File | null>(null);
const selectedTypeId = ref<number | null>(null);
const showModal = ref(false);
const dropZoneRef = ref<InstanceType<typeof DropZone> | null>(null);

const isSuccess = ref(props.paymentSuccess === true); 

const form = useForm({
  file: null as File | null,
  type_document_pays_id: null as number | null,
});

// --- Computed ---
const canAdd = computed(() => {
  return selectedFile.value !== null && selectedTypeId.value !== null;
});

const hasAllRequiredDocuments = computed(() => {
  return props.documents.length > 0;
});

// --- Actions d'Upload ---
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

// --- Actions de Validation Finale ---
const handleValidate = () => {
  showModal.value = true;
};

const handleConfirmPayment = () => {
  // 1. Fermer la modale
  showModal.value = false;

  // 2. On appelle le contrôleur Stripe
  router.post(route('stripe.checkout', props.dossier.id));
};
</script>

<template>
  <Head title="Ajouter vos documents" />

  <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Lien retour : Caché si on est en succès pour éviter de quitter l'écran par erreur -->
          <div class="w-20">
            <Link v-if="!isSuccess" href="/" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">
              <ArrowLeft class="w-5 h-5" />
              Retour
            </Link>
          </div>

          <h1 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <span v-if="isSuccess">🎉</span>
            <span v-else>📋</span>
            DossierAppart
          </h1>

          <div class="w-20"></div> <!-- Spacer d'équilibrage -->
        </div>
      </div>
    </nav>

    <!-- CONTAINER PRINCIPAL -->
    <!-- Utilisation de v-if / v-else pour basculer entre les deux modes -->
    
    <!-- MODE 1 : SUCCÈS (Fire & Forget) -->
    <div v-if="isSuccess" class="max-w-xl mx-auto px-4 py-16 text-center animate-fade-in-up">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
        
        <!-- Icone Succès -->
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
          <CheckCircle class="h-12 w-12 text-green-600 dark:text-green-400" />
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4">
          Dossier en cours de création !
        </h2>
        
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
          Merci ! Vos documents ont été reçus et sécurisés. <br>
          Le traitement automatique (filigrane + fusion) a démarré.
        </p>

        <!-- Notification Email -->
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 mb-8 text-left flex gap-4 border border-blue-100 dark:border-blue-800">
          <div class="flex-shrink-0 pt-1">
            <Mail class="h-6 w-6 text-blue-600 dark:text-blue-400" />
          </div>
          <div>
            <h3 class="font-bold text-blue-900 dark:text-blue-100">Surveillez votre boîte mail</h3>
            <p class="text-sm text-blue-700 dark:text-blue-300 mt-2">
              Vous allez recevoir votre <strong>lien de téléchargement sécurisé</strong> (valide 24h) à l'adresse fournie lors du paiement d'ici quelques minutes.
            </p>
          </div>
        </div>

        <Link href="/" class="inline-flex w-full justify-center items-center bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold py-4 rounded-xl hover:opacity-90 transition-all shadow-lg">
          Retour à l'accueil
        </Link>
      </div>
    </div>


    <!-- MODE 2 : FORMULAIRE D'UPLOAD (Classique) -->
    <div v-else class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      
      <!-- Titre -->
      <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
          Constituez votre dossier pour la {{ dossier.pays.nom }}
        </h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Ajoutez vos documents un par un, nous les sécurisons.
        </p>
      </div>

      <!-- Zone de Drag & Drop -->
      <DropZone ref="dropZoneRef" @file-selected="handleFileSelected" />

      <!-- Sélecteur + Bouton Ajout -->
      <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 space-y-4">
        <div>
          <label for="document-type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Catégorie du document
          </label>
          <div class="relative">
            <select id="document-type" v-model="selectedTypeId"
              class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-lg dark:bg-gray-700 dark:text-white">
              <option :value="null">-- Choisir le type (ex: Identité, Revenus...) --</option>
              <option v-for="type in documentTypes" :key="type.id" :value="type.id">
                {{ type.libelle }}
              </option>
            </select>
          </div>
          <p v-if="selectedTypeId" class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
            <FileText class="w-4 h-4" />
            {{ documentTypes.find(t => t.id === selectedTypeId)?.description }}
          </p>
        </div>

        <div class="flex justify-end pt-2">
          <button type="button" @click="handleAdd" :disabled="!canAdd || form.processing"
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md hover:shadow-lg">
            <UploadCloud v-if="!form.processing" class="w-5 h-5 mr-2" />
            <svg v-else class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="form.processing">Envoi en cours...</span>
            <span v-else>Ajouter ce document</span>
          </button>
        </div>

        <div v-if="form.errors.file" class="mt-2 text-sm text-red-600 bg-red-50 dark:bg-red-900/20 p-2 rounded">
          ⚠️ {{ form.errors.file }}
        </div>
      </div>

      <!-- Liste des documents déjà ajoutés -->
      <DocumentList :documents="documents" :document-types="documentTypes" :dossier-id="dossier.id" />

      <!-- Footer d'Action -->
      <div class="flex justify-end pt-6">
        <button 
          type="button" 
          @click="handleValidate" 
          :disabled="!hasAllRequiredDocuments"
          class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-bold rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all transform active:scale-[0.98]"
        >
          <CheckCircle class="w-6 h-6 mr-2" />
          Valider et Sécuriser mon dossier
        </button>
      </div>
    </div>

    <!-- Modale de Confirmation -->
    <ValidationModal :show="showModal" :has-all-documents="hasAllRequiredDocuments" @close="showModal = false"
      @confirm="handleConfirmPayment" />
  </div>
</template>

<style scoped>
/* Petite animation d'apparition douce pour le message de succès */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out forwards;
}
</style>
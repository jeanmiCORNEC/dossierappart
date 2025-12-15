<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { CheckCircle, Mail, ArrowLeft, Lock, FileText, UploadCloud } from 'lucide-vue-next';
import DropZone from '@/Components/DropZone.vue';
import DocumentList from '@/Components/DocumentList.vue';
import ValidationModal from '@/Components/ValidationModal.vue';
import AppFooter from '@/Components/AppFooter.vue';

// --- Interfaces ---
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
  pays: Array<{
    id: number;
    code: string;
    nom: string;
    actif: boolean;
  }>;
  paymentSuccess?: boolean;
}

const props = defineProps<Props>();

// --- State ---
const selectedFile = ref<File | null>(null);
const selectedTypeId = ref<number | null>(null);
const showModal = ref(false);
const dropZoneRef = ref<InstanceType<typeof DropZone> | null>(null);
const showCountryDropdown = ref(false);

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

// --- Actions ---
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
  showModal.value = false;
  router.post(route('stripe.checkout', props.dossier.id));
};

const changeCountry = (paysCode: string) => {
  router.put(route('dossiers.updatePays', props.dossier.id), { pays_code: paysCode }, {
    preserveScroll: true,
    onSuccess: () => {
      showCountryDropdown.value = false;
      // Reload to update documentTypes
      window.location.reload();
    },
  });
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

  <Head title="Ajouter vos documents" />

  <div class="min-h-screen bg-[#F9FAFB] font-sans text-gray-900">

    <!-- HEADER -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
      <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <Link href="/">
          <img src="/storage/logoHeader.png" alt="DossierAppart" class="h-8 w-auto" />
        </Link>

        <div
          class="flex items-center gap-2 text-gray-500 text-sm bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
          <Lock class="w-4 h-4 text-emerald-500" />
          <span class="font-medium">Connexion Sécurisée SSL</span>
        </div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="max-w-3xl mx-auto px-4 py-8 pb-32">

      <!-- SUCCESS MODE -->
      <div v-if="isSuccess"
        class="bg-white rounded-xl shadow-sm p-8 text-center border border-gray-200 animate-fade-in-up">
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-100 mb-6">
          <CheckCircle class="h-10 w-10 text-emerald-600" />
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Dossier en cours de sécurisation !</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
          Merci ! Vos documents sont en cours de traitement. Vous recevrez le lien de téléchargement par email d'ici
          quelques minutes.
        </p>
        <Link href="/"
          class="inline-flex justify-center items-center px-6 py-3 bg-gray-900 text-white font-bold rounded-lg hover:bg-gray-800 transition-colors">
          Retour à l'accueil
        </Link>
      </div>

      <!-- UPLOAD MODE -->
      <div v-else class="space-y-8">

        <!-- Titles -->
        <div class="text-center space-y-2">
          <div class="relative inline-block">
            <button
              @click="showCountryDropdown = !showCountryDropdown"
              class="text-2xl font-display font-bold text-gray-900 hover:text-primary transition-colors flex items-center gap-2"
            >
              <span>Dossier de Location :</span>
              <span class="flex items-center gap-1">
                <span>{{ getFlag(props.dossier.pays.code) }}</span>
                <span>{{ props.dossier.pays.nom }}</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </span>
            </button>
            <div v-if="showCountryDropdown" @click.stop class="absolute top-full left-1/2 transform -translate-x-1/2 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50 min-w-max">
              <div v-for="pays in props.pays" :key="pays.id" class="px-4 py-2 hover:bg-gray-50 cursor-pointer flex items-center gap-2" @click="changeCountry(pays.code)">
                <span>{{ getFlag(pays.code) }}</span>
                <span>{{ pays.nom }}</span>
              </div>
            </div>
          </div>
          <p class="text-sm text-gray-500 max-w-lg mx-auto">
            Importez vos pièces une par une. Nous appliquons le filigrane instantanément.
          </p>
        </div>

        <!-- Upload Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">

          <!-- Dropzone -->
          <DropZone ref="dropZoneRef" @file-selected="handleFileSelected" />

          <!-- Type Selector & Actions -->
          <div class="space-y-4 pt-2">
            <div class="relative">
              <select v-model="selectedTypeId"
                class="block w-full pl-4 pr-10 py-3 text-base border-gray-200 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg bg-gray-50 text-gray-900 font-medium">
                <option :value="null">-- Choisir le type de document --</option>
                <option v-for="type in documentTypes" :key="type.id" :value="type.id">
                  {{ type.libelle }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                <FileText class="w-5 h-5" />
              </div>
            </div>

            <!-- Warning Recto/Verso -->
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
              <div class="flex">
                <div class="flex-shrink-0">
                  <span class="text-amber-500 text-lg">💡</span>
                </div>
                <div class="ml-3">
                  <p class="text-sm text-amber-900">
                    <span class="font-bold">Astuce :</span> Si vous avez un document Recto/Verso, ajoutez deux fichiers
                    distincts.
                  </p>
                </div>
              </div>
            </div>

            <!-- Add Button -->
            <button @click="handleAdd" :disabled="!canAdd || form.processing"
              class="w-full flex items-center justify-center px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
              <UploadCloud v-if="!form.processing" class="w-5 h-5 mr-2" />
              <span v-if="form.processing">Envoi en cours...</span>
              <span v-else>Ajouter ce document sécurisé</span>
            </button>

            <div v-if="form.errors.file" class="text-sm text-red-600 text-center">
              ⚠️ {{ form.errors.file }}
            </div>
          </div>
        </div>

        <!-- Documents List -->
        <DocumentList :documents="documents" :document-types="documentTypes" :dossier-id="dossier.id" />
      </div>

    </main>

    <!-- STICKY FOOTER (Mobile & Desktop) -->
    <div v-if="!isSuccess && hasAllRequiredDocuments"
      class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-lg z-40 transform transition-transform duration-300">
      <div class="max-w-3xl mx-auto">
        <button @click="handleValidate"
          class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-lg rounded-xl shadow-md hover:shadow-lg transition-all">
          <Lock class="w-5 h-5" />
          Sécuriser mon dossier (4,9€ TTC)
        </button>
      </div>
    </div>

    <!-- Modale Confirmation -->
    <ValidationModal :show="showModal" :has-all-documents="hasAllRequiredDocuments" @close="showModal = false"
      @confirm="handleConfirmPayment" />

    <!-- FOOTER -->
    <AppFooter />

  </div>
</template>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.4s ease-out forwards;
}
</style>
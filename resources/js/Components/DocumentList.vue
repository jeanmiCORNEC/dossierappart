<script setup lang="ts">
import { router } from '@inertiajs/vue3';

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
}

interface Props {
  documents: Document[];
  documentTypes: DocumentType[];
  dossierId: string;
}

const props = defineProps<Props>();

const groupedDocuments = () => {
  const groups: Record<number, Document[]> = {};

  props.documents.forEach(doc => {
    if (!groups[doc.type_document_pays_id]) {
      groups[doc.type_document_pays_id] = [];
    }
    groups[doc.type_document_pays_id].push(doc);
  });

  return groups;
};

const getTypeName = (typeId: number) => {
  return props.documentTypes.find(t => t.id === typeId)?.libelle || 'Document';
};

const viewDocument = (doc: Document) => {
  // Ouvrir dans un nouvel onglet
  const url = route('dossiers.viewDocument', {
    dossier: props.dossierId,
    document: doc.id
  });
  window.open(url, '_blank');
};

const deleteDocument = (doc: Document) => {
  if (confirm(`Êtes-vous sûr de vouloir supprimer "${doc.original_filename}" ?`)) {
    router.delete(route('dossiers.deleteDocument', {
      dossier: props.dossierId,
      document: doc.id
    }), {
      preserveScroll: true,
    });
  }
};

const isImage = (filename: string): boolean => {
  const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
  const extension = filename.split('.').pop()?.toLowerCase();
  return extension ? imageExtensions.includes(extension) : false;
};

const getThumbnailUrl = (doc: Document): string => {
  return route('dossiers.viewDocument', {
    dossier: props.dossierId,
    document: doc.id
  });
};
</script>

<template>
  <div v-if="documents.length > 0" class="space-y-4">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
      Documents ajoutés
    </h3>

    <div class="space-y-3">
      <div v-for="(docs, typeId) in groupedDocuments()" :key="typeId"
        class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-2">
          <h4 class="font-medium text-gray-900 dark:text-white">
            {{ getTypeName(Number(typeId)) }}
          </h4>
          <span class="text-sm text-gray-500 dark:text-gray-400">
            x{{ docs.length }}
          </span>
        </div>

        <ul class="space-y-2">
          <li v-for="doc in docs" :key="doc.id" class="flex items-center gap-3 text-sm">
            <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>

            <!-- Thumbnail -->
            <button @click="viewDocument(doc)"
              class="flex-shrink-0 w-12 h-12 rounded overflow-hidden border border-gray-300 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors">
              <!-- Image thumbnail -->
              <img v-if="isImage(doc.original_filename)" :src="getThumbnailUrl(doc)" :alt="doc.original_filename"
                class="w-full h-full object-cover" />
              <!-- PDF icon -->
              <div v-else class="w-full h-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M4 18h12V6h-4V2H4v16zm-2 1V0h12l4 4v16H2v-1z" />
                  <text x="50%" y="70%" text-anchor="middle" font-size="6" font-weight="bold"
                    fill="currentColor">PDF</text>
                </svg>
              </div>
            </button>

            <button @click="viewDocument(doc)"
              class="flex-1 text-left text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline transition-colors truncate">
              {{ doc.original_filename }}
            </button>

            <button @click="deleteDocument(doc)"
              class="flex-shrink-0 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors p-1"
              title="Supprimer ce document">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
    Aucun document ajouté pour le moment
  </div>
</template>

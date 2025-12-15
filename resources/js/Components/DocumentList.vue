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
    <div class="flex items-center justify-between">
      <h3 class="font-bold text-gray-900">
        Vos documents
        <span
          class="ml-2 inline-flex items-center justify-center bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full h-5 w-5">
          {{ documents.length }}
        </span>
      </h3>
    </div>

    <div class="space-y-3">
      <div v-for="doc in documents" :key="doc.id"
        class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex items-center gap-4 transition-shadow hover:shadow-md">
        <!-- Thumbnail -->
        <button @click="viewDocument(doc)"
          class="relative w-12 h-12 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
          <img v-if="isImage(doc.original_filename)" :src="getThumbnailUrl(doc)" :alt="doc.original_filename"
            class="w-full h-full object-cover" />
          <div v-else class="w-full h-full flex items-center justify-center">
            <span class="text-[10px] font-bold text-gray-500">PDF</span>
          </div>
        </button>

        <!-- Info -->
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">
            {{ doc.original_filename }}
          </p>
          <p class="text-xs text-indigo-600 font-medium">
            {{ getTypeName(doc.type_document_pays_id) }}
          </p>
        </div>

        <!-- Delete Action -->
        <button @click="deleteDocument(doc)"
          class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors" title="Supprimer">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

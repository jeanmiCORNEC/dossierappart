<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  show: boolean;
  hasAllDocuments: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  close: [];
  confirm: [];
}>();

const acceptedCGU = ref(false);

const handleConfirm = () => {
  if (acceptedCGU.value) {
    emit('confirm');
  }
};

const handleClose = () => {
  acceptedCGU.value = false;
  emit('close');
};
</script>

<template>
  <teleport to="body">
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="show" 
        class="fixed inset-0 z-50 overflow-y-auto"
        @click.self="handleClose"
      >
        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Backdrop -->
          <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

          <!-- Modal -->
          <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Finaliser votre dossier
              </h3>
              <button
                type="button"
                @click="handleClose"
                class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <div class="space-y-4 text-sm text-gray-600 dark:text-gray-300">
              <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex gap-3">
                  <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                  </svg>
                  <div>
                    <p class="font-medium text-yellow-800 dark:text-yellow-400">
                      Attention – Adresse email
                    </p>
                    <p class="mt-1 text-yellow-700 dark:text-yellow-300">
                      L'email utilisé lors du paiement Stripe sera celui où vous recevrez le lien de téléchargement de votre dossier. Vérifiez-le attentivement.
                    </p>
                  </div>
                </div>
              </div>

              <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <div class="flex gap-3">
                  <svg class="w-5 h-5 text-red-600 dark:text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <div>
                    <p class="font-medium text-red-800 dark:text-red-400">
                      Aucun remboursement
                    </p>
                    <p class="mt-1 text-red-700 dark:text-red-300">
                      Une fois le paiement effectué et le dossier généré, aucun remboursement ne sera possible. Assurez-vous d'avoir téléchargé tous les documents nécessaires.
                    </p>
                  </div>
                </div>
              </div>

              <label class="flex items-start gap-3 cursor-pointer">
                <input
                  v-model="acceptedCGU"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                />
                <span class="text-gray-700 dark:text-gray-300">
                  J'ai lu et j'accepte les 
                  <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">Conditions Générales d'Utilisation</a>
                </span>
              </label>
            </div>

            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="handleClose"
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                Annuler
              </button>
              <button
                type="button"
                @click="handleConfirm"
                :disabled="!acceptedCGU || !hasAllDocuments"
                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                Payer et finaliser (4,99€)
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { AlertTriangle, Mail, ShieldAlert } from 'lucide-vue-next';

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
  // On ne reset pas forcément acceptedCGU pour éviter de frustrer l'user s'il réouvre
  emit('close');
};
</script>

<template>
  <teleport to="body">
    <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0"
      enter-to-class="opacity-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100"
      leave-to-class="opacity-0">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click.self="handleClose">
        <!-- Backdrop flou -->
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4">
          <!-- Modal -->
          <div
            class="relative bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 space-y-6 transform transition-all border border-gray-100">

            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
              <h3 class="text-xl font-bold text-gray-900">
                Dernière étape avant sécurisation
              </h3>
              <button type="button" @click="handleClose" class="text-gray-400 hover:text-gray-500 transition-colors">
                <span class="sr-only">Fermer</span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Contenu d'avertissement -->
            <div class="space-y-4 text-sm text-gray-600">

              <!-- Alerte Email -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex gap-3">
                <Mail class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p class="font-bold text-blue-800">
                    Adresse email de réception
                  </p>
                  <p class="mt-1 text-blue-700">
                    Le lien de téléchargement sera envoyé à l'adresse que vous saisirez sur la page de paiement
                    <strong>Stripe</strong>. Vérifiez-la bien !
                  </p>
                </div>
              </div>

              <!-- Alerte Rétractation (Juridique) -->
              <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3">
                <AlertTriangle class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                <div>
                  <p class="font-bold text-amber-800">
                    Renoncement au remboursement
                  </p>
                  <p class="mt-1 text-amber-700 text-xs">
                    Le service étant exécuté immédiatement après paiement (génération du fichier), vous ne pourrez pas
                    exercer votre droit de rétractation une fois le dossier livré.
                  </p>
                </div>
              </div>

              <!-- Checkbox Legale (Obligatoire) -->
              <div class="pt-2">
                <label class="flex items-start gap-3 cursor-pointer group">
                  <div class="relative flex items-center">
                    <input v-model="acceptedCGU" type="checkbox"
                      class="h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all cursor-pointer" />
                  </div>
                  <span class="text-gray-700 text-sm leading-relaxed group-hover:text-gray-900 transition-colors">
                    Je renonce expressément à mon droit de rétractation de 14 jours pour bénéficier du service
                    immédiatement. J'accepte les
                    <a href="/cgvu" target="_blank" class="text-indigo-600 hover:underline font-medium">CGVU</a>
                    et la
                    <a href="/confidentialite" target="_blank"
                      class="text-indigo-600 hover:underline font-medium">Politique de
                      confidentialité</a>.
                  </span>
                </label>
              </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex gap-3 pt-2 border-t border-gray-100">
              <button type="button" @click="handleClose"
                class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                Annuler
              </button>

              <button type="button" @click="handleConfirm" :disabled="!acceptedCGU || !hasAllDocuments" :class="[
                'flex-1 px-4 py-3 text-white rounded-lg font-bold shadow-lg transition-all transform active:scale-[0.98] flex justify-center items-center gap-2',
                acceptedCGU
                  ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-500/30'
                  : 'bg-gray-400 cursor-not-allowed opacity-70'
              ]">
                <span>Payer et Sécuriser (4,9€ TTC)</span>
                <ShieldAlert v-if="!acceptedCGU" class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </teleport>
</template>
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="py-12 bg-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Navigation Links -->
            <nav class="flex flex-wrap justify-center gap-x-8 gap-y-4 mb-8 text-sm">
                <Link href="/" class="text-slate-600 hover:text-primary transition-colors">
                    Accueil
                </Link>
                <Link href="/cgvu" class="text-slate-600 hover:text-primary transition-colors">
                    CGV & CGU
                </Link>
                <Link href="/confidentialite" class="text-slate-600 hover:text-primary transition-colors">
                    Politique de Confidentialité
                </Link>
                <Link href="/mentions-legales" class="text-slate-600 hover:text-primary transition-colors">
                    Mentions Légales
                </Link>
                <a href="mailto:contact@dossierappart.fr" class="text-slate-600 hover:text-primary transition-colors">
                    Contact Support
                </a>
            </nav>

            <!-- Trust Badges -->
            <div class="flex flex-wrap items-center justify-center gap-4 mb-8 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <span>🇪🇺</span>
                    <span>Hébergé en UE (Allemagne)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>🔒</span>
                    <span>Email chiffré</span>
                </div>
                <span class="hidden sm:inline text-slate-300">•</span>
                <div class="flex items-center gap-2">
                    <span>🗑️</span>
                    <span>Suppression automatique</span>
                </div>
                <span class="hidden sm:inline text-slate-300">•</span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    <span>Paiement sécurisé par Stripe</span>
                </div>
                <span class="hidden sm:inline text-slate-300">•</span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
                    </svg>
                    <span>Conforme RGPD</span>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-center text-sm text-slate-500">
                <p>&copy; {{ new Date().getFullYear() }} DossierAppart. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</template>

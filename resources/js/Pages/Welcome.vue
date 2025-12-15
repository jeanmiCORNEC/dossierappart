<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/Landing/NavBar.vue';
import HeroSection from '@/Components/Landing/HeroSection.vue';
import FeatureGrid from '@/Components/Landing/FeatureGrid.vue';
import HowItWorks from '@/Components/Landing/HowItWorks.vue';
import TestimonialsSection from '@/Components/Landing/TestimonialsSection.vue';
import StickyMobileCTA from '@/Components/Landing/StickyMobileCTA.vue';
import AppFooter from '@/Components/AppFooter.vue';
import ProblemSection from '@/Components/Landing/ProblemSection.vue';

defineProps<{
    pays: Array<{
        id: number;
        code: string;
        nom: string;
        actif: boolean;
    }>;
}>();

const selectedCountry = ref('FR');
</script>

<template>

    <Head title="DossierAppart - Sécurisez votre dossier de location" />

    <div class="min-h-screen">
        <!-- Navigation -->
        <NavBar :pays="pays" v-model:selectedCountry="selectedCountry" />

        <!-- Hero Section -->
        <HeroSection :pays="pays" :selected-country="selectedCountry" />

        <!-- Problem/Solution Section -->
        <ProblemSection />

        <!-- Features Grid -->
        <FeatureGrid />

        <!-- How It Works -->
        <HowItWorks />

        <!-- Testimonials -->
        <TestimonialsSection />

        <!-- Footer -->
        <AppFooter class="pb-24 md:pb-0" />

        <!-- Sticky Mobile CTA -->
        <StickyMobileCTA :pays-code="selectedCountry" />
    </div>
</template>

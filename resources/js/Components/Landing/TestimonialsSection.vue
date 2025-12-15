<script setup lang="ts">
const testimonials = [
    {
        name: 'Thomas L.',
        location: 'Lyon',
        text: "J'ai failli me faire avoir sur Leboncoin en envoyant mon dossier complet à une arnaque. Maintenant je ne prends plus de risque, je floute tout. Rapide et efficace.",
        rating: 5
    },
    {
        name: 'Sophie M.',
        location: 'Paris',
        text: "Simple à utiliser. J'avais peur que le propriétaire refuse le dossier avec les filigranes, mais au contraire, ça a montré mon sérieux. Dossier accepté !",
        rating: 5
    },
    {
        name: 'Marc D.',
        location: 'Bordeaux',
        text: "Pour le prix d'un café, j'ai l'esprit tranquille. L'outil est super intuitif et le PDF final est très propre. Je recommande à 100%.",
        rating: 5
    }
];

const stats = [
    { number: '2 min', label: 'Temps moyen' },
    { number: '24h', label: 'Suppression auto' },
    { number: '4,9€', label: 'Prix unique' },
    { number: '100%', label: 'Sécurisé' },
];
</script>

<template>
    <section class="py-16 bg-slate-50 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-display font-bold text-slate-900 mb-4">
                    Ils ont sécurisé leur dossier
                </h2>
                <div class="flex justify-center items-center gap-2 mb-2">
                    <div class="flex text-yellow-400">
                        <svg v-for="i in 5" :key="i" class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>
                    <span class="font-bold text-slate-700">4.9/5</span>
                </div>
                <p class="text-slate-500">Moyenne basée sur nos premiers utilisateurs</p>
            </div>

            <!-- Testimonials Grid -->
            <div class="grid md:grid-cols-3 gap-8 mb-16">
                <div v-for="(testimonial, index) in testimonials" :key="index"
                    class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col h-full transform transition-all duration-300 hover:-translate-y-1 hover:shadow-md">

                    <!-- Stars -->
                    <div class="flex text-yellow-400 mb-4">
                        <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    </div>

                    <p class="text-slate-600 mb-6 flex-grow italic">"{{ testimonial.text }}"</p>

                    <div class="mt-auto flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500 text-sm">
                            {{ testimonial.name.charAt(0) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-900 text-sm">{{ testimonial.name }}</div>
                            <div class="text-xs text-slate-500">{{ testimonial.location }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid (Keeping this as a bonus trust builder at the bottom) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 border-t border-slate-200 pt-12">
                <div v-for="stat in stats" :key="stat.label" class="text-center p-4">
                    <div class="text-2xl md:text-3xl font-display font-bold text-primary mb-1">
                        {{ stat.number }}
                    </div>
                    <div class="text-sm text-slate-600">
                        {{ stat.label }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

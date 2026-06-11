<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// On crée de fausses stats pour l'instant pour habiller le tableau de bord du Chef
const stats = [
    { name: 'Véhicules à l\'atelier', value: '12', icon: '🚗', color: 'bg-blue-500' },
    { name: 'Tâches en attente', value: '4', icon: '⏳', color: 'bg-yellow-500' },
    { name: 'Fiches de travail aujourd\'hui', value: '6', icon: '📝', color: 'bg-green-500' },
    { name: 'Alerte Stock Faible', value: '2', icon: '⚠️', color: 'bg-red-500' },
];
</script>

<template>
    <Head title="Tableau de bord - Chef d'Atelier" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tableau de Bord Supervision</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                    <div class="text-gray-900 font-bold text-lg">
                        Bonjour, {{ $page.props.auth.user.name }} ! 👋
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        Bienvenue sur votre espace de gestion d'atelier. Vous êtes connecté en tant que <span class="font-semibold text-indigo-600">Chef d'Atelier</span>.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div v-for="stat in stats" :key="stat.name" class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">{{ stat.name }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">{{ stat.value }}</p>
                        </div>
                        <div :class="['w-12 h-12 rounded-full flex items-center justify-center text-xl text-white', stat.color]">
                            {{ stat.icon }}
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-bold text-gray-700 text-md mb-4">Actions de supervision rapides</h3>
                    <div class="flex flex-wrap gap-4">
                        <Link :href="route('tasks.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-semibold shadow">
                            + Planifier & Assigner une Tâche
                        </Link>
                        <Link :href="route('vehicules.index')" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded text-sm font-semibold shadow">
                            Consulter le Parc Véhicules
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

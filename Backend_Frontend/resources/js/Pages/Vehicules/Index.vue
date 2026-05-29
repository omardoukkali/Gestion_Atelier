<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    vehicules: Array,
});
</script>

<template>
    <Head title="Véhicules" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Véhicules</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Parc Automobile</h3>
                        <Link :href="route('vehicules.create')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Ajouter un véhicule
                        </Link>
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Immatriculation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Véhicule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Année</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kilométrage</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="vehicules.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun véhicule enregistré.</td>
                            </tr>
                            <tr v-for="vehicule in vehicules" :key="vehicule.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ vehicule.immatriculation }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ vehicule.marque }} {{ vehicule.modele }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ vehicule.annee }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ vehicule.km_actuel }} km</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

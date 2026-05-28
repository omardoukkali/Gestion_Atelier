<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// On déclare la variable 'demandes' envoyée par Laravel
defineProps({
    demandes: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tableau de bord de l'Atelier</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 text-gray-900">Liste des Demandes de Travail</h3>
                    <div class="flex justify-between items-center mb-4">

                        <Link
                            v-if="$page.props.auth.user.role === 'employe'"
                            :href="route('demandes.create')"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Nouvelle Demande
                        </Link>
                    </div>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employé</th>
                                <th v-if="$page.props.auth.user.role === 'chef_atelier'" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="demandes.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune demande pour le moment.</td>
                            </tr>

                            <tr v-for="demande in demandes" :key="demande.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ demande.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold capitalize text-blue-600">{{ demande.type }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ demande.description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ demande.statut.replace('_', ' ') }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ demande.employe ? demande.employe.name : 'Inconnu' }}
                                </td>
                                <td v-if="$page.props.auth.user.role === 'chef_atelier'" class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div v-if="demande.statut === 'en_attente'" class="flex justify-end gap-2">
                                        <Link :href="route('demandes.accepter', demande.id)" method="patch" as="button" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-bold transition">
                                            Accepter
                                        </Link>
                                        <Link :href="route('demandes.refuser', demande.id)" method="patch" as="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold transition">
                                            Refuser
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

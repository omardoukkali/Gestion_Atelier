<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ commandes: Array });

// Petite fonction pour formater la date
const formatDate = (dateString) => {
    const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
};
</script>

<template>
    <Head title="Commandes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suivi des Commandes</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Historique des commandes</h3>
                    <Link :href="route('commandes.create')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        + Nouvelle Commande
                    </Link>
                </div>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° / Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Détails (Pièces)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Par</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-if="commandes.length === 0">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune commande trouvée.</td>
                        </tr>
                        <tr v-for="cmd in commandes" :key="cmd.id">
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                <span class="font-bold">CMD-#{{ cmd.id }}</span><br>
                                <span class="text-gray-500 text-xs">{{ formatDate(cmd.date_commande) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ cmd.fournisseur.nom }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <ul class="list-disc pl-4">
                                    <li v-for="ligne in cmd.lignes" :key="ligne.id">
                                        {{ ligne.quantite }}x {{ ligne.piece.designation }}
                                    </li>
                                </ul>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ cmd.statut }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ cmd.chef.name }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

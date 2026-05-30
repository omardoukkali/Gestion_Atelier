<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    commandesEnAttente: Array,
    livraisons: Array
});

const form = useForm({
    commande_id: null,
    statut: ''
});

const validerReception = (id, statut) => {
    if (confirm(`Confirmer la réception comme étant ${statut} ?`)) {
        form.commande_id = id;
        form.statut = statut;
        form.post(route('livraisons.store'), {
            onSuccess: () => {
                form.reset();
            }
        });
    }
};

const formatDate = (date) => new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
});
</script>

<template>
    <Head title="Livraisons" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Réception des Livraisons</h2>
        </template>

        <div class="py-12 space-y-8 max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-lg font-bold mb-4 text-blue-800">📦 Commandes en attente de réception</h3>

                <div v-if="commandesEnAttente.length === 0" class="text-gray-500 italic">
                    Aucune commande en attente pour le moment.
                </div>

                <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div v-for="cmd in commandesEnAttente" :key="cmd.id" class="border rounded-lg p-4 bg-gray-50 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-lg">CMD #{{ cmd.id }}</span>
                            <span class="text-xs text-gray-500">{{ formatDate(cmd.created_at) }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Fournisseur : {{ cmd.fournisseur.nom }}</p>

                        <ul class="text-sm text-gray-600 mb-4 bg-white p-2 rounded border">
                            <li v-for="ligne in cmd.lignes" :key="ligne.id">
                                • {{ ligne.quantite }}x {{ ligne.piece.designation }}
                            </li>
                        </ul>

                        <div class="flex gap-2">
                            <button
                                @click="validerReception(cmd.id, 'conforme')"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded text-sm font-bold transition"
                            >
                                ✅ Conforme
                            </button>
                            <button
                                @click="validerReception(cmd.id, 'non_conforme')"
                                class="flex-1 bg-red-100 hover:bg-red-200 text-red-700 py-2 rounded text-sm font-bold transition border border-red-300"
                            >
                                ❌ Rejeter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">📜 Historique des livraisons traitées</h3>

                <table class="min-w-full divide-y divide-gray-200 border">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Commande</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ouvrier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Résultat</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="liv in livraisons" :key="liv.id">
                        <td class="px-6 py-4 text-sm">{{ formatDate(liv.date_reception) }}</td>
                        <td class="px-6 py-4 text-sm font-medium">CMD #{{ liv.commande_id }} ({{ liv.commande.fournisseur.nom }})</td>
                        <td class="px-6 py-4 text-sm">{{ liv.ouvrier.name }}</td>
                        <td class="px-6 py-4">
                                <span :class="liv.statut === 'conforme' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs font-bold uppercase">
                                    {{ liv.statut }}
                                </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

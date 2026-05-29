<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ pieces: Array });
</script>

<template>
    <Head title="Stock" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion du Stock</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-bold">Pièces de rechange</h3>
                        <Link :href="route('stock-pieces.create')" class="bg-blue-600 text-white px-4 py-2 rounded">+ Ajouter une pièce</Link>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Désignation</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fournisseur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix Unit.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="p in pieces" :key="p.id">
                            <td class="px-6 py-4 font-medium">{{ p.designation }}</td>
                            <td class="px-6 py-4 text-sm">{{ p.fournisseur.nom }}</td>
                            <td class="px-6 py-4" :class="{'text-red-600 font-bold': p.quantite <= p.seuil_alerte}">
                                {{ p.quantite }}
                            </td>
                            <td class="px-6 py-4">{{ p.prix_unitaire }} €</td>
                            <td class="px-6 py-4 text-xs">
                                    <span v-if="p.quantite <= p.seuil_alerte" class="bg-red-100 text-red-800 px-2 py-1 rounded-full font-semibold">
                                        À commander
                                    </span>
                                <span v-else class="bg-green-100 text-green-800 px-2 py-1 rounded-full font-semibold">
                                        OK
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

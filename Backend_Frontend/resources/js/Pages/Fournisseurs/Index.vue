<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ fournisseurs: Array });
</script>

<template>
    <Head title="Fournisseurs" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Annuaire des Fournisseurs</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div class="flex justify-between mb-4">
                        <h3 class="text-lg font-bold">Partenaires</h3>
                        <Link :href="route('fournisseurs.create')" class="bg-green-600 text-white px-4 py-2 rounded">+ Nouveau Fournisseur</Link>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 border">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="f in fournisseurs" :key="f.id">
                            <td class="px-6 py-4 font-bold">{{ f.nom }}</td>
                            <td class="px-6 py-4">{{ f.email }}</td>
                            <td class="px-6 py-4">{{ f.telephone }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

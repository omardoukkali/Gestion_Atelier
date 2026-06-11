<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    demandes: Array
});

const form = useForm({});

const updateStatus = (id, action) => {
    form.patch(route(`demandes.${action}`, id), {
        preserveScroll: true,
    });
};

const statusClass = (s) => {
    if (s === 'acceptee') return 'bg-green-100 text-green-800';
    if (s === 'refusee') return 'bg-red-100 text-red-800';
    return 'bg-yellow-100 text-yellow-800';
};
</script>

<template>
    <Head title="Liste des Demandes" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suivi des Demandes</h2>
                <Link v-if="$page.props.auth.user.role === 'employe' || $page.props.auth.user.role === 'chef_atelier'" :href="route('demandes.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-bold text-sm">
                    + Nouvelle Demande
                </Link>
            </div>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3" v-if="$page.props.auth.user.role === 'chef_atelier'">Employé</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3" v-if="$page.props.auth.user.role === 'chef_atelier'">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="demande in demandes" :key="demande.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-bold">#{{ demande.id }}</td>
                        <td class="px-6 py-4 font-medium" v-if="$page.props.auth.user.role === 'chef_atelier'">
                            {{ demande.employe ? demande.employe.name : 'Inconnu' }}
                        </td>
                        <td class="px-6 py-4 capitalize">{{ demande.type }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ demande.description }}</td>
                        <td class="px-6 py-4">
                                <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold', statusClass(demande.statut)]">
                                    {{ demande.statut.replace('_', ' ') }}
                                </span>
                        </td>
                        <td class="px-6 py-4 flex gap-2" v-if="$page.props.auth.user.role === 'chef_atelier'">
                            <button v-if="demande.statut === 'en_attente'" @click="updateStatus(demande.id, 'accepter')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-bold">Accepter</button>
                            <button v-if="demande.statut === 'en_attente'" @click="updateStatus(demande.id, 'refuser')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold">Refuser</button>
                        </td>
                    </tr>
                    <tr v-if="demandes.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">Aucune demande trouvée.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

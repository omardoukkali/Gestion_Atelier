<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    vehicles: Array,
    mechanics: Array
});

const form = useForm({
    vehicle_id: '',
    assigne_id: '',
    type: 'entretien',
    priorite: 'normale',
    description: ''
});

const submit = () => {
    form.post(route('tasks.store'));
};
</script>

<template>
    <Head title="Créer une Tâche" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Créer et Assigner une Tâche</h2>
        </template>

        <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form @submit.prevent="submit" class="space-y-6">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Sélectionner le Véhicule</label>
                        <select v-model="form.vehicle_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled>Choisir un véhicule...</option>
                            <option v-for="v in vehicles" :key="v.id" :value="v.id">
                                {{ v.immatriculation }} - {{ v.marque }} {{ v.modele }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Assigner à un Mécanicien</label>
                        <select v-model="form.assigne_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled>Choisir un ouvrier...</option>
                            <option v-for="m in mechanics" :key="m.id" :value="m.id">
                                {{ m.name }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Type d'intervention</label>
                            <select v-model="form.type" class="block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="entretien">Entretien</option>
                                <option value="fabrication">Fabrication</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Priorité</label>
                            <select v-model="form.priorite" class="block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="basse">Basse</option>
                                <option value="normale">Normale</option>
                                <option value="haute">Haute</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description des travaux à faire</label>
                        <textarea v-model="form.description" rows="4" class="block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Détaillez le problème ou l'objectif..."></textarea>
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <Link :href="route('tasks.index')" class="text-gray-600 hover:underline pt-2">Annuler</Link>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded font-bold" :disabled="form.processing">
                            Créer la Tâche
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

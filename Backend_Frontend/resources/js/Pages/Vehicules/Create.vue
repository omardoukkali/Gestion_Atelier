<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    immatriculation: '',
    marque: '',
    modele: '',
    annee: '',
    km_actuel: '',
});

const submit = () => {
    form.post(route('vehicules.store'));
};
</script>

<template>
    <Head title="Ajouter un Véhicule" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un nouveau véhicule</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <pre class="text-xs bg-gray-100 p-2">type = {{ form.type }} | vehicules = {{ vehicules.length }}</pre>
                    <form @submit.prevent="submit" class="space-y-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Immatriculation (Plaque)</label>
                            <input v-model="form.immatriculation" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Ex: AB-123-CD">
                            <div v-if="form.errors.immatriculation" class="text-red-600 text-sm mt-1">{{ form.errors.immatriculation }}</div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Marque</label>
                                <input v-model="form.marque" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Ex: Renault">
                                <div v-if="form.errors.marque" class="text-red-600 text-sm mt-1">{{ form.errors.marque }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Modèle</label>
                                <input v-model="form.modele" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Ex: Clio 4">
                                <div v-if="form.errors.modele" class="text-red-600 text-sm mt-1">{{ form.errors.modele }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Année</label>
                                <input v-model="form.annee" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Ex: 2018">
                                <div v-if="form.errors.annee" class="text-red-600 text-sm mt-1">{{ form.errors.annee }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kilométrage actuel</label>
                                <input v-model="form.km_actuel" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Ex: 150000">
                                <div v-if="form.errors.km_actuel" class="text-red-600 text-sm mt-1">{{ form.errors.km_actuel }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-4">
                            <Link :href="route('vehicules.index')" class="text-gray-600 hover:underline">Annuler</Link>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" :disabled="form.processing">
                                Enregistrer
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

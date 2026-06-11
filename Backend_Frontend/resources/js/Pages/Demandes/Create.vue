<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

// Initialisation du formulaire
const form = useForm({
    type: '',
    description: '',
});

// Fonction pour envoyer les données au backend
const submit = () => {
    form.post(route('demandes.store'), {
        onSuccess: () => {
            // Optionnel : tu pourrais ajouter un message flash de succès ici
        }
    });
};
</script>

<template>
    <Head title="Nouvelle Demande" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Créer une nouvelle demande</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <form @submit.prevent="submit" class="space-y-6">

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Type de demande</label>
                            <select
                                id="type"
                                v-model="form.type"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="" disabled>Choisissez un type...</option>
                                <option value="entretien">Entretien</option>
                                <option value="fabrication">Fabrication</option>
                            </select>
                            <div v-if="form.errors.type" class="text-red-600 text-sm mt-1">{{ form.errors.type }}</div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description détaillée</label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Décrivez le problème ou le besoin..."
                                required
                            ></textarea>
                            <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-4">
                            <Link :href="route('demandes.index')" class="text-gray-600 hover:underline">
                                Annuler
                            </Link>

                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                :disabled="form.processing"
                            >
                                Envoyer la demande
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

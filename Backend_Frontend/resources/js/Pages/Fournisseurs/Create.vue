<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    nom: '',
    email: '',
    telephone: '',
    adresse: '',
});

const submit = () => form.post(route('fournisseurs.store'));
</script>

<template>
    <Head title="Ajouter Fournisseur" />
    <AuthenticatedLayout>
        <div class="py-12 max-w-2xl mx-auto">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom de l'entreprise</label>
                        <input v-model="form.nom" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email professionnel</label>
                        <input v-model="form.email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input v-model="form.telephone" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Adresse</label>
                        <textarea v-model="form.adresse" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" rows="3"></textarea>
                    </div>
                    <div class="flex justify-end gap-4">
                        <Link :href="route('fournisseurs.index')">Annuler</Link>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

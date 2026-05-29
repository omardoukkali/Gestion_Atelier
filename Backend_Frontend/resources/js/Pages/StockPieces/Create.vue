<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({ fournisseurs: Array });

const form = useForm({
    designation: '',
    quantite: 0,
    seuil_alerte: 5,
    prix_unitaire: '',
    fournisseur_id: '',
});

const submit = () => form.post(route('stock-pieces.store'));
</script>

<template>
    <Head title="Ajouter au Stock" />
    <AuthenticatedLayout>
        <div class="py-12 max-w-2xl mx-auto">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Désignation de la pièce</label>
                        <input v-model="form.designation" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Fournisseur</label>
                        <select v-model="form.fournisseur_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Sélectionner un fournisseur</option>
                            <option v-for="f in fournisseurs" :key="f.id" :value="f.id">{{ f.nom }}</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Quantité Initiale</label>
                            <input v-model="form.quantite" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Seuil d'alerte</label>
                            <input v-model="form.seuil_alerte" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Prix Unitaire (€)</label>
                        <input v-model="form.prix_unitaire" type="number" step="0.01" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <Link :href="route('stock-pieces.index')" class="text-gray-600 hover:underline pt-2">Annuler</Link>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded" :disabled="form.processing">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

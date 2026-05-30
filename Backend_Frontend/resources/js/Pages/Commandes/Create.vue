<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    fournisseurs: Array,
    pieces: Array,
});

const form = useForm({
    fournisseur_id: '',
    lignes: [
        { stock_piece_id: '', quantite: 1 } // On initialise avec une ligne vide par défaut
    ],
});

// Ajouter une nouvelle ligne vide
const addLigne = () => {
    form.lignes.push({ stock_piece_id: '', quantite: 1 });
};

// Supprimer une ligne spécifique
const removeLigne = (index) => {
    if (form.lignes.length > 1) {
        form.lignes.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('commandes.store'));
};
</script>

<template>
    <Head title="Nouvelle Commande" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Créer une Commande</h2>
        </template>

        <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="bg-gray-50 p-4 rounded-lg border">
                        <label class="block text-sm font-bold text-gray-700 mb-2">1. Sélectionner le Fournisseur</label>
                        <select v-model="form.fournisseur_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled>Choisir un fournisseur...</option>
                            <option v-for="f in fournisseurs" :key="f.id" :value="f.id">{{ f.nom }}</option>
                        </select>
                        <div v-if="form.errors.fournisseur_id" class="text-red-600 text-sm mt-1">{{ form.errors.fournisseur_id }}</div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border space-y-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-700">2. Pièces à commander</label>
                            <button type="button" @click="addLigne" class="text-sm bg-green-500 hover:bg-green-600 text-white py-1 px-3 rounded">
                                + Ajouter une pièce
                            </button>
                        </div>

                        <div v-for="(ligne, index) in form.lignes" :key="index" class="flex items-center gap-4 bg-white p-3 border rounded shadow-sm">
                            <div class="flex-1">
                                <select v-model="ligne.stock_piece_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="" disabled>Choisir une pièce...</option>
                                    <option v-for="p in pieces" :key="p.id" :value="p.id">{{ p.designation }} ({{ p.prix_unitaire }} €)</option>
                                </select>
                            </div>

                            <div class="w-24">
                                <input v-model="ligne.quantite" type="number" min="1" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Qté">
                            </div>

                            <button type="button" @click="removeLigne(index)" v-if="form.lignes.length > 1" class="text-red-500 hover:text-red-700 p-2" title="Supprimer la ligne">
                                ❌
                            </button>
                        </div>
                        <div v-if="form.errors.lignes" class="text-red-600 text-sm mt-1">{{ form.errors.lignes }}</div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <Link :href="route('commandes.index')" class="text-gray-600 hover:underline pt-2">Annuler</Link>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-bold" :disabled="form.processing">
                            Valider la commande
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

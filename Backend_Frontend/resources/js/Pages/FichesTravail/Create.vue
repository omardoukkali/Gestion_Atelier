<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    tasks: Array,
    pieces: Array,
});

const form = useForm({
    tache_id: '',
    date_debut: '',
    description: '',
    pieces: [] // Tableau vide au départ, on ajoute des pièces à la volée
});

const addPiece = () => {
    form.pieces.push({ stock_piece_id: '', quantite: 1 });
};

const removePiece = (index) => {
    form.pieces.splice(index, 1);
};

const submit = () => {
    form.post(route('fiches-travail.store'));
};
</script>

<template>
    <Head title="Nouvelle Fiche de Travail" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Saisir une Fiche de Travail</h2>
        </template>

        <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form @submit.prevent="submit" class="space-y-6">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">1. Tâche à traiter</label>
                        <select v-model="form.tache_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled>Choisir la tâche...</option>
                            <option v-for="t in tasks" :key="t.id" :value="t.id">
                                Tâche #{{ t.id }} - {{ t.type }} (Priorité: {{ t.priorite }})
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Date de début</label>
                            <input v-model="form.date_debut" type="datetime-local" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Description des travaux réalisés</label>
                        <textarea v-model="form.description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm" required placeholder="Ex: Vidange effectuée, remplacement du filtre..."></textarea>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border space-y-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-bold text-gray-700">2. Pièces utilisées (Optionnel)</label>
                            <button type="button" @click="addPiece" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-3 rounded">
                                + Ajouter une pièce
                            </button>
                        </div>

                        <div v-for="(piece, index) in form.pieces" :key="index" class="flex items-center gap-4 bg-white p-3 border rounded shadow-sm">
                            <div class="flex-1">
                                <select v-model="piece.stock_piece_id" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required>
                                    <option value="" disabled>Choisir une pièce...</option>
                                    <option v-for="p in pieces" :key="p.id" :value="p.id">
                                        {{ p.designation }} (En stock: {{ p.quantite }})
                                    </option>
                                </select>
                            </div>

                            <div class="w-24">
                                <input v-model="piece.quantite" type="number" min="1" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" required placeholder="Qté">
                            </div>

                            <button type="button" @click="removePiece(index)" class="text-red-500 hover:text-red-700 p-2 text-lg">
                                ❌
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <Link :href="route('dashboard')" class="text-gray-600 hover:underline pt-2">Annuler</Link>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-bold" :disabled="form.processing">
                            Enregistrer la Fiche
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

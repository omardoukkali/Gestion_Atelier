<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    tasks: Array,
    stockPieces: Array
});

// --- Gestion des statuts et couleurs des tâches ---
const priorityClass = (p) => {
    if (p === 'urgente') return 'bg-red-100 text-red-800 font-extrabold';
    if (p === 'haute') return 'bg-orange-100 text-orange-800';
    if (p === 'normale') return 'bg-blue-100 text-blue-800';
    return 'bg-gray-100 text-gray-800';
};

const statusClass = (s) => {
    if (s === 'terminee') return 'bg-green-100 text-green-800';
    if (s === 'en_cours') return 'bg-yellow-100 text-yellow-800';
    if (s === 'annulee') return 'bg-purple-100 text-purple-800';
    return 'bg-gray-100 text-gray-600';
};

// --- NOUVEAU : Couleurs pour le statut des pièces du devis ---
const devisStatusClass = (s) => {
    if (s === 'validee') return 'bg-green-100 text-green-700 font-bold';
    if (s === 'refusee') return 'bg-red-100 text-red-700';
    return 'bg-amber-100 text-amber-700 animate-pulse';
};

// --- Formulaires ---
const formStatus = useForm({});
const formActionDevis = useForm({}); // Formulaire pour valider/refuser une ligne

const updateTaskStatus = (id, action) => {
    formStatus.patch(route(`tasks.${action}`, id), { preserveScroll: true });
};

// Gestion des actions du Chef sur le devis
const validerLigne = (ligneId) => {
    formActionDevis.patch(route('ligne-devis.valider', ligneId), { preserveScroll: true });
};

const refuserLigne = (ligneId) => {
    formActionDevis.patch(route('ligne-devis.refuser', ligneId), { preserveScroll: true });
};

// --- Gestion Modale Ouvrier ---
const isModalOpen = ref(false);
const selectedTaskId = ref(null);
const devisForm = useForm({ stock_piece_id: '', quantite: 1 });

const openDevisModal = (taskId) => {
    selectedTaskId.value = taskId;
    devisForm.reset();
    isModalOpen.value = true;
};

const closeDevisModal = () => {
    isModalOpen.value = false;
    selectedTaskId.value = null;
    devisForm.reset();
};

const submitDevis = () => {
    devisForm.post(route('ligne-devis.store', selectedTaskId.value), {
        preserveScroll: true,
        onSuccess: () => closeDevisModal(),
    });
};
</script>

<template>
    <Head title="Liste des Tâches" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestion des Tâches</h2>
                <Link v-if="$page.props.auth.user.role === 'chef_atelier'" :href="route('tasks.create')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-bold text-sm">
                    + Nouvelle Tâche
                </Link>
            </div>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div v-if="$page.props.flash?.message" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm font-medium">
                {{ $page.props.flash.message }}
            </div>

            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3">ID / Détails & Devis</th>
                        <th class="px-6 py-3">Véhicule</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Assigné à</th>
                        <th class="px-6 py-3">Priorité</th>
                        <th class="px-6 py-3">Statut</th>
                        <th class="px-6 py-3" v-if="$page.props.auth.user.role === 'ouvrier'">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                    <template v-for="task in tasks" :key="task.id">
                        <tr class="hover:bg-gray-50 border-t-2 border-gray-100">
                            <td class="px-6 py-4 font-bold text-indigo-600">#{{ task.id }}</td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-900 block" v-if="task.vehicle">
                                    {{ task.vehicle.marque }} {{ task.vehicle.modele }}
                                </span>
                                <span class="text-xs text-gray-500" v-if="task.vehicle">{{ task.vehicle.immatriculation }}</span>
                            </td>
                            <td class="px-6 py-4 capitalize text-gray-600">{{ task.type }}</td>
                            <td class="px-6 py-4 text-gray-700 font-medium">
                                {{ task.assigne ? task.assigne.name : 'Non assigné' }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold uppercase', priorityClass(task.priorite)]">
                                    {{ task.priorite }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span :class="['px-2.5 py-1 rounded-full text-xs font-semibold', statusClass(task.statut)]">
                                    {{ task.statut.replace('_', ' ') }}
                                </span>
                            </td>

                            <td class="px-6 py-4 flex gap-2" v-if="$page.props.auth.user.role === 'ouvrier'">
                                <button v-if="task.statut === 'en_attente'" @click="updateTaskStatus(task.id, 'start')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-bold" :disabled="formStatus.processing">▶ Démarrer</button>
                                <button v-if="task.statut === 'en_cours'" @click="openDevisModal(task.id)" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-xs font-bold shadow-sm">🔧 Matériel</button>
                                <button v-if="task.statut === 'en_cours'" @click="updateTaskStatus(task.id, 'complete')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-bold" :disabled="formStatus.processing">✔ Terminer</button>
                            </td>
                        </tr>

                        <tr class="bg-gray-50/50">
                            <td colspan="7" class="px-8 py-3 text-xs border-b border-gray-200">
                                <div class="mb-2"><strong class="text-gray-700">Description :</strong> <span class="text-gray-600">{{ task.description }}</span></div>

                                <div v-if="task.lignes_devis && task.lignes_devis.length > 0" class="mt-3 bg-white p-3 rounded border border-gray-200 max-w-2xl">
                                    <h4 class="font-bold text-gray-700 mb-2 flex items-center gap-1">🛠️ Pièces demandées pour cette tâche :</h4>
                                    <div class="space-y-2">
                                        <div v-for="ligne in task.lignes_devis" :key="ligne.id" class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-100">
                                            <div>
                                                <span class="font-semibold text-gray-800">{{ ligne.piece?.designation }}</span>
                                                <span class="text-gray-500 mx-2">|</span> Quantité: <span class="font-bold text-indigo-600">{{ ligne.quantite }}</span>
                                                <span class="text-gray-500 mx-2">|</span> PU: <span class="text-gray-600">{{ ligne.prix_unitaire }} €</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span :class="['px-2 py-0.5 rounded text-[10px] uppercase tracking-wider', devisStatusClass(ligne.statut)]">
                                                    {{ ligne.statut.replace('_', ' ') }}
                                                </span>

                                                <div v-if="$page.props.auth.user.role === 'chef_atelier' && ligne.statut === 'en_attente'" class="flex gap-1">
                                                    <button @click="validerLigne(ligne.id)" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded font-bold text-[10px]" :disabled="formActionDevis.processing">✔ Valider</button>
                                                    <button @click="refuserLigne(ligne.id)" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded font-bold text-[10px]" :disabled="formActionDevis.processing">❌ Refuser</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr v-if="tasks.length === 0">
                        <td :colspan="$page.props.auth.user.role === 'ouvrier' ? 7 : 6" class="px-6 py-10 text-center text-gray-400">
                            Aucune tâche enregistrée pour le moment.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Demander du matériel (Tâche #{{ selectedTaskId }})</h3>
                <form @submit.prevent="submitDevis">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pièce requise</label>
                        <select v-model="devisForm.stock_piece_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="" disabled>-- Choisir une pièce --</option>
                            <option v-for="piece in stockPieces" :key="piece.id" :value="piece.id">
                                {{ piece.designation }} (Stock: {{ piece.quantite }})
                            </option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                        <input type="number" v-model="devisForm.quantite" min="1" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required />
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="closeDevisModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded font-bold text-sm hover:bg-gray-300">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded font-bold text-sm hover:bg-indigo-700" :disabled="devisForm.processing">Soumettre</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

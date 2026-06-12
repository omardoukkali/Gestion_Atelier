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

// --- Couleurs pour le statut des pièces du devis ---
const devisStatusClass = (s) => {
    if (s === 'validee') return 'bg-green-100 text-green-700 font-bold';
    if (s === 'refusee') return 'bg-red-100 text-red-700';
    return 'bg-amber-100 text-amber-700 animate-pulse';
};

// --- NOUVEAU : Vérifie si une tâche a des pièces en attente ---
const aDesPiecesEnAttente = (task) => {
    if (!task.lignes_devis) return false;
    return task.lignes_devis.some(ligne => ligne.statut === 'en_attente');
};

// --- Formulaires ---
const formStatus = useForm({});

// 🛒 MODIFIÉ : Formulaire pour valider/refuser une ligne (inclut la quantité)
const formActionDevis = useForm({
    quantite: 1
});

const updateTaskStatus = (id, action) => {
    formStatus.patch(route(`tasks.${action}`, id), { preserveScroll: true });
};

// 🛠️ MODIFIÉ : Gestion des actions du Chef sur le devis avec la quantité modifiée
const validerLigne = (ligneId, quantiteModifiee) => {
    formActionDevis.quantite = quantiteModifiee; // On injecte la quantité saisie dans le formulaire
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


// États pour la modale de compte-rendu
const showReportModal = ref(false);
const selectedTaskIdForReport = ref(null);

// Formulaire Inertia pour envoyer le compte-rendu
const reportForm = useForm({
    compte_rendu: ''
});

// Fonction déclenchée lors du clic sur le bouton vert "Terminer"
const ouvrirModaleCompteRendu = (taskId) => {
    selectedTaskIdForReport.value = taskId;
    reportForm.compte_rendu = ''; // On vide le champ
    showReportModal.value = true;  // On affiche la modale
};

// Fonction qui soumet le compte-rendu au backend
const soumettreFinTache = () => {
    reportForm.patch(route('tasks.complete', selectedTaskIdForReport.value), {
        onSuccess: () => {
            showReportModal.value = false; // Ferme la modale si succès
            reportForm.reset();
        }
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
                                <button v-if="task.statut === 'en_cours'" @click="ouvrirModaleCompteRendu(task.id)" :class="['px-3 py-1 rounded text-xs font-bold transition-all',aDesPiecesEnAttente(task) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 text-white']" :disabled="formStatus.processing || aDesPiecesEnAttente(task)" :title="aDesPiecesEnAttente(task) ? 'Impossible de terminer : attente de validation par le chef' : ''">✔ Terminer</button>
                            </td>
                        </tr>

                        <tr class="bg-gray-50/50">
                            <td colspan="7" class="px-8 py-3 text-xs border-b border-gray-200">
                                <div class="mb-2"><strong class="text-gray-700">Description :</strong> <span class="text-gray-600">{{ task.description }}</span></div>

                                <div v-if="task.statut === 'terminee' && task.compte_rendu" class="mt-3 mb-3 p-3 bg-green-50 border-l-4 border-green-500 rounded-r shadow-sm max-w-2xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-green-700 font-bold text-xs uppercase tracking-wider">
                                            📝 Compte-rendu de clôture
                                        </span>
                                    </div>
                                    <p class="text-sm text-green-900 italic">
                                        "{{ task.compte_rendu }}"
                                    </p>
                                </div>
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

                                                <div v-if="$page.props.auth.user.role === 'chef_atelier' && ligne.statut === 'en_attente'" class="flex items-center gap-2">
                                                    <div class="flex items-center bg-gray-100 px-1.5 py-0.5 rounded border border-gray-300">
                                                        <span class="text-[10px] text-gray-500 font-bold mr-1">Qté:</span>
                                                        <input
                                                            type="number"
                                                            v-model="ligne.quantite"
                                                            min="1"
                                                            class="w-12 p-0 text-xs border-0 bg-transparent focus:ring-0 text-center font-bold text-indigo-700"
                                                        />
                                                    </div>

                                                    <button @click="validerLigne(ligne.id, ligne.quantite)" class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded font-bold text-[10px]" :disabled="formActionDevis.processing">
                                                        ✔ Valider
                                                    </button>
                                                    <button @click="refuserLigne(ligne.id)" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded font-bold text-[10px]" :disabled="formActionDevis.processing">
                                                        ❌ Refuser
                                                    </button>
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

    <div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 text-slate-800">
            <h3 class="text-lg font-bold text-gray-900 mb-2">📝 Rapport de fin de tâche</h3>
            <p class="text-sm text-gray-600 mb-4">
                Expliquez brièvement les actions réalisées (surtout si des pièces ont été refusées) pour clore le dossier du véhicule.
            </p>

            <form @submit.prevent="soumettreFinTache">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Votre compte-rendu :</label>
                    <textarea
                        v-model="reportForm.compte_rendu"
                        rows="4"
                        class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none"
                        placeholder="Ex: Nettoyage des plaquettes existantes effectué, le freinage est à nouveau fonctionnel sans remplacement..."
                        required
                    ></textarea>
                    <div v-if="reportForm.errors.compte_rendu" class="text-red-500 text-xs mt-1">
                        {{ reportForm.errors.compte_rendu }}
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="showReportModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        :disabled="reportForm.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700 disabled:opacity-50"
                    >
                        {{ reportForm.processing ? 'Envoi...' : 'Valider et Clôturer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    demandes: Array,
    ouvriers: { type: Array, default: () => [] },
    vehicules: { type: Array, default: () => [] },
});

// --- Refus (simple) ---
const refuseForm = useForm({});
const refuser = (id) => {
    refuseForm.patch(route('demandes.refuser', id), { preserveScroll: true });
};

// --- Acceptation (via modale) ---
const showAcceptModal = ref(false);
const currentDemande = ref(null);
const acceptForm = useForm({
    assigne_id: '',
    priorite: 'normale',
    vehicle_id: '',
});

const openAccept = (demande) => {
    currentDemande.value = demande;
    acceptForm.reset();
    acceptForm.clearErrors();
    showAcceptModal.value = true;
};

const closeModal = () => {
    showAcceptModal.value = false;
    currentDemande.value = null;
};

const submitAccept = () => {
    acceptForm.patch(route('demandes.accepter', currentDemande.value.id), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
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
                            <button v-if="demande.statut === 'en_attente'" @click="openAccept(demande)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs font-bold">Accepter</button>
                            <button v-if="demande.statut === 'en_attente'" @click="refuser(demande.id)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs font-bold">Refuser</button>
                        </td>
                    </tr>
                    <tr v-if="demandes.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-gray-400">Aucune demande trouvée.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modale d'acceptation -->
        <Modal :show="showAcceptModal" @close="closeModal">
            <div class="p-6" v-if="currentDemande">
                <h2 class="text-lg font-bold text-gray-900">
                    Accepter la demande #{{ currentDemande.id }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Type : <span class="capitalize font-medium">{{ currentDemande.type }}</span> — {{ currentDemande.description }}
                </p>

                <div class="mt-6 space-y-4">
                    <div>
                        <InputLabel for="assigne_id" value="Affecter à l'ouvrier" />
                        <select id="assigne_id" v-model="acceptForm.assigne_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="" disabled>-- Choisir un ouvrier --</option>
                            <option v-for="o in ouvriers" :key="o.id" :value="o.id">{{ o.name }}</option>
                        </select>
                        <InputError :message="acceptForm.errors.assigne_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="priorite" value="Priorité" />
                        <select id="priorite" v-model="acceptForm.priorite"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="basse">Basse</option>
                            <option value="normale">Normale</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                        </select>
                        <InputError :message="acceptForm.errors.priorite" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="vehicle_id" value="Véhicule concerné (optionnel)" />
                        <select id="vehicle_id" v-model="acceptForm.vehicle_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- Aucun / Fabrication --</option>
                            <option v-for="v in vehicules" :key="v.id" :value="v.id">
                                {{ v.immatriculation }} — {{ v.marque }} {{ v.modele }}
                            </option>
                        </select>
                        <InputError :message="acceptForm.errors.vehicle_id" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal">Annuler</SecondaryButton>
                    <PrimaryButton :class="{ 'opacity-25': acceptForm.processing }" :disabled="acceptForm.processing" @click="submitAccept">
                        Accepter &amp; créer la tâche
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    tachesParStatut: Object,
    piecesEnAlerte: Array,
    valeurStock: [Number, String],
    dernieresDemandes: Array,
    chargeOuvriers: Array,
});

// Cartes du haut construites à partir des vraies stats
const cards = computed(() => [
    { name: 'Véhicules (parc)',        value: props.stats.vehicules,        icon: '🚗', color: 'bg-blue-500' },
    { name: 'Tâches en cours',         value: props.stats.taches_en_cours,  icon: '🔧', color: 'bg-indigo-500' },
    { name: 'Fiches aujourd\'hui',     value: props.stats.fiches_du_jour,   icon: '📝', color: 'bg-green-500' },
    { name: 'Alertes stock',           value: props.stats.stock_alerte,     icon: '⚠️', color: 'bg-red-500' },
    { name: 'Demandes en attente',     value: props.stats.demandes_attente, icon: '⏳', color: 'bg-yellow-500' },
    { name: 'Commandes à livrer',      value: props.stats.commandes_attente,icon: '📦', color: 'bg-purple-500' },
]);

// Échelle pour les barres "tâches par statut"
const maxTaches = computed(() =>
    Math.max(1, ...Object.values(props.tachesParStatut ?? {}))
);
const maxCharge = computed(() =>
    Math.max(1, ...(props.chargeOuvriers ?? []).map(o => o.total))
);

const libelleStatut = { en_attente: 'En attente', en_cours: 'En cours', terminee: 'Terminées', annulee: 'Annulées' };
const couleurStatut = { en_attente: 'bg-yellow-500', en_cours: 'bg-indigo-500', terminee: 'bg-green-500', annulee: 'bg-gray-400' };

const valeurStockFmt = computed(() =>
    new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(props.valeurStock ?? 0)
);
</script>

<template>
    <Head title="Tableau de bord - Chef d'Atelier" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tableau de Bord Supervision</h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- Bandeau bienvenue + valeur stock -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 flex items-center justify-between">
                    <div>
                        <div class="text-gray-900 font-bold text-lg">Bonjour, {{ $page.props.auth.user.name }} ! 👋</div>
                        <p class="text-sm text-gray-500 mt-1">Espace de supervision — <span class="font-semibold text-indigo-600">Chef d'Atelier</span>.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Valeur du stock</p>
                        <p class="text-2xl font-bold text-gray-900">{{ valeurStockFmt }}</p>
                    </div>
                </div>

                <!-- Cartes KPI -->
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div v-for="c in cards" :key="c.name" class="bg-white p-5 rounded-lg shadow-sm">
                        <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-lg text-white mb-3', c.color]">{{ c.icon }}</div>
                        <p class="text-2xl font-bold text-gray-900">{{ c.value }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ c.name }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Rapport : tâches par statut -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-4">Tâches par statut</h3>
                        <div class="space-y-3">
                            <div v-for="(total, statut) in tachesParStatut" :key="statut">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ libelleStatut[statut] ?? statut }}</span>
                                    <span class="font-semibold">{{ total }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div :class="['h-2.5 rounded-full', couleurStatut[statut] ?? 'bg-gray-400']"
                                         :style="{ width: (total / maxTaches * 100) + '%' }"></div>
                                </div>
                            </div>
                            <p v-if="!Object.keys(tachesParStatut).length" class="text-sm text-gray-400">Aucune tâche.</p>
                        </div>
                    </div>

                    <!-- Rapport : charge par ouvrier -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-4">Charge de travail par ouvrier</h3>
                        <div class="space-y-3">
                            <div v-for="o in chargeOuvriers" :key="o.ouvrier">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ o.ouvrier }}</span>
                                    <span class="font-semibold">{{ o.total }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-indigo-500 h-2.5 rounded-full" :style="{ width: (o.total / maxCharge * 100) + '%' }"></div>
                                </div>
                            </div>
                            <p v-if="!chargeOuvriers.length" class="text-sm text-gray-400">Aucune tâche affectée.</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Rapport : pièces sous le seuil -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-4">⚠️ Pièces à réapprovisionner</h3>
                        <table class="w-full text-sm">
                            <thead class="text-left text-gray-500 border-b">
                            <tr><th class="py-2">Pièce</th><th>Stock</th><th>Seuil</th><th>Fournisseur</th></tr>
                            </thead>
                            <tbody>
                            <tr v-for="p in piecesEnAlerte" :key="p.id" class="border-b last:border-0">
                                <td class="py-2 font-medium text-gray-800">{{ p.designation }}</td>
                                <td class="text-red-600 font-bold">{{ p.quantite }}</td>
                                <td class="text-gray-500">{{ p.seuil_alerte }}</td>
                                <td class="text-gray-500">{{ p.fournisseur?.nom ?? '—' }}</td>
                            </tr>
                            </tbody>
                        </table>
                        <p v-if="!piecesEnAlerte.length" class="text-sm text-green-600 mt-2">✅ Aucun stock sous le seuil.</p>
                    </div>

                    <!-- Rapport : dernières demandes -->
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-bold text-gray-700 mb-4">Dernières demandes</h3>
                        <ul class="divide-y">
                            <li v-for="d in dernieresDemandes" :key="d.id" class="py-2 flex justify-between text-sm">
                                <span class="text-gray-700">{{ d.employe?.name ?? 'N/A' }} — {{ d.type }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                      :class="d.statut === 'en_attente' ? 'bg-yellow-100 text-yellow-700' : d.statut === 'acceptee' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                    {{ d.statut }}
                                </span>
                            </li>
                        </ul>
                        <Link href="/demandes" class="text-indigo-600 text-sm font-semibold mt-3 inline-block">Voir toutes →</Link>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

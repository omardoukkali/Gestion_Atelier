<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

/* ---------- MODÈLE 1 : DURÉE DE VIE PLAQUETTES (régression) ---------- */
const form = ref({
    pct_ville: 50,
    style_conduite: 'normal',
    type_vehicule: 'berline',
    transmission: 'manuelle',
    relief: 'plat',
    qualite_plaquette: 'standard',
});
const resultat = ref(null);
const loading = ref(false);
const erreur = ref(null);

const predire = async () => {
    loading.value = true; erreur.value = null; resultat.value = null;
    try {
        const { data } = await axios.post('/api/predict-lifespan', form.value);
        resultat.value = data.duree_vie_km;
    } catch (e) {
        erreur.value = "Impossible de contacter le service IA.";
    } finally { loading.value = false; }
};

/* ---------- MODÈLE 2 : BESOIN DE MAINTENANCE (classification) ---------- */
const formM = ref({
    Vehicle_Model: 'Car', Mileage: 60000, Maintenance_History: 'Good',
    Reported_Issues: 1, Vehicle_Age: 5, Fuel_Type: 'Petrol',
    Transmission_Type: 'Manual', Engine_Size: 1600, Odometer_Reading: 60000,
    Owner_Type: 'First', Insurance_Premium: 15000, Service_History: 4,
    Accident_History: 0, Fuel_Efficiency: 15.0, Tire_Condition: 'Good',
    Brake_Condition: 'Good', Battery_Status: 'Good',
});
const resultatM = ref(null);
const loadingM = ref(false);
const erreurM = ref(null);

// Remplit le formulaire avec un véhicule d'exemple (usé → devrait déclencher une alerte)
const exemple = () => {
    formM.value = {
        Vehicle_Model: 'Truck', Mileage: 145000, Maintenance_History: 'Poor',
        Reported_Issues: 4, Vehicle_Age: 9, Fuel_Type: 'Diesel',
        Transmission_Type: 'Automatic', Engine_Size: 2500, Odometer_Reading: 145000,
        Owner_Type: 'Third', Insurance_Premium: 32000, Service_History: 1,
        Accident_History: 3, Fuel_Efficiency: 9.5, Tire_Condition: 'Worn Out',
        Brake_Condition: 'Worn Out', Battery_Status: 'Weak',
    };
    resultatM.value = null;
};

const predireM = async () => {
    loadingM.value = true; erreurM.value = null; resultatM.value = null;
    try {
        const { data } = await axios.post('/api/predict-maintenance', formM.value);
        resultatM.value = data;   // { need_maintenance, probability }
    } catch (e) {
        erreurM.value = "Impossible de contacter le service IA.";
    } finally { loadingM.value = false; }
};

// Options des menus déroulants (valeurs exactes du dataset)
const OPT = {
    Vehicle_Model: ['Bus','Car','Motorcycle','SUV','Truck','Van'],
    Maintenance_History: ['Average','Good','Poor'],
    Fuel_Type: ['Diesel','Electric','Petrol'],
    Transmission_Type: ['Automatic','Manual'],
    Owner_Type: ['First','Second','Third'],
    Tire_Condition: ['Good','New','Worn Out'],
    Brake_Condition: ['Good','New','Worn Out'],
    Battery_Status: ['Good','New','Weak'],
};
</script>

<template>
    <Head title="Prédictions IA" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Prédictions IA — Atelier</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-8">

                <!-- ============ CARTE 1 : DURÉE DE VIE PLAQUETTES ============ -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900">Durée de vie des plaquettes de frein</h3>
                    <p class="text-sm text-gray-500">Conditions d'usage → estimation en km.</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Conduite en ville : {{ form.pct_ville }} %
                        </label>
                        <input type="range" min="0" max="100" v-model.number="form.pct_ville" class="w-full mt-1">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Style de conduite</label>
                            <select v-model="form.style_conduite" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="eco">Éco</option><option value="normal">Normal</option><option value="sportif">Sportif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type de véhicule</label>
                            <select v-model="form.type_vehicule" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="citadine">Citadine</option><option value="berline">Berline</option>
                                <option value="SUV">SUV</option><option value="utilitaire">Utilitaire</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transmission</label>
                            <select v-model="form.transmission" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="manuelle">Manuelle</option><option value="automatique">Automatique</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Relief</label>
                            <select v-model="form.relief" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="plat">Plat</option><option value="vallonne">Vallonné</option><option value="montagne">Montagne</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Qualité des plaquettes</label>
                            <select v-model="form.qualite_plaquette" class="mt-1 w-full rounded-md border-gray-300">
                                <option value="economique">Économique</option><option value="standard">Standard</option><option value="premium">Premium</option>
                            </select>
                        </div>
                    </div>

                    <button @click="predire" :disabled="loading"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                        {{ loading ? 'Calcul...' : 'Estimer la durée de vie' }}
                    </button>

                    <div v-if="resultat !== null" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                        <p class="text-sm text-gray-600">Durée de vie estimée</p>
                        <p class="text-3xl font-bold text-green-700">≈ {{ resultat.toLocaleString('fr-FR') }} km</p>
                    </div>
                    <div v-if="erreur" class="mt-2 p-3 bg-red-50 text-red-700 rounded">{{ erreur }}</div>
                </div>

                <!-- ============ CARTE 2 : BESOIN DE MAINTENANCE ============ -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Besoin de maintenance ? (oui/non)</h3>
                        <button @click="exemple" class="text-sm bg-gray-100 hover:bg-gray-200 py-1 px-3 rounded">
                            Remplir un exemple
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Menus déroulants (texte) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Modèle</label>
                            <select v-model="formM.Vehicle_Model" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Vehicle_Model" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Historique entretien</label>
                            <select v-model="formM.Maintenance_History" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Maintenance_History" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Carburant</label>
                            <select v-model="formM.Fuel_Type" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Fuel_Type" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Transmission</label>
                            <select v-model="formM.Transmission_Type" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Transmission_Type" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Propriétaire</label>
                            <select v-model="formM.Owner_Type" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Owner_Type" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">État pneus</label>
                            <select v-model="formM.Tire_Condition" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Tire_Condition" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">État freins</label>
                            <select v-model="formM.Brake_Condition" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Brake_Condition" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600">Batterie</label>
                            <select v-model="formM.Battery_Status" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                <option v-for="o in OPT.Battery_Status" :key="o" :value="o">{{ o }}</option>
                            </select>
                        </div>

                        <!-- Champs numériques -->
                        <div><label class="block text-xs font-medium text-gray-600">Kilométrage (Mileage)</label>
                            <input type="number" v-model.number="formM.Mileage" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Odomètre</label>
                            <input type="number" v-model.number="formM.Odometer_Reading" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Âge (ans)</label>
                            <input type="number" v-model.number="formM.Vehicle_Age" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Cylindrée</label>
                            <input type="number" v-model.number="formM.Engine_Size" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Pannes signalées</label>
                            <input type="number" v-model.number="formM.Reported_Issues" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Nb entretiens</label>
                            <input type="number" v-model.number="formM.Service_History" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Nb accidents</label>
                            <input type="number" v-model.number="formM.Accident_History" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Prime assurance</label>
                            <input type="number" v-model.number="formM.Insurance_Premium" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                        <div><label class="block text-xs font-medium text-gray-600">Conso (L/100)</label>
                            <input type="number" step="0.1" v-model.number="formM.Fuel_Efficiency" class="mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                    </div>

                    <button @click="predireM" :disabled="loadingM"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50">
                        {{ loadingM ? 'Analyse...' : 'Analyser' }}
                    </button>

                    <!-- Résultat : badge vert ou rouge -->
                    <div v-if="resultatM" class="mt-4 p-4 rounded-lg text-center"
                         :class="resultatM.need_maintenance ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'">
                        <p class="text-2xl font-bold" :class="resultatM.need_maintenance ? 'text-red-700' : 'text-green-700'">
                            {{ resultatM.need_maintenance ? '⚠️ Maintenance conseillée' : '✅ Rien à signaler' }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            Probabilité : {{ (resultatM.probability * 100).toFixed(1) }} %
                        </p>
                    </div>
                    <div v-if="erreurM" class="mt-2 p-3 bg-red-50 text-red-700 rounded">{{ erreurM }}</div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

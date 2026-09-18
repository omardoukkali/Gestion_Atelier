# train.py
import pandas as pd
import joblib
from sklearn.model_selection import train_test_split
from sklearn.compose import ColumnTransformer, make_column_selector
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.pipeline import Pipeline
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import classification_report, confusion_matrix, roc_auc_score

# 1. Charger le CSV
df = pd.read_csv("vehicle_maintenance.csv")   # ← adapte au nom exact de ton fichier

# 2. Retirer les 2 colonnes de dates (texte brut, inutilisables telles quelles)
df = df.drop(columns=["Last_Service_Date", "Warranty_Expiry_Date"])

# 3. Séparer les entrées (X) de la cible (y)
target = "Need_Maintenance"
X = df.drop(columns=[target])
y = df[target]

# 4. Pré-traitement AUTOMATIQUE : numérique -> standardisé, texte -> encodé
preprocessor = ColumnTransformer([
    ("num", StandardScaler(), make_column_selector(dtype_include="number")),
    ("cat", OneHotEncoder(handle_unknown="ignore"), make_column_selector(dtype_include="object")),
])

# 5. Le modèle = pré-traitement + régression logistique, en un seul objet
model = Pipeline([
    ("prep", preprocessor),
    ("clf", LogisticRegression(max_iter=1000, class_weight="balanced")),
])

# 6. Séparer 80% entraînement / 20% test (stratify garde la proportion 81/19)
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y
)

# 7. Entraîner
model.fit(X_train, y_train)

# 8. Évaluer (jamais l'accuracy seule)
y_pred = model.predict(X_test)
y_proba = model.predict_proba(X_test)[:, 1]
print(classification_report(y_test, y_pred, digits=3))
print("Matrice de confusion :\n", confusion_matrix(y_test, y_pred))
print("ROC-AUC :", round(roc_auc_score(y_test, y_proba), 3))

# 9. Sauvegarder le modèle complet -> à copier ensuite dans DevOps/ml-service/
joblib.dump(model, "model.pkl")
# Sauvegarder le "contrat d'entrée" : colonnes + types attendus par l'API
X.dtypes.astype(str).to_json("model_columns.json")
print("model_columns.json sauvegardé.")
print("\nmodel.pkl sauvegardé.")
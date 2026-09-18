from fastapi import FastAPI
from typing import Dict, Any
import joblib
import pandas as pd

app = FastAPI(title="Atelier ML - Prédiction maintenance")

model = joblib.load("model.pkl")
dtypes = pd.read_json("model_columns.json", typ="series")   # colonnes + types attendus

@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/predict")
def predict(features: Dict[str, Any]):
    df = pd.DataFrame([features])[dtypes.index]   # bon ordre de colonnes
    df = df.astype(dtypes.to_dict())              # mêmes types qu'à l'entraînement
    proba = float(model.predict_proba(df)[0][1])
    return {
        "need_maintenance": int(proba >= 0.5),
        "probability": round(proba, 3),
    }
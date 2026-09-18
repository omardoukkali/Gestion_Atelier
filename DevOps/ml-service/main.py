from fastapi import FastAPI
from typing import Dict, Any
import joblib
import pandas as pd

app = FastAPI(title="Atelier ML")

# --- Modèle 1 : maintenance oui/non ---
model = joblib.load("model.pkl")
dtypes = pd.read_json("model_columns.json", typ="series")

# --- Modèle 2 : durée de vie des plaquettes ---
model_life = joblib.load("model_lifespan.pkl")
dtypes_life = pd.read_json("model_lifespan_columns.json", typ="series")

@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/predict")
def predict(features: Dict[str, Any]):
    df = pd.DataFrame([features])[dtypes.index].astype(dtypes.to_dict())
    proba = float(model.predict_proba(df)[0][1])
    return {"need_maintenance": int(proba >= 0.5), "probability": round(proba, 3)}

@app.post("/predict-lifespan")
def predict_lifespan(features: Dict[str, Any]):
    df = pd.DataFrame([features])[dtypes_life.index].astype(dtypes_life.to_dict())
    km = float(model_life.predict(df)[0])
    return {"duree_vie_km": round(km)}
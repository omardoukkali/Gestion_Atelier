<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MlService
{
    public function predict(array $features): array
    {
        return Http::timeout(5)
            ->post(config('services.ml.url').'/predict', $features)
            ->json();
    }

    public function predictLifespan(array $features): array
    {
        return Http::timeout(5)
            ->post(config('services.ml.url').'/predict-lifespan', $features)
            ->json();
    }
}

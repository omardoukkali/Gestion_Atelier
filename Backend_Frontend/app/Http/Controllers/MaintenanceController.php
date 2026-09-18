<?php

namespace App\Http\Controllers;

use App\Services\MlService;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function predict(Request $request, MlService $ml)
    {
        return response()->json($ml->predict($request->all()));
    }
}

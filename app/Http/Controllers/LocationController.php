<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function getCitiesByState(Request $request, string $stateId): JsonResponse
    {
        $cities = City::where('state_id', $stateId)->get(['id', 'name']);
        return response()->json($cities);
    }
}

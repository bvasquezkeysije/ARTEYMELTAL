<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SunatController extends Controller
{
    public function consultarRuc(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'numero' => ['required', 'string', 'size:11', 'regex:/^[0-9]{11}$/'],
        ]);

        $apiKey = (string) config('services.decolecta.api_key');
        $baseUrl = rtrim((string) config('services.decolecta.base_url', 'https://api.decolecta.com'), '/');

        if ($apiKey === '') {
            return response()->json([
                'message' => 'No hay API key configurada para Decolecta.',
            ], 500);
        }

        $response = Http::timeout(15)
            ->acceptJson()
            ->withToken($apiKey)
            ->get($baseUrl.'/v1/sunat/ruc', [
                'numero' => $datos['numero'],
            ]);

        if (! $response->ok()) {
            return response()->json([
                'message' => 'No se pudo consultar SUNAT en este momento.',
                'status' => $response->status(),
                'error' => $response->json('message'),
            ], 422);
        }

        return response()->json($response->json());
    }
}


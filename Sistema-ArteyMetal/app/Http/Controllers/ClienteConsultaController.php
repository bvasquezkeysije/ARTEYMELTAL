<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClienteConsultaController extends Controller
{
    public function consultarPorDocumento(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'numero' => ['required', 'string'],
        ]);

        $numero = preg_replace('/\D+/', '', trim((string) $datos['numero']));
        if (! in_array(strlen($numero), [8, 11], true)) {
            return response()->json([
                'ok' => false,
                'fuente' => 'manual',
                'message' => 'El documento debe tener 8 digitos (DNI) o 11 digitos (RUC).',
                'cliente' => null,
            ], 422);
        }

        $tipo = strlen($numero) === 11 ? 'ruc' : 'dni';

        $clienteLocal = Cliente::query()->where('documento', $numero)->first();
        if ($clienteLocal) {
            return response()->json([
                'ok' => true,
                'fuente' => 'local',
                'tipo' => $tipo,
                'message' => 'Cliente encontrado en el sistema.',
                'cliente' => [
                    'id' => $clienteLocal->id,
                    'nombre' => $clienteLocal->nombre_completo,
                    'documento' => $clienteLocal->documento,
                    'telefono' => $clienteLocal->telefono,
                    'correo' => $clienteLocal->correo,
                    'direccion' => $clienteLocal->direccion,
                ],
            ]);
        }

        if ($tipo === 'dni') {
            return $this->consultarDniReniec($numero);
        }

        if ($tipo === 'ruc') {
            return $this->consultarRucSunat($numero);
        }
    }

    private function consultarDniReniec(string $numero): JsonResponse
    {
        $apiKey = (string) config('services.decolecta.api_key');
        $baseUrl = rtrim((string) config('services.decolecta.base_url', 'https://api.decolecta.com'), '/');
        $path = (string) config('services.decolecta.reniec_dni_path', '/v1/reniec/dni');

        if ($apiKey === '') {
            return response()->json([
                'ok' => false,
                'fuente' => 'manual',
                'tipo' => 'dni',
                'message' => 'No hay API key configurada para consulta RENIEC.',
                'cliente' => null,
            ], 422);
        }

        $response = Http::timeout(15)
            ->acceptJson()
            ->withToken($apiKey)
            ->get($baseUrl.$path, [
                'numero' => $numero,
                'token' => $apiKey,
            ]);

        if (! $response->ok()) {
            $status = $response->status();
            $mensaje = $status === 401
                ? 'RENIEC rechazo la autenticacion (token invalido o expirado).'
                : ($response->json('message') ?: 'No se pudo consultar RENIEC.');

            return response()->json([
                'ok' => false,
                'fuente' => 'manual',
                'tipo' => 'dni',
                'message' => $mensaje.' Completa los datos manualmente.',
                'status' => $status,
                'cliente' => null,
            ], 422);
        }

        $data = $response->json();
        $nombre = trim((string) ($data['nombre_completo']
            ?? $data['full_name']
            ?? (($data['nombres'] ?? '') . ' ' . ($data['apellido_paterno'] ?? '') . ' ' . ($data['apellido_materno'] ?? ''))));
        $nombreLimpio = $nombre !== '' ? preg_replace('/\s+/', ' ', $nombre) : null;

        return response()->json([
            'ok' => true,
            'fuente' => 'reniec',
            'tipo' => 'dni',
            'message' => 'DNI encontrado en RENIEC. Datos cargados para completar el pedido.',
            'cliente' => [
                'id' => null,
                'nombre' => $nombreLimpio,
                'documento' => $data['numero_documento'] ?? $data['document_number'] ?? $data['dni'] ?? $numero,
                'telefono' => null,
                'correo' => null,
                'direccion' => null,
            ],
        ]);
    }

    private function consultarRucSunat(string $numero): JsonResponse
    {
        $apiKey = (string) config('services.decolecta.api_key');
        $baseUrl = rtrim((string) config('services.decolecta.base_url', 'https://api.decolecta.com'), '/');
        $path = (string) config('services.decolecta.sunat_ruc_path', '/v1/sunat/ruc');

        if ($apiKey === '') {
            return response()->json([
                'ok' => false,
                'fuente' => 'manual',
                'tipo' => 'ruc',
                'message' => 'No hay API key configurada para consulta SUNAT.',
                'cliente' => null,
            ], 422);
        }

        $response = Http::timeout(15)
            ->acceptJson()
            ->withToken($apiKey)
            ->get($baseUrl.$path, [
                'numero' => $numero,
                'token' => $apiKey,
            ]);

        if (! $response->ok()) {
            $status = $response->status();
            $mensaje = $status === 401
                ? 'SUNAT rechazo la autenticacion (token invalido o expirado).'
                : ($response->json('message') ?: 'No se pudo consultar SUNAT.');

            return response()->json([
                'ok' => false,
                'fuente' => 'manual',
                'tipo' => 'ruc',
                'message' => $mensaje.' Completa los datos manualmente.',
                'status' => $status,
                'cliente' => null,
            ], 422);
        }

        $data = $response->json();
        $direccion = trim((string) ($data['direccion'] ?? ''));

        return response()->json([
            'ok' => true,
            'fuente' => 'sunat',
            'tipo' => 'ruc',
            'message' => 'RUC encontrado en SUNAT. Datos cargados para completar el pedido.',
            'cliente' => [
                'id' => null,
                'nombre' => $data['razon_social'] ?? null,
                'documento' => $data['numero_documento'] ?? $numero,
                'telefono' => null,
                'correo' => null,
                'direccion' => $direccion !== '' ? $direccion : null,
                'distrito' => $data['distrito'] ?? null,
                'estado' => $data['estado'] ?? null,
                'condicion' => $data['condicion'] ?? null,
            ],
        ]);
    }
}

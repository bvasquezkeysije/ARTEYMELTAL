<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteInicialSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre_completo' => 'Bardales Vasquez Keysi Jeanpierre',
                'documento' => '76636255',
                'telefono' => '999999999',
                'correo' => 'bvasquezkeysije@gmail.com',
                'direccion' => 'Chiclayo',
                'observaciones' => 'Cliente frecuente de pedidos personalizados.',
            ],
            [
                'nombre_completo' => 'Asenjo Carranza Enrique David',
                'documento' => '16753899',
                'telefono' => '987456123',
                'correo' => 'asenjo.david@gmail.com',
                'direccion' => 'Lambayeque',
                'observaciones' => null,
            ],
            [
                'nombre_completo' => 'Rosa Campos',
                'documento' => '72114455',
                'telefono' => '951112233',
                'correo' => 'rosa.campos@outlook.com',
                'direccion' => 'Pimentel',
                'observaciones' => 'Prefiere coordinacion por WhatsApp.',
            ],
            [
                'nombre_completo' => 'Carlos Mena',
                'documento' => '74561230',
                'telefono' => '987654321',
                'correo' => 'cmena@gmail.com',
                'direccion' => 'Jose Leonardo Ortiz',
                'observaciones' => null,
            ],
            [
                'nombre_completo' => 'Municipalidad Distrital de Pimentel',
                'documento' => '20123456789',
                'telefono' => '979555444',
                'correo' => 'logistica@munipimentel.gob.pe',
                'direccion' => 'Calle Grau 100, Pimentel',
                'observaciones' => 'Solicita orden de compra adjunta.',
            ],
            [
                'nombre_completo' => 'Colegio San Jose',
                'documento' => '20512345678',
                'telefono' => '999111222',
                'correo' => 'compras@sanjose.edu.pe',
                'direccion' => 'Av. Saenz Pena 425, Chiclayo',
                'observaciones' => 'Solicita factura.',
            ],
        ];

        foreach ($clientes as $cliente) {
            Cliente::query()->updateOrCreate(
                ['documento' => $cliente['documento']],
                $cliente
            );
        }
    }
}


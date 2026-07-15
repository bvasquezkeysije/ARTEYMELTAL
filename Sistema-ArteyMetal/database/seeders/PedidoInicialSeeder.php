<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Seeder;

class PedidoInicialSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioAdmin = User::query()
            ->where('email', 'bvasquezkeysije@arteymetal.online')
            ->orWhere('email', 'pfernandezadeli@arteymetal.online')
            ->orderBy('id')
            ->first();

        if (! $usuarioAdmin) {
            return;
        }

        $clienteMunicipalidad = Cliente::query()->where('documento', '20123456789')->first();
        $clienteColegio = Cliente::query()->where('documento', '20512345678')->first();
        $clienteRosa = Cliente::query()->where('documento', '72114455')->first();

        $pedidos = [
            [
                'codigo' => 'PED-000001',
                'cliente_id' => $clienteColegio?->id,
                'nombre_cliente' => 'Colegio San Jose',
                'telefono_cliente' => '999111222',
                'documento_cliente' => '20512345678',
                'correo_cliente' => 'compras@sanjose.edu.pe',
                'tipo_producto' => 'Medallas 1er, 2do y 3er puesto',
                'tipo_entrega' => 'local',
                'direccion_entrega' => null,
                'referencia_entrega' => null,
                'distrito_entrega' => null,
                'codigo_postal_entrega' => null,
                'nombre_recibe' => null,
                'telefono_recibe' => null,
                'costo_delivery' => null,
                'detalle_trabajo' => 'Lote de 40 medallas con cintas institucionales y grabado de evento.',
                'cantidad' => 40,
                'estado' => 'entregado',
                'estado_personalizacion' => 'entregado',
                'fecha_entrega_compromiso' => '2026-05-02',
                'fecha_inicio_diseno' => '2026-04-23',
                'fecha_aprobacion_diseno' => '2026-04-24',
                'archivo_diseno_path' => null,
                'observaciones_personalizacion' => 'Diseno aprobado por comite organizador.',
                'monto_total' => 800.00,
                'estado_pago' => 'pagado_completo',
                'monto_adelanto' => 400.00,
                'monto_saldo' => 0.00,
                'observaciones' => 'Pedido cerrado y entregado.',
                'usuario_id' => $usuarioAdmin->id,
            ],
            [
                'codigo' => 'PED-000002',
                'cliente_id' => $clienteMunicipalidad?->id,
                'nombre_cliente' => 'Municipalidad Distrital de Pimentel',
                'telefono_cliente' => '979555444',
                'documento_cliente' => '20123456789',
                'correo_cliente' => 'logistica@munipimentel.gob.pe',
                'tipo_producto' => 'Placas conmemorativas en bronce',
                'tipo_entrega' => 'agencia',
                'direccion_entrega' => 'Terminal de agencia Flores, Chiclayo',
                'referencia_entrega' => 'Enviar a nombre de Logistica MDP',
                'distrito_entrega' => 'Chiclayo',
                'codigo_postal_entrega' => '14001',
                'nombre_recibe' => 'Jose Perez',
                'telefono_recibe' => '945888777',
                'costo_delivery' => 25.00,
                'detalle_trabajo' => '12 placas institucionales con base de marmol y grabado laser.',
                'cantidad' => 12,
                'estado' => 'en_produccion',
                'estado_personalizacion' => 'en_produccion',
                'fecha_entrega_compromiso' => '2026-05-10',
                'fecha_inicio_diseno' => '2026-04-25',
                'fecha_aprobacion_diseno' => '2026-04-26',
                'archivo_diseno_path' => null,
                'observaciones_personalizacion' => 'Orden de compra validada.',
                'monto_total' => 1200.00,
                'estado_pago' => 'adelanto_pagado',
                'monto_adelanto' => 600.00,
                'monto_saldo' => 600.00,
                'observaciones' => 'Requiere envio con guia y sello de recepcion.',
                'usuario_id' => $usuarioAdmin->id,
            ],
            [
                'codigo' => 'PED-000003',
                'cliente_id' => $clienteRosa?->id,
                'nombre_cliente' => 'Rosa Campos',
                'telefono_cliente' => '951112233',
                'documento_cliente' => '72114455',
                'correo_cliente' => 'rosa.campos@outlook.com',
                'tipo_producto' => 'Reconocimientos en vidrio grabado',
                'tipo_entrega' => 'delivery',
                'direccion_entrega' => 'Calle Los Alamos 245',
                'referencia_entrega' => 'Casa color blanco, frente al parque',
                'distrito_entrega' => 'Pimentel',
                'codigo_postal_entrega' => '14000',
                'nombre_recibe' => 'Rosa Campos',
                'telefono_recibe' => '951112233',
                'costo_delivery' => 10.00,
                'detalle_trabajo' => 'Set de 6 reconocimientos para premiacion academica.',
                'cantidad' => 6,
                'estado' => 'registrado',
                'estado_personalizacion' => 'en_diseno',
                'fecha_entrega_compromiso' => '2026-05-15',
                'fecha_inicio_diseno' => '2026-04-27',
                'fecha_aprobacion_diseno' => null,
                'archivo_diseno_path' => null,
                'observaciones_personalizacion' => 'Pendiente aprobacion de arte final.',
                'monto_total' => 540.00,
                'estado_pago' => 'pendiente_adelanto',
                'monto_adelanto' => 0.00,
                'monto_saldo' => 540.00,
                'observaciones' => 'Cliente solicita entrega por la tarde.',
                'usuario_id' => $usuarioAdmin->id,
            ],
        ];

        foreach ($pedidos as $pedido) {
            Pedido::query()->updateOrCreate(
                ['codigo' => $pedido['codigo']],
                $pedido
            );
        }
    }
}

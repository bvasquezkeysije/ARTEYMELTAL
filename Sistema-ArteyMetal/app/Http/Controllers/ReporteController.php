<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtrosVentas = [
            'fecha_inicio' => $request->input('ventas_fecha_inicio'),
            'fecha_fin' => $request->input('ventas_fecha_fin'),
            'tipo' => $request->input('ventas_tipo'),
        ];

        $queryVentas = $this->queryVentasFiltradas($filtrosVentas);

        $kpisVentas = [
            'total_vendido' => (float) (clone $queryVentas)->sum('monto_total'),
            'total_cobrado' => (float) (clone $queryVentas)->sum('monto_cobrado'),
            'ticket_promedio' => (float) ((clone $queryVentas)->avg('monto_total') ?? 0),
            'cantidad' => (int) (clone $queryVentas)->count(),
        ];

        $ventas = (clone $queryVentas)
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $filtrosPedidos = [
            'fecha_inicio' => $request->input('pedidos_fecha_inicio'),
            'fecha_fin' => $request->input('pedidos_fecha_fin'),
            'estado' => $request->input('pedidos_estado'),
            'tipo_entrega' => $request->input('pedidos_tipo_entrega'),
        ];

        $queryPedidos = $this->queryPedidosFiltrados($filtrosPedidos);

        $kpisPedidos = [
            'total' => (int) (clone $queryPedidos)->count(),
            'registrados' => (int) (clone $queryPedidos)->where('estado', 'registrado')->count(),
            'en_produccion' => (int) (clone $queryPedidos)->where('estado', 'en_produccion')->count(),
            'listo_entrega' => (int) (clone $queryPedidos)->where('estado', 'listo_entrega')->count(),
            'entregados' => (int) (clone $queryPedidos)->where('estado', 'entregado')->count(),
            'atrasados' => (int) (clone $queryPedidos)
                ->whereDate('fecha_entrega_compromiso', '<', now()->toDateString())
                ->whereNotIn('estado', ['entregado', 'cancelado'])
                ->count(),
        ];

        $pedidos = (clone $queryPedidos)
            ->orderByDesc('fecha_entrega_compromiso')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $querySaldosPendientes = Pedido::query()
            ->where(function ($query) {
                $query->where('monto_saldo', '>', 0)
                    ->orWhere('estado_pago', '!=', 'pagado_completo');
            });

        if (! empty($filtrosPedidos['fecha_inicio'])) {
            $querySaldosPendientes->whereDate('created_at', '>=', $filtrosPedidos['fecha_inicio']);
        }
        if (! empty($filtrosPedidos['fecha_fin'])) {
            $querySaldosPendientes->whereDate('created_at', '<=', $filtrosPedidos['fecha_fin']);
        }

        $saldosPendientes = (clone $querySaldosPendientes)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        $kpisSaldos = [
            'pedidos_con_saldo' => (int) (clone $querySaldosPendientes)->count(),
            'total_pendiente' => (float) (clone $querySaldosPendientes)->sum('monto_saldo'),
        ];

        $umbralStock = max((int) $request->input('stock_umbral', 10), 0);
        $queryStock = Producto::query()->where('stock_actual', '<=', $umbralStock);

        $stockBajo = (clone $queryStock)
            ->orderBy('stock_actual')
            ->orderBy('nombre')
            ->limit(30)
            ->get();

        $kpisStock = [
            'umbral' => $umbralStock,
            'productos_bajo' => (int) (clone $queryStock)->count(),
            'unidades_en_riesgo' => (int) (clone $queryStock)->sum('stock_actual'),
        ];

        $ventasPorDia = (clone $queryVentas)
            ->selectRaw('DATE(fecha_venta) as dia, SUM(monto_cobrado) as total')
            ->groupByRaw('DATE(fecha_venta)')
            ->orderBy('dia')
            ->get();

        $ventasPorTipo = (clone $queryVentas)
            ->selectRaw('tipo_venta, COUNT(*) as cantidad')
            ->groupBy('tipo_venta')
            ->orderBy('tipo_venta')
            ->get();

        $pedidosPorEstado = (clone $queryPedidos)
            ->selectRaw('estado, COUNT(*) as cantidad')
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        $pedidosPorEntrega = (clone $queryPedidos)
            ->selectRaw('COALESCE(tipo_entrega, \'local\') as tipo_entrega, COUNT(*) as cantidad')
            ->groupByRaw('COALESCE(tipo_entrega, \'local\')')
            ->orderBy('tipo_entrega')
            ->get();

        $stockCriticoTop = (clone $queryStock)
            ->orderBy('stock_actual')
            ->orderBy('nombre')
            ->limit(10)
            ->get(['nombre', 'stock_actual']);

        $graficos = [
            'ventas_por_dia' => [
                'labels' => $ventasPorDia->map(fn ($f) => Carbon::parse((string) $f->dia)->format('d/m'))->values(),
                'data' => $ventasPorDia->map(fn ($f) => round((float) $f->total, 2))->values(),
            ],
            'ventas_por_tipo' => [
                'labels' => $ventasPorTipo->map(fn ($f) => $f->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock')->values(),
                'data' => $ventasPorTipo->map(fn ($f) => (int) $f->cantidad)->values(),
            ],
            'pedidos_por_estado' => [
                'labels' => $pedidosPorEstado->map(fn ($f) => ucfirst(str_replace('_', ' ', (string) $f->estado)))->values(),
                'data' => $pedidosPorEstado->map(fn ($f) => (int) $f->cantidad)->values(),
            ],
            'pedidos_por_entrega' => [
                'labels' => $pedidosPorEntrega->map(fn ($f) => ucfirst((string) $f->tipo_entrega))->values(),
                'data' => $pedidosPorEntrega->map(fn ($f) => (int) $f->cantidad)->values(),
            ],
            'stock_critico' => [
                'labels' => $stockCriticoTop->map(fn ($f) => (string) $f->nombre)->values(),
                'data' => $stockCriticoTop->map(fn ($f) => (int) $f->stock_actual)->values(),
            ],
        ];

        return view('reportes.index', compact(
            'filtrosVentas',
            'kpisVentas',
            'ventas',
            'filtrosPedidos',
            'kpisPedidos',
            'pedidos',
            'saldosPendientes',
            'kpisSaldos',
            'stockBajo',
            'kpisStock',
            'graficos'
        ));
    }

    public function exportarVentasCsv(Request $request): StreamedResponse
    {
        $filtrosVentas = [
            'fecha_inicio' => $request->input('ventas_fecha_inicio'),
            'fecha_fin' => $request->input('ventas_fecha_fin'),
            'tipo' => $request->input('ventas_tipo'),
        ];

        $ventas = $this->queryVentasFiltradas($filtrosVentas)
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->get();

        $nombre = 'reporte_ventas_' . now()->format('Ymd_His');

        return $this->descargarCsv($nombre, [
            'Codigo', 'Fecha', 'Tipo', 'Cliente', 'Monto total', 'Monto cobrado', 'Estado pago',
        ], $ventas->map(function ($venta) {
            return [
                $venta->codigo,
                optional($venta->fecha_venta)->format('d/m/Y'),
                $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock',
                $venta->cliente_nombre ?: '-',
                number_format((float) $venta->monto_total, 2, '.', ''),
                number_format((float) $venta->monto_cobrado, 2, '.', ''),
                str_replace('_', ' ', (string) $venta->estado_pago),
            ];
        })->all());
    }

    public function exportarVentasExcel(Request $request): StreamedResponse
    {
        $filtrosVentas = [
            'fecha_inicio' => $request->input('ventas_fecha_inicio'),
            'fecha_fin' => $request->input('ventas_fecha_fin'),
            'tipo' => $request->input('ventas_tipo'),
        ];

        $ventas = $this->queryVentasFiltradas($filtrosVentas)
            ->orderByDesc('fecha_venta')
            ->orderByDesc('id')
            ->get();

        $nombre = 'reporte_ventas_' . now()->format('Ymd_His');

        return $this->descargarXlsx(
            $nombre,
            'Ventas',
            ['Codigo', 'Fecha', 'Tipo', 'Cliente', 'Monto total', 'Monto cobrado', 'Estado pago'],
            $ventas->map(function ($venta) {
                return [
                    $venta->codigo,
                    optional($venta->fecha_venta)->format('d/m/Y'),
                    $venta->tipo_venta === 'pedido' ? 'Cierre pedido' : 'Venta stock',
                    $venta->cliente_nombre ?: '-',
                    number_format((float) $venta->monto_total, 2, '.', ''),
                    number_format((float) $venta->monto_cobrado, 2, '.', ''),
                    str_replace('_', ' ', (string) $venta->estado_pago),
                ];
            })->all()
        );
    }

    public function exportarPedidosCsv(Request $request): StreamedResponse
    {
        $filtros = [
            'fecha_inicio' => $request->input('pedidos_fecha_inicio'),
            'fecha_fin' => $request->input('pedidos_fecha_fin'),
            'estado' => $request->input('pedidos_estado'),
            'tipo_entrega' => $request->input('pedidos_tipo_entrega'),
        ];

        $pedidos = $this->queryPedidosFiltrados($filtros)
            ->orderByDesc('fecha_entrega_compromiso')
            ->orderByDesc('id')
            ->get();

        return $this->descargarCsv(
            'reporte_pedidos_' . now()->format('Ymd_His'),
            ['Codigo', 'Cliente', 'Estado', 'Tipo entrega', 'Fecha compromiso', 'Total'],
            $pedidos->map(function ($pedido) {
                return [
                    $pedido->codigo,
                    $pedido->nombre_cliente,
                    str_replace('_', ' ', (string) $pedido->estado),
                    ucfirst((string) ($pedido->tipo_entrega ?? 'local')),
                    optional($pedido->fecha_entrega_compromiso)->format('d/m/Y') ?: '-',
                    $pedido->monto_total !== null ? number_format((float) $pedido->monto_total, 2, '.', '') : '',
                ];
            })->all()
        );
    }

    public function exportarPedidosExcel(Request $request): StreamedResponse
    {
        $filtros = [
            'fecha_inicio' => $request->input('pedidos_fecha_inicio'),
            'fecha_fin' => $request->input('pedidos_fecha_fin'),
            'estado' => $request->input('pedidos_estado'),
            'tipo_entrega' => $request->input('pedidos_tipo_entrega'),
        ];

        $pedidos = $this->queryPedidosFiltrados($filtros)
            ->orderByDesc('fecha_entrega_compromiso')
            ->orderByDesc('id')
            ->get();

        return $this->descargarXlsx(
            'reporte_pedidos_' . now()->format('Ymd_His'),
            'Pedidos',
            ['Codigo', 'Cliente', 'Estado', 'Tipo entrega', 'Fecha compromiso', 'Total'],
            $pedidos->map(function ($pedido) {
                return [
                    $pedido->codigo,
                    $pedido->nombre_cliente,
                    str_replace('_', ' ', (string) $pedido->estado),
                    ucfirst((string) ($pedido->tipo_entrega ?? 'local')),
                    optional($pedido->fecha_entrega_compromiso)->format('d/m/Y') ?: '-',
                    $pedido->monto_total !== null ? number_format((float) $pedido->monto_total, 2, '.', '') : '',
                ];
            })->all()
        );
    }

    public function exportarSaldosCsv(Request $request): StreamedResponse
    {
        $querySaldos = Pedido::query()
            ->where(function ($query) {
                $query->where('monto_saldo', '>', 0)
                    ->orWhere('estado_pago', '!=', 'pagado_completo');
            });

        if ($request->filled('pedidos_fecha_inicio')) {
            $querySaldos->whereDate('created_at', '>=', $request->input('pedidos_fecha_inicio'));
        }
        if ($request->filled('pedidos_fecha_fin')) {
            $querySaldos->whereDate('created_at', '<=', $request->input('pedidos_fecha_fin'));
        }

        $saldos = (clone $querySaldos)->orderByDesc('id')->get();

        return $this->descargarCsv(
            'reporte_saldos_' . now()->format('Ymd_His'),
            ['Codigo', 'Cliente', 'Cancelado', 'Saldo pendiente', 'Porcentaje cancelado'],
            $saldos->map(function ($pedido) {
                $total = (float) ($pedido->monto_total ?? 0);
                $adelanto = (float) ($pedido->monto_adelanto ?? 0);
                $saldo = (float) ($pedido->monto_saldo ?? 0);
                $porcentaje = $total > 0 ? round((($total - $saldo) / $total) * 100, 2) : 0;
                return [
                    $pedido->codigo,
                    $pedido->nombre_cliente,
                    number_format($adelanto, 2, '.', ''),
                    number_format($saldo, 2, '.', ''),
                    number_format($porcentaje, 2, '.', '') . '%',
                ];
            })->all()
        );
    }

    public function exportarSaldosExcel(Request $request): StreamedResponse
    {
        $querySaldos = Pedido::query()
            ->where(function ($query) {
                $query->where('monto_saldo', '>', 0)
                    ->orWhere('estado_pago', '!=', 'pagado_completo');
            });

        if ($request->filled('pedidos_fecha_inicio')) {
            $querySaldos->whereDate('created_at', '>=', $request->input('pedidos_fecha_inicio'));
        }
        if ($request->filled('pedidos_fecha_fin')) {
            $querySaldos->whereDate('created_at', '<=', $request->input('pedidos_fecha_fin'));
        }

        $saldos = (clone $querySaldos)->orderByDesc('id')->get();

        return $this->descargarXlsx(
            'reporte_saldos_' . now()->format('Ymd_His'),
            'Saldos',
            ['Codigo', 'Cliente', 'Cancelado', 'Saldo pendiente', 'Porcentaje cancelado'],
            $saldos->map(function ($pedido) {
                $total = (float) ($pedido->monto_total ?? 0);
                $adelanto = (float) ($pedido->monto_adelanto ?? 0);
                $saldo = (float) ($pedido->monto_saldo ?? 0);
                $porcentaje = $total > 0 ? round((($total - $saldo) / $total) * 100, 2) : 0;
                return [
                    $pedido->codigo,
                    $pedido->nombre_cliente,
                    number_format($adelanto, 2, '.', ''),
                    number_format($saldo, 2, '.', ''),
                    number_format($porcentaje, 2, '.', '') . '%',
                ];
            })->all()
        );
    }

    public function exportarStockCsv(Request $request): StreamedResponse
    {
        $umbral = max((int) $request->input('stock_umbral', 10), 0);
        $stock = Producto::query()
            ->where('stock_actual', '<=', $umbral)
            ->orderBy('stock_actual')
            ->orderBy('nombre')
            ->get(['codigo', 'nombre', 'categoria', 'stock_actual']);

        return $this->descargarCsv(
            'reporte_stock_bajo_' . now()->format('Ymd_His'),
            ['Codigo', 'Producto', 'Categoria', 'Stock actual'],
            $stock->map(fn ($producto) => [
                $producto->codigo,
                $producto->nombre,
                $producto->categoria,
                (string) $producto->stock_actual,
            ])->all()
        );
    }

    public function exportarStockExcel(Request $request): StreamedResponse
    {
        $umbral = max((int) $request->input('stock_umbral', 10), 0);
        $stock = Producto::query()
            ->where('stock_actual', '<=', $umbral)
            ->orderBy('stock_actual')
            ->orderBy('nombre')
            ->get(['codigo', 'nombre', 'categoria', 'stock_actual']);

        return $this->descargarXlsx(
            'reporte_stock_bajo_' . now()->format('Ymd_His'),
            'Stock',
            ['Codigo', 'Producto', 'Categoria', 'Stock actual'],
            $stock->map(fn ($producto) => [
                $producto->codigo,
                $producto->nombre,
                $producto->categoria,
                (string) $producto->stock_actual,
            ])->all()
        );
    }

    private function queryVentasFiltradas(array $filtros)
    {
        return Venta::query()
            ->when(! empty($filtros['fecha_inicio']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '>=', $filtros['fecha_inicio']);
            })
            ->when(! empty($filtros['fecha_fin']), function ($query) use ($filtros) {
                $query->whereDate('fecha_venta', '<=', $filtros['fecha_fin']);
            })
            ->when(! empty($filtros['tipo']), function ($query) use ($filtros) {
                $query->where('tipo_venta', $filtros['tipo']);
            });
    }

    private function queryPedidosFiltrados(array $filtros)
    {
        return Pedido::query()
            ->when(! empty($filtros['fecha_inicio']), function ($query) use ($filtros) {
                $query->whereDate('created_at', '>=', $filtros['fecha_inicio']);
            })
            ->when(! empty($filtros['fecha_fin']), function ($query) use ($filtros) {
                $query->whereDate('created_at', '<=', $filtros['fecha_fin']);
            })
            ->when(! empty($filtros['estado']), function ($query) use ($filtros) {
                $query->where('estado', $filtros['estado']);
            })
            ->when(! empty($filtros['tipo_entrega']), function ($query) use ($filtros) {
                $query->where('tipo_entrega', $filtros['tipo_entrega']);
            });
    }

    private function descargarCsv(string $nombreBase, array $encabezados, array $filas): StreamedResponse
    {
        return response()->streamDownload(function () use ($encabezados, $filas) {
            $salida = fopen('php://output', 'w');
            fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fwrite($salida, "sep=;\n");
            fputcsv($salida, $encabezados, ';');
            foreach ($filas as $fila) {
                fputcsv($salida, $fila, ';');
            }
            fclose($salida);
        }, $nombreBase . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function descargarXlsx(string $nombreBase, string $hoja, array $encabezados, array $filas): StreamedResponse
    {
        $sheetName = mb_substr((string) (preg_replace('/[\[\]\*\/\\\\\?\:]/', '', $hoja) ?: 'Reporte'), 0, 31);
        $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');

        if ($tmpFile === false) {
            abort(500, 'No se pudo generar el archivo Excel.');
        }

        $zip = new ZipArchive();
        $openResult = $zip->open($tmpFile, ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            @unlink($tmpFile);
            abort(500, 'No se pudo crear el archivo Excel.');
        }

        $columnCount = max(1, count($encabezados));
        $lastColumn = $this->excelColumnName($columnCount);
        $rowCount = 1 + count($filas);

        $worksheetRows = '';
        $worksheetRows .= '<row r="1">';
        foreach ($encabezados as $i => $titulo) {
            $cellRef = $this->excelColumnName($i + 1) . '1';
            $worksheetRows .= '<c r="' . $cellRef . '" s="1" t="inlineStr"><is><t xml:space="preserve">' . $this->xmlEscape((string) $titulo) . '</t></is></c>';
        }
        $worksheetRows .= '</row>';

        foreach ($filas as $rowIndex => $fila) {
            $r = $rowIndex + 2;
            $worksheetRows .= '<row r="' . $r . '">';
            for ($col = 0; $col < $columnCount; $col++) {
                $valor = array_key_exists($col, $fila) ? (string) $fila[$col] : '';
                $cellRef = $this->excelColumnName($col + 1) . $r;
                $worksheetRows .= '<c r="' . $cellRef . '" t="inlineStr"><is><t xml:space="preserve">' . $this->xmlEscape($valor) . '</t></is></c>';
            }
            $worksheetRows .= '</row>';
        }

        $contentTypes = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $this->xmlEscape($sheetName) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';

        $workbookRels = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';

        $styles = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2"><font><sz val="10"/><name val="Calibri"/></font><font><b/><sz val="10"/><name val="Calibri"/></font></fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF5ECCE"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="1" borderId="0" xfId="0" applyFont="1" applyFill="1"/></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';

        $worksheet = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<dimension ref="A1:' . $lastColumn . $rowCount . '"/>'
            . '<sheetData>' . $worksheetRows . '</sheetData>'
            . '<autoFilter ref="A1:' . $lastColumn . '1"/>'
            . '</worksheet>';

        $created = now()->toIso8601String();
        $core = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>Arte y Metales</dc:creator>'
            . '<cp:lastModifiedBy>Arte y Metales</cp:lastModifiedBy>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $created . '</dcterms:modified>'
            . '</cp:coreProperties>';

        $app = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            . '<Application>Arte y Metales</Application>'
            . '</Properties>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
        $zip->addFromString('docProps/core.xml', $core);
        $zip->addFromString('docProps/app.xml', $app);
        $zip->close();

        return response()->streamDownload(function () use ($tmpFile) {
            $fp = fopen($tmpFile, 'rb');
            if ($fp !== false) {
                fpassthru($fp);
                fclose($fp);
            }
            @unlink($tmpFile);
        }, $nombreBase . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    private function excelColumnName(int $column): string
    {
        $name = '';
        while ($column > 0) {
            $mod = ($column - 1) % 26;
            $name = chr(65 + $mod) . $name;
            $column = intdiv($column - 1, 26);
        }
        return $name ?: 'A';
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}

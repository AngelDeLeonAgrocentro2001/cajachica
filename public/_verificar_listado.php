<?php
/**
 * SCRIPT TEMPORAL DE VERIFICACIÓN — BORRAR DESPUÉS DE USAR.
 *
 * Compara el "Total Gastos" calculado con el método VIEJO (traer todas las facturas
 * anidadas y sumar en PHP) contra el método NUEVO (SUM en SQL) para el listado en
 * modo revisar. Debe reportar 0 diferencias.
 *
 * Uso: iniciar sesión en la app y abrir:
 *   http://localhost:8080/agrocaja-chica/public/_verificar_listado.php
 */

header('Content-Type: text/plain; charset=utf-8');
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Inicia sesión en la app primero, luego recarga esta página.\n";
    exit;
}

require_once '../config/database.php';
require_once '../models/Liquidacion.php';
require_once '../models/DetalleLiquidacion.php';

$liquidacionModel = new Liquidacion();
$detalleModel = new DetalleLiquidacion();

// Mismos estados que usa listLiquidaciones() en modo revisar
$estadosRevisar = ['PENDIENTE_REVISION_CONTABILIDAD', 'FINALIZADO', 'RECHAZADO_POR_CONTABILIDAD', 'EN_PROCESO'];

$liquidaciones = $liquidacionModel->getAllLiquidaciones(null, null, $estadosRevisar);
$liquidaciones = array_values(array_reduce($liquidaciones, function ($carry, $l) {
    $carry[$l['id']] = $l;
    return $carry;
}, []));
$ids = array_column($liquidaciones, 'id');

echo "Liquidaciones en modo revisar: " . count($liquidaciones) . "\n";
echo str_repeat('-', 60) . "\n";

// ---- MÉTODO VIEJO ----
$t0 = microtime(true);
$detallesPorLiquidacion = $detalleModel->getDetallesByLiquidacionIds($ids);
$viejo = [];
$totalFilasDetalle = 0;
foreach ($liquidaciones as $l) {
    $dets = $detallesPorLiquidacion[$l['id']] ?? [];
    $totalFilasDetalle += count($dets);
    $suma = 0;
    foreach ($dets as $d) {
        $suma += floatval($d['total_factura'] ?? 0);
    }
    $viejo[$l['id']] = number_format($suma, 2);
}
$tViejo = microtime(true) - $t0;

// Tamaño aproximado del JSON viejo (con detalles anidados)
$liqViejo = [];
foreach ($liquidaciones as $l) {
    $l['detalles'] = $detallesPorLiquidacion[$l['id']] ?? [];
    $l['total_gastos'] = $viejo[$l['id']];
    $liqViejo[] = $l;
}
$bytesViejo = strlen(json_encode($liqViejo, JSON_UNESCAPED_UNICODE));

// ---- MÉTODO NUEVO ----
$t0 = microtime(true);
$totalesPorLiquidacion = $detalleModel->getTotalGastosByLiquidacionIds($ids);
$nuevo = [];
foreach ($liquidaciones as $l) {
    $nuevo[$l['id']] = number_format(floatval($totalesPorLiquidacion[$l['id']] ?? 0), 2);
}
$tNuevo = microtime(true) - $t0;

$liqNuevo = [];
foreach ($liquidaciones as $l) {
    $l['total_gastos'] = $nuevo[$l['id']];
    $liqNuevo[] = $l;
}
$bytesNuevo = strlen(json_encode($liqNuevo, JSON_UNESCAPED_UNICODE));

// ---- COMPARACIÓN ----
$difs = [];
foreach ($liquidaciones as $l) {
    if ($viejo[$l['id']] !== $nuevo[$l['id']]) {
        $difs[] = "  Liquidación {$l['id']}: viejo={$viejo[$l['id']]}  nuevo={$nuevo[$l['id']]}";
    }
}

echo "Filas de detalle que traía el método viejo: " . $totalFilasDetalle . "\n";
echo "Filas que trae el método nuevo:            " . count($totalesPorLiquidacion) . " (una por liquidación con facturas)\n";
echo "\n";
echo "Tiempo método viejo: " . number_format($tViejo * 1000, 1) . " ms\n";
echo "Tiempo método nuevo: " . number_format($tNuevo * 1000, 1) . " ms\n";
echo "\n";
echo "Tamaño JSON de 'liquidaciones' (viejo, con facturas anidadas): " . number_format($bytesViejo / 1024, 1) . " KB\n";
echo "Tamaño JSON de 'liquidaciones' (nuevo, sin facturas):          " . number_format($bytesNuevo / 1024, 1) . " KB\n";
echo "Reducción: " . number_format((1 - $bytesNuevo / max($bytesViejo, 1)) * 100, 1) . " %\n";
echo str_repeat('-', 60) . "\n";

if (empty($difs)) {
    echo "RESULTADO: OK — 0 diferencias en 'Total Gastos'. El listado se ve igual.\n";
} else {
    echo "RESULTADO: " . count($difs) . " DIFERENCIA(S) encontradas:\n";
    echo implode("\n", array_slice($difs, 0, 30)) . "\n";
    if (count($difs) > 30) {
        echo "  ... y " . (count($difs) - 30) . " más\n";
    }
}

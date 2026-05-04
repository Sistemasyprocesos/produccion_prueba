<?php
// pdf_reporte_planificacion.php
// Ruta de dompdf: sale de registers/ y entra a dompdf/
require_once __DIR__ . '/../dompdf/autoload.inc.php';
include '../connection/conexion.php';

use Dompdf\Dompdf;
use Dompdf\Options;
date_default_timezone_set('America/Guayaquil');
$desde = $_GET['desde'] ?? '';
$hasta  = $_GET['hasta']  ?? '';

if (empty($desde) || empty($hasta)) {
    die('Fechas requeridas');
}

$desde = $conn->real_escape_string($desde);
$hasta  = $conn->real_escape_string($hasta);

$f = $conn->query("SELECT 
    p.id_pedido,
    p.num_pedido as identificacionpedido,
    pr.nombre as pedidonom,
    f.secuencia,
    GROUP_CONCAT(DISTINCT ap.abreviatura ORDER BY ap.abreviatura SEPARATOR '+') as actividades,
    a.turnodn as turno, 
    f.unds as undsstd,
    a.fecha_turno,
    u.sigla as sigla,
    a.obj_kg as objetivo,
    a.unidades_reales as reales,
    a.hc as personas,
  ROUND(
    (
        SUM(a.unidades_reales * f.peso_env * u.equivalente_kg)
        /
        NULLIF(SUM(a.obj_kg), 0)
    ) * 100
,2) as cumplimiento
FROM prod_pedidos as p 
INNER JOIN prod_productos as pr ON pr.id = p.producto
INNER JOIN prod_fases_prod as f ON f.producto = pr.id
INNER JOIN prod_act_prod as ap ON f.actividad = ap.id
INNER JOIN prod_udm as u ON u.id = f.udm_env
LEFT JOIN prod_avance_pedido a 
    ON a.id_pedido = p.id_pedido
    AND a.secuencia = f.secuencia
    AND a.fecha_turno BETWEEN '$desde' AND '$hasta'
WHERE a.fecha_turno IS NOT NULL
GROUP BY 
    p.id_pedido, f.secuencia, a.fecha_turno,
    f.unds, u.sigla, a.obj_kg, a.unidades_reales, a.turnodn, a.hc
ORDER BY 
    a.fecha_turno ASC, p.id_pedido DESC, f.secuencia ASC");

$rows = [];
while ($g = $f->fetch_assoc()) {
    $rows[] = $g;
}

// ── Construir HTML para dompdf ──────────────────────────────────────────────
// ── Construir HTML para dompdf ──────────────────────────────────────────────
$filas = '';

// Agrupar por fecha
$porFecha = [];
foreach ($rows as $r) {
    $porFecha[$r['fecha_turno']][] = $r;
}

foreach ($porFecha as $fecha => $grupo) {
    $totalFilas = count($grupo);

    foreach ($grupo as $i => $r) {
        $cumplimiento = floatval($r['cumplimiento'] ?? 0);
        $hue   = round((min($cumplimiento, 100) * 120) / 100);
        $color = "hsl($hue, 70%, 35%)";
        $orden = htmlspecialchars($r['pedidonom'] . ' (' . $r['actividades'] . ')');

        // La celda de fecha solo va en la primera fila del grupo
        $celdaFecha = '';
        if ($i === 0) {
            $celdaFecha = "
            <td rowspan='{$totalFilas}' 
                style='background:#d1e7dd; font-weight:bold; color:#0a3622; 
                       vertical-align:middle; text-align:center; border-right:2px solid #198754;'>
                {$fecha}
            </td>";
        }

        $filas .= "
        <tr>
          {$celdaFecha}
            <td>" . htmlspecialchars($r['identificacionpedido']) . "</td>
            <td style='text-align:left;'>{$orden}</td>
            <td>" . htmlspecialchars($r['turno'] ?? '—') . "</td>
            <td>" . htmlspecialchars($r['undsstd'] ?? '—') . "</td>
            <td>" . htmlspecialchars($r['objetivo'] . ' ' . $r['sigla']) . "</td>
            <td>" . htmlspecialchars($r['reales'] ?? '—') . "</td>
            <td style='color:{$color}; font-weight:bold;'>{$cumplimiento}%</td>
            <td>" . htmlspecialchars($r['personas'] ?? '—') . "</td>
        </tr>";
    }

    // Fila de subtotal por fecha
    $subObj   = array_sum(array_column($grupo, 'objetivo'));
    $subReal  = array_sum(array_column($grupo, 'reales'));
    $subcumplimiento=array_sum(array_column($grupo, 'cumplimiento')) / count($grupo);
    $subCump  = $subObj > 0 ? round($subcumplimiento, 2) : 0;
    $subColor = "hsl(" . round((min($subCump,100)*120)/100) . ", 70%, 35%)";
$subpersonal= array_sum(array_column($grupo, 'personas'));
    $filas .= "
    <tr style='background:#f0fdf4; font-style:italic;'>
        <td colspan='5' style='text-align:right; color:#555; font-size:8px;'>
            Subtotal {$fecha}</td>
        <td style='font-weight:bold;'>" . number_format($subObj, 2) . "</td>
        <td style='font-weight:bold;'>" . number_format($subReal, 2) . "</td>
        <td style='color:{$subColor}; font-weight:bold;'>{$subCump}%</td>
        <td style='font-weight:bold;'>" . number_format($subpersonal) . "</td>
    </tr>";
}

if (empty($filas)) {
    $filas = "<tr><td colspan='9' style='text-align:center;color:#888;'>
                No se encontraron registros en ese rango de fechas
              </td></tr>";
}

$html = "
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<style>
  body        { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 20px; }
  h2          { font-size: 14px; text-align: center; margin-bottom: 4px; }
  .sub        { text-align: center; color: #555; font-size: 9px; margin-bottom: 12px; }
  table       { width: 100%; border-collapse: collapse; }
  th          { background: #198754; color: #fff; padding: 5px 4px; text-align: center; font-size: 8px; }
  td          { padding: 4px; border: 1px solid #ddd; text-align: center; vertical-align: middle; }
  tr:nth-child(even) td { background: #f9f9f9; }

  .footer     { margin-top: 14px; font-size: 8px; color: #888; text-align: right; }
</style>
</head>
<body>
  <h2>Reporte de Planificación</h2>
  <p class='sub'>Período: <strong>{$desde}</strong> al <strong>{$hasta}</strong></p>

  <table>
    <thead>
      <tr>
        <th>FECHA PLANIFICADA</th>
        <th>PEDIDO</th>
        <th>ORDEN DE PRODUCCIÓN</th>
        <th>TURNO</th>
        <th>UNIDADES ESTÁNDAR</th>
        <th>OBJETIVO</th>
        <th>UNIDADES REALES</th>
        <th>CUMPLIMIENTO</th>
        <th>HC</th>
      </tr>
    </thead>
    <tbody>
      {$filas}
    </tbody>
  </table>

  <p class='footer'>Generado el " . date('d/m/Y H:i') . "</p>
</body>
</html>";

// ── Renderizar con dompdf ───────────────────────────────────────────────────
$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');   // horizontal por la cantidad de columnas
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream(
    "reporte_planificacion_{$desde}_{$hasta}.pdf",
    ['Attachment' => false]   // false = abre en el navegador; true = descarga
);
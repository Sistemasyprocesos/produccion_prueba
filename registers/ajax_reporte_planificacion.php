<?php
include '../connection/conexion.php';

$desde = $_POST['desde'] ?? '';
$hasta = $_POST['hasta'] ?? '';

if (empty($desde) || empty($hasta)) {
    echo json_encode(['error' => 'Fechas requeridas']);
    exit;
}

$desde = $conn->real_escape_string($desde);
$hasta = $conn->real_escape_string($hasta);

$whereFecha = " AND a.fecha_turno BETWEEN '$desde' AND '$hasta' ";

$f = $conn->query("SELECT 
    p.id_pedido,
    p.num_pedido as identificacionpedido,
    pr.nombre as pedidonom,
    f.secuencia,
    u.equivalente_kg as pesoequi,
    GROUP_CONCAT(DISTINCT ap.abreviatura ORDER BY ap.abreviatura SEPARATOR '+') as actividades,
    a.turnodn as turno, 
    f.unds as undsstd,
    a.fecha_turno,
    u.sigla as sigla,
    a.obj_kg as objetivo,
    a.unidades_reales as reales,
    a.hc as personas,
    f.peso_env as pesoenv,
    ar.nombre as areanombre,
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
INNER JOIN prod_udm as u ON u.id=f.udm_env
inner join prod_area_prod as ar on ar.id=f.area
LEFT JOIN prod_avance_pedido a 
    ON a.id_pedido = p.id_pedido
    AND a.secuencia = f.secuencia
    AND a.fecha_turno BETWEEN '$desde' AND '$hasta'

    where a.fecha_turno IS NOT NULL

GROUP BY 
    p.id_pedido,
    f.secuencia,
    a.fecha_turno,
    f.unds,u.sigla,a.obj_kg,a.unidades_reales,a.turnodn,a.hc,u.equivalente_kg,f.peso_env,ar.nombre

ORDER BY 
    a.fecha_turno ASC,
    p.id_pedido DESC,
    f.secuencia ASC;");

$rows = [];
while ($g = $f->fetch_assoc()) {
    $rows[] = [
        'area'        => $g['areanombre'],
        'fecha_turno' => $g['fecha_turno'],
        'orden'       => $g['pedidonom'] . ' (' . $g['actividades'].')',
        'undsstd'     => $g['undsstd'],
        'objetivo'    => $g['objetivo'].' '.$g['sigla'],
        'reales'      => $g['reales']*$g['pesoenv']*$g['pesoequi'].' KG',
        'cumplimiento'=> $g['cumplimiento'],
        'turno'       => $g['turno'],
        'personas'    => $g['personas'],
        'idenpedido'  => $g['identificacionpedido'],    
    ];
}

header('Content-Type: application/json');
echo json_encode($rows);
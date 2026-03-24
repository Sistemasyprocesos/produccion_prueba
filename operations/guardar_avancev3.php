<?php
include '../connection/conexion.php';

$id_pedido = intval($_POST['id_pedido']);

$real    = $_POST['real']    ?? [];
$fecha   = $_POST['fecha']   ?? [];
$jornada = $_POST['jornada'] ?? [];
$hc      = $_POST['hc']      ?? [];
$peso_arr = $_POST['peso'] ?? [];
$eq_arr   = $_POST['eq'] ?? [];


$sql = "INSERT INTO prod_avance_pedido 
            (id_pedido, secuencia, turno, fecha_turno, turnodn, hc, unidades_reales,kg_real)
        VALUES (?, ?, ?, ?, ?, ?, ?,?)
        ON DUPLICATE KEY UPDATE
            fecha_turno   = VALUES(fecha_turno),
            turnodn = VALUES(turnodn),
            hc      = VALUES(hc),
        unidades_reales = VALUES(unidades_reales),
        kg_real = VALUES(kg_real)
        ";

$stmt = $conn->prepare($sql);
$guardados = 0;
$errores   = 0;

foreach ($real as $secuencia => $turnos) {
    foreach ($turnos as $turno => $kg) {
$k = ($kg !== '') ? floatval($kg) : null;
$peso = $peso_arr[$secuencia][$turno] ?? 0;
$eq   = $eq_arr[$secuencia][$turno] ?? 1;

$kg_real = ($k !== null) ? $k * $peso * $eq : null;


        // Omitir filas completamente vacías
        $f = $fecha[$secuencia][$turno]    ?? null;
        $j = $jornada[$secuencia][$turno]  ?? null;
        $h = !empty($hc[$secuencia][$turno]) ? intval($hc[$secuencia][$turno]) : null;
        

        if ($k === null && empty($f)) continue;

        $stmt->bind_param("iiissidd",
            $id_pedido,
            $secuencia,
            $turno,
            $f,
            $j,
            $h,
            $k,
            $kg_real
        );

        $stmt->execute() ? $guardados++ : $errores++;
    }
}

$stmt->close();
$conn->close();

echo json_encode([
    'ok'        => $errores === 0,
    'guardados' => $guardados,
    'errores'   => $errores
]);
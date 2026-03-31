<?php
require '../connection/conexion.php';

$id_pedido = $_POST['id_pedido'];

$real    = $_POST['real'] ?? [];
$fecha   = $_POST['fecha'] ?? [];
$jornada = $_POST['jornada'] ?? [];
$hc      = $_POST['hc'] ?? [];
$peso    = $_POST['peso'] ?? [];
$eq      = $_POST['eq'] ?? [];
$obj = $_POST['obj'] ?? [];



$guardados = 0;
$errores   = 0;

foreach ($real as $secuencia => $turnos) {

    foreach ($turnos as $turno => $valor) {

        $unidades = floatval($valor);

        // Ignorar filas vacías
        if ($unidades <= 0) continue;

        $f = $fecha[$secuencia][$turno] ?? null;
        $j = $jornada[$secuencia][$turno] ?? null;
        $h = $hc[$secuencia][$turno] ?? 0;

        $p = $peso[$secuencia][$turno] ?? 0;
        $e = $eq[$secuencia][$turno] ?? 1;
        $obj_kg = $obj[$secuencia][$turno] ?? 0;

        $kg_real = $unidades * $p * $e;

        try {

            // 🔥 INSERT o UPDATE (evita duplicados)
       $stmt = $conn->prepare("
    INSERT INTO prod_avance_pedido
    (id_pedido, secuencia, turno, unidades_reales, kg_real, obj_kg, fecha_turno, turnodn, hc)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        unidades_reales = VALUES(unidades_reales),
        kg_real = VALUES(kg_real),
        obj_kg = VALUES(obj_kg),
        fecha_turno = VALUES(fecha_turno),
        turnodn = VALUES(turnodn),
        hc = VALUES(hc)
");

          $stmt->bind_param(
    "iiidddssi",
    $id_pedido,
    $secuencia,
    $turno,
    $unidades,
    $kg_real,
    $obj_kg,
    $f,
    $j,
    $h
);
            $stmt->execute();

            $guardados++;

        } catch (Exception $e) {
            $errores++;
        }
    }
}

echo json_encode([
    "ok" => true,
    "guardados" => $guardados,
    "errores" => $errores
]);
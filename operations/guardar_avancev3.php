<?php
require '../connection/conexion.php';

            $id_pedido = $_POST['id_pedido'];

            $real    = $_POST['real'] ?? [];
            $fecha   = $_POST['fecha'] ?? [];
            $jornada = $_POST['jornada'] ?? [];
            $hc      = $_POST['hc'] ?? [];
            $peso    = $_POST['peso'] ?? [];
            $eq      = $_POST['eq'] ?? [];
            $obj     = $_POST['obj'] ?? [];



    $guardados = 0;
    $errores   = 0;

foreach ($real as $secuencia => $turnos) {

    foreach ($turnos as $turno => $valor) {

     $unidades = floatval($valor);
$f_check = $fecha[$secuencia][$turno] ?? '';
$j_check = $jornada[$secuencia][$turno] ?? '';
$h_check = $hc[$secuencia][$turno] ?? '';
$obj_check = $obj[$secuencia][$turno] ?? 0;

if (
    $unidades <= 0 &&
    empty($f_check) &&
    empty($j_check) &&
    intval($h_check) <= 0 &&
    floatval($obj_check) <= 0
) continue;


$f = !empty($fecha[$secuencia][$turno]) ? $fecha[$secuencia][$turno] : null;
$j = !empty($jornada[$secuencia][$turno]) ? $jornada[$secuencia][$turno] : null;
$h = intval($hc[$secuencia][$turno] ?? 0);
$p = floatval($peso[$secuencia][$turno] ?? 0);
$e = floatval($eq[$secuencia][$turno] ?? 1);
$obj_kg = floatval($obj[$secuencia][$turno] ?? 0);
$kg_real = $unidades * $p * $e;

        try {

            // 🔥 INSERT o UPDATE (evita duplicados)
       $stmt = $conn->prepare("
    INSERT INTO prod_avance_pedido
    (id_pedido, 
    secuencia, 
    turno, 
    unidades_reales, 
    kg_real, 
    obj_kg, 
    fecha_turno, 
    turnodn, 
    hc
    )
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

    }
     catch (Exception $e) {
     $errores++;
    }
    }
}




/* ===================================
   OBTENER ULTIMA FASE DEL PRODUCTO
=================================== */

$q=$conn->prepare("
        SELECT MAX(secuencia) as ultima
        FROM prod_fases_prod
        WHERE producto = (
            SELECT producto 
            FROM prod_pedidos 
            WHERE id_pedido=?
        )
");

$q->bind_param("i",$id_pedido);
$q->execute();
$r=$q->get_result()->fetch_assoc();

$ultima_fase = $r['ultima'];


/* ===================================
   SUMAR PRODUCCION DE ULTIMA FASE
=================================== */

$q2=$conn->prepare("SELECT SUM(unidades_reales) as total
        FROM prod_avance_pedido
        WHERE id_pedido=? AND secuencia=?
");

$q2->bind_param("ii",$id_pedido,$ultima_fase);
$q2->execute();

$r2=$q2->get_result()->fetch_assoc();
$total_producido = $r2['total'] ?? 0;

/* ===================================
   OBTENER CANTIDAD DEL PEDIDO
=================================== */

$q3=$conn->prepare("
        SELECT cantidad
        FROM prod_pedidos
        WHERE id_pedido=?
");

$q3->bind_param("i",$id_pedido);
$q3->execute();

$r3=$q3->get_result()->fetch_assoc();

$cantidad_pedido = $r3['cantidad'];


/* ===================================
   CAMBIAR ESTADO DEL PEDIDO
=================================== */

    if($total_producido >= $cantidad_pedido){
        $est=2;   // COMPLETADO
    }else{
        $est=1;   // ACTIVO
    }

$up=$conn->prepare("
    UPDATE prod_pedidos 
    SET estado = ? 
    WHERE id_pedido=?
");

$up->bind_param("ii",$est,$id_pedido);
$up->execute();




echo json_encode([
    "ok" => true,
    "guardados" => $guardados,
    "errores" => $errores
]);
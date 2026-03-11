<?php
include '../connection/conexion.php';

$date = date('Y-m-d');
$id_pedido = $_POST['id_pedido'];
foreach($_POST['real'] as $turno => $fases){

    foreach($fases as $secuencia => $kg_real){

        $jornada = $_POST['jornada'][$turno][$secuencia] ?? null;
        $hc = $_POST['hc'][$turno][$secuencia] ?? 0;

       $stmt=$conn->prepare("
INSERT INTO prod_avance_pedido
(id_pedido,turno,secuencia,kg_real,turnodn,hc,fecha_registro)
VALUES (?,?,?,?,?,?,?)
ON DUPLICATE KEY UPDATE
kg_real = VALUES(kg_real),
turnodn = VALUES(turnodn),
hc = VALUES(hc)
");

$stmt->bind_param("iiidsds",
    $id_pedido,
    $turno,
    $secuencia,
    $kg_real,
    $jornada,
    $hc,
    $date
);

        $stmt->execute();
    }
}

/* ===================================
   OBTENER ULTIMA FASE
=================================== */

$q=$conn->prepare("
SELECT MAX(secuencia) as ultima
FROM prod_avance_pedido
WHERE id_pedido=?
");

$q->bind_param("i",$id_pedido);
$q->execute();
$r=$q->get_result()->fetch_assoc();

$ultima_fase = $r['ultima'];


/* ===================================
   SUMAR PRODUCCION DE ULTIMA FASE
=================================== */

$q2=$conn->prepare("
SELECT SUM(kg_real) as total
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
   SI YA SE COMPLETO → CAMBIAR ESTADO
=================================== */

if($total_producido >= $cantidad_pedido){
    $est=2;  
}else {
    $est=1;
}

 $up=$conn->prepare(" UPDATE prod_pedidos SET estado = ? WHERE id_pedido=?");
    $up->bind_param("ii",$est ,$id_pedido);
    $up->execute();
?>
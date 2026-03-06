<?php
include '../connection/conexion.php';

$date = date('Y-m-d');
$id_pedido = $_POST['id_pedido'];

foreach($_POST['real'] as $turno => $fases){

    foreach($fases as $secuencia => $kg_real){

        $stmt=$conn->prepare("
        INSERT INTO prod_avance_pedido
        (id_pedido,turno,secuencia,kg_real,fecha_registro)
        VALUES (?,?,?,?,?)
        ON DUPLICATE KEY UPDATE
        kg_real = VALUES(kg_real)
        ");

        $stmt->bind_param("iiids",
            $id_pedido,
            $turno,
            $secuencia,
            $kg_real,
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

    $up=$conn->prepare("
    UPDATE prod_pedidos
    SET estado = 2
    WHERE id_pedido=?
    ");

    $up->bind_param("i",$id_pedido);
    $up->execute();
}

echo "ok";
?>
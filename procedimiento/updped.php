<?php
include '../connection/conexion.php';

$id      = intval($_POST['id_pedido']);
$clte    = intval($_POST['clte']);
$prod    = intval($_POST['prod']);
$cant    = floatval($_POST['cant']);
$unds    = intval($_POST['unds']);
$fechreg = $_POST['fechreg'];
$fentreg = $_POST['fentreg'];

$stmt = $conn->prepare("UPDATE prod_pedidos SET
  id_cliente = ?,
  producto = ?,
  cantidad = ?,
  und_medida = ?,
  fecha_registro = ?,
  fecha_entrega = ?
  WHERE id_pedido = ?");

$stmt->bind_param(
  "iidissi",
  $clte,
  $prod,
  $cant,
  $unds,
  $fechreg,
  $fentreg,
  $id
);

$stmt->execute();



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

$q->bind_param("i",$id);
$q->execute();
$r=$q->get_result()->fetch_assoc();

$ultima_fase = $r['ultima'];


/* ===================================
   SUMAR PRODUCCION DE ULTIMA FASE
=================================== */

$q2=$conn->prepare("SELECT SUM(kg_real) as total
        FROM prod_avance_pedido
        WHERE id_pedido=? AND secuencia=?
");

$q2->bind_param("ii",$id,$ultima_fase);
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

$q3->bind_param("i",$id);
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

$up->bind_param("ii",$est,$id);
$up->execute();





if($stmt->affected_rows > 0){
    echo json_encode(["ok"=>true]);
}else{
    echo json_encode(["ok"=>false,"msg"=>"No se actualizó"]);
}

$stmt->close();
$conn->close();
?>
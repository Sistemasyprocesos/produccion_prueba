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

if($stmt->affected_rows > 0){
    echo json_encode(["ok"=>true]);
}else{
    echo json_encode(["ok"=>false,"msg"=>"No se actualizó"]);
}

$stmt->close();
$conn->close();
?>
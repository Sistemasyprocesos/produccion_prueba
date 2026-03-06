<?php
include '../connection/conexion.php';

$id = $_POST['id'];

$sql = "SELECT 
p.num_pedido,
p.fecha_registro,
p.fecha_entrega,
p.cantidad,
c.razon_social as cliente,
pr.nombre as producto,
u.sigla as udm
FROM prod_pedidos p
INNER JOIN prod_clientes c ON c.id=p.id_cliente
INNER JOIN prod_productos pr ON pr.id=p.producto
inner join prod_udm as u on u.id=p.und_medida
WHERE p.id_pedido = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if($row = $res->fetch_assoc()){
?>

<div class="row">
  <div class="col-6"><b>Pedido:</b> <?= $row['num_pedido'] ?></div>
  <div class="col-6"><b>Cliente:</b> <?= $row['cliente'] ?></div>
</div>

<div class="row mt-2">
  <div class="col-6"><b>Producto:</b> <?= $row['producto'] ?></div>
  <div class="col-6"><b>Cantidad:</b> <?= $row['cantidad'].' '.$row['udm'] ?></div>
</div>

<div class="row mt-2">
  <div class="col-6"><b>Fecha registro:</b> <?= $row['fecha_registro'] ?></div>
  <div class="col-6"><b>Fecha entrega:</b> <?= $row['fecha_entrega'] ?></div>
</div>

<?php
}else{
  echo "Pedido no encontrado";
}
?>
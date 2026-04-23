<?php
require '../connection/conexion.php';
$id = $_GET['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cerrar pedido</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
if (!$id) {
?>
<script>
Swal.fire({
  icon: "error",
  title: "Error",
  text: "No se encontró el ID del pedido",
  timer: 2000,
  showConfirmButton: false
}).then(() => {
  window.location.href = "../operations/pedidos.php";
});
</script>
<?php
exit;
} else {

  $est = 1;

  $sql = "UPDATE prod_pedidos SET estado=? WHERE id_pedido = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii",$est, $id);

  if ($stmt->execute()) {
?>
<script>
Swal.fire({
  icon: "success",
  title: "Pedido abierto",
  text: "El pedido ha sido abierto correctamente",
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = "../operations/pedidos.php";
});
</script>
<?php
  } else {
?>
<script>
Swal.fire({
  icon: "error",
  title: "Error",
  text: "No se pudo abrir el pedido"
}).then(() => {
  window.location.href = "../operations/pedidos.php";
});
</script>
<?php
  }

}
?>

</body>
</html>
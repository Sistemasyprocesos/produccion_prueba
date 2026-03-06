<?php
require '../connection/conexion.php';

$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Eliminar cliente</title>
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
</body>
</html>
<?php
exit;
}

$sql = "DELETE FROM prod_pedidos WHERE id_pedido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
?>

<script>
Swal.fire({
  icon: "success",
  title: "Eliminado",
  text: "El cliente ha sido eliminado correctamente",
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = "../operations/pedidos.php";
});
</script>

</body>
</html>

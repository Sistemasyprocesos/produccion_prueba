<?php
require '../connection/conexion.php';
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Eliminar pedido</title>
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

// Verificar si existen registros en prod_avance_ped
$chk = $conn->prepare("SELECT COUNT(*) as total FROM prod_avance_pedido WHERE id_pedido = ?");
$chk->bind_param("i", $id);
$chk->execute();
$result = $chk->get_result()->fetch_assoc();

if ($result['total'] > 0) {
?>
<script>
Swal.fire({
  icon: "warning",
  title: "No se puede eliminar",
  text: "Este pedido tiene producción registrada y no puede ser eliminado.",
  confirmButtonColor: "#d33",
  confirmButtonText: "Entendido"
}).then(() => {
  window.location.href = "../operations/pedidos.php";
});
</script>
<?php
} else {
  $sql = "DELETE FROM prod_pedidos WHERE id_pedido = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $id);
  $stmt->execute();
?>
<script>
Swal.fire({
  icon: "success",
  title: "Eliminado",
  text: "El pedido ha sido eliminado correctamente",
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = "../operations/pedidos.php";
});
</script>
<?php
}
?>
</body>
</html>
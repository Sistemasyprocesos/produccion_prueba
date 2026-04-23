<?php
require '../connection/conexion.php';

    $rzscl = strtoupper($_POST['razon_social']);
    $iden  = $_POST['iden'];
    $tc    = $_POST['tipo_cliente'];
    $dir   = strtoupper($_POST['direccion']);
    $documento = $_POST['tipo_documento'];

$sql = "INSERT INTO prod_clientes (razon_social, identificacion, tipo, direccion,estado,tipo_identi)
        VALUES (?,?, ?, ?,?, ?)";
$esta=1;
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisi", $rzscl, $iden, $tc, $dir, $esta,$documento);

if ($stmt->execute()) {
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8">
      <title>Guardando...</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
<body>

<script>
Swal.fire({
  title: "Éxito",
  text: "Cliente creado correctamente",
  icon: "success",
  timer: 1500,
        showConfirmButton: false
}).then(() => {
  window.location.href = "../registers/clts.php";
});
</script>

</body>
</html>
<?php
exit;
} else {
    echo "Error al registrar cliente: " . $conn->error;
}
?>

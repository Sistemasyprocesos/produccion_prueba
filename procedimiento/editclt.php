<?php
require '../connection/conexion.php';

$rzscl = strtoupper($_POST['rs']);
$iden  = $_POST['iden'];//ID DEL CLIENTE
$tc    = $_POST['tc'];
$dir   = strtoupper($_POST['dir']);
$est   = $_POST['est'];
$identificacion= $_POST['identi'];
$sql = "update prod_clientes set razon_social=?, identificacion=?, tipo=?, direccion=?, estado=? where id=?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisii", $rzscl, $identificacion, $tc, $dir, $est, $iden);

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
  text: "Cliente modificado correctamente",
  icon: "success",
  confirmButtonText: "Ok"
}).then(() => {
  window.location.href = "../registers/clts.php";
});
</script>

</body>
</html>
<?php
exit;
} else {
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
  title: "Error",
  text: "Ocurrio un error al modificar el cliente",
  icon: "danger",
  confirmButtonText: "Ok"
}).then(() => {
  window.location.href = "../registers/clts.php";
});
</script>

</body>
</html>
<?php
    echo "Error al modificar cliente: " . $conn->error;
}
?>

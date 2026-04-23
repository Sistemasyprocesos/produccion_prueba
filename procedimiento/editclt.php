<?php
require '../connection/conexion.php';

$rzscl          = strtoupper($_POST['rs']);
$iden           = $_POST['idclient'];        
$tc             = $_POST['tc'];  
$dir            = strtoupper($_POST['dir']);
$est            = $_POST['est']; 
$identificacion = $_POST['identi']; 

$sql = "UPDATE prod_clientes 
        SET razon_social=?, identificacion=?, tipo=?, direccion=?, estado=? 
        WHERE id=?";

$stmt = $conn->prepare($sql);

// s=razon_social, s=identificacion, i=tipo, s=direccion, i=estado, i=id
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
} else {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Error</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
  title: "Error",
  text: "Ocurrió un error al modificar el cliente",
  icon: "error",   /* ← corregido: era "danger" */
  confirmButtonText: "Ok"
}).then(() => {
  window.location.href = "../registers/clts.php";
});
</script>
</body>
</html>
<?php
    error_log("Error al modificar cliente: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
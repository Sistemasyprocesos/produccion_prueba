<?php
require '../connection/conexion.php';

$id = $_POST['iden'] ?? null;
$codigo = $_POST['cod'] ?? null;
$nombre = $_POST['nombre'] ?? null;
$tipo = $_POST['tipo'] ?? null;
$cat = $_POST['cate'] ?? null;

$peso = $_POST['peso'] ?? null;
$udm = $_POST['udm'] ?? null;
$env = $_POST['env'] ?? null;
$udmenvase = $_POST['udmenvase'] ?? null;
$undcjsc = $_POST['undscjsc'] ?? null;
$undpallet = $_POST['und_pallet'] ?? null;
$estado = $_POST['est'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Actualizar producto</title>
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
  text: "No se encontró el ID del producto",
  timer: 2000,
  showConfirmButton: false
}).then(() => {
  window.location.href = "prods.php";
});
</script>
</body>
</html>
<?php
exit;
}

$er = $conn->prepare("UPDATE prod_productos SET 
codigo_prod=?,
nombre=?,
tipo_prod=?,
cat_prod=?,

peso_prod=?,
udm=?,
envase=?,
udm=?,
unds_cjsc=?,
und_pallet=?,
estado=?
WHERE id=?");  

$er->bind_param("ssisiiiiiiii",
$codigo,
$nombre,
$tipo,
$cat,

$peso,
$udm,
$env,
$udmenvase,
$undcjsc,
$undpallet,
$estado,
$id
);

$er->execute();

if ($er->affected_rows > 0) {
?>
<script>
Swal.fire({
  icon: "success",
  title: "Actualizado",
  text: "El producto ha sido actualizado correctamente",
  timer: 1500,
  showConfirmButton: false
}).then(() => {
  window.location.href = "prods.php";
});
</script>
<?php
} else {
?>
<script>
Swal.fire({
  icon: "error",
  title: "Error",
  text: "No se pudo actualizar el producto",
  timer: 2000,
  showConfirmButton: false
}).then(() => {
  window.location.href = "prods.php";
});
</script>
<?php
}
?>

</body>
</html>
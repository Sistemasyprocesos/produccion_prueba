<?php
require '../connection/conexion.php';

$id =           $_POST['iden'] ?? null;
$codigo =       strtoupper($_POST['cod'] )?? null;
$nombre =       strtoupper($_POST['nuevonombreprod']) ?? null;
$tipo =         $_POST['tipo'] ?? null;
$cat =          $_POST['cate'] ?? null;
$nuevacate =    strtoupper($_POST['nuevacate']) ?? null;
$peso =         $_POST['peso'] ?? null;
$udm =          $_POST['um'] ?? null;
$env =          $_POST['env'] ?? null;
$tipo_emb =     $_POST['tipo_emb'] ?? null;
$undcjsc =      $_POST['undscjsc'] ?? null;
$undpallet =    $_POST['und_pallet'] ?? null;
$estado =       $_POST['estate'] ?? null;


//OBTENER ABREVIATURA DEL ENVASE
$r=$conn->prepare("SELECT abreviatura FROM prod_envase WHERE id = ?");
    $r->bind_param("i", $env);  
      $r->execute();
  $env_result = $r->get_result();
  $env_abreviatura = $env_result->fetch_assoc()['abreviatura'];



//OBTENER ABREVIATURA DE UDM
$j=$conn->prepare("SELECT sigla FROM prod_udm WHERE id = ?");
      $j->bind_param("i", $udm);  
        $j->execute();
    $udm_result = $j->get_result();
    $udm_sigla = $udm_result->fetch_assoc()['sigla'];


$nuevonom=$nombre." ".$env_abreviatura." ".$peso." ".$udm_sigla;

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
// SI INGRESA NUEVA CATEGORIA
if (!empty($nuevacate)) {

    // Verificar si ya existe
    $check = $conn->prepare("SELECT id_cat FROM prod_categoria_prod WHERE cat_nombre = ?");
    $check->bind_param("s", $nuevacate);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        // Ya existe → usar ese ID
        $row = $res->fetch_assoc();
        $cat = $row['id_cat'];
    } else {
        // No existe → insertar
        $insert = $conn->prepare("INSERT INTO prod_categoria_prod (cat_nombre) VALUES (?)");
        $insert->bind_param("s", $nuevacate);
        $insert->execute();

        // Obtener ID insertado
        $cat = $insert->insert_id;
    }
}

$er = $conn->prepare("UPDATE prod_productos SET 
    tipo_embalaje=?,
    codigo_prod=?,
    nombre=?,
    tipo_prod=?,
    cat_prod=?,
    peso_prod=?,
    udm=?,
    envase=?,
    unds_cjsc=?,
    und_pallet=?,
    estado=?
  WHERE id=?"
);  

$er->bind_param("isssssiiiiii",
    $tipo_emb,
    $codigo,
    $nuevonom,
    $tipo,
    $cat,
    $peso,
    $udm,
    $env,
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
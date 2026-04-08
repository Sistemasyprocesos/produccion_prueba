<?php
require '../connection/conexion.php';


// ------------------ DATOS ------------------
$codprod     = strtoupper($_POST['codigoprod']);
$nombreprod  = strtoupper($_POST['nombreprod']);

$catselect   = $_POST['categoriaselect'] ?? '';
$catnueva    = strtoupper($_POST['categoria'] ?? '');

$tipoprod    = $_POST['tipoprod'];
$pesoprod    = $_POST['pesoprod'];
$envase      = $_POST['envase_prod'];
$undcjsc     = $_POST['unds_cjsc'];
$embalaje    = $_POST['tipo_embalaje'];
$unds_pallet = $_POST['unds_pallet'];
$base        = $_POST['prod_base'];
$udm         = $_POST['udm'];

$estado = 1;
$faseado = 1;

// ------------------ CATEGORIA ------------------
$categoria_id = null;

// Si selecciona una categoría existente
if (!empty($catselect)) {

    $categoria_id = $catselect;

} 
// Si escribe una nueva categoría
elseif (!empty($catnueva)) {

    // Verificar si ya existe
    $check = $conn->prepare("SELECT id_cat FROM prod_categoria_prod WHERE cat_nombre = ?");
    $check->bind_param("s", $catnueva);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $categoria_id = $row['id_cat'];
    } else {
        // Insertar nueva categoría
        $insertCat = $conn->prepare("INSERT INTO prod_categoria_prod (cat_nombre) VALUES (?)");
        $insertCat->bind_param("s", $catnueva);
        $insertCat->execute();

        $categoria_id = $insertCat->insert_id;
    }
}

// Validación básica
if (!$categoria_id) {
    echo json_encode([
        'success' => false,
        'msg' => 'Debe seleccionar o ingresar una categoría'
    ]);
    exit;
}

// ------------------ ENVASE ------------------
$q = $conn->prepare("SELECT abreviatura FROM prod_envase WHERE id = ?");
$q->bind_param("i", $envase);
$q->execute();
$res = $q->get_result()->fetch_assoc();
$envase_txt = $res['abreviatura'] ?? '';

// ------------------ UDM ------------------
$n = $conn->prepare("SELECT sigla FROM prod_udm WHERE id = ?");
$n->bind_param("i", $udm);
$n->execute();
$ress = $n->get_result()->fetch_assoc();
$unidadmed = $ress['sigla'] ?? '';

// ------------------ NOMBRE CONCATENADO ------------------
$nombreconcat = $nombreprod . ' ' . $envase_txt . ' ' . $pesoprod . ' ' . $unidadmed;

// ------------------ INSERT ------------------
$sql = "INSERT INTO prod_productos (
  codigo_prod,
  nombre, 
  nombre_prod,
  cat_prod,
  tipo_prod,
  envase,
  peso_prod,
  unds_cjsc, 
  tipo_embalaje,
  und_pallet,
  producto_base,
  estado,
  udm,
  fase
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
  "sssiiidiiisiii",
  $codprod,
  $nombreconcat,
  $nombreprod,
  $categoria_id, // 👈 FK correcta
  $tipoprod,
  $envase,
  $pesoprod,
  $undcjsc,
  $embalaje,
  $unds_pallet,
  $base,
  $estado,
  $udm,
  $faseado
);

if ($stmt->execute()) {

    header("Location: ../registers/prods.php?ok=1");
    exit;

} else {

   header("Location: ../registers/prods.php?error=Error al guardar el producto");
    exit;

}
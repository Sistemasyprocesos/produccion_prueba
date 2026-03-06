<?php
require '../connection/conexion.php';
header('Content-Type: application/json');

$codprod =strtoupper($_POST['codigoprod']);
$nombreprod =$_POST['nombreprod'];
$cat =strtoupper($_POST['categoria']);
$tipoprod = $_POST['tipoprod'];
$pesoprod = $_POST['pesoprod'];
$envase = $_POST['envase_prod'];
$undcjsc = $_POST['unds_cjsc'];
$embalaje = $_POST['tipo_embalaje'];
$unds_pallet = $_POST['unds_pallet'];
$pvp = $_POST['pvp'];
$base = $_POST['prod_base'];
$udm = $_POST['udm'];
$estado = 1;
$faseado=1;

$q = $conn->prepare("SELECT abreviatura FROM prod_envase WHERE id = ?");
$q->bind_param("i", $envase);
$q->execute();
$res = $q->get_result()->fetch_assoc();

$envase_txt = $res['abreviatura'] ?? '';


$nombre_completo = strtoupper(trim(
  $nombreprod . ' ' .
  $pesoprod . 'KG ' .
  $envase_txt
));




$sql = "INSERT INTO prod_productos (
  nombre, 
  cat_prod,
  tipo_prod,
  envase,
  peso_prod,
  unds_cjsc, 
  tipo_embalaje,
  und_pallet,
  producto_base,
  estado,
  codigo_prod,
  pvp,
  udm,
  fase
) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "ssiidiiisisdi",
  $nombre_completo,
  $cat,
  $tipoprod,
  $envase,
  $pesoprod,
  $undcjsc,
  $embalaje,
  $unds_pallet,
  $base,
  $estado,
  $codprod,
  $pvp,
  $udm,
  $faseado
);

if ($stmt->execute()) {
  echo json_encode([
    'success' => true,
    'id' => $stmt->insert_id,
    'codigo_prod'=>$codprod,
    'nombre'=>$nombreprod,
    'cat_prod'=>$cat,
    'tipo_prod'=>$tipoprod,
    'peso_prod'=>$pesoprod,
    'envase'=>$envase,
    'unds_cjsc'=>$undcjsc,
    'tipo_embalaje'=>$embalaje,
    'und_pallet'=>$unds_pallet,
    'producto_base'=>$base

  ]);
} else {
  echo json_encode([
    'success' => false,
    'msg' => $stmt->error
  ]);
}

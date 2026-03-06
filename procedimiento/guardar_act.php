<?php
include '../connection/conexion.php';

$nom =mb_strtoupper( $_POST['act_nom'] ?? '');
$abr =mb_strtoupper( $_POST['act_abreviatura'] ?? '');
$est=1;
if ($nom == '' || $abr == '') {
  echo json_encode([
    'success' => false,
    'msg' => 'Complete todos los campos'
  ]);
  exit;
}

$stmt = $conn->prepare(
  "INSERT INTO prod_act_prod (nombre, abreviatura,estado) VALUES (?, ?,?)"
);
$stmt->bind_param("ssi", $nom, $abr,$est);

if ($stmt->execute()) {
  echo json_encode([
    'success' => true,
    'id' => $conn->insert_id,
    'nombre' => $nom,
    'abreviatura' => $abr,
    'estado'=>$est
  ]);
} else {
  echo json_encode(['success' => false]);
}

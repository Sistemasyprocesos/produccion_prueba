<?php
include '../connection/conexion.php';

$id  = $_POST['iden'] ?? '';
$nom = mb_strtoupper($_POST['nombre_act'] ?? '');
$abr = mb_strtoupper($_POST['abrevia_act'] ?? '');
$est = $_POST['est'] ?? '';

if ($id === '' || $nom === '' || $abr === '' || $est === '') {
  echo json_encode([
    'success' => false,
    'msg' => 'Datos incompletos'
  ]);
  exit;
}

$stmt = $conn->prepare(
  "UPDATE prod_act_prod
   SET nombre = ?, abreviatura = ?, estado = ?
   WHERE id = ?"
);

$stmt->bind_param("ssii", $nom, $abr, $est, $id);

if ($stmt->execute()) {
  echo json_encode([
    'success' => true,
    'id' => $id,
    'nombre' => $nom,
    'abreviatura' => $abr,
    'estado_id' => $est
  ]);
} else {
  echo json_encode([
    'success' => false,
    'msg' => 'Error al actualizar'
  ]);
}

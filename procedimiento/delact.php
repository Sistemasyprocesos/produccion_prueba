<?php
include '../connection/conexion.php';

if (!isset($_POST['id'])) {
  echo json_encode(['success' => false]);
  exit;
}

$id = intval($_POST['id']);

$sql = $conn->prepare("DELETE FROM prod_act_prod WHERE id = ?");
$sql->bind_param("i", $id);

if ($sql->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false]);
}

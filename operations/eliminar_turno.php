<?php
include '../connection/conexion.php';

$id_pedido  = intval($_POST['id_pedido']);
$secuencia  = intval($_POST['secuencia']);
$turno      = intval($_POST['turno']);

$stmt = $conn->prepare("DELETE FROM prod_avance_pedido 
                         WHERE id_pedido = ? AND secuencia = ? AND turno = ?");
$stmt->bind_param("iii", $id_pedido, $secuencia, $turno);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false]);
}

$stmt->close();
$conn->close();
?>
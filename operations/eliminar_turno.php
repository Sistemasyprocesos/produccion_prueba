<?php
include '../connection/conexion.php';

$id_pedido = intval($_POST['id_pedido']);
$secuencia = intval($_POST['secuencia']);
$turno     = intval($_POST['turno']);

// 1. Eliminar avance real si existe
$stmt = $conn->prepare("DELETE FROM prod_avance_pedido 
                         WHERE id_pedido=? AND secuencia=? AND turno=?");
$stmt->bind_param("iii", $id_pedido, $secuencia, $turno);
$stmt->execute();

// 2. Marcar turno como eliminado
$stmt2 = $conn->prepare("
    INSERT IGNORE INTO prod_avance_turnos_eliminados (id_pedido, secuencia, turno)
    VALUES (?, ?, ?)
");
$stmt2->bind_param("iii", $id_pedido, $secuencia, $turno);
$stmt2->execute();

echo json_encode(['ok' => true]);
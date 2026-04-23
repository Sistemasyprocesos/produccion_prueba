<?php
require '../connection/conexion.php';

$nombre = strtoupper(trim($_POST['cliente'] ?? ''));

if ($nombre === '') {
    echo json_encode(['existe' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM prod_clientes WHERE razon_social = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    'existe' => $res->num_rows > 0
]);
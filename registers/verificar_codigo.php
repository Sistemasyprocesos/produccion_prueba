<?php
require '../connection/conexion.php';

$codigo = $_POST['codigo'] ?? '';

$sql = "SELECT id FROM prod_productos WHERE codigo_prod = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    "existe" => $stmt->num_rows > 0
]);

$stmt->close();
$conn->close();
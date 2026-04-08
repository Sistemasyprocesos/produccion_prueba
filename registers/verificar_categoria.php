<?php
require '../connection/conexion.php';

$nombre = strtoupper(trim($_POST['categoria'] ?? ''));

if ($nombre === '') {
    echo json_encode(['existe' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id_cat FROM prod_categoria_prod WHERE cat_nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$res = $stmt->get_result();

echo json_encode([
    'existe' => $res->num_rows > 0
]);
<?php

require '../connection/conexion.php';

$cod = $_GET['id'];

$sql = $conn->prepare("DELETE FROM prod_envase WHERE id = ?");
$sql->bind_param("i", $cod);

if ($sql->execute()) {
    header("Location: ../registers/env.php");
    exit;
} else {
    echo "Error al eliminar: " . $sql->error;
}

$sql->close();
$conn->close();
?>

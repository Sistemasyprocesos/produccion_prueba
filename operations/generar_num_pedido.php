<?php
require '../connection/conexion.php';

$anio = date('Y');

$stmt = $conn->prepare("
    SELECT valor 
    FROM secuencias 
    WHERE nombre='pedido' AND anio=?
");
$stmt->bind_param("i", $anio);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $siguiente = $row['valor'] + 1;
} else {
    $siguiente = 1;
}

$num_pedido = "PED-$anio-" . str_pad($siguiente, 4, "0", STR_PAD_LEFT);
?>
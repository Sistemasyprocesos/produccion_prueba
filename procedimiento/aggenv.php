<?php 

require '../connection/conexion.php';

$c=strtoupper($_POST['nomb']);
$ab=strtoupper($_POST['ab']);
$est=1;

$sql="insert into prod_envase(nombre,abreviatura,estado) values('$c','$ab','$est')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../registers/env.php"); 

    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

$conn->close();
?>
<?php

require '../connection/conexion.php';

$id = $_POST['cod'];
$nombre = strtoupper($_POST['envase']);       
$abreviatura = strtoupper($_POST['abrev']);
    
$up=$conn->prepare("UPDATE prod_envase SET nombre=?, abreviatura=? WHERE id=?");
$up->bind_param("ssi",$nombre,$abreviatura,$id);
$up->execute();

if($up->affected_rows > 0){
    header("Location: ../registers/env.php");
} else {
    echo "Error al actualizar el envase: " . $up->error;
}   

?>
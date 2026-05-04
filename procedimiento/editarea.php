<?php

require '../connection/conexion.php';

$areanom=strtoupper($_POST['areanom']);
$areaabrev=strtoupper($_POST['areaabrev']);
$id=$_POST['idarea'];


$edit=$conn->prepare("UPDATE prod_area_prod SET nombre=?, abreviatura=? WHERE id=?");
$edit->bind_param("ssi",$areanom,$areaabrev,$id);
if($edit->execute()){
    header("Location: ../registers/area.php");
}
else{
    echo "Error al actualizar el area";
}
?>
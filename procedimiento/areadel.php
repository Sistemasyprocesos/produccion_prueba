<?php
require '../connection/conexion.php';

$codigo=$_GET['codigo'];

$delete=$conn->prepare("DELETE FROM prod_area_prod WHERE id=?");
$delete->bind_param("i",$codigo);

if($delete->execute()){
    header("Location: ../registers/area.php");
}
else{
    echo "Error al eliminar el area";
}







?>
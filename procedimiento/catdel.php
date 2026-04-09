<?php
require '../connection/conexion.php';

$codigo=$_GET['codigo'];

$delete=$conn->prepare("DELETE FROM prod_categoria_prod WHERE id_cat=?");
$delete->bind_param("i",$codigo);

if($delete->execute()){
    header("Location: ../registers/categorias.php");
}
else{
    echo "Error al eliminar la categoria";
}







?>
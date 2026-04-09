<?php

require '../connection/conexion.php';

$areanom=strtoupper($_POST['catnom']);

$id=$_POST['idcat'];


$edit=$conn->prepare("UPDATE prod_categoria_prod SET cat_nombre=? WHERE id_cat=?");
$edit->bind_param("ss",$areanom,$id);
if($edit->execute()){
    header("Location: ../registers/categorias.php");
}
else{
    echo "Error al actualizar la categoria";
}

?>
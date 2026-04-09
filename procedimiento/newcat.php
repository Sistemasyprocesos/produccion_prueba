<?php   
require '../connection/conexion.php';

$nombre=strtoupper($_POST['cat_nombre']);
 

$sql=$conn->prepare("INSERT INTO prod_categoria_prod(cat_nombre) VALUES (?)");
$sql->bind_param("s",$nombre);
$sql->execute();

if($sql->affected_rows > 0){
    header("Location: ../registers/categorias.php");
}
else{
    header("Location: ../registers/categorias.php");
}

?>
<?php

require '../connection/conexion.php';

$cod=$_POST['cod'];
$nombre=strtoupper($_POST['nombre']);
$sigla=strtoupper($_POST['sigla']);

$cd=$conn->prepare("UPDATE prod_udm SET nombre=?, sigla=? WHERE id=?");
$cd->bind_param("ssi",$nombre,$sigla,$cod);
$cd->execute();

if($cd->affected_rows > 0){
 header("Location: ../registers/udm.php");
}
else{
    
    header("Location: ../registers/udm.php");
}



?>
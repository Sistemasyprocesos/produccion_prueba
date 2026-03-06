<?php 

require '../connection/conexion.php';

$nombre=strtoupper($_POST['nombre']);
$sigla=strtoupper($_POST['sigla']);

$cd=$conn->prepare("INSERT INTO prod_udm(nombre,sigla) VALUES (?,?)");
$cd->bind_param("ss",$nombre,$sigla);
$cd->execute();

if($cd->affected_rows > 0){
 header("Location: ../registers/udm.php");
}
else{
    
    header("Location: ../registers/udm.php");
}





?>
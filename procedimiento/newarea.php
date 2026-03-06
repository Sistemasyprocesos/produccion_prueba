<?php   
require '../connection/conexion.php';

$nombre=strtoupper($_POST['area_nombre']);
$abreviatura=strtoupper($_POST['area_abreviatura']);    
$estado=1;
$sql=$conn->prepare("INSERT INTO prod_area_prod(nombre,abreviatura,estado) VALUES (?,?,?)");
$sql->bind_param("ssi",$nombre,$abreviatura,$estado);
$sql->execute();

if($sql->affected_rows > 0){
    header("Location: ../registers/area.php");
}
else{
    header("Location: ../registers/area.php");
}

?>
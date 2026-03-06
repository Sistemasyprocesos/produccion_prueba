<?php

require '../connection/conexion.php';


$cod=$_GET['codigo'];


$elm=$conn->prepare("DELETE FROM prod_udm WHERE id=?");
$elm->bind_param("i",$cod);
$elm->execute();    

if($elm->affected_rows > 0){
 header("Location: ../registers/udm.php");
}
else{
    
    header("Location: ../registers/udm.php");
}



?>
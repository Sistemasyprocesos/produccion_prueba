<?php
require '../connection/conexion.php';

if (isset($_GET['codigo'])) {
$cv=$_GET['codigo'];
echo $cv;
$r=$conn->prepare("DELETE FROM prod_fases_prod WHERE proceso_id = ?");

$r->bind_param("i", $cv);

$r->execute();

if ($r->affected_rows > 0) {

header("Location: fases.php");
} 
else {
header("Location: fases.php");
}


}
else{
    echo "No se recibió el código de la fase a eliminar.";
}
?>


<?php

require '../connection/conexion.php';


if (isset($_GET['codigo'])) {
$cv=$_GET['codigo'];
echo $cv;
$r=$conn->prepare("DELETE FROM prod_productos WHERE id = ?");

$r->bind_param("i", $cv);

$r->execute();

if ($r->affected_rows > 0) {

header("Location: prods.php");
} 
else {
header("Location: prods.php");
}


}
else{
    echo "No se recibió el código del producto a eliminar.";
}
?>
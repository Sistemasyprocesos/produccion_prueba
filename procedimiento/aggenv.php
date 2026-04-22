<?php 

require '../connection/conexion.php';

$c = strtoupper($_POST['nomb']);
$ab = strtoupper($_POST['ab']);
$est = 1;

$sql = $conn->prepare("INSERT INTO prod_envase (nombre, abreviatura, estado) VALUES (?, ?, ?)");
$sql->bind_param("ssi", $c, $ab, $est);

?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
if ($sql->execute()) {
    echo "
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Envase registrado correctamente'
        }).then(() => {
            window.location.href = '../registers/env.php';
        });
    </script>
    ";
} else {
    echo "
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '". $sql->error ."'
        }).then(() => {
            window.history.back();
        });
    </script>
    ";
}

$conn->close();
?>

</body>
</html>
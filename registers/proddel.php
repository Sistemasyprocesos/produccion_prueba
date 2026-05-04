<?php
require '../connection/conexion.php';

$mensaje = '';
$icono = '';
$titulo = '';

if (isset($_GET['codigo'])) {
    $cv = $_GET['codigo'];
    try {
        $r = $conn->prepare("DELETE FROM prod_productos WHERE id = ?");
        $r->bind_param("i", $cv);
        $r->execute();

        if ($r->affected_rows > 0) {
            $icono = 'success';
            $titulo = 'Eliminado';
            $mensaje = 'Producto eliminado correctamente';
        } else {
            $icono = 'warning';
            $titulo = 'No encontrado';
            $mensaje = 'No se encontró el producto a eliminar';
        }

    } catch (mysqli_sql_exception $e) {
        $icono = 'error';
        $titulo = 'No se puede eliminar';
        $mensaje = 'Este producto tiene producción asociada y no puede ser eliminado';
    }

    } else {
        $icono = 'error';
        $titulo = 'Error';
        $mensaje = 'No se recibió el código del producto a eliminar';
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: '<?= $icono ?>',
            title: '<?= $titulo ?>',
            text: '<?= $mensaje ?>',
            timer: 3000,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'prods.php';
        });
    </script>
</body>
</html>
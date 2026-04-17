<?php
require '../connection/conexion.php';

$clte     = $_POST['clte'];
$freg     = $_POST['fechreg'];
$fentreg  = $_POST['fentreg'];
$prod     = $_POST['prod'];
$cantidad = $_POST['cant'];
$unds     = $_POST['unds'];

$anio = date('Y');
$estado=1;
$conn->begin_transaction();

try {

    /* =====================================
       1. OBTENER Y ACTUALIZAR CONSECUTIVO
    ===================================== */
    $stmt = $conn->prepare("
        SELECT valor 
        FROM secuencias 
        WHERE nombre='pedido' AND anio=? 
        FOR UPDATE
    ");
    $stmt->bind_param("i", $anio);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $siguiente = $row['valor'] + 1;

        $up = $conn->prepare("
            UPDATE secuencias 
            SET valor=? 
            WHERE nombre='pedido' AND anio=?
        ");
        $up->bind_param("ii", $siguiente, $anio);
        $up->execute();

    } else {
        $siguiente = 1;

        $ins = $conn->prepare("
            INSERT INTO secuencias (nombre, anio, valor)
            VALUES ('pedido', ?, 1)
        ");
        $ins->bind_param("i", $anio);
        $ins->execute();
    }

    /* =====================================
       2. GENERAR NUMERO PEDIDO REAL
    ===================================== */
    $num_pedido = "PED-$anio-" . str_pad($siguiente, 4, "0", STR_PAD_LEFT);
$pedn=1;
    /* =====================================
       3. INSERTAR PEDIDO
    ===================================== */
    $er = $conn->prepare("
        INSERT INTO prod_pedidos(
            id_cliente,
            num_pedido,
            fecha_registro,
            fecha_entrega,
            producto,
            cantidad,
            und_medida,
            estado,
            est_ped
        ) 
        VALUES (?,?,?,?,?,?,?,?,?)
    ");

    $er->bind_param(
        "isssiiiii",
        $clte,
        $num_pedido,
        $freg,
        $fentreg,
        $prod,
        $cantidad,
        $unds,
        $estado,
        $pedn
    );

    $er->execute();

    if ($er->affected_rows <= 0) {
        throw new Exception("No se insertó el pedido");
    }

    /* =====================================
       4. CONFIRMAR TODO
    ===================================== */
    $conn->commit();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Guardando...</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>

        <script>
            Swal.fire({
            title: "Éxito",
            text: "Pedido creado correctamente\nN° <?= $num_pedido ?>",
            icon: "success",
            timer: 1800,
            showConfirmButton: false
            }).then(() => {
            window.location.href = "../operations/pedidos.php";
            });
        </script>

    </body>
</html>

<?php

} catch (Exception $e) {

    $conn->rollback();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Error</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>

        <script>
            Swal.fire({
            title: "Error",
            text: "No se pudo registrar el pedido",
            icon: "error",
            timer: 2000,
            showConfirmButton: false
            }).then(() => {
            window.history.back();
            });
        </script>

    </body>
</html>

<?php
}
?>
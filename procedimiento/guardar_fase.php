<?php
include "../connection/conexion.php";

if(!isset($_POST['producto'])){
    echo "No se recibieron datos";
    exit;
}

$producto  = $_POST['producto'];
$secuencia = $_POST['secuencia'];
$tipo      = $_POST['tipo'];
$area      = $_POST['area'];
$act       = $_POST['act'];
$envase    = $_POST['envase'];
$personas=$_POST['personas'];
$kgstd = isset($_POST['kgstd']) ? $_POST['kgstd'] : [];

$proceso_id = date('YmdHis') . '_' . uniqid();

$conn->begin_transaction();

try {

    for ($i = 0; $i < count($secuencia); $i++) {

        $seq = $secuencia[$i];
        $tip = $tipo[$i];
        $are = $area[$i];
        $env = $envase[$i];
        $kg  = isset($kgstd[$i]) ? $kgstd[$i] : 0;
$people=$personas[$i];
        $actividades = $act[$i];

        foreach ($actividades as $actividad) {

            $sql = "INSERT INTO prod_fases_prod
                    (producto, secuencia, tipo_fase, area, actividad, kg_std, proceso_id, envase,personas_std)
                    VALUES (?,?,?,?,?,?,?,?,?)";

            $stmt = $conn->prepare($sql);

            if(!$stmt){
                throw new Exception($conn->error);
            }

            $stmt->bind_param(
                "iiiiiiiii",
                $producto,
                $seq,
                $tip,
                $are,
                $actividad,
                $kg,
                $proceso_id,
                $env,
                $people
            );

            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
        }
    }

    // SOLO SI TODO FUNCIONÓ
    $act = $conn->prepare("UPDATE prod_productos SET fase = 2 WHERE id = ?");
    $act->bind_param("i", $producto);

    if(!$act->execute()){
        throw new Exception($act->error);
    }

    $conn->commit();

    echo "Proceso guardado correctamente";

} catch (Exception $e) {

    $conn->rollback();
    echo "Error en la transacción: " . $e->getMessage();
}
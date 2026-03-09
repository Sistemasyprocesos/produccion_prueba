<?php
include '../connection/conexion.php';
header('Content-Type: application/json');

$proceso_id = intval($_POST['proceso_id'] ?? 0);

if (!$proceso_id) {
    echo json_encode(['ok'=>false,'msg'=>'proceso_id requerido']);
    exit;
}

$tipo     = $_POST['tipo'] ?? [];
$area     = $_POST['area'] ?? [];
$envase   = $_POST['envase'] ?? [];
$kgstd    = $_POST['kgstd'] ?? [];
$personas = $_POST['personas'] ?? [];

$conn->begin_transaction();

try {

    $stmt = $conn->prepare("
        UPDATE prod_fases_prod
        SET tipo_fase = ?,
            area = ?,
            envase = ?,
            kg_std = ?,
            personas_std = ?
        WHERE proceso_id = ?
        AND secuencia = ?
    ");

    foreach($tipo as $sec => $t){

        $a  = intval($area[$sec]);
        $en = intval($envase[$sec]);
        $kg = floatval($kgstd[$sec]);
        $pe = intval($personas[$sec]);

        $stmt->bind_param(
            "iiidiii",
            $t,
            $a,
            $en,
            $kg,
            $pe,
            $proceso_id,
            $sec
        );
        
        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }

    }

    $conn->commit();

    echo json_encode([
        "ok"=>true,
        "msg"=>"Fases actualizadas"
    ]);

} catch(Exception $e){

    $conn->rollback();
    echo json_encode([
        "ok"=>false,
        "msg"=>$e->getMessage()
    ]);
}
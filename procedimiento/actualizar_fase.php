<?php
include '../connection/conexion.php';
header('Content-Type: application/json');

$proceso_id = intval($_POST['proceso_id'] ?? 0);
if (!$proceso_id) {
    echo json_encode(['ok'=>false,'msg'=>'proceso_id requerido']);
    exit;
}

$tipo      = $_POST['tipo']      ?? [];
$area      = $_POST['area']      ?? [];
$envase    = $_POST['envase']    ?? [];
$kgstd     = $_POST['kgstd']     ?? [];
$personas  = $_POST['personas']  ?? [];
$pesoenv   = $_POST['pesoenv']   ?? [];
$udmenv    = $_POST['udmenv']    ?? [];
$actividad = $_POST['actividad'] ?? [];

$conn->begin_transaction();

try {

    // 1. Obtener producto desde el proceso (necesario para reinsert)
    $stmtProd = $conn->prepare("SELECT producto FROM prod_fases_prod WHERE proceso_id = ? LIMIT 1");
    $stmtProd->bind_param("i", $proceso_id);
    $stmtProd->execute();
    $resProd = $stmtProd->get_result()->fetch_assoc();
    if (!$resProd) throw new Exception("Proceso no encontrado");
    $producto_id = $resProd['producto'];

    // 2. Eliminar todas las filas del proceso
    $stmtDel = $conn->prepare("DELETE FROM prod_fases_prod WHERE proceso_id = ?");
    $stmtDel->bind_param("i", $proceso_id);
    $stmtDel->execute();

    // 3. Reinsertar una fila por cada actividad de cada fase
    $stmtIns = $conn->prepare("
        INSERT INTO prod_fases_prod
            (proceso_id, producto, secuencia, tipo_fase, area, envase, peso_env, udm_env, kg_std, personas_std, actividad)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($tipo as $sec => $t) {

        $acts = $actividad[$sec] ?? [];

        if (empty($acts)) {
            throw new Exception("La fase {$sec} debe tener al menos una actividad.");
        }

        foreach ($acts as $act_id) {

            $t_val   = intval($t);
            $a_val   = intval($area[$sec]);
            $en_val  = intval($envase[$sec]);
            $pe_val  = floatval($pesoenv[$sec]);
            $udm_val = intval($udmenv[$sec]);
            $kg_val  = floatval($kgstd[$sec]);
            $per_val = intval($personas[$sec]);
            $act_val = intval($act_id);
            $sec_val = intval($sec);

            $stmtIns->bind_param(
                "iiiiiddiiii",
                $proceso_id,
                $producto_id,
                $sec_val,
                $t_val,
                $a_val,
                $en_val,
                $pe_val,
                $udm_val,
                $kg_val,
                $per_val,
                $act_val
            );

            if (!$stmtIns->execute()) {
                throw new Exception($stmtIns->error);
            }
        }
    }

    $conn->commit();
    echo json_encode(['ok'=>true, 'msg'=>'Fases actualizadas']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['ok'=>false, 'msg'=>$e->getMessage()]);
}
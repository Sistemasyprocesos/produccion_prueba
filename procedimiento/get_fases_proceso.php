<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include '../connection/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$proceso_id = intval($_GET['proceso_id'] ?? 0);

if(!$proceso_id){
    echo json_encode(["error"=>"proceso_id requerido"]);
    exit;
}

$sql="SELECT
    f.secuencia,
    f.unds,
    f.personas_std,
    f.tipo_fase,
    f.area,
    f.envase,
    f.peso_env,
    u.sigla,
    p.nombre AS producto,
    GROUP_CONCAT(a.nombre ORDER BY a.nombre SEPARATOR ', ') AS actividades
FROM prod_fases_prod f
INNER JOIN prod_productos p ON p.id=f.producto
INNER JOIN prod_act_prod a ON a.id=f.actividad
inner join prod_udm as u on u.id=f.udm_env
WHERE f.proceso_id=?
GROUP BY f.secuencia,f.unds,f.personas_std,f.tipo_fase,f.area,f.envase,p.nombre,f.peso_env,u.sigla
ORDER BY f.secuencia
";

$stmt=$conn->prepare($sql);
// Consulta solo del producto (1 sola fila)
$sqlProducto = "
    SELECT 
        p.nombre,
        p.peso_prod,
        u.sigla,
        e.nombre AS nombre_envase
    FROM prod_fases_prod f
    INNER JOIN prod_productos p ON p.id = f.producto
    INNER JOIN prod_udm u ON u.id = p.udm
    INNER JOIN prod_envase e ON e.id = p.envase
    WHERE f.proceso_id = ?
    LIMIT 1
";

$stmtP = $conn->prepare($sqlProducto);
$stmtP->bind_param("i", $proceso_id);
$stmtP->execute();
$producto = $stmtP->get_result()->fetch_assoc();




if(!$stmt){
    echo json_encode(["error"=>$conn->error]);
    exit;
}

$stmt->bind_param("i",$proceso_id);
$stmt->execute();

$res=$stmt->get_result();

$fases=[];

while($r=$res->fetch_assoc()){

    $acts=[];

    if(!empty($r['actividades'])){
        $lista=explode(",",$r['actividades']);

        foreach($lista as $a){
            $acts[]=["nombre"=>trim($a)];
        }
    }

    $r['actividades']=$acts;

    $fases[]=$r;
}

/* catalogos */

$tipos=$conn->query("SELECT cod,abreviatura FROM prod_tipo_prod ORDER BY abreviatura")->fetch_all(MYSQLI_ASSOC);

$areas=$conn->query("SELECT id,nombre FROM prod_area_prod ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

$envases=$conn->query("SELECT id,nombre FROM prod_envase ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

$actividades=$conn->query("
SELECT id,nombre 
FROM prod_act_prod 
ORDER BY nombre
")->fetch_all(MYSQLI_ASSOC);

$udms = $conn->query("SELECT id, sigla FROM prod_udm ORDER BY sigla")->fetch_all(MYSQLI_ASSOC);
echo json_encode([
    "fases"         =>$fases,
    "tipos"         =>$tipos,
    "areas"         =>$areas,
    "envases"       =>$envases,
    "udms"          => $udms,  
    "actividades"   =>$actividades,
    "producto"      => $producto   // ← NUEVO
],JSON_UNESCAPED_UNICODE);
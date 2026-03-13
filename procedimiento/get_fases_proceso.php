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
    f.kg_std,
    f.personas_std,
    f.tipo_fase,
    f.area,
    f.envase,
    
    p.nombre AS producto,
    GROUP_CONCAT(a.nombre ORDER BY a.nombre SEPARATOR ', ') AS actividades
FROM prod_fases_prod f
INNER JOIN prod_productos p ON p.id=f.producto
INNER JOIN prod_act_prod a ON a.id=f.actividad

WHERE f.proceso_id=?
GROUP BY f.secuencia,f.kg_std,f.personas_std,f.tipo_fase,f.area,f.envase,p.nombre
ORDER BY f.secuencia
";

$stmt=$conn->prepare($sql);

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


echo json_encode([
    "fases"=>$fases,
    "tipos"=>$tipos,
    "areas"=>$areas,
   "envases"=>$envases,
    "actividades"=>$actividades
],JSON_UNESCAPED_UNICODE);
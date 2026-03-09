<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<?php

include '../connection/conexion.php';

$id = $_POST['id']; 
$avance = [];



/*---------SE USA PAR PRECARGAR LOS INPUTS-----------*/

$q="SELECT turno,secuencia,kg_real
    FROM prod_avance_pedido
    WHERE id_pedido=?";

$stmt=$conn->prepare($q);
$stmt->bind_param("i",$id);
$stmt->execute();
$res=$stmt->get_result();

while($r=$res->fetch_assoc()){
    $avance[$r['turno']][$r['secuencia']] = $r['kg_real'];
}

/*----------------*/
/* ===============================
   CONSULTA → UNA FILA POR FASE
================================*/
$d="SELECT 
p.fecha_registro,
p.fecha_entrega,
p.cantidad,
p.num_pedido,
p.id_pedido,
c.razon_social,
f.secuencia,
GROUP_CONCAT(a.abreviatura ORDER BY a.abreviatura SEPARATOR '/') as etapanombre,
/*ACA USA LA ULTIMA FASE DE LA PRODUCCION PARA CALCULAR LOS TURNOS DQUE SE VAN A USAR POR ERSO SE USA EL MAX*/
MAX(f.kg_std) as kg_std
from prod_pedidos as p 
inner join prod_productos as pr on pr.id = p.producto 
inner join prod_fases_prod as f on f.producto = pr.id
inner join prod_clientes as c on c.id = p.id_cliente
inner join prod_act_prod as a on a.id=f.actividad
where p.id_pedido=?
GROUP BY f.secuencia,

         p.fecha_registro,
         p.fecha_entrega,
         p.cantidad,
         p.num_pedido,
         p.id_pedido,
         c.razon_social
ORDER BY f.secuencia";

$ff=$conn->prepare($d);
$ff->bind_param("i",$id);   
$ff->execute();

$result = $ff->get_result();    

/* ===============================
   GUARDAR PEDIDO Y FASES
================================*/
$fases = [];
$pedido = null;

while($row = $result->fetch_assoc()){

    if(!$pedido){
        $pedido = $row;
    }

    $fases[] = [
        'secuencia' => $row['secuencia'],
        'std' => $row['kg_std'],
        'etapa'=>$row['etapanombre']
    ];
}

if($pedido){

    $fecha_registro = $pedido['fecha_registro'];
    $fecha_entrega = $pedido['fecha_entrega'];
    $cantidad = $pedido['cantidad'];
    $num_pedido = $pedido['num_pedido'];
    $cliente = $pedido['razon_social'];
    
    /* ===============================
              CALCULOS
    ================================*/
    $std_base = $fases[0]['std']; // referencia para calcular turnos
    $dias = ceil($cantidad / $std_base);
    $totalFases = count($fases);
?>

<div class="row mb-2">
    <div class="col-12"><b>Pedido: </b> <?= $num_pedido ?></div>
    <div class="col-auto"><b>Cliente: </b> <?= $cliente ?></div>
    <div class="col-auto"><b>Fecha Registro: </b> <?= $fecha_registro ?></div>
    <div class="col-auto"><b>Fecha Entrega: </b><?= $fecha_entrega ?>
 <div class="col-auto">
        <button class="btn btn-sm btn-primary" id="btnAgregarFilaModal"><i class="bi bi-plus-circle-fill"></i> Añadir linea</button>
    </div>
</div>

   

</div>

<!----ENVIA EL FORM------->
<form id="formAvance">
<input type="hidden" name="id_pedido" value="<?= $id ?>">
<table class="table table-bordered">
<thead class="table-dark">
<tr>
    <th>Turno</th>
    <th>Etapa</th>
    <th>Estimado(KG)</th>
    <th>Produccion real(KG)</th>
    <th>Jornada</th>
    <th>HC</th>
    <th></th>
</tr>
</thead>
<tbody class="table-group-divider">

<?php for($turno=1; $turno <= $dias; $turno++): ?>

    <?php foreach($fases as $index => $fase): ?>

        <tr>

            <?php if($index == 0): ?>
                <td class="text-center align-middle" rowspan="<?= $totalFases ?>">
                <!------SECUENCIADOR-------------->    
                <?= $turno ?>
                </td>
            <?php endif; ?>


            <!--------AQUI DEBERIAN IR LA CANTIDAD DE FASES DE PRODUCCION --------------------------->

            <td><?=$fase['etapa']?></td>
            <td><?= $fase['std'] ?></td>
            <td>
               <?php
$valor = $avance[$turno][$fase['secuencia']] ?? '';
?>

<input type="text"
class="form-control"
value="<?= $valor ?>"
name="real[<?= $turno ?>][<?= $fase['secuencia'] ?>]">
            </td>

<td>
    <select class="form-select">
        <option value="1">DIA</option>
        <option value="2">NOCHE</option>
    </select>
</td>


<td>
<input type="number" class="form-select" min=0>
</td>


<td><button class="btn btn-sm btn-danger"><i class="bi bi-eraser-fill"></i></button></td>

        </tr>

    <?php endforeach; ?>
    <?php endfor; ?>

</tbody>
</table>
            </form>
<?php                   

}else{
    echo "Producto no tiene fases definidas.";
}


$stmt->close();
$ff->close();
$conn->close();

?>
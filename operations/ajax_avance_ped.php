<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<?php

include '../connection/conexion.php';

$id = $_POST['id']; 
$avance = [];

/*---------PRECARGAR AVANCES EXISTENTES-----------*/

$q="SELECT turno,secuencia,kg_real,turnodn,hc
    FROM prod_avance_pedido
    WHERE id_pedido=?";

$stmt=$conn->prepare($q);
$stmt->bind_param("i",$id);
$stmt->execute();
$res=$stmt->get_result();
$max_turno = 1;
while($r=$res->fetch_assoc()){

    $avance[$r['turno']][$r['secuencia']] = [
        'kg_real'=>$r['kg_real'],
        'turnodn'=>$r['turnodn'],
        'hc'=>$r['hc']
    ];



if(!empty($avance)){
    $max_turno = max(array_keys($avance));
}



}

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

$std_base = $fases[0]['std'];
$dias = ceil($cantidad / $std_base);
$totalFases = count($fases);
?>

<div class="row mb-2">

<div class="col-12"><b>Pedido: </b> <?= $num_pedido ?></div>
<div class="col-auto"><b>Cliente: </b> <?= $cliente ?></div>
<div class="col-auto"><b>Fecha Registro: </b> <?= $fecha_registro ?></div>
<div class="col-auto"><b>Fecha Entrega: </b> <?= $fecha_entrega ?></div>

</div>

<div class="row mb-3">
<div class="col-auto"><b>Turnos Aprox: </b><?= $dias?></div>
<div class="col-auto">
<button type="button" class="btn btn-sm btn-primary" id="btnAgregarTurno">
Añadir turno
</button>
</div>
</div>


<form id="formAvance">

<input type="hidden" name="id_pedido" value="<?= $id ?>">

<table class="table table-bordered" id="tablaAvance">

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

<tbody>

<?php for($turno=1; $turno <= $max_turno; $turno++): ?>

<?php foreach($fases as $index => $fase): ?>

<tr>

<?php if($index == 0): ?>

<td class="text-center align-middle" rowspan="<?= $totalFases ?>">
<?= $turno ?>
</td>

<?php endif; ?>

<td><?=$fase['etapa']?></td>

<td><?= $fase['std'] ?></td>

<td>

<?php
$valor = $avance[$turno][$fase['secuencia']]['kg_real'] ?? '';
?>

<input type="text"
class="form-control"
value="<?= $valor ?>"
onkeypress="soloNumeros(event)"
name="real[<?= $turno ?>][<?= $fase['secuencia'] ?>]">

</td>

<td>

<?php
$jornada = $avance[$turno][$fase['secuencia']]['turnodn'] ?? '';
?>

<select class="form-select"
name="jornada[<?= $turno ?>][<?= $fase['secuencia'] ?>]">

<option value="DIA" <?= $jornada=='DIA'?'selected':'' ?>>DIA</option>
<option value="NOCHE" <?= $jornada=='NOCHE'?'selected':'' ?>>NOCHE</option>

</select>

</td>

<td>

<?php
$hc = $avance[$turno][$fase['secuencia']]['hc'] ?? '';
?>

<input type="number"
class="form-control" onkeypress=soloNumeros(event)
value="<?= $hc ?>"
name="hc[<?= $turno ?>][<?= $fase['secuencia'] ?>]"
min="0">

</td>

<td>

<button type="button"
class="btn btn-sm btn-danger btnEliminarFila">
<i class="bi bi-trash"></i>
</button>

</td>

</tr>

<?php endforeach; ?>
<?php endfor; ?>

</tbody>

</table>

</form>

<?php

}else{
echo "Producto no tiene fases definidas.Debe agregarlas";
}

$stmt->close();
$ff->close();
$conn->close();

?>

<script>

$(function(){

const fases = <?= json_encode($fases) ?>;
const totalFases = fases.length;


/* =====================
AGREGAR TURNO
=====================*/

$(document).off('click','#btnAgregarTurno').on('click','#btnAgregarTurno',function(){

const tbody = $('#tablaAvance tbody');

const turnoActual = tbody.find('td[rowspan]').length;
const nuevoTurno = turnoActual + 1;

fases.forEach(function(fase,index){

let tr = $('<tr></tr>');

if(index === 0){

tr.append(`
<td class="text-center align-middle" rowspan="${totalFases}">
${nuevoTurno}
</td>
`);

}

tr.append(`

<td>${fase.etapa}</td>

<td>${fase.std}</td>

<td>
<input type="text"
class="form-control"
onkeypress="soloNumeros(event)"
name="real[${nuevoTurno}][${fase.secuencia}]">
</td>

<td>
<select class="form-select"
name="jornada[${nuevoTurno}][${fase.secuencia}]">
<option value="DIA">DIA</option>
<option value="NOCHE">NOCHE</option>
</select>
</td>

<td>
<input type="number"
class="form-control"
name="hc[${nuevoTurno}][${fase.secuencia}]"
min="0">
</td>

<td>
<button type="button"
class="btn btn-sm btn-danger btnEliminarFila">
<i class="bi bi-trash"></i>
</button>
</td>

`);

tbody.append(tr);

});

});

/* =====================
ELIMINAR FILA (AJUSTA ROWSPAN)
=====================*/

$(document).on('click', '.btnEliminarFila', function(){

    const fila = $(this).closest('tr');
    const celdaPropia = fila.find('td[rowspan]');

    if(celdaPropia.length){
        // Esta fila ES la primera del grupo (tiene el rowspan)
        const span = parseInt(celdaPropia.attr('rowspan'));

        if(span > 1){
            // Pasar el rowspan a la siguiente fila antes de eliminar
            const siguienteFila = fila.next('tr');
            celdaPropia.attr('rowspan', span - 1);
            siguienteFila.prepend(celdaPropia); // mover celda turno a la sig fila
        }

    } else {
        // Fila intermedia — solo reducir el rowspan del turno dueño
        let filaAnterior = fila.prev('tr');
        let celdaDueno = null;

        // Subir filas hasta encontrar la que tiene el rowspan
        while(filaAnterior.length){
            celdaDueno = filaAnterior.find('td[rowspan]');
            if(celdaDueno.length) break;
            filaAnterior = filaAnterior.prev('tr');
        }

        if(celdaDueno && celdaDueno.length){
            const span = parseInt(celdaDueno.attr('rowspan'));
            celdaDueno.attr('rowspan', span - 1);
        }
    }

    fila.remove();
});

});

</script>

<script>

function soloNumeros(e){

if (!/[0-9.,]/.test(e.key)) {
e.preventDefault();
}

}

</script>
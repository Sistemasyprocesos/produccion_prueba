<style>
#tablaAvance thead th{
    position: sticky;
    top: 0;
    z-index: 100;
    background: #212529;
    color: white;
}
</style>



<?php
include '../connection/conexion.php';

$id = $_POST['id'];

/* =====================================
ACTIVIDADES DEL PRODUCTO
===================================== */

$qAct = $conn->prepare("
            SELECT 
                f.secuencia,
                GROUP_CONCAT(DISTINCT a.abreviatura ORDER BY a.abreviatura SEPARATOR ' / ') AS etapanombre,
                MAX(f.kg_std) AS kg_std,
                p.fecha_registro,
                p.fecha_entrega,
                p.cantidad,
                p.num_pedido,
                c.razon_social
            FROM prod_pedidos p
            INNER JOIN prod_clientes c ON c.id = p.id_cliente
            INNER JOIN prod_productos pr ON pr.id = p.producto
            INNER JOIN prod_fases_prod f ON f.producto = pr.id
            INNER JOIN prod_act_prod a ON a.id = f.actividad
            WHERE p.id_pedido = ?
            GROUP BY 
                f.secuencia,
                p.fecha_registro,
                p.fecha_entrega,
                p.cantidad,
                p.num_pedido,
                c.razon_social
            ORDER BY f.secuencia
");

$qAct->bind_param("i",$id);
$qAct->execute();
$actividades = $qAct->get_result()->fetch_all(MYSQLI_ASSOC);

if(empty($actividades))
    {
        echo "<div class='alert alert-warning'>Este producto no tiene fases configuradas.</div>";
        exit;
    }

/* =====================================
DATOS DEL PEDIDO
===================================== */

        $fecha_registro = $actividades[0]['fecha_registro'];
        $fecha_entrega  = $actividades[0]['fecha_entrega'];
        $cantidad       = $actividades[0]['cantidad'];
        $num_pedido     = $actividades[0]['num_pedido'];
        $cliente        = $actividades[0]['razon_social'];

/* =====================================
OBTENER AVANCES GUARDADOS
===================================== */

$stmt = $conn->prepare("SELECT * 
        FROM prod_avance_pedido 
        WHERE id_pedido=? 
        ORDER BY turno
");

$stmt->bind_param("i",$id);
$stmt->execute();
$filas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if(empty($filas)){
    $filas[]=[
        'turno'=>1,
        'secuencia'=>'',
        'kg_real'=>'',
        'turnodn'=>'',
        'hc'=>''
    ];
}

$actividadesJS = json_encode($actividades,JSON_UNESCAPED_UNICODE);
?>



<div class="row mb-2">
    <div class="col-12"><b>Pedido:</b> <?= $num_pedido ?></div>
    <div class="col-auto"><b>Cliente:</b> <?= $cliente ?></div>
    <div class="col-auto"><b>Fecha Registro:</b> <?= $fecha_registro ?></div>
    <div class="col-auto"><b>Fecha Entrega:</b> <?= $fecha_entrega ?></div>
    <div class="col-auto"><b>Cantidad:</b> <?= $cantidad ?> KG</div>
</div>



<div class="row mb-3">
    <div class="col-auto">
        <button type="button" class="btn btn-sm btn-primary" id="btnAgregarTurno">
                <i class="bi bi-plus-circle"></i> Añadir fila
        </button>
    </div>
</div>



<form id="formAvance">
    <div style="max-height:400px; overflow-y:auto;">
    <input type="hidden" name="id_pedido" value="<?= $id ?>">
        <table class="table table-bordered" id="tablaAvance">
            <thead class="table-dark">
                <tr>
                    <th>Turno</th>
                    <th>Etapa</th>
                    <th>Kg (S)</th>
                    <th>Kg(R)</th>
                    <th>Jornada</th>
                    <th>HC</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

                <?php foreach($filas as $f): ?>
                    <tr>
                        <td>
                            <input type="number" min=1 class="form-control" name="turno[]" value="<?= $f['turno'] ?>">
                        </td>
                        <td>
                            <select class="form-select" name="fase[]">
                                <option disabled>--Seleccione--</option>
                                    <?php foreach($actividades as $act): ?>

                                <option value="<?= $act['secuencia'] ?>" 
                                    data-kg="<?= $act['kg_std'] ?>"
                                    <?= $f['secuencia']==$act['secuencia']?'selected':'' ?>>
                                    <?= $act['etapanombre'] ?>
                                </option>

                                    <?php endforeach; ?>
                            </select>
                        </td>

                        <td>
                            <input type="text" class="form-control estimado" onkeypress=soloNumeros(event)  name="estimado[]" readonly>
                        </td>
                        <td>
                            <input type="text" onkeypress=soloNumeros(event) class="form-control" value="<?= $f['kg_real'] ?>" name="real[]">
                        </td>

                        <td>
                            <select class="form-select" name="jornada[]">
                                <option value="DIA" <?= $f['turnodn']=='DIA'?'selected':'' ?>>
                                    DIA
                                </option>
                                <option value="NOCHE" <?= $f['turnodn']=='NOCHE'?'selected':'' ?>>
                                    NOCHE
                                </option>
                            </select>
                        </td>


                        <td>
                            <input type="number" class="form-control" onkeypress=soloNumeros(event) value="<?= $f['hc'] ?>" name="hc[]" min="0">
                        </td>

                        <td>
                            <button type="button" class="btn btn-sm btn-danger btnEliminarFila">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
</form>
                                    </div>


<script>
$(function(){

/* ======================
AGREGAR FILA
======================*/

    $(document).off('click','#btnAgregarTurno').on('click','#btnAgregarTurno',function(){

        let ultimoTurno = parseInt(
            $('#tablaAvance tbody tr:last input[name="turno[]"]').val()
        ) || 1;

        let opciones = '';

    $('select[name="fase[]"]:first option').each(function(){

        let kg = $(this).data('kg');
        opciones += `<option value="${$(this).val()}" data-kg="${kg}">
        ${$(this).text()}
        </option>`;

    });

let fila = `

    <tr>

        <td>
           <input type="number" class="form-control" name="turno[]" onkeypress=soloNumeros(event) value="${ultimoTurno+1}">
        </td>

        <td>
            <select class="form-select" name="fase[]">
                ${opciones}
            </select>
        </td>

        <td>
            <input type="text" class="form-control estimado" name="estimado[]" readonly>
        </td>

        <td>
            <input type="text" class="form-control" onkeypress=soloNumeros(event) name="real[]">
        </td>

        <td>
            <select class="form-select" name="jornada[]">
                <option value="DIA">DIA</option>
                <option value="NOCHE">NOCHE</option>
            </select>
        </td>

        <td>
            <input type="number" class="form-control" onkeypress=soloNumeros(event) name="hc[]" min="0">
        </td>

        <td>
            <button type="button" class="btn btn-sm btn-danger btnEliminarFila">
                <i class="bi bi-trash"></i>
            </button>
        </td>

    </tr>

`;

    $('#tablaAvance tbody').append(fila);

    });


/* ======================
ELIMINAR FILA
======================*/

    $(document).on('click','.btnEliminarFila',function(){

        if($('#tablaAvance tbody tr').length>1){
            $(this).closest('tr').remove();
        }

    });


/* ======================
AUTO ESTIMADO
======================*/

function cargarEstimado(select){

        let kg = $(select).find(':selected').data('kg');
            $(select).closest('tr').find('.estimado').val(kg);
        }

        $(document).on('change','select[name="fase[]"]',function(){
            cargarEstimado(this);
        });


/* ======================
CARGAR ESTIMADOS AL INICIO
======================*/

        $('select[name="fase[]"]').each(function(){
            cargarEstimado(this);
        });

});

/* ======================
VALIDAR NUMEROS
======================*/

function soloNumeros(e){
    if(!/[0-9.,]/.test(e.key)){
        e.preventDefault();
    }
}

</script>
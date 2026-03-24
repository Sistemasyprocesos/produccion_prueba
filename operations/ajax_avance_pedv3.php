<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
include '../connection/conexion.php';
$id = $_POST['id']; 
$avance = [];

/*---------SE USA PARA PRECARGAR LOS INPUTS-----------*/
$q = "SELECT turno, secuencia, unidades_reales, fecha_turno, turnodn, hc
      FROM prod_avance_pedido
      WHERE id_pedido = ?";
$stmt = $conn->prepare($q);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $avance[$r['secuencia']][$r['turno']] = [
        'kg_real' => $r['unidades_reales'],
        'fecha'   => $r['fecha_turno'],
        'jornada' => $r['turnodn'],
        'hc'      => $r['hc'],
    ];
}
/* ===============================
   CONSULTA → UNA FILA POR FASE
================================*/
$d = "SELECT 
    p.fecha_registro,
    p.fecha_entrega,
    p.cantidad,
    pr.nombre as productonombre,
    pr.peso_prod,
    p.num_pedido,
    p.id_pedido,
    c.razon_social,
    e.nombre as nombreenvase,
    e.abreviatura,
    f.peso_env as pen,
    f.secuencia,
     u_fase.sigla as udm, 
     u_prod.sigla,
      u_prod.equivalente_kg as eq_kg,
      u_fase.equivalente_kg as eq_kg_fase,
    GROUP_CONCAT(a.nombre ORDER BY a.nombre SEPARATOR '/') as etapanombre,
    MAX(f.unds) as unds
FROM prod_pedidos AS p 
INNER JOIN prod_productos AS pr ON pr.id = p.producto 
INNER JOIN prod_fases_prod AS f ON f.producto = pr.id
INNER JOIN prod_clientes AS c ON c.id = p.id_cliente
INNER JOIN prod_act_prod AS a ON a.id = f.actividad
inner join prod_envase as e on e.id=f.envase
INNER JOIN prod_udm u_prod ON u_prod.id = pr.udm
INNER JOIN prod_udm u_fase ON u_fase.id = f.udm_env
WHERE p.id_pedido = ?
GROUP BY 
    f.secuencia,
    f.peso_env,
    p.fecha_registro,
    p.fecha_entrega,
    p.cantidad,
    p.num_pedido,
    p.id_pedido,
    c.razon_social,
    pr.nombre,
    pr.peso_prod,
    e.nombre,
    u_fase.sigla,
    u_prod.equivalente_kg,
    u_prod.sigla,
    e.abreviatura,
    u_fase.equivalente_kg
        
ORDER BY f.secuencia";

$ff = $conn->prepare($d);
$ff->bind_param("i", $id);
$ff->execute();
$result = $ff->get_result();

/* ===============================
   GUARDAR PEDIDO Y FASES
================================*/
$fases   = [];
$pedido  = null;

while ($row = $result->fetch_assoc()) {
    if (!$pedido) {
        $pedido = $row;
    }
    $fases[] = [
        'secuencia' => $row['secuencia'],
        'std'       => $row['unds'],
        'peso_env'=>$row['pen'],
        'etapa'     => $row['etapanombre'],
        'udm'       => $row['udm'],
        'sigenv'=>$row['abreviatura'],
          'eq_kg_fase'=> $row['eq_kg_fase'] ?? 1,
    ];
}


if ($pedido) {
    $fecha_registro = $pedido['fecha_registro'];
    $fecha_entrega  = $pedido['fecha_entrega'];
    $cantidad       = $pedido['cantidad'] ?? 0;
    $num_pedido     = $pedido['num_pedido'];
    $cliente        = $pedido['razon_social'];
    $envase         = $pedido["nombreenvase"];
    $prod           = $pedido["productonombre"];
    $pesoenva       = $pedido["pen"];
    $pesoprod       = $pedido["peso_prod"] ?? 0;
    $usigla         = $pedido["sigla"];
    $unds=$pedido["unds"];

  
    $equivalente = $pedido["eq_kg"] ?? 1;
$eq_fase = $pedido["eq_kg_fase"] ?? 1;


    if ($equivalente > 0) {
        $kilos = $cantidad * ($pesoprod * $equivalente);
      
       $obj = $unds * ($pesoenva * $eq_fase);
    } else {
        $kilos = 0;
    }
?>

<!-- ===== ENCABEZADO DEL PEDIDO ===== -->
<div class="row mb-3">
    <div class="col-auto"><b># Pedido:</b> <?= htmlspecialchars($num_pedido) ?></div>
</div>

<div class="row mb-3 text-center">

    <div class="col-3 border-end">
        <div class="row">
            <b>Pedido:</b> 
        </div>    
        <div>
            <?= htmlspecialchars($prod).' '.$envase.' '. $pesoprod.' '.$usigla  ?>
        </div>
    </div>
<!----------------------->
    <div class="col-3 border-end">
        <div class="row">
            <b>Cliente:</b> 
        </div>
        <div>
            <?= htmlspecialchars($cliente) ?>
        </div>
    </div>
<!----------------------------->
    <div class="col-3 border-end">
        <div class="row">
            <b>Cantidad: </b>
        </div>
        <div>
            <?=$cantidad.' ('.$envase.' '. $pesoprod.' '.$usigla.')' ?>
        </div>
      <div><?=number_format($kilos, 2).' kg'?> </div>
    </div>
    <!---------------->
    <div class="col-3">
        <div class="row">
            <b>Fecha Entrega:</b> 
        </div>
        <div>
            <?= htmlspecialchars($fecha_entrega) ?>
        </div>    
    </div>

</div>


<!-- ===== FORMULARIO ===== -->
<form id="formAvance">
    <input type="hidden" name="id_pedido" value="<?= $id ?>">

    <?php foreach ($fases as $fase): ?>

        <?php
        $peso   = $fase['peso_env'];
        $unds   = $fase['std'];
        $eq     = $fase['eq_kg_fase'];

        $obj_fase = ($eq > 0) ? $unds * ($peso * $eq) : 0;
            $turnosFase = ($fase['std'] > 0) ? ceil($cantidad / $fase['std']) : 0;
        ?>

        <!-- CONTENEDOR POR FASE: el botón busca su tabla dentro de este div -->

<hr>
        <div class="fase-bloque mb-4">

            <div class="row mb-2">

                <div class="col-auto">
                    <h6 class="mt-2 mb-1">
                                <span class="badge bg-primary me-2 fs-4">Fase <?= $fase['secuencia'] ?></span>
                                <?= htmlspecialchars($fase['etapa']) ?>
                                <small class="text-muted ms-2">(Std: <?= $fase['std'] ?> kg — <?= $turnosFase ?> turnos)</small>
                            </h6>
                </div>

                <div class="col-auto">
                    <!-- CLASE en lugar de ID + datos de la fase en data-* -->
                   <button type="button"
                        class="btn btn-sm btn-success btnAgregarTurno"
                        data-std="<?= $fase['std'] ?>"
                        data-secuencia="<?= $fase['secuencia'] ?>"
                        data-peso-env="<?= $fase['peso_env'] ?>"
                        data-sigenv="<?= htmlspecialchars($fase['sigenv']) ?>"
                        data-udm="<?= htmlspecialchars($fase['udm']) ?>"
                        data-eq="<?= $fase['eq_kg_fase']?>"
                        data-obj-total="<?= $obj_fase * $turnosFase ?>"
                        >
                    <i class="bi bi-plus-circle"></i>
                     AÑADIR TURNO
                    </button>
                </div>
            </div>

            <!-- CLASE en lugar de ID -->
            <table class="table table-bordered border-dark table-sm table-light table-hover table-striped tablaAvance">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:70px;">Turno</th>
                        <th>Fecha</th>
                        <th>Jornada</th>
                        <th># Colab</th>
                        <th>Unidades Estandar</th>
                        <th>Objetivo</th>
                        <th>Unidades Producidas</th>
                        <th>KG Reales</th>
                        <th>Dif</th>
                        <th>Cumplimiento %</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php for ($turno = 1; $turno <= $turnosFase; $turno++): ?>
                        <?php
                            $producidoAcum = ($turno - 1) * $fase['std'];
                            $restante      = $cantidad - $producidoAcum;
                            $estimado      = min($fase['std'], $restante);
                            $peso=$fase['peso_env'];
                          $val_kg      = $avance[$fase['secuencia']][$turno]['kg_real'] ?? '';
$val_fecha   = $avance[$fase['secuencia']][$turno]['fecha']   ?? '';
$val_jornada = $avance[$fase['secuencia']][$turno]['jornada'] ?? '';
$val_hc      = $avance[$fase['secuencia']][$turno]['hc']      ?? '';
                        ?>

                        <tr data-peso="<?= $fase['peso_env'] ?>" data-eq="<?= $fase['eq_kg_fase'] ?>">

                            <td class="text-center align-middle turno-num">
                                <?= $turno ?>
                            </td>
                            
                            <td>
                              <!-- Fecha -->
                    <input type="date" class="form-control"
    name="fecha[<?=$fase['secuencia']?>][<?=$turno?>]"
    value="<?= htmlspecialchars($val_fecha) ?>">
                            </td>

                            <!------JORNADA-------------->
                            <td>
                              <!-- Jornada -->
<select class="form-select" name="jornada[<?=$fase['secuencia']?>][<?=$turno?>]">
    <option value=""></option>
    <option value="DIA"   <?= $val_jornada==='DIA'   ? 'selected':'' ?>>DIA</option>
    <option value="NOCHE" <?= $val_jornada==='NOCHE' ? 'selected':'' ?>>NOCHE</option>
</select>
                            </td>
                            
                            <!-----COLAB------>
                            <td style="width:80px;">
                                <input type="number" class="form-control" min="0" step="1"
    name="hc[<?=$fase['secuencia']?>][<?=$turno?>]"
    value="<?= htmlspecialchars($val_hc) ?>"
    onkeydown="return /[\d]|Backspace|Delete|Arrow/.test(event.key)">
                            </td>
                         

                            <!------ESTIMADO------------->
                            <td class="text-center align-middle td-unds"><?=$fase['std'].' '.$fase['sigenv'].' '. $peso.' '.$fase['udm']  ?></td>

                              <!-------obj---------------->
                            <td style="width:auto;" class="text-center align-middle  td-obj" data-obj="<?= $obj_fase ?>">
                                <?= number_format($obj_fase,2).' KG' ?></td>
                            </td>

                          <!--unidades Real -->
                            <td>
                             <!-- Unidades producidas -->
<input type="number" step="0.01" min="0"
    class="form-control form-control-sm input-real"
    value="<?= htmlspecialchars($val_kg) ?>"
    name="real[<?=$fase['secuencia']?>][<?=$turno?>]"
    placeholder="0.00">
                            </td>
                        <!-----KG REALES------------->
                 <td class="text-center align-middle td-kg"></td>

                <!-- Dif -->
                <td class="text-center align-middle td-dif"></td>

                <!-- Cumplimiento -->
                <td class="text-center align-middle td-cumpl"></td>
                         
                        </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot class="table-primary table bordered">
                    <tr>
                            <td colspan="4" class="text-center align-middle"><b>TOTAL PROCESO</b></td>  
                        <!------SUMATORIA DE UNIDADES ESTANDAR---------->
                            <td class="text-center align-middle total-unds"><b></b></td>           
                        <!------SUMATORIA DE OBJETIVO---------->
                            <td class="text-center align-middle total-obj"><b></b></td>     
                      
                            <td></td>
  <!------SUMATORIA DE KG REAL---------->
                            <td class="text-center align-middle total-real"><b></b></td>  
                            
                        <!------SUMATORIA DE DIF---------->
                            <td class="text-center align-middle total-dif"><b></b></td>                        
                        <!------SUMATORIA DE CUNPLIMIENTO---------->
                            <td class="text-center align-middle total-cumpl"><b></b></td>                       
                    </tr>
                </tfoot>
            </table>

        </div><!-- /fase-bloque -->

    <?php endforeach; ?>

</form>

<?php
} else {
    echo '<div class="alert alert-warning">Producto no tiene fases definidas.</div>';
}

$stmt->close();
$ff->close();
$conn->close();
?>

<script>
/* ======================
      AGREGAR FILA — solo en la tabla del bloque donde se hizo clic
======================*/
$(document).off('click', '.btnAgregarTurno').on('click', '.btnAgregarTurno', function () {

    const $bloque    = $(this).closest('.fase-bloque');
    const $tbody     = $bloque.find('.tablaAvance tbody');
    const $tabla     = $bloque.find('.tablaAvance');
    const secuencia  = $(this).data('secuencia');
    const std        = $(this).data('std');
    const pesoEnv    = $(this).data('peso-env');
    const sigenv     = $(this).data('sigenv');
    const udm        = $(this).data('udm');
    const eq         = $(this).data('eq') || 1;
    const objTotal   = parseFloat($(this).data('obj-total')) || 0;

    const nextTurno  = $tbody.find('tr').length + 1;

    // Sumar KG reales ya producidos en las filas existentes
    let kgAcumulado = 0;
    $tbody.find('tr').each(function () {
        const unds = parseFloat($(this).find('.input-real').val()) || 0;
        const p    = parseFloat($(this).data('peso')) || 0;
        const e    = parseFloat($(this).data('eq'))   || 1;
        kgAcumulado += unds * p * e;
    });

    // Objetivo de esta nueva fila = lo que falta
    const objFila = Math.max(objTotal - kgAcumulado, 0);

    const pesoestimado = std * pesoEnv * eq; // estándar (solo para mostrar en Und. Estándar)

    const fila = `
        <tr data-peso="${pesoEnv}" data-eq="${eq}">
            <td class="text-center align-middle turno-num">${nextTurno}</td>
            <td><input type="date" class="form-control" name="fecha[${secuencia}][${nextTurno}]"></td>
            <td>
                <select class="form-select" name="jornada[${secuencia}][${nextTurno}]">
                    <option value=""></option>
                    <option value="DIA">DIA</option>
                    <option value="NOCHE">NOCHE</option>
                </select>
            </td>
            <td style="width:80px;">
                <input type="number" class="form-control" min="0" step="1"
                    name="hc[${secuencia}][${nextTurno}]"
                    onkeydown="return /[\\d]|Backspace|Delete|Arrow/.test(event.key)">
            </td>
            <td class="text-center align-middle td-unds">${std} ${sigenv} ${pesoEnv} ${udm}</td>
            <td class="text-center align-middle td-obj" data-obj="${objFila.toFixed(2)}">${objFila.toFixed(2)} KG</td>
            <td>
                <input type="number" step="0.01" min="0"
                    class="form-control form-control-sm input-real"
                    name="real[${secuencia}][${nextTurno}]"
                    placeholder="0.00">
            </td>
            <td class="text-center align-middle td-kg"></td>
            <td class="text-center align-middle td-dif"></td>
            <td class="text-center align-middle td-cumpl"></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btnEliminarFila">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $tbody.append(fila);
    recalcularTotales($tabla);
});
/* ======================
   ELIMINAR FILA + renumerar
======================*/
$(document).off('click', '.btnEliminarFila').on('click', '.btnEliminarFila', function () {
    const $tbody = $(this).closest('tbody');
    $(this).closest('tr').remove();

        // Renumerar turnos para que queden consecutivos
    $tbody.find('tr').each(function (i) {
        $(this).find('.turno-num').text(i + 1);
    });
    const $tabla = $tbody.closest('.tablaAvance');
recalcularTotales($tabla);
});





/* ======================
   RECALCULAR TOTALES DEL FOOTER
======================*/
function recalcularTotales($tabla) {
    let totalObj  = 0;
    let totalReal = 0;
    let totalKg   = 0;

    $tabla.find('tbody tr').each(function () {
        const obj     = parseFloat($(this).find('.td-obj').data('obj')) || 0;
        const unds    = parseFloat($(this).find('.input-real').val()) || 0;
        const peso    = parseFloat($(this).data('peso')) || 0;
        const eq      = parseFloat($(this).data('eq')) || 1;

        const kg      = unds * peso * eq;
        const dif     = kg - obj;
        const cumpl   = obj > 0 ? ((kg / obj) * 100).toFixed(1) + '%' : '-';

        $(this).find('.td-kg').text(kg.toFixed(2) + ' KG');
        $(this).find('.td-dif').text(dif.toFixed(2) + ' KG');
        $(this).find('.td-cumpl').text(cumpl);

        totalObj  += obj;
        totalKg   += kg;
    });

    const totalDif   = totalKg - totalObj;
    const totalCumpl = totalObj > 0 ? ((totalKg / totalObj) * 100).toFixed(1) + '%' : '-';

    const $tfoot = $tabla.find('tfoot');
    $tfoot.find('.total-obj').html('<b>'  + totalObj.toFixed(2)  + ' KG</b>');
    $tfoot.find('.total-real').html('<b>' + totalKg.toFixed(2)   + ' KG</b>');
    $tfoot.find('.total-dif').html('<b>'  + totalDif.toFixed(2)  + ' KG</b>');
    $tfoot.find('.total-cumpl').html('<b>' + totalCumpl          + '</b>');
}

// Disparar al escribir en cualquier input real
$(document).on('input', '.input-real', function () {
    const $tabla = $(this).closest('.tablaAvance');
    recalcularTotales($tabla);
});

// Disparar también al agregar o eliminar fila
// (llamar recalcularTotales al final de ambos eventos ya existentes)

// Última línea del <script> en ajax_avance_pedv3.php
$(".tablaAvance").each(function () {
    recalcularTotales($(this));
});
</script>
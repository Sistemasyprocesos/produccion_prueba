<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<?php
include '../connection/conexion.php';
$id = $_POST['id']; 
$avance = [];

/*---------SE USA PARA PRECARGAR LOS INPUTS-----------*/
$q = "SELECT turno, secuencia, kg_real
      FROM prod_avance_pedido
      WHERE id_pedido = ?";
$stmt = $conn->prepare($q);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $avance[$r['secuencia']][$r['turno']] = $r['kg_real'];
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
    f.peso_env as pen,
    f.secuencia,
     u_fase.sigla as udm, 
     u_prod.sigla,
    GROUP_CONCAT(a.nombre ORDER BY a.nombre SEPARATOR '/') as etapanombre,
    MAX(f.kg_std) as kg_std
FROM prod_pedidos AS p 
INNER JOIN prod_productos AS pr ON pr.id = p.producto 
INNER JOIN prod_fases_prod AS f ON f.producto = pr.id
INNER JOIN prod_clientes AS c ON c.id = p.id_cliente
INNER JOIN prod_act_prod AS a ON a.id = f.actividad
inner join prod_envase as e on e.id=pr.envase
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
    u_prod.sigla
        
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
        'std'       => $row['kg_std'],
        'peso_env'=>$row['pen'],
        'etapa'     => $row['etapanombre'],
        'udm'       => $row['udm'],
    ];
}

if ($pedido) {
    $fecha_registro = $pedido['fecha_registro'];
    $fecha_entrega  = $pedido['fecha_entrega'];
    $cantidad       = $pedido['cantidad'];
    $num_pedido     = $pedido['num_pedido'];
    $cliente        = $pedido['razon_social'];
    $envase=$pedido["nombreenvase"];
    $prod=$pedido["productonombre"];
    $pesoenva=$pedido["pen"];
    $pesoprod=$pedido["peso_prod"];
    $usigla=$pedido["sigla"];
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
        <div class="row">
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
            $turnosFase = ($fase['std'] > 0) ? ceil($cantidad / $fase['std']) : 0;
        ?>

        <!-- CONTENEDOR POR FASE: el botón busca su tabla dentro de este div -->

<hr>
        <div class="fase-bloque mb-4">

           

            <div class="row mb-2">

                <div class="col-auto">
                    <h6 class="mt-2 mb-1">
                                <span class="badge bg-primary me-2">Fase <?= $fase['secuencia'] ?></span>
                                <?= htmlspecialchars($fase['etapa']) ?>
                                <small class="text-muted ms-2">(Std: <?= $fase['std'] ?> kg — <?= $turnosFase ?> turnos)</small>
                            </h6>
                </div>

                <div class="col-auto">
                    <!-- CLASE en lugar de ID + datos de la fase en data-* -->
                    <button type="button"
                            class="btn btn-sm btn-success btnAgregarTurno"
                            data-std="<?= $fase['std'] ?>"
                            data-secuencia="<?= $fase['secuencia'] ?>">
                        <i class="bi bi-plus-circle"></i> AÑADIR TURNO
                    </button>
                </div>
            </div>

            <!-- CLASE en lugar de ID -->
            <table class="table table-bordered table-sm tablaAvance">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:80px;">Turno</th>
                        <th>Fecha</th>
                        <th>Jornada</th>
                        <th># Colab</th>
                        <th>UND</th>
                        <th>Estimado (KG)</th>
                        <th>Real (KG)</th>
                        <th>Dif</th>
                        <th>Cump</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php for ($turno = 1; $turno <= $turnosFase; $turno++): ?>
                        <?php
                            $producidoAcum = ($turno - 1) * $fase['std'];
                            $restante      = $cantidad - $producidoAcum;
                            $estimado      = min($fase['std'], $restante);
                            $peso=$fase['peso_env'];
                            $valor         = $avance[$fase['secuencia']][$turno] ?? '';
                        ?>
                        <tr>
                            <td class="text-center align-middle turno-num"><?= $turno ?></td>
                            <td><input type="date" class="form-control" name="fecha[<?= $fase['secuencia'] ?>][<?= $turno ?>]"></td>
                            <!------JORNADA-------------->
                            <td>
                                <select class="form-select" name="jornada[<?= $fase['secuencia'] ?>][<?= $turno ?>]">
                                    <option value=""></option>
                                    <option value="DIA">DIA</option>
                                    <option value="NOCHE">NOCHE</option>
                                </select>
                            </td>
                            <!-----COLAB------>
                            <td style="width:80px;"><input type="number" class="form-control" min="0" step="1" name="hc[<?= $fase['secuencia'] ?>][<?= $turno ?>]" onkeydown="return /[\d]|Backspace|Delete|Arrow/.test(event.key)">
                                
                            </td>
                            <!-------UNID---------------->

                            <td><?=$fase['std'].' '.$fase['udm'] ?></td>
                            <!------ESTIMADO------------->
                            <td class="text-center align-middle"><?= $peso ?></td>
                            
                            <td>
                                <input type="number" step="0.01" min="0"
                                    class="form-control form-control-sm"
                                    value="<?= htmlspecialchars($valor) ?>"
                                    name="real[<?= $fase['secuencia'] ?>][<?= $turno ?>]"
                                    placeholder="0.00">
                            </td>

                            <td></td>
                            <td></td>
                         
                        </tr>
                    <?php endfor; ?>
                </tbody>
                <tfoot class="table-info">
                    <tr>
                        <td colspan="2"></td>
                        <td><b>TOTAL</b></td>
                        <td><b>total colab</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                       
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

    // 1. Encontrar el bloque padre de ESTE botón (no de todos)
    const $bloque   = $(this).closest('.fase-bloque');
    const $tbody    = $bloque.find('.tablaAvance tbody');
    const secuencia = $(this).data('secuencia');
    const std       = $(this).data('std');

    // 2. Número de filas actuales = número del próximo turno
    const nextTurno = $tbody.find('tr').length + 1;

       const cantidad = <?= $cantidad ?>;

// producido acumulado antes de este turno
const producidoAcum = (nextTurno - 1) * std;

// lo que falta producir
const restante = cantidad - producidoAcum;

// estimado del turno
const estimado = Math.min(std, restante);
    // 3. Construir fila
    const fila = `
        <tr>
            <td class="text-center align-middle turno-num">${nextTurno}</td>
            <td><input type="date" class="form-control" name="fecha[${secuencia}][${nextTurno}]"></td>
            <td>
                <select class="form-select" name="jornada[${secuencia}][${nextTurno}]">
                    <option value=""></option>
                    <option value="DIA">DIA</option>
                    <option value="NOCHE">NOCHE</option>
                </select>
            </td>
            <td><input type="number" class="form-control" min="0" step="1" name="hc[${secuencia}][${nextTurno}]" onkeydown="return /[\d]|Backspace|Delete|Arrow/.test(event.key)"></td>
            <td class="text-center align-middle">${std}</td>
           
  

<td class="text-center align-middle">${estimado}</td>
            
           <td>
                <input type="number" step="0.01" min="0"
                    class="form-control form-control-sm"
                    name="real[${secuencia}][${nextTurno}]"
                    placeholder="0.00">
            </td>
            
            <td></td>
            <td></td>
            <td>
                <button type="button" class="btn btn-sm btn-danger btnEliminarFila">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $tbody.append(fila);
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
});
</script>
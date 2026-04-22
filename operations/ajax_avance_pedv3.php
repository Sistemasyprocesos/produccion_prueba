<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

.total-obj {
    display: none;
}


/* ── Tabla minimalista ── */
.tablaAvance {
    border-collapse: collapse;
    width: 100%;
    font-size: 0.82rem;
    border: none !important;
}
.tablaAvance thead th {
    background: #8c96a72e;
    color: #030a17;
    font-weight: 500;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border: none;
    border-bottom: 1.5px solid #e5e7eb;
    padding: 8px 10px;
}
.tablaAvance tbody tr {
    border-bottom: 1px solid #d1d1d2;  /* ya la tienes */
    transition: background 0.15s;
    transition: background 0.15s;
}
.tablaAvance tbody tr:hover {
    background-color: #b6b6b63d;
}
.tablaAvance tbody tr.table-warning {
    background-color: #2ab8db4b !important;
}
.tablaAvance tbody tr.table-warning:hover {
    background-color: #2ab8dba5 !important;
}
.tablaAvance tbody td {
    border: none;
    padding: 7px 10px;
    vertical-align: middle;
    color: #070b11;
}
.tablaAvance tfoot tr {
    border-top: 1.5px solid #e5e7eb;
    background: #f9fafb !important;
}
.tablaAvance tfoot td {
    border: none;
    padding: 8px 10px;
    font-size: 0.8rem;
    color: #374151;
}

/* ── Inputs dentro de la tabla ── */
.tablaAvance .form-control,
.tablaAvance .form-select {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.8rem;
    padding: 4px 8px;
    height: 32px;
    background: white;
    color: #374151;
    box-shadow: none;
     appearance: auto;
    -webkit-appearance: auto;
}
.tablaAvance .form-control:focus,
.tablaAvance .form-select:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 3px rgba(147,197,253,0.2);
    outline: none;
}

/* ── Fase bloque ── */
.fase-bloque {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 1.5rem;
}

/* ── Badge de fase ── */
.fase-bloque .badge.bg-primary {
    background: #eff6ff !important;
    color: #1727cf !important;
    font-size: 0.90rem !important;
    font-weight: 700;
    border-radius: 6px;
    padding: 4px 10px;
    border: 1px solid #24c379;
}

/* ── Botón añadir turno ── */
.btnAgregarTurno {
    font-size: 0.78rem; 
    padding: 4px 12px;
    border-radius: 6px;
    border: 1px solid #bbf7d0;
    background: #f0fdf4;
    color: #15803d;
    font-weight: 500;
}
.btnAgregarTurno:hover {
    background: #dcfce7;
    border-color: #86efac;
    color: #185f32;
}

/* ── Botón eliminar ── */
.btnEliminarFila {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.75rem;
}

/* ── Progress bar ── */
.progress {
    height: 12px;
    border-radius: 99px;
    background: #f3f4f6;
    overflow: hidden;
}
.barra-cumplimiento {
    border-radius: 99px;
    transition: width 0.4s ease, background-color 0.4s ease;
    font-size: 0;
}



.encabezado-pedido {
    position: sticky;
    top: 0;
    z-index: 3;
    background-color: white;
    border-bottom: 2px solid #dee2e6;
    transition: all 0.3s ease;
    padding: 10px 0;
    overflow: hidden;
}
.encabezado-pedido * {
    transition: font-size 0.3s ease, padding 0.3s ease;
}
.encabezado-pedido.compacto {
    padding: 3px 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.encabezado-pedido.compacto .num-pedido { font-size: 0.75rem; }
.encabezado-pedido.compacto .detalle-pedido { font-size: 0.70rem; }
.encabezado-pedido.compacto .detalle-pedido b { font-size: 0.72rem; }
.encabezado-pedido.compacto .row { margin-bottom: 0 !important; }


.progress {
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}
.barra-cumplimiento {
    transition: width 0.4s ease, background-color 0.4s ease;
    font-size: 0.75rem;
    font-weight: 600;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.35);
    min-width: 36px;
}

</style>

<?php
include '../connection/conexion.php';
$id = $_POST['id'];
$avance = [];

/*---------SE USA PARA PRECARGAR LOS INPUTS-----------*/
$q = "SELECT turno, secuencia, unidades_reales, fecha_turno, turnodn, hc, obj_kg
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
        'obj'     => $r['obj_kg']
    ];

   

}
 $turnosEliminados = [];

$qDel = "SELECT secuencia, turno 
         FROM prod_avance_turnos_eliminados
         WHERE id_pedido = ?";
$stmtDel = $conn->prepare($qDel);
$stmtDel->bind_param("i", $id);
$stmtDel->execute();
$resDel = $stmtDel->get_result();

while ($r = $resDel->fetch_assoc()) {
    $turnosEliminados[$r['secuencia']][] = $r['turno'];
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
    p.estado,
    p.id_pedido,
    c.razon_social,
    e.nombre as nombreenvase,
    w.nombre as wnombre,
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
INNER JOIN prod_envase AS e ON e.id = f.envase
INNER JOIN prod_envase AS w ON w.id = pr.envase
INNER JOIN prod_udm u_prod ON u_prod.id = pr.udm
INNER JOIN prod_udm u_fase ON u_fase.id = f.udm_env
WHERE p.id_pedido = ?
GROUP BY
    f.secuencia, f.peso_env, p.fecha_registro, p.fecha_entrega,
    p.cantidad, p.num_pedido, p.id_pedido, c.razon_social,
    pr.nombre, pr.peso_prod, e.nombre, u_fase.sigla,
    u_prod.equivalente_kg, u_prod.sigla, e.abreviatura, u_fase.equivalente_kg
ORDER BY f.secuencia";

$ff = $conn->prepare($d);
$ff->bind_param("i", $id);
$ff->execute();
$result = $ff->get_result();

$fases  = [];
$pedido = null;

while ($row = $result->fetch_assoc()) {
    if (!$pedido) $pedido = $row;
    $fases[] = [
        'secuencia'  => $row['secuencia'],
        'std'        => $row['unds'],
        'peso_env'   => $row['pen'],
        'etapa'      => $row['etapanombre'],
        'udm'        => $row['udm'],
        'sigenv'     => $row['abreviatura'],
        'eq_kg_fase' => $row['eq_kg_fase'] ?? 1,
        'nomenvase'  => $row['nombreenvase']
    ];
}

if ($pedido) {
    $fecha_registro = $pedido['fecha_registro'];
    $fecha_entrega  = date('Y/m/d', strtotime($pedido['fecha_entrega']));



    $cantidad       = $pedido['cantidad'] ?? 0;
    $num_pedido     = $pedido['num_pedido'];
    $cliente        = $pedido['razon_social'];
    $envase         = $pedido['wnombre'];
    $prod           = $pedido['productonombre'];
    $pesoenva       = $pedido['pen'];
    $pesoprod       = $pedido['peso_prod'] ?? 0;
    $usigla         = $pedido['sigla'];
    $unds           = $pedido['unds'];
    $equivalente    = $pedido['eq_kg'] ?? 1;
    $eq_fase        = $pedido['eq_kg_fase'] ?? 1;
    $estado         = $pedido['estado']?? '';
    $cerrado=($estado == 2);

    $kilos = ($equivalente > 0) ? $cantidad * ($pesoprod * $equivalente) : 0;
    $obj   = $unds * ($pesoenva * $eq_fase);
?>

<!-- ===== ENCABEZADO DEL PEDIDO ===== -->
<div class="row mb-3">
    <div class="col-auto pedido-box">
        <b># Pedido:</b> <?= htmlspecialchars($num_pedido) ?>
    </div>
</div>

<style>
.pedido-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 5px 14px;
    color: #1d4ed8;
    font-weight: 500;
    font-size: 0.85rem;
    letter-spacing: 0.02em;
}
</style>

<div class="encabezado-pedido">
    <div class="row mb-3 text-center">
        <div class="col-3 border-end shadow">
            <div class="row"><b>Pedido:</b></div>
            <div><?= htmlspecialchars($prod).' '.$envase.' '.$pesoprod.' '.$usigla ?></div>
        </div>
        <div class="col-3 border-end shadow">
            <div class="row"><b>Cliente:</b></div>
            <div><?= htmlspecialchars($cliente) ?></div>
        </div>
        <div class="col-3 border-end shadow">
            <div class="row"><b>Cantidad:</b></div>
            <div><?= $cantidad.' ('.$envase.'S '.$pesoprod.' '.$usigla.')' ?></div>
            <div><?= number_format($kilos, 2).' kg' ?></div>
        </div>
        <div class="col-3 shadow">
            <div class="row"><b>Fecha Entrega:</b></div>
            <div><?= htmlspecialchars($fecha_entrega) ?></div>
        </div>
    </div>
</div>
</div>

<!-----Pasar $cerrado al HTML como variable JS y úsala ----->
<script>
    var pedidoCerrado = <?= $cerrado ? 'true' : 'false' ?>;
</script>

<!-- ===== FORMULARIO ===== -->
<form id="formAvance">
    <input type="hidden" name="id_pedido" value="<?= $id ?>">

    <?php foreach ($fases as $fase): ?>
        <?php
        $peso          = $fase['peso_env'];
        $unds          = $fase['std'];
        $eq            = $fase['eq_kg_fase'];
        $obj_fase_base = ($eq > 0) ? $unds * ($peso * $eq) : 0;
        $turnosFase    = ($obj_fase_base > 0) ? ceil($kilos / $obj_fase_base) : 0;
        ?>
        <hr>
        <div class="fase-bloque mb-4">
            <div class="row mb-2">
                <div class="col-auto">
                    <h6 class="mt-2 mb-1">
                        <span class="badge bg-primary me-2 fs-4">Fase <?= $fase['secuencia'] ?></span>
                        <?= htmlspecialchars($fase['etapa']) ?>
                        <small class="text-muted ms-2">
                            (Std: <?= $fase['std'].' '.$fase['nomenvase'].'S' ?> — <?= $turnosFase ?> turnos)
                        </small>
                    </h6>
                </div>
                <div class="col-auto">
                    <button type="button"
                        class="btn btn-sm btn-success btnAgregarTurno shadow"
                        data-std="<?= $fase['std'] ?>"
                        data-secuencia="<?= $fase['secuencia'] ?>"
                        data-peso-env="<?= $fase['peso_env'] ?>"
                        data-sigenv="<?= htmlspecialchars($fase['sigenv']) ?>"
                        data-udm="<?= htmlspecialchars($fase['udm']) ?>"
                        data-eq="<?= $fase['eq_kg_fase'] ?>"
                        data-obj-total="<?= $kilos ?>">
                        <i class="fa-solid fa-square-plus" style="color: rgb(41, 132, 180);"></i> AÑADIR TURNO
                    </button>
                </div>

                <!---------------BARRA DE PROGRESO---------------------------->
        <div class="col text-end">
            <div class="progress mt-2">
                <div class="progress-bar barra-cumplimiento" style="width: 0%">0%</div>
            </div>
        </div>
<!-------------------------------------------------------------------->

            </div>

            <table class=" tablaAvance shadow-lg">
                <thead >
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
                        <th style="width:50px;"></th>  <!-- columna acciones -->
                    </tr>
                </thead>
                <tbody>
                    <?php
$turnosExistentes = [];

// turnos calculados normales
for ($i = 1; $i <= $turnosFase; $i++) {

    //  si está eliminado, NO lo agregues
    if (isset($turnosEliminados[$fase['secuencia']]) &&
        in_array($i, $turnosEliminados[$fase['secuencia']])) {
        continue;
    }

    $turnosExistentes[] = $i;
}

// turnos guardados en BD
if (isset($avance[$fase['secuencia']])) {
 foreach (array_keys($avance[$fase['secuencia']]) as $t) {

    if (isset($turnosEliminados[$fase['secuencia']]) &&
        in_array($t, $turnosEliminados[$fase['secuencia']])) {
        continue;
    }

    if (!in_array($t, $turnosExistentes)) {
        $turnosExistentes[] = $t;
    }
}
}

// ordenar
sort($turnosExistentes);

// recorrer SOLO los reales
foreach ($turnosExistentes as $turno):

                        // Objetivo calculado por turno
                        if ($turno == $turnosFase) {
                            $obj_fase = $kilos - ($obj_fase_base * ($turnosFase - 1));
                        } else {
                            $obj_fase = $obj_fase_base;
                        }
                        $obj_fase = max($obj_fase, 0);

                        $peso        = $fase['peso_env'];
                        $val_obj     = $avance[$fase['secuencia']][$turno]['obj']     ?? null;
                        $val_kg      = $avance[$fase['secuencia']][$turno]['kg_real'] ?? '';
                        $val_fecha   = $avance[$fase['secuencia']][$turno]['fecha']   ?? '';
                        $val_jornada = $avance[$fase['secuencia']][$turno]['jornada'] ?? '';
                        $val_hc      = $avance[$fase['secuencia']][$turno]['hc']      ?? '';

                     // ✅ CORREGIDO: Solo filas extra usan obj_kg de BD
$obj_mostrar   = ($turno > $turnosFase && $val_obj !== null && $val_obj !== '')
                    ? (float)$val_obj
                    : $obj_fase;
$cant_obj_prod = ($peso > 0) ? $obj_mostrar / $peso : 0;
                  
                  

                  ?>

                    <tr class="<?= ($turno > $turnosFase) ? 'table-warning' : '' ?>"
                        data-peso="<?= $fase['peso_env'] ?>"
                        data-eq="<?= $fase['eq_kg_fase'] ?>">

                        <td class="text-center align-middle turno-num"><?= $turno ?></td>

                        <td>
                            <input type="date" class="form-control"
                                name="fecha[<?= $fase['secuencia'] ?>][<?= $turno ?>]"
                                value="<?= htmlspecialchars($val_fecha) ?>">
                        </td>

                        <td>
                            <select class="form-select" name="jornada[<?= $fase['secuencia'] ?>][<?= $turno ?>]">
                                <option value=""></option>
                                <option value="DIA"   <?= $val_jornada === 'DIA'   ? 'selected' : '' ?>>DIA</option>
                                <option value="NOCHE" <?= $val_jornada === 'NOCHE' ? 'selected' : '' ?>>NOCHE</option>
                            </select>
                        </td>

            <!---------# COLABORADORES----------------->
                        <td style="width:80px;">
                            <input type="number" class="form-control" min="0" step="1"
                                name="hc[<?= $fase['secuencia'] ?>][<?= $turno ?>]"
                                value="<?= htmlspecialchars($val_hc) ?>"
                                onkeydown="return /[\d]|Backspace|Delete|Arrow/.test(event.key)">
                        </td>


            <!---------# UNDS ESTANDAR----------------->
                        <td class="text-center align-middle td-unds">
                            <?= $fase['std'].' '.$fase['sigenv'].' '.$peso.' '.$fase['udm'] ?>
                        </td>

            <!---------OBJETIVO----------------->
                        <!-- ✅ CORREGIDO: data-obj y texto usan $obj_mostrar (BD o calculado) -->
                    <td>
                            <?php if ($turno > $turnosFase || $val_obj !== null): ?>
                                <input type="number" step="0.01" min="0"
                                    
                                    class="form-control form-control-sm input-obj"
                                    name="obj[<?= $fase['secuencia'] ?>][<?= $turno ?>]"
                                    value="<?= number_format($obj_mostrar, 2, '.', '') ?>">
                            <?php else: ?>
                        <div class="text-center align-middle td-obj" data-obj="<?= $obj_mostrar ?>">
                            <?= number_format($obj_mostrar, 2).' KG' ?>
                        </div>
                            <?php endif; ?>
                    </td>

            
            <!---------UNIDADES PRODUCIDAS----------------->
                        <td>
                            <input type="number" step="0.01" min="0" 
                                onkeydown="return /[\d]|Backspace|Delete|Arrow/.test(event.key)"
                                class="form-control form-control-sm input-real"
                                value="<?= htmlspecialchars($val_kg) ?>"
                                name="real[<?= $fase['secuencia'] ?>][<?= $turno ?>]"
                                placeholder="0.00">
                        </td>

                        <input type="hidden" name="peso[<?= $fase['secuencia'] ?>][<?= $turno ?>]" value="<?= $fase['peso_env'] ?>">
                        <input type="hidden" name="eq[<?= $fase['secuencia'] ?>][<?= $turno ?>]"   value="<?= $fase['eq_kg_fase'] ?>">

            <!---------KG REALES, DIFERENCIA, CUMPLIMIENTO----------------->
                        <td class="text-center align-middle td-kg"></td>
                        <td class="text-center align-middle td-dif"></td>
                        <td class="text-center align-middle td-cumpl"></td>
                        
            <!---------BOTON ELIMINAR----------------->
                        <td class="text-center align-middle">
                        <?php if ($turno == $turnosFase || $turno > $turnosFase): ?>
                            <button type="button" 
                                class="btn btn-sm btn-danger btnEliminarFila"
                                data-id="<?= $id ?>"
                                data-secuencia="<?= $fase['secuencia'] ?>"
                                data-turno="<?= $turno ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                        </td>
                    </tr>

                    <?php endforeach; ?>
                </tbody>

                <tfoot >
    <tr>
        <td colspan="5" class="text-center align-middle"><b>TOTAL PROCESO</b></td>
        <td></td>
        <td class="text-center align-middle total-obj"></td>
        <td class="text-center align-middle total-unds"></td>
        <td class="text-center align-middle total-real"></td>
        <td class="text-center align-middle total-dif"></td>
        <td class="text-center align-middle total-cumpl"></td>
        <td></td>
    </tr>
</tfoot>
            </table>





        </div><!-- /fase-bloque -->

    <?php endforeach; ?>
</form>

<?php
} else 
    {
        echo '<div class="alert alert-warning">Producto no tiene fases definidas.</div>';
    }
        $stmt->close();
        $ff->close();
        $conn->close();
?>

<!-- RECALCULO AL EDITAR EL OBJETIVO -->
<script>
$(document).on('input', '.input-obj', function () {
    const $tabla = $(this).closest('.tablaAvance');
    recalcularTotales($tabla);
});
</script>

<script>
/* ======================
   AGREGAR FILA
======================*/
$(document).off('click', '.btnAgregarTurno').on('click', '.btnAgregarTurno', function () {

    const $bloque  = $(this).closest('.fase-bloque');
    const $tbody   = $bloque.find('tbody');
    const $tabla   = $bloque.find('.tablaAvance');

    const secuencia = $(this).data('secuencia');
    const std       = $(this).data('std');
    const pesoEnv   = $(this).data('peso-env');
    const sigenv    = $(this).data('sigenv');
    const udm       = $(this).data('udm');
    const eq        = $(this).data('eq') || 1;
    const objTotal  = parseFloat($(this).data('obj-total')) || 0;

    // 🔥 obtener turno real máximo (NO length)
    let maxTurno = 0;
    $tbody.find('.turno-num').each(function () {
        const t = parseInt($(this).text()) || 0;
        if (t > maxTurno) maxTurno = t;
    });

    const nextTurno = maxTurno + 1;

    // calcular kg acumulado
    let kgAcumulado = 0;
    $tbody.find('tr').each(function () {
        const unds = parseFloat($(this).find('.input-real').val()) || 0;
        const p    = parseFloat($(this).data('peso')) || 0;
        const e    = parseFloat($(this).data('eq'))   || 1;
        kgAcumulado += unds * p * e;
    });

    const objFila = Math.max(objTotal - kgAcumulado, 0);

    const fila = `
<tr data-peso="${pesoEnv}" data-eq="${eq}">
    <td class="text-center turno-num">${nextTurno}</td>
    <td><input type="date" class="form-control" name="fecha[${secuencia}][${nextTurno}]"></td>
    <td>
        <select class="form-select" name="jornada[${secuencia}][${nextTurno}]">
            <option value=""></option>
            <option value="DIA">DIA</option>
            <option value="NOCHE">NOCHE</option>
        </select>
    </td>
    <td><input type="number" class="form-control" name="hc[${secuencia}][${nextTurno}]"></td>

    <td class="text-center">${std} ${sigenv} ${pesoEnv} ${udm}</td>

    <td>
        <input type="number" class="form-control form-control-sm input-obj"
            name="obj[${secuencia}][${nextTurno}]"
            value="${objFila}">
    </td>

    <td>
        <input type="number" class="form-control form-control-sm input-real"
            name="real[${secuencia}][${nextTurno}]">
    </td>

    <input type="hidden" name="peso[${secuencia}][${nextTurno}]" value="${pesoEnv}">
    <input type="hidden" name="eq[${secuencia}][${nextTurno}]" value="${eq}">

    <td class="td-kg text-center"></td>
    <td class="td-dif text-center"></td>
    <td class="td-cumpl text-center"></td>

    <td>
        <button type="button" class="btn btn-danger btn-sm btnEliminarFila">
            🗑
        </button>
    </td>
</tr>`;

    $tbody.append(fila);
    recalcularTotales($tabla);
});




/* ======================
   ELIMINAR FILA + renumerar
======================*/
$(document).off('click', '.btnEliminarFila').on('click', '.btnEliminarFila', function () {

    const $btn = $(this);
    const $fila = $btn.closest('tr');
    const $tbody = $fila.closest('tbody');
    const id_pedido = $btn.data('id');
    const secuencia = $btn.data('secuencia');
    const turno     = $btn.data('turno');

    // 🔥 SWEET ALERT CONFIRMACIÓN
    Swal.fire({
        title: '¿Eliminar turno?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {

        if (!result.isConfirmed) return;

        // ✅ Si es una fila nueva (sin guardar en BD)
     if (!turno) {
    $fila.remove();
    recalcularTotales($tbody.closest('.tablaAvance'));
    return;


            Swal.fire({
                icon: 'success',
                title: 'Eliminado',
                text: 'Fila eliminada correctamente',
                timer: 1200,
                showConfirmButton: false
            });

            return;
        }

        // ✅ Eliminar en BD
        $.ajax({
            url: 'eliminar_turno.php',
            method: 'POST',
            data: {
                id_pedido: id_pedido,
                secuencia: secuencia,
                turno: turno
            },
            success: function (res) {
                try {
                    const r = JSON.parse(res);

                    if (r.ok) {
                        $fila.remove();
                        renumerar($tbody);
                        recalcularTotales($tbody.closest('.tablaAvance'));

                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'El turno fue eliminado correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    } else {
                        Swal.fire('Error', 'No se pudo eliminar en BD', 'error');
                    }

                } catch (e) {
                    console.error(res);
                    Swal.fire('Error', 'Respuesta inválida del servidor', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Error en la petición AJAX', 'error');
            }
        });

    });
});

/*  REENUMERAR TURNOS */

function renumerar($tbody) {
    $tbody.find('tr').each(function (i) {
        $(this).find('.turno-num').text(i + 1);
    });
}


/* ======================
   RECALCULAR TOTALES
======================*/
function recalcularTotales($tabla) {
    let totalKg       = 0;
    let totalUndsReal = 0;
    let totalObj      = parseFloat($tabla.closest('.fase-bloque').find('.btnAgregarTurno').data('obj-total')) || 0;





    
    $tabla.find('tbody tr').each(function () {
      let obj = 0;

// prioridad: si existe input (fila agregada o editable), usar ese
const $inputObj = $(this).find('.input-obj');
const $tdObj    = $(this).find('.td-obj');

if ($inputObj.length) {
    obj = parseFloat($inputObj.val()) || 0;
} else if ($tdObj.length) {
    obj = parseFloat($tdObj.data('obj')) || 0;
}
        const unds  = parseFloat($(this).find('.input-real').val()) || 0;
        const peso  = parseFloat($(this).data('peso')) || 0;
        const eq    = parseFloat($(this).data('eq'))   || 1;
        const kg    = unds * peso * eq;
        const dif   = kg - obj;
        const cumpl = obj > 0 ? ((kg / obj) * 100).toFixed(1) + '%' : '-';

        $(this).find('.td-kg').text(kg.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' KG');
        $(this).find('.td-dif').text(dif.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' KG');
        $(this).find('.td-cumpl').text(cumpl);

        totalKg       += kg;
        totalUndsReal += unds;
    });

    const totalDif   = totalKg - totalObj;
    const totalCumpl = totalObj > 0 ? ((totalKg / totalObj) * 100).toFixed(1) + '%' : '-';
    const $tfoot     = $tabla.find('tfoot');

    $tfoot.find('.total-unds').html('<b>'  + totalUndsReal.toFixed(2) + '</b>');
    $tfoot.find('.total-obj').html('<b>'   + totalObj.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' KG</b>');
    $tfoot.find('.total-real').html('<b>'  + totalKg.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' KG</b>');
    $tfoot.find('.total-dif').html('<b>'   + totalDif.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' KG</b>');
    $tfoot.find('.total-cumpl').html('<b>' + totalCumpl + '</b>');



// 🌈 ACTUALIZAR BARRA DE PROGRESO CON DEGRADADO ROJO → VERDE
const porcentajeNum = totalObj > 0 ? (totalKg / totalObj) * 100 : 0;
const porcentaje = Math.min(porcentajeNum, 100).toFixed(1);

const $barra = $tabla.closest('.fase-bloque').find('.barra-cumplimiento');

// Calcular color interpolado rojo → amarillo → verde
const t = Math.min(porcentajeNum / 100, 1); // 0 a 1
let r, g, b;
if (t < 0.5) {
    // Rojo → Amarillo (0% a 50%)
    r = 220;
    g = Math.round(t * 2 * 180);
    b = 0;
} else {
    // Amarillo → Verde (50% a 100%)
    r = Math.round((1 - (t - 0.5) * 2) * 180);
    g = 160 + Math.round((t - 0.5) * 2 * 40);
    b = 0;
}

const color = `rgb(${r}, ${g}, ${b})`;

$barra
    .removeClass('bg-success bg-warning bg-danger')
    .css({
        'width': porcentaje + '%',
        'background-color': color,
        'background-image': 'none'
    })
    .text(porcentaje + '%');
}

$(document).on('input', '.input-real', function () {
    recalcularTotales($(this).closest('.tablaAvance'));
});

$('.tablaAvance').each(function () {
    recalcularTotales($(this));
});



// ── MODO SOLO LECTURA SI PEDIDO CERRADO ──────────────────────
// Primero limpiar siempre cualquier estado anterior
$('#formAvance input:not([type="hidden"]), #formAvance select').prop('disabled', false);
$('.btnAgregarTurno').show();
$('.btnEliminarFila').show();
$('#formAvance .alert-pedido-cerrado').remove();

// Luego aplicar restricciones solo si corresponde
if (pedidoCerrado) {
    $('#formAvance input:not([type="hidden"]), #formAvance select').prop('disabled', true);
    $('.btnAgregarTurno').hide();
    $('.btnEliminarFila').hide();
    $('#formAvance').prepend(`
        <div class="alert alert-warning alert-pedido-cerrado d-flex align-items-center mb-3" role="alert">
            <i class="bi bi-lock-fill me-2 fs-5"></i>
            <span>Este pedido está <strong>cerrado</strong>. Los datos son de solo lectura.</span>
        </div>
    `);
}



</script>

<!-- ENCABEZADO COMPACTO AL SCROLL -->
<script>
document.addEventListener("shown.bs.modal", function () {
    const modalBody  = document.querySelector("#modaldetallesv3 .modal-body");
    const encabezado = document.querySelector(".encabezado-pedido");
    if (!modalBody || !encabezado) return;
    modalBody.addEventListener("scroll", function () {
        encabezado.classList.toggle("compacto", modalBody.scrollTop > 60);
    });
});
</script>
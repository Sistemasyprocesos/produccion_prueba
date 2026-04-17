<!DOCTYPE html>
<html lang="es">

<head>
  <title>Registro de Pedidos</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
/* Animación de entrada */
.fila-animada {
  opacity: 0;
  transform: translateY(10px);
  animation: aparecer 0.4s ease forwards;
}

@keyframes aparecer {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Animación al desaparecer */
.fade-out {
  opacity: 0;
  transform: translateY(-10px);
  transition: all 0.2s ease;
}


.listado-scroll {
  max-height: 350px;   /* puedes ajustar */
  overflow-y: auto;
}

/* Scroll más bonito (opcional) */
.listado-scroll::-webkit-scrollbar {
  width: 6px;
}

.listado-scroll::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 10px;
}

.listado-scroll::-webkit-scrollbar-thumb:hover {
  background: #999;
}
</style>

</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';

?>
<?php
$alertas = $conn->query("
SELECT 
  p.id_pedido,
  p.num_pedido,
  c.razon_social AS cliente,
  p.fecha_entrega,
  ROUND((COALESCE(SUM(a.unidades_reales),0) / p.cantidad) * 100 ,2) as cumplimiento

FROM prod_pedidos p
INNER JOIN prod_clientes c ON c.id = p.id_cliente
inner join prod_productos as pr on pr.id=p.producto
LEFT JOIN prod_avance_pedido a ON a.id_pedido = p.id_pedido
  AND a.secuencia = (
                    SELECT MAX(secuencia)
                    FROM prod_fases_prod
                    WHERE producto = pr.id
                )
WHERE p.estado = 1
AND DATEDIFF(p.fecha_entrega, CURDATE()) <= 3

GROUP BY p.id_pedido
ORDER BY p.fecha_entrega ASC
");

$total_alertas = $alertas->num_rows;
?>
<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1 class="mt-1"><i class="fa-solid fa-arrow-down-wide-short" style="color: rgb(0, 0, 0);"></i> Pedidos</h1>
  <div class="row mt-1 gx-0">

  
  <!-- IZQUIERDA -->
<div class="col-md-3 col-lg-2 pe-1 border-end">

    <!-- KPI -->
    <div class="card shadow border-0 rounded-4 bg-danger text-white text-center mb-3">
    <div class="card-body p-2">
        <h6 class="mb-1">
          <i class="fa-solid fa-triangle-exclamation"></i>
          Pedidos con entrega próxima a vencer
        </h6>
        <h2><?= $total_alertas ?></h2>
      </div>
    </div>

    <!-- LISTADO -->
    <div class="card shadow-sm rounded-4">
      <div class="card-header bg-light">
        <strong>
          <i class="fa-solid fa-calendar-xmark"></i>
          Órdenes próximas a vencer
        </strong>
      </div>

<div class="card-body p-2 listado-scroll">        
        <?php if($total_alertas == 0){ ?>
          <div class="text-success">✔ No hay pedidos con fecha de entrega próxima a vencer</div>
        <?php } ?>

        <?php while($a = $alertas->fetch_assoc()){ ?>

          <div class="mb-2 pb-1 border-bottom">

            <div class="d-flex justify-content-between">
              <small><?= $a['num_pedido'] ?> <br> <?= $a['cliente'] ?></small>
              <span class="text-danger">
                <?= date('Y/m/d', strtotime($a['fecha_entrega'])) ?>
              </span>
            </div>

            <div class="progress mt-1" style="height:12px;"
              data-porcentaje="<?= $a['cumplimiento'] ?>">
              <div class="progress-bar bg-danger"
                style="width: <?= min(100,$a['cumplimiento']) ?>%">
              </div>
            </div>

            <small class="text-muted">
              Avance: <?= $a['cumplimiento'] ?>%
            </small>

          </div>

        <?php } ?>

      </div>
    </div>

  </div>

  <!-- DIVISOR -->


  <!-- DERECHA -->
 <!-- DERECHA -->
<div class="col-md-10 ps-3">

    <!-- CONTROLES -->
    <div class="row g-2 mb-2">

      <!-- BUSCADOR -->
      <div class="col-md-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </label>
            <input type="text" id="Buscador" class="form-control"
              placeholder="Fecha, pedido, producto, cliente...">
          </div>
        </div>
      </div>

      <!-- NUEVO PEDIDO -->
      <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 h-100 d-flex justify-content-center">
          <div class="card-body text-center">
            <button class="btn btn-success  rounded-3"
              data-bs-toggle="modal" data-bs-target="#modalnuevo">
              <i class="fa-solid fa-square-plus"></i>
              Nuevo Pedido
            </button>
          </div>
        </div>
      </div>

      <!-- FILTRO -->
      <div class="col-md-3">
        <div class="card shadow-sm border-0 rounded-4 h-100">
          <div class="card-body">
            <label class="form-label fw-semibold">
              <i class="fa-solid fa-filter"></i> Estado
            </label>
            <select class="form-select" id="selectestado">
              <option selected>TODAS</option>
              <option value="1">ACTIVAS</option>
              <option value="2">COMPLETAS</option>
            </select>
          </div>
        </div>
      </div>

    </div>
        <table class="table mt-2 table-sm table-hover" id="tblcolab">
          <thead class="table-dark">
            <tr>
              <th data-col="1">PEDIDO</th>
              <th data-col="2">CLIENTE</th>
              <th data-col="3">FECHA DE ENTREGA</th>
              <th data-col="4">CANTIDAD</th>
              <th data-col="5">PRODUCTO</th>
              <th data-col="6">ESTADO</th>
              <th>ACCIONES</th>
              <th>% CUMPLIMIENTO</th>
            </tr>
        </thead>
        <tbody>
            <?php 
              $f=$conn->query("SELECT 
                p.id_pedido,
                p.id_cliente,
                p.fecha_registro,
                p.fecha_entrega,
                p.producto,
                p.cantidad,
                p.und_medida,
                pr.peso_prod as peso,
                u_prod.sigla AS sigla_producto,
                u_ped.sigla  AS sigla_pedido,
                p.num_pedido,
                u_prod.equivalente_kg as equi,
                e.nom as estado,
                p.est_ped as estadoped,
                c.razon_social as cliente,
                pr.nombre as producto,
                ev.abreviatura as envase,

                COALESCE(SUM(a.unidades_reales),0) as producido,

                ROUND((COALESCE(SUM(a.unidades_reales),0) / p.cantidad) * 100 ,2)
                 as cumplimiento

                from prod_pedidos as p 
                inner join prod_clientes as c on c.id=p.id_cliente
                inner join prod_productos as pr on pr.id=p.producto
                inner join prod_estados as e on e.id=p.estado
                inner join prod_envase as ev on ev.id=pr.envase
                INNER JOIN prod_udm u_prod 
                ON u_prod.id = pr.udm   -- producto

                INNER JOIN prod_udm u_ped 
                ON u_ped.id = p.und_medida  -- pedido

                left join prod_avance_pedido a 
                on a.id_pedido = p.id_pedido
                AND a.secuencia = (
                    SELECT MAX(secuencia)
                    FROM prod_fases_prod
                    WHERE producto = pr.id
                )

                group by p.id_pedido
                order by p.id_pedido desc");
          
            while($g=$f->fetch_assoc()){

      $pedido = number_format(($g['cantidad'] * $g['peso']) * $g['equi'], 2);
              ?> 
             <tr data-estado="<?= $g['estado'] ?>" class="<?= ((strtotime($g['fecha_entrega']) - time()) < (3*86400) && $g['estado'] == 'ACTIVO') ? 'table-danger' : '' ?>">
                <td>
                  <?=$g['num_pedido'] ?>
                    <button 
                      class="btn btn-primary btn-sm btnVerPedido"
                      data-id="<?=$g['id_pedido']?>"
                      data-bs-toggle="modal"
                      data-bs-target="#modalver">
                      <i class="fa-solid fa-eye" style="color: rgb(255, 255, 255);"></i>
                    </button>

                    <!-----------DATOS EN LA TABLA----------------------->
                </td>
                <td><?=$g['cliente'] ?></td>
                <td><?=date('Y/m/d', strtotime($g['fecha_entrega'])) ?></td>
                <td><?=$g['cantidad'].' '.$g['sigla_pedido']. ' ('.$pedido.' KG)' ?></td>
                <td><?=$g['producto']?></td>

                <?php  
                if($g['estado'] == 'ACTIVO'){
                ?>
                <td class="text-success"><i class="fa-solid fa-circle-check" style="color: rgb(118, 216, 52);"></i></td>
                  <?php  }
                    elseif($g['estado'] == 'INACTIVO'){
                  ?>
                <td class="text-danger"><i class="fa-solid fa-circle-xmark" style="color: rgb(194, 9, 9);"></i></td>
                  <?php  }
                  ?>
                <td>
     
                  <!----------------------------------------------->
                  <button class="btn btn-success btn-sm btnVerDetallev3" data-iddet="<?=$g['id_pedido']?>" data-bs-toggle="modal"data-bs-target="#modaldetallesv3"><i class="fa-solid fa-eye" style="color: rgb(255, 255, 255);"></i> Detalle</button>

                <!---------BOTON EDITAR---------------------------->
                  <button class="btn btn-sm btn-warning btnEditar"
                    data-identi="<?=$g['id_pedido']?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modaleditar">
                    <i class="fa-solid fa-pen-to-square" style="color: rgb(255, 255, 255);"></i>
                  </button> 

            <!-----------BOTON ELIMINAR------------------------------->
                  <button class="btn btn-sm btn-danger btn-elim" data-identificacion="<?=$g['id_pedido']?>">
                    <i class="fa-solid fa-trash-can" style="color: rgb(255, 255, 255);"></i>
                  </button>

                  <!-------------------------------------------------------------------->

            <button class="btn btn-sm btn-danger btn-cerrar" data-ident="<?=$g['id_pedido']?>"><i class="fa-solid fa-rectangle-xmark" style="color: rgb(255, 255, 255);"></i></button>


                </td>
                <td style=" width: 150px;">
                  <div class="d-flex align-items-center gap-2">



              <!-----------BARRA DE PROGRESO----------------------------->
                      <div class="progress flex-grow-1 " role="progressbar"
                          aria-label="Cumplimiento de pedido"
                          aria-valuenow="<?= $g['cumplimiento'] ?>"
                          data-porcentaje="<?= $g['cumplimiento'] ?>"
                          aria-valuemin="0"
                          aria-valuemax="100">
                      <div class="progress-bar" style="width: <?= $g['cumplimiento'] ?>% ;  min-width: fit-content;"><?= $g['cumplimiento'] ?>%</div>

                    
                  </div>
                  </div>
                </td>
            </tr>
          <?php
            } 
            ?>

      </tbody>
        </table>

    <!----PAGINADOR------->
          <nav>
            
              <ul class="pagination justify-content-center" id="pagination"></ul>
          </nav>

    </div>
      </div>
</main>

<!--------------GENERA NUMERO DEL PEDIDO AUTOMATICAMENTE-------------------------------------->
<?php
$num_pedido = file_get_contents("generar_num_pedido.php");
 require 'generar_num_pedido.php';
?>

<!--------------MODAL AGREGAR NUEVO--------------------->
<div class="modal fade" tabindex="-1" id="modalnuevo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Registro de nuevo pedido</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
              <div class="col-12 ">
                <form action="../procedimiento/newped.php" method="post">
          <div class="row mb-3">
                <div class="col-12">
                  <label class="form-label"># Pedido</label>
                  <input class="form-control" readonly type="text" value="<?= $num_pedido ?>" name="numped" id="numped">
                </div>
          </div>

          <div class="row mb-3">
              <div class="col-6">

                <!----CLIENTE-------->
                <label class="form-label">Cliente</label>
                  <select class="form-select" name="clte" id="clte" required>
                      <?php $c=$conn->query("select id,razon_social,estado from prod_clientes where estado=1"); ?>
                    <option></option>
                      <?php while($f=$c->fetch_assoc()){     ?>
                    <option value="<?=$f['id'] ?>"><?=$f['razon_social']  ?></option>
                      <?php }?>
                  </select>
                </div>

                <!---------PRODUCTO----------->
              <div class="col-6">
                  <label class="form-label">Producto</label>
                    <select name="prod" id="prod" class="form-select">
                      <?php $c=$conn->query("
                      select
                      p.id,
                      p.nombre,
                      p.estado,
                      p.fase,
                      p.peso_prod,
                      u.sigla,
                       u.equivalente_kg,  
                      e.abreviatura
                      from prod_productos as p
                        inner join prod_envase as e on e.id=p.envase
                        inner join prod_udm as u on u.id=p.udm
                        
                        where p.estado=1 and p.fase= 2"); 
                        ?>
                        <option></option>
                          <?php while($f=$c->fetch_assoc()){     ?>
                        <option value="<?=$f['id'] ?>" 
                        data-peso="<?=$f['peso_prod'] ?>"
                          data-eq="<?=$f['equivalente_kg'] ?>"
                        >
    <?=$f['nombre']?>
</option>
                      <?php }?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Fecha de registro</label>
                      <input type="date" name="fechreg" required id="fechreg" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label">Fecha de entrega</label>  
                      <input type="date" class="form-control" required name="fentreg" id="fentreg">
                </div>
            </div>
<hr>
            <div class="row mb-3">
                <div class="col-5">
                    <label class="form-label">Cantidad (unidades)</label>
                      <input type="text" class="form-control" name="cant" id="cant" required>
                </div>

                <div class="col-2">
                    
                      
                      <input type="text" class="form-control" name="unds" id="unds" hidden value="4">
                </div>
                <div class="col-3">
                  <label class="form-label">Equivalente (kg)</label>
                    <input type="text" readonly class="form-control" id="cant_equiv">
                </div>




            </div>
        </div>
      </div>
      <div class="modal-footer  justify-content-center">
        <button type="submit" class="btn btn-primary"><i class="bi bi-floppy2-fill"></i> Guardar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill"></i> Cancelar</button>
      </form>
      </div>
    </div>
  </div>
</div>



<!--------------------MODAL VER----------------------->
<div class="modal fade" id="modalver" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Detalle del pedido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="contenidoPedido">
        Cargando...
      </div>
    </div>
  </div>
</div>



<!--------------------MODAL DETALLEv3----------------------->
<div class="modal fade" id="modaldetallesv3" tabindex="-1">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Produccion Pedido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="avancepedidov3">
        Cargando...
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="fa-solid fa-circle-xmark" style="color: rgb(255, 255, 255);"></i> Cerrar</button>
        <button class="btn btn-success btn-sm"  id="guardarAvancev3"><i class="fa-solid fa-circle-check" style="color: rgb(255, 255, 255);"></i>  Guardar</button>
      </div>
    </div>
  </div>
</div>



<!--------------MODAL EDITAR PEDIDO--------------------->
<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0d6efd; color: white;">
        <h5 class="modal-title"><i class="fa-solid fa-pen-to-square" style="color: rgb(255, 255, 255);"></i> Editar pedido</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="contenidoEditar">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Cargando datos...</p>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-primary" id="btnGuardarEdicion">
         <i class="fa-solid fa-floppy-disk" style="color: rgb(255, 255, 255);"></i> Guardar cambios
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
          <i class="fa-solid fa-circle-xmark" style="color: rgb(255, 255, 255);"></i> Cancelar
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!------AJAX DETALLE PEDIDO--------------------------------------------------->

<script>
$(document).on("click", ".btnVerPedido", function(){

  const id = $(this).data("id");

  $("#contenidoPedido").html("Cargando...");

  $.ajax({
      url: "ajax_detalle_pedido.php",
      type: "POST",
      data: { id: id },

    success: function(res){
      $("#contenidoPedido").html(res);
    },

    error: function(){
      $("#contenidoPedido").html("Error al cargar datos");
    }
  });

});
</script>



<!----------AVANCE V3---------------->
<script>
$(document).on("click", ".btnVerDetallev3", function () {
    const id = $(this).data("iddet");
    $("#avancepedidov3").html("Cargando...");

    $.ajax({
        url: "ajax_avance_pedv3.php",
        type: "POST",
        data: { id: id },
        success: function (res) {
            $("#avancepedidov3").html(res);

            // ← ESTO ES LO QUE FALTABA: recalcular cada tabla al cargar
            $("#avancepedidov3 .tablaAvance").each(function () {
                recalcularTotales($(this));
            });
        },
        error: function () {
            $("#avancepedidov3").html("Error al cargar datos");
        }
    });
});
</script>


<!------AJAX EDITAR PEDIDO--------------------------------------------------->
<script>
$(document).on("click", ".btnEditar", function(){
  const id = $(this).data("identi");
  $("#contenidoEditar").html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Cargando datos...</p></div>');

  $.ajax({
    url: "ajax_editar_pedido.php",
    type: "POST",
    data: { id: id },
    success: function(res){
      $("#contenidoEditar").html(res);
    },
    error: function(){
      $("#contenidoEditar").html('<div class="alert alert-danger">Error al cargar datos</div>');
    }
  });
});

$(document).on("click", "#btnGuardarEdicion", function(){
  const datos = $("#formEditar").serialize();

  $.ajax({
    url: "../procedimiento/updped.php",
    type: "POST",
    data: datos,
    success: function(res){
      Swal.fire({
        icon: 'success',
        title: 'Actualizado',
        text: 'Pedido actualizado correctamente'
      }).then(() => {
        $('#modaleditar').modal('hide');
        location.reload();
      });
    },
    error: function(){
      Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar' });
    }
  });
});
</script>

<!----------------------->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  document.querySelectorAll('.btn-elim').forEach(btn => {
    btn.addEventListener('click', function () {

      const id = this.getAttribute('data-identificacion');

      Swal.fire({
        title: "Eliminar pedido",
        text: "Va a eliminar este pedido. ¿Desea continuar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir al PHP que elimina
            window.location.href = "../procedimiento/delped.php?id=" + id;
      }
    });

  });
});

</script>



<script>
  document.querySelectorAll('.btn-cerrar').forEach(btn => {
    btn.addEventListener('click', function () {

      const id = this.getAttribute('data-ident');

      Swal.fire({
        title: "Cerrar pedido",
        text: "Va a cerrar este pedido, esto ya no se podrá revertir. ¿Desea continuar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Si, cerrar pedido",
        cancelButtonText: "Cancelar"
      }).then((result) => {
        if (result.isConfirmed) {
            // Redirigir al PHP que elimina
            window.location.href = "../procedimiento/closeped.php?id=" + id;
      }
    });

  });
});

</script>

<!------CALCULO AUTOMATICO DE UDM----------------------------------->

<script>
function calcularEquivalente() {
    let cantidad  = parseFloat($("#cant").val()) || 0;
    let peso      = parseFloat($("#prod option:selected").data("peso")) || 0;
    let eq        = parseFloat($("#prod option:selected").data("eq")) || 0;

    // cantidad (UND) × peso por unidad × factor a KG
    let resultado = cantidad * peso * eq;

    $("#cant_equiv").val(
        resultado > 0
            ? resultado.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' KG'
            : ''
    );
}

$(document).on("input",  "#cant", calcularEquivalente);
$(document).on("change", "#prod", calcularEquivalente);
</script>


<!------------GUARDAR AVANCE V3--------------------------------------->
<script>
$(document).on("click", "#guardarAvancev3", function () {
    let datos = $("#formAvance").serialize();

    $.ajax({
        url: "guardar_avancev3.php",
        type: "POST",
        data: datos,
        dataType: "json",
        success: function (resp) {
            if (resp.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: resp.guardados + ' turno(s) guardados correctamente'
                }).then(() => {
                    $('#modaldetallesv3').modal('hide');
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Guardado parcial',
                    text: resp.guardados + ' guardados, ' + resp.errores + ' con error'
                });
            }
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar' });
        }
    });
});


</script>

<!--------------------------->
<script>
document.querySelectorAll(".progress-bar").forEach(function(barra){

  let porcentaje = parseInt(barra.closest('.progress').dataset.porcentaje);

  // Limitar entre 0 y 100
  porcentaje = Math.max(0, Math.min(100, porcentaje));

  // Calcular color (rojo → verde)
  let hue = (porcentaje * 120) / 100;

  // Aplicar gradiente dinámico
  barra.style.background = `linear-gradient(90deg, 
    hsl(${hue - 20}, 30%, 40%), 
    hsl(${hue}, 80%, 50%)
  )`;

  // Texto
  if(porcentaje >= 100){
    barra.textContent = "PEDIDO COMPLETO";
  } else {
    barra.textContent = porcentaje + "%";
  }

});
</script>

<!--------------------------------->
<script>
document.addEventListener("DOMContentLoaded", function () {

  const rowsPerPage = 10;
  const table = document.getElementById("tblcolab");
  const tbody = table.querySelector("tbody");
  const allRows = Array.from(tbody.querySelectorAll("tr"));
  const pagination = document.getElementById("pagination");
  const buscador = document.getElementById("Buscador");
  const selectEstado = document.getElementById("selectestado");

  let currentPage = 1;
  let filteredRows = [...allRows];

  function aplicarFiltros(){

    const texto = buscador.value.toLowerCase();
    const estado = selectEstado.value;

    filteredRows = allRows.filter(row => {

      let coincideTexto = row.textContent.toLowerCase().includes(texto);
      let coincideEstado = true;
      let estadoFila = row.dataset.estado;

if(estado === "1"){
  coincideEstado = (estadoFila === "ACTIVO");
}
else if(estado === "2"){
  coincideEstado = (estadoFila === "INACTIVO");
}

      return coincideTexto && coincideEstado;

    });

    currentPage = 1;
    update();
  }



// ----------------CARGA LAS FILAS CON ANIMACION-----------------

 function displayRows(){

  // Animar salida
  const filasActuales = tbody.querySelectorAll("tr");
  filasActuales.forEach(f => f.classList.add("fade-out"));

  setTimeout(() => {

    tbody.innerHTML = "";

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;

    filteredRows.slice(start, end).forEach((row, index) => {
      row.classList.remove("fade-out");
      row.classList.add("fila-animada");

      // delay progresivo (efecto cascada)
      row.style.animationDelay = (index * 0.02) + "s";

      tbody.appendChild(row);
    });

  }, 5); // tiempo de salida
}

  function createPagination(){

    pagination.innerHTML = "";

    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

    if(totalPages <= 1) return;

    pagination.innerHTML += `
    <li class="page-item ${currentPage === 1 ? 'disabled':''}">
      <a class="page-link" href="#" data-page="prev">Anterior</a>
    </li>`;

    for(let i=1;i<=totalPages;i++){
      pagination.innerHTML += `
      <li class="page-item ${i===currentPage?'active':''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
    }

    pagination.innerHTML += `
    <li class="page-item ${currentPage===totalPages?'disabled':''}">
      <a class="page-link" href="#" data-page="next">Siguiente</a>
    </li>`;
  }

  pagination.addEventListener("click", function(e){

    e.preventDefault();

    const page = e.target.dataset.page;
    const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

    if(page === "prev" && currentPage > 1) currentPage--;
    else if(page === "next" && currentPage < totalPages) currentPage++;
    else if(!isNaN(page)) currentPage = parseInt(page);

    update();

  });

  buscador.addEventListener("keyup", aplicarFiltros);
  selectEstado.addEventListener("change", aplicarFiltros);

  function update(){
    displayRows();
    createPagination();
  }

  update();

});
</script>

<!---------------------------->
</body>
</html>
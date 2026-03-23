<!DOCTYPE html>
<html lang="es">

<head>
  <title>Registro de Pedidos</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1 class="mt-1"><i class="bi bi-journal-text"></i> Pedidos</h1>
    <div class="row justify-content-center">
      <!----------------------------->
      <!------------------------>
      <div class="container mt-1">
          <div class="row mb-3">
            <div class="col-4">
              <input  type="text"  id="Buscador"  class="form-control mb-3"  placeholder="Buscar pedido (fecha,pedido,producto,cliente)...">
            </div>
            <div class="col-2">
              <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="bi bi-plus-square"></i> Nuevo Pedido</button>
            </div>

           
              <div class="col-2">
              <label class="form-label">Estado del pedido:</label>
              </div>
              <div class=" col-2">
              <select class="form-select" id="selectestado">
                <option selected>TODAS</option>
                <option value="1">ACTIVAS</option>
                <option value="2">COMPLETAS</option>
              </select>
            </div>
          </div>

        <table class="table mt-2 table-sm" id="tblcolab">
          <thead class="table-dark">
            <tr>
              <th>PEDIDO</th>
              <th>CLIENTE</th>
              <th>FECHA DE ENTREGA</th>
              <th>CANTIDAD</th>
              <th>PRODUCTO</th>
              <th>ESTADO</th>
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
                e.nom as estado,
                c.razon_social as cliente,
                pr.nombre as producto,
                ev.abreviatura as envase,

                COALESCE(SUM(a.kg_real),0) as producido,

                ROUND(
                (COALESCE(SUM(a.kg_real),0) / p.cantidad) * 100
                ,0) as cumplimiento

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
              ?> 
              <tr>
                <td>
                  <?=$g['num_pedido'] ?>
                    <button 
                      class="btn btn-warning btn-sm btnVerPedido"
                      data-id="<?=$g['id_pedido']?>"
                      data-bs-toggle="modal"
                      data-bs-target="#modalver">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                </td>
                <td><?=$g['cliente'] ?></td>
                <td><?=$g['fecha_entrega'] ?></td>
                <td><?=$g['cantidad'].' '.$g['sigla_pedido'] ?></td>
                <td><?=$g['producto'].' '.$g['envase'].' '.$g['peso'].' '.$g['sigla_producto'] ?></td>

                <?php  
                if($g['estado'] == 'ACTIVO'){
                ?>
                <td class="text-success"><i class="bi bi-check-circle-fill"></i></td>
                  <?php  }
                    elseif($g['estado'] == 'INACTIVO'){
                  ?>
                <td class="text-danger"><i class="bi bi-x-circle-fill"></i></td>
                  <?php  }
                  ?>
                <td>
            <!---------BOTON DE DETALLES---------------------------->
                  <button class="btn btn-success btn-sm btnVerDetalle" data-iddet="<?=$g['id_pedido']?>" data-bs-toggle="modal"data-bs-target="#modaldetalles"><i class="bi bi-card-checklist"></i>V1</button>
                
            <!---------BOTON DE DETALLES---------------------------->
                  <button class="btn btn-success btn-sm btnVerDetallev2" data-iddet="<?=$g['id_pedido']?>" data-bs-toggle="modal"data-bs-target="#modaldetallesv2"><i class="bi bi-card-checklist"></i> V2</button>

                  <!----------------------------------------------->
                  <button class="btn btn-success btn-sm btnVerDetallev3" data-iddet="<?=$g['id_pedido']?>" data-bs-toggle="modal"data-bs-target="#modaldetallesv3"><i class="bi bi-card-checklist"></i>V3</button>

                <!---------BOTON EDITAR---------------------------->
                  <button class="btn btn-sm btn-primary btnEditar"
                    data-identi="<?=$g['id_pedido']?>"
                    data-bs-toggle="modal"
                    data-bs-target="#modaleditar">
                    <i class="bi bi-pencil-square"></i>
                  </button> 

            <!-----------BOTON ELIMINAR------------------------------->
                  <button class="btn btn-sm btn-danger btn-elim" data-identificacion="<?=$g['id_pedido']?>">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </td>
                <td style=" width: 400px;">
                  <div class="d-flex align-items-center gap-2">
              <!-----------BARRA DE PROGRESO----------------------------->
                      <div class="progress flex-grow-1" role="progressbar"
                          aria-label="Cumplimiento de pedido"
                          aria-valuenow="<?= $g['cumplimiento'] ?>"
                          data-porcentaje="<?= $g['cumplimiento'] ?>"
                          aria-valuemin="0"
                          aria-valuemax="100">
                      <div class="progress-bar" style="width: <?= $g['cumplimiento'] ?>%"><?= $g['cumplimiento'] ?>%</div>
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
              <?php include '../complemento/paginator.php' ?>
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
                      e.abreviatura
                      from prod_productos as p
                        inner join prod_envase as e on e.id=p.envase
                        inner join prod_udm as u on u.id=p.udm
                        
                        where p.estado=1 and p.fase= 2"); 
                        ?>
                        <option></option>
                          <?php while($f=$c->fetch_assoc()){     ?>
                        <option value="<?=$f['id'] ?>"><?=$f['nombre'].' '.$f['abreviatura'].' '.$f['peso_prod'].' '.$f['sigla']  ?></option>
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

            <div class="row mb-3">
                <div class="col-5">
                    <label class="form-label">Cantidad</label>
                      <input type="text" class="form-control" name="cant" id="cant" required>
                </div>

                <div class="col-2">
                    <label class="form-label">UM</label>
                      <select name="unds" id="unds" required class="form-select">
                        <?php
                        $r=$conn->query("select id,nombre,sigla from prod_udm order by sigla desc");
                        while($s=$r->fetch_assoc()){
                        ?>
                        <option value="<?=$s['id'] ?>"><?=$s['sigla'] ?></option>
                        <?php } ?>
                      </select>
                </div>

            </div>
        </div>
      </div>
      <div class="modal-footer  justify-content-center">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
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



<!--------------------MODAL DETALLE----------------------->
<div class="modal fade" id="modaldetalles" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Detalle de cumplimiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="avanceped">
        Cargando...
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cerrar</button>
        <button class="btn btn-success btn-sm"  id="guardarAvance"><i class="bi bi-floppy2-fill"></i>  Guardar</button>
      </div>
    </div>
  </div>
</div>




<!--------------------MODAL DETALLEv2----------------------->
<div class="modal fade" id="modaldetallesv2" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Añadir produccion real</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="avancepedido">
        Cargando...
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cerrar</button>
        <button class="btn btn-success btn-sm"  id="guardarAvancev2"><i class="bi bi-floppy2-fill"></i>  Guardar</button>
      </div>
    </div>
  </div>
</div>


<!--------------------MODAL DETALLEv3----------------------->
<div class="modal fade" id="modaldetallesv3" tabindex="-1">
  <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Añadir produccion real</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="avancepedidov3">
        Cargando...
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cerrar</button>
        <button class="btn btn-success btn-sm"  id="guardarAvancev2"><i class="bi bi-floppy2-fill"></i>  Guardar</button>
      </div>
    </div>
  </div>
</div>




<!--------------------------------------------------->
<!--------------MODAL EDITAR PEDIDO--------------------->
<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0d6efd; color: white;">
        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar pedido</h5>
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
          <i class="bi bi-floppy2-fill"></i> Guardar cambios
        </button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
          <i class="bi bi-x-circle"></i> Cancelar
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

<!-------------------------------------------------->
<!-----------AJAX PROGRESO PEDIDO--------------------------------------->
<script>
$(document).on("click", ".btnVerDetalle", function(){

  const id = $(this).data("iddet");

  $("#avanceped").html("Cargando...");

  $.ajax({
      url: "ajax_avance_ped.php",
      type: "POST",
      data: { id: id },

    success: function(res){
      $("#avanceped").html(res);
    },

    error: function(){
      $("#avanceped").html("Error al cargar datos");
    }
  });

});
</script>

<!----------AVANCE V2---------------->
<script>
$(document).on("click", ".btnVerDetallev2", function(){

  const id = $(this).data("iddet");

  $("#avancepedido").html("Cargando...");

  $.ajax({
      url: "ajax_avance_pedv2.php",
      type: "POST",
      data: { id: id },

    success: function(res){
      $("#avancepedido").html(res);
    },

    error: function(){
      $("#avancepedido").html("Error al cargar datos");
    }
  });

});
</script>


<!----------AVANCE V3---------------->
<script>
$(document).on("click", ".btnVerDetallev3", function(){

  const id = $(this).data("iddet");

  $("#avancepedidov3").html("Cargando...");

  $.ajax({
      url: "ajax_avance_pedv3.php",
      type: "POST",
      data: { id: id },

    success: function(res){
      $("#avancepedidov3").html(res);
    },

    error: function(){
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

  $(document).on("click","#guardarAvance",function(){
    let datos = $("#formAvance").serialize();

    $.ajax({
        url:"guardar_avance.php",
        type:"POST",
        data:datos,

        success:function(resp){

            Swal.fire({
                icon:'success',
                title:'Guardado',
                text:'Producción registrada'
            });
        },
        error:function(){
            alert("Error al guardar");
        }
    });
});
</script>

<!---------------------->
<script>

  $(document).on("click","#guardarAvancev2",function(){

    let datos = $("#formAvance").serialize();

    $.ajax({
        url:"guardar_avancev2.php",
        type:"POST",
        data:datos,

        success:function(resp){

            Swal.fire({
                icon:'success',
                title:'Guardado',
                text:'Producción registrada'
            }).then(()=>{
                
                // cerrar modal
                $('#modaldetallesv2').modal('hide');

                // recargar página
                location.reload();

            });

        },
        error:function(){
           Swal.fire({
                icon:'error',
                title:'Error',
                text:'No se pudo guardar'
            });
        }
    });

});
</script>



<!--------------------------->
<script>
document.querySelectorAll(".progress-bar").forEach(function(barra){
  let porcentaje = parseInt(barra.closest('.progress').dataset.porcentaje);


    barra.classList.remove(
    "bg-danger",
    "bg-warning",
    "bg-info",
    "bg-success"
  );

    if(porcentaje < 30){
        barra.classList.add("bg-danger");
    }
    else if(porcentaje < 60){
        barra.classList.add("bg-warning");
    }
    else if(porcentaje < 90){
        barra.classList.add("bg-info");
    }
    else{
        barra.classList.add("bg-success");
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

      if(estado === "1"){
        coincideEstado = row.innerHTML.includes("check-circle-fill");
      }
      else if(estado === "2"){
        coincideEstado = row.innerHTML.includes("x-circle-fill");
      }

      return coincideTexto && coincideEstado;

    });

    currentPage = 1;
    update();
  }

  function displayRows(){
    tbody.innerHTML = "";

    let start = (currentPage - 1) * rowsPerPage;
    let end = start + rowsPerPage;

    filteredRows.slice(start, end).forEach(row => tbody.appendChild(row));
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
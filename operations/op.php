<!DOCTYPE html>
<html lang="es">

<head>
  <title>Ordenes de produccion</title>
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
    <h1 class="mt-1"><i class="bi bi-file-text"></i> Orden de Produccion</h1>
  <div class="row justify-content-center">
    <!----------------------------->
  

<!------------------------>
<div class="container mt-1">
  
<div class="row mb-3">
<div class="col-2">
<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="bi bi-plus-square"></i> Nuevo</button>
</div>
<div class="col-4">
<input  type="text"  id="Buscador"  class="form-control mb-3"  placeholder="Buscar orden de produccion...">
</div>
</div>

<table class="table mt-2 table-sm" id="tblcolab">
  <thead class="table-dark">
    <tr>
      <th >PEDIDO</th>
      <th>CLIENTE</th>
      <th >FECHA DE ENTREGA</th>
      <th >CANTIDAD</th>
        <th>PRODUCTO</th>
        <th></th>
       <th >% CUMPLIMIENTO</th>
      
      
    </tr>
  </thead>
  <tbody>
  <?php 
  $f=$conn->query("select 
  p.id_pedido,
  p.id_cliente,
  p.fecha_registro,
  p.fecha_entrega,
  p.producto,
  p.cantidad,
  p.und_medida,
  p.num_pedido,
  c.razon_social as cliente,
  pr.nombre as producto
  from prod_pedidos as p 
  inner join prod_clientes as c on c.id=p.id_cliente
  inner join prod_productos as pr on pr.id=p.producto order by p.id_pedido desc");
  
  while($g=$f->fetch_assoc()){

  ?> 
        <!-----
            <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">
  <div class="progress-bar" style="width: 55%">55%</div>
</div>

------>

<?php $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); ?>
  <tr>
    <td><?= $esc($g['num_pedido']) ?></td>
    <td><?=$esc($g['cliente']) ?></td>
    <td><?=$esc($g['fecha_entrega']) ?></td>
    <td><?= $esc($g['cantidad']) ?></td>
    <td><?=$esc($g['producto']) ?></td>
    <td>
         <button 
  class="btn btn-warning btn-sm btnVerPedido"
data-id="<?= $esc($g['id_pedido']) ?>"
  data-bs-toggle="modal"
  data-bs-target="#modalver">
  <i class="bi bi-eye-fill"></i>
</button>

</td><td>
          <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100">
  <div class="progress-bar" style="width: 55%">55%</div>
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
          <label class="form-label">Cliente</label>
      
           <select class="form-select" name="clte" id="clte" required>
            <?php $c=$conn->query("select id,razon_social,estado from prod_clientes where estado=1"); ?>
           <option></option>
            <?php while($f=$c->fetch_assoc()){     ?>
             <option value="<?= $esc($f['id']) ?>"><?= $esc($f['razon_social']) ?></option>
            <?php }?>
            </select>
        </div>
 <div class="col-6">
          <label class="form-label">Producto</label>
         
           <select name="prod" id="prod" class="form-select">
            <?php $c=$conn->query("select id,nombre,estado from prod_productos where estado=1"); ?>
           <option></option>
            <?php while($f=$c->fetch_assoc()){     ?>
              <option value="<?= $esc($f['id']) ?>"><?= $esc($f['nombre']) ?></option>
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
          <input type="date" class="form-control" required name="fentreg" id="fentreg" class="form-control">
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
                <option value="<?= $esc($s['id']) ?>"><?= $esc($s['sigla']) ?></option>
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


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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





</body>
</html>
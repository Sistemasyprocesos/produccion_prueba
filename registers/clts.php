<!DOCTYPE html>
<html lang="es">

<head>
  <title>Registro de Clientes</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

<?php

include '../complemento/sidebar.php';
include '../connection/conexion.php';


$consulta="select c.id,c.identificacion,c.direccion,l.id as tipo,l.nombre as nombretipo,c.razon_social,e.nom as estado,e.id as estado_id from prod_clientes as c inner join prod_tipo_cliente as l on l.id=c.tipo
inner join prod_estados as e on e.id=c.estado   
order by c.razon_social asc";

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1>Clientes</h1>
  <div class="row justify-content-center">
    <!----------------------------->
  

<!------------------------>
<div class="container mt-1">
  
<div class="row mb-3">
<div class="col-2">
<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="bi bi-plus-square"></i> Agregar Cliente</button>
</div>
<div class="col-4">
<input  type="text"  id="Buscador"  class="form-control mb-3"  placeholder="Buscar cliente...">
</div>
</div>

<table class="table mt-5 table-sm" id="tblcolab">
  <thead class="table-dark">
    <tr>
     <th>Razón Social</th>
      <th>Identificación</th>
      <th>Tipo</th>
      <th>Estado</th>
      <th>Acciones</th>
   
    </tr>
  </thead>
  <tbody>
    <?php
  $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    $result = $conn->query($consulta);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { ?>
            <tr>
            <td><?= $esc($row["razon_social"]) ?></td>
            <td><?=  $row["identificacion"]?></td>
            <td><?=  $row["nombretipo"]?></td>
            <td><?=  $row["estado"]?></td>
            <td>
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modaleditar"
              data-id="<?= $esc($row["id"]) ?>"
              data-rs="<?= $esc($row["razon_social"]) ?>"
              data-iden="<?=  $row["identificacion"]?>"
              data-tipo="<?=  $row["tipo"]?>"
              data-dir="<?=  $row["direccion"]?>"
             data-est="<?=  $row["estado_id"]?>"
              ><i class="bi bi-pencil-square"></i></button>

              <button class="btn btn-danger btn-sm btn-eliminar" data-id=<?= $row["id"] ?>><i class="bi bi-trash3"></i></button></td>


            </tr>
     <?php   }
    } else {
      ?>
        <tr><td colspan='5'>No hay registros</td></tr>
   <?php }
    ?>
  </tbody>
</table>


<!-------------PAGINADOR----------------------->
<nav>
<?php include '../complemento/paginator.php' ?>
<ul class="pagination justify-content-center" id="pagination"></ul>
</nav>

</div>
  </div>
</main>


<!--------------MODAL AGREGAR NUEVO--------------------->
<div class="modal fade" tabindex="-1" id="modalnuevo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

<form action="../procedimiento/newclt.php" method="post">

<div class="modal-header" style="background-color: #198754; color: white;">
  <h5 class="modal-title">Registro de cliente</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <div class="row mb-3">
    <div class="col-6">
      <label class="form-label">Razon Social</label>
      <input type="text" class="form-control" required name="razon_social">
    </div>
    <div class="col-6">
      <label class="form-label">Identificación</label>
      <input type="text" class="form-control" onkeypress="return soloNumeros(event);"  required name="iden">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-6">
      <label class="form-label">Tipo de Cliente</label>

      <?php
      $tipocliente="select id,nombre from prod_tipo_cliente order by nombre asc";

      $result = $conn->query($tipocliente);
      ?>

      <select class="form-select" required name="tipo_cliente">
        <option>Seleccione...</option>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { ?>
                <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
            <?php   }
        }
     ?>   
      </select>
    </div>
    
  </div>

  <div class="row mb-3">
    <div class="col-12">
      <label class="form-label">Direccion</label>
      <input type="text" required name="direccion" class="form-control">
    </div>
  </div>
</div>

<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
</div>

</form>

    </div>
  </div>
</div>



<!--------------MODAL EDITAR--------------------->

<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

<form action="../procedimiento/editclt.php" method="post">

<div class="modal-header" style="background-color: #198754; color: white;">
  <h5 class="modal-title">Editar informacion de cliente</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <div class="row mb-3">
    <div class="col-6">

    <input type="hidden" name="iden" id="iden">

      <label class="form-label">Razon Social</label>
      <input type="text" class="form-control" name="rs" id="rs">
    </div>
    <div class="col-6">
      <label class="form-label">Identificación</label>
      <input type="text" class="form-control" onkeypress="return soloNumeros(event);" name="identi" id="identi">
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-6">
      <label class="form-label">Tipo de Cliente</label>
      <select class="form-select" name="tc" id="tc">
        <?php
        $cl=$conn->query("select id,nombre from prod_tipo_cliente order by nombre asc");
        if ($cl->num_rows > 0) {
            while($row = $cl->fetch_assoc()) { ?>
                <option value="<?=$esc($row['id'])?>"><?=$esc($row['nombre'])?></option>
            <?php   }
        }
        ?>
      </select>
    </div>
   <div class="col-6">
      <label class="form-label">Estado</label>
      <select class="form-select" name="est" id="est">
        <?php
        $est=$conn->query("select id,nom from prod_estados order by nom asc");
        if ($est->num_rows > 0) {
            while($row = $est->fetch_assoc()) { ?>
                <option value="<?=$esc($row['id']) ?>"><?= $esc($row['nom'])?></option>
            <?php   }
        }
        ?>
      </select>

   </div>
  </div>

  <div class="row mb-3">
    <div class="col-12">
      <label class="form-label">Direccion</label>
      <input type="text" class="form-control" name="dir" id="dir">
    </div>
  </div>
</div>

<div class="modal-footer">
  <button type="submit" class="btn btn-primary">Guardar</button>
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
</div>

</form>

    </div>
  </div>
</div>

<!---------------PASAR LOS DATOS A CAJAS DE TEXTO DE EDITAR-------------------->
<script>
  const modaleditar = document.getElementById('modaleditar');
  modaleditar.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget; // botón que abrió el modal

  // Extraer info desde data-*
  const id = button.getAttribute('data-id');//id
  const rz = button.getAttribute('data-rs');
  const iden = button.getAttribute('data-iden');
  const tipo = button.getAttribute('data-tipo');
  const est = button.getAttribute('data-est');
  const dir = button.getAttribute('data-dir');

// Pasar info a los inputs del modal
  document.getElementById('iden').value = id;//id de identificacion
  document.getElementById('rs').value = rz;
  document.getElementById('identi').value = iden;//cedula
  document.getElementById('tc').value = tipo;
  document.getElementById('est').value = est;
  document.getElementById('dir').value = dir;

  

});

  </script>
<!------SWEET ALERT DE ELIMINAR------------------------------>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-eliminar').forEach(btn => {
  btn.addEventListener('click', function () {

    const id = this.getAttribute('data-id');

    Swal.fire({
      title: "Confirmar eliminación",
      text: "Va a eliminar este cliente. ¿Desea continuar?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Eliminar",
      cancelButtonText: "Cancelar"
    }).then((result) => {
      if (result.isConfirmed) {

        // Redirigir al PHP que elimina
        window.location.href = "../procedimiento/deletectl.php?id=" + id;

      }
    });

  });
});
</script>

<script>
function soloNumeros(event) {
  const char = event.key;
  return /^[0-9]$/.test(char);
}
  </script>
<!------------------------------------------------------------------------------------->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php $conn->close(); ?>
</body>
</html>

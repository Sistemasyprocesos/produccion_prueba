<!DOCTYPE html>
<html lang="es">

<head>
      <title>Registro de Unidades de Medida</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';


$consulta="select id,nombre,sigla from prod_udm order by nombre asc";

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1>Unidades de Medida</h1>
  <div class="row justify-content-center">
    <!----------------------------->
<!------------------------>
<div class="container mt-2">
    <div class="row mb-3">
      <div class="col-2">
          <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="bi bi-plus-square"></i> Agregar Unidades de Medida</button>
      </div>
      <div class="col-4">
          <input  type="text"  id="Buscador"  class="form-control mb-3"  placeholder="Buscar unidad de medida...">
      </div>
</div>

<table class="table mt-5 table-sm" id="tblcolab">
  <thead class="table-dark">
    <tr>
      <th>Nombre</th>
      <th>Sigla</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php
  
    $result = $conn->query($consulta);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?=  $row["nombre"]?></td>
                <td><?=  $row["sigla"]?></td>
                
                <td>
                  <!----MODAL EDITAR-------->
                  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modaleditar"
                      data-id="<?= $row["id"]?>"
                      data-nombre="<?= $row["nombre"]?>"
                      data-sigla="<?= $row["sigla"]?>">             
                      <i class="bi bi-pencil-square"></i></button>
                  <!------BOTON ELIMINAR------>
                  <button class="btn btn-danger btn-sm btn-eliminar" data-cod="<?=$row["id"]?>"><i class="bi bi-trash3"></i></button></td>
            </tr>
     <?php   }
    } else {
      ?>
        <tr>
          <td colspan='5'>No hay registros</td>
        </tr>
   <?php }
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


<!--------------MODAL AGREGAR NUEVO--------------------->
<div class="modal fade" tabindex="-1" id="modalnuevo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Registro de Unidad de Medida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="col-12 ">
 
      <form method="post" action="../procedimiento/newudm.php">
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-control" name="nombre" id="nombre" required>
        </div>
        <div class="col-6">
          <label class="form-label">Sigla</label>
          <input type="text"  class="form-control" name="sigla" id="sigla" required>
        </div>
      </div>

    </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
      </form>
      </div>
    </div>
  </div>
</div>

<!----------------------------------->

<!--------------MODAL EDITAR--------------------->
<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Editar Unidad de Medida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="col-12 ">
      <form method="post" action="../procedimiento/actudm.php">
        <input name="cod" id="cod" required type="hidden">
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Nombre</label>
          <input type="text" required class="form-control" name="nombre" id="nombre">
        </div>
        <div class="col-6">
          <label class="form-label">Sigla</label>
          <input type="text" required class="form-control" name="sigla" id="sigla">
        </div>
      </div>

    </div>
      </div>
      <div class="modal-footer">
        
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
      </form>
      </div>
    </div>
  </div>
</div>



<script>
const modaleditar=document.getElementById('modaleditar');
  modaleditar.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const nombre = button.getAttribute('data-nombre');
      const sigla = button.getAttribute('data-sigla');

      document.getElementById('cod').value=id;
      document.getElementById('nombre').value=nombre;
      document.getElementById('sigla').value=sigla;
});
</script>


<!---------SCRIPT DE SWEET ALERT-------------------------->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
document.addEventListener("click", function(e){
  if(e.target.closest(".btn-eliminar")){
    const btn=e.target.closest(".btn-eliminar");
    const cod = btn.dataset.cod;
    Swal.fire({
      title: '¿Estás seguro?',
      text: "¡No podrás revertir esto!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar!',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `../procedimiento/deludm.php?codigo=${cod}`;
      }
    });
  }
});
  
</script>
<!------------------------------------------------------------------------------------->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

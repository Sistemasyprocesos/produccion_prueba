<!DOCTYPE html>
<html lang="es">

<head>
      <title>Registro de Envases</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';


$consulta="select id, nombre,abreviatura from prod_envase order by nombre asc";

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1>Envases</h1>
  <div class="row justify-content-center">
  
  

<!------------------------>
<div class="container mt-2">
<div class="row mb-3">
<div class="col-2">
<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="fa-solid fa-square-plus" style="color: rgb(255, 255, 255);"></i> Agregar Envase</button>
</div>
<div class="col-4">
<input  type="text"  id="Buscador"  class="form-control mb-3"  placeholder="Buscar envase...">
</div>
</div>

<div class="row justify-content-center">
    <table class="table mt-5 table-sm shadow" style="width:70%" id="tblcolab">
      <thead class="table-dark">
        <tr>
          <th>Envase</th>
          <th>abreviatura</th>
      
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
                <td><?=  $row["abreviatura"]?></td>
                <td class="text-center">
                  <!---------EDITAR------------>
                  <button class="btn btn-primary btn-sm"  
                  data-bs-toggle="modal"
                  data-bs-target="#modaleditar"
                    data-id="<?= $row["id"]?>"
                    data-nombre="<?= $row["nombre"]?>"
                    data-abreviatura="<?= $row["abreviatura"]?>">
                  <i class="bi bi-pencil-square"></i>
                  </button>

                  <!---------ELIMNAR------------>
                  <button class="btn btn-danger btn-sm btn-eliminar" data-cod="<?= $row["id"]?>"><i class="fa-solid fa-trash-can" style="color: rgb(255, 255, 255);"></i></button></td>
                </tr>
        <?php   }
        } else {
          ?>
            <tr><td colspan='5'>No hay registros</td></tr>
      <?php }
        ?>
      </tbody>
    </table>
</div>

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
        <h5 class="modal-title">Registro de nuevo envase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="col-12 ">
 

      <form action="../procedimiento/aggenv.php" method="POST">
        
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Envase</label>
          <input type="text" class="form-control" name="nomb" id="nomb">
        </div>
        <div class="col-6">
          <label class="form-label">Abreviatura</label>
          <input type="text" class="form-control" name="ab" id="ab">
        </div>
      </div>

    </div>
      </div>
      <div class="modal-footer">
        
          <button type="submit" class="btn btn-primary">
              Guardar
          </button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
              Cancelar
          </button>
      </form>
      </div>
    </div>
  </div>
</div>

<!---------------------------MODAL EDITAR--------------------->
<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Editar Envase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="col-12 ">
 

      <form action="../procedimiento/actenv.php" method="POST">
        <input type="hidden" name="cod" id="cod">

      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Envase</label>
          <input type="text" class="form-control" name="envase" id="envase">
        </div>
      </div> 

      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Abreviatura</label>
          <input type="text" class="form-control" name="abrev" id="abrev">
        </div> 
      </div>

    </div>
      </div>
      <div class="modal-footer">        
        <button type="submit" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
          Cancelar
        </button>
      </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
document.addEventListener("click", function(e){

  if(e.target.closest(".btn-eliminar")){
    const btn = e.target.closest(".btn-eliminar");
    const codigo = btn.dataset.cod;

    Swal.fire({
      title: '¿Eliminar envase?',
      text: "¿Estás seguro de que deseas eliminar este envase?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {

      if (result.isConfirmed) {
        window.location.href = "../procedimiento/elimenv.php?id=" + codigo;
      }

    });

  }

});
</script>

<script>

  const modaleditar = document.getElementById('modaleditar');
  modaleditar.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget; // botón que abrió el modal

  // Extraer info desde data-*
  const id = button.getAttribute('data-id');//id
  const abrev = button.getAttribute('data-abreviatura');
  const nombre = button.getAttribute('data-nombre');

// Pasar info a los inputs del modal
  document.getElementById('cod').value = id;//id de identificacion
  document.getElementById('abrev').value = abrev;
  document.getElementById('envase').value = nombre;

});

</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

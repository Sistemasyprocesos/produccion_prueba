<!DOCTYPE html>
<html lang="es">

<head>
      <title>Categorias</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';


$consulta="select id_cat,cat_nombre from prod_categoria_prod order by cat_nombre asc";

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1>Registro de categorias</h1>
  <div class="row justify-content-center">
    <!----------------------------->
  

<!------------------------>
<div class="container mt-2">
<div class="row mb-3">
<div class="col-2">
<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="bi bi-plus-square"></i> Agregar nueva categoria</button>
</div>
<div class="col-4">
<input  type="text" id="Buscador"  class="form-control mb-3"  placeholder="Buscar categoria...">
</div>
</div>

<table class="table mt-5 table-sm" id="tblcolab">
  <thead class="table-dark">
    <tr>
      <th>Nombre</th>
      
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php
  
    $result = $conn->query($consulta);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { ?>
            <tr>
            <td><?=  $row["cat_nombre"]?></td>
            
            
            <td>
              <!--------BOTON EDITAR---------->
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modaleditar" 
              data-cod="<?= $row["id_cat"]?>"
              data-nombre="<?= $row["cat_nombre"]?>"  
           
              ><i class="bi bi-pencil-square"></i></button>
              <!--------BOTON ELIMINAR---------->
              <button class="btn btn-danger btn-sm btn-eliminar" data-eliminar="<?=$row['id_cat'] ?>"><i class="bi bi-trash3"></i></button></td>
            </tr>
     <?php   }
    } else {
      ?>
        <tr><td colspan='5'>No hay registros</td></tr>
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
        <h5 class="modal-title">Registro de nueva categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="col-12 ">
 

      <form action="../procedimiento/newcat.php" method="POST">
        
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-control" name="cat_nombre" id="cat_nombre">
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
        <h5 class="modal-title">Editar Categoria</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="col-12 ">
 

      <form action="../procedimiento/editcat.php" method="POST">
        <input type="hidden" name="idcat" id="idcat">
      <div class="row mb-3">
        <div class="col-6">
          <label class="form-label">Nombre</label>
          <input type="text" class="form-control" id="catnom" name="catnom">
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
  var modalEditar = document.getElementById('modaleditar');
  modalEditar.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var cod = button.getAttribute('data-cod');
    var nombre = button.getAttribute('data-nombre');


    var inputCod = modalEditar.querySelector('#idcat');
    var inputNombre = modalEditar.querySelector('#catnom');


    inputCod.value = cod;
    inputNombre.value = nombre;
  
  });

</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
document.addEventListener("click", function(e) {

    if (e.target.closest(".btn-eliminar")) {

        const btn = e.target.closest(".btn-eliminar");
        const codigo = btn.dataset.eliminar;

        Swal.fire({
            title: '¿Eliminar categoria?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {

            if (result.isConfirmed) {
                
                // 🔹 aquí haces la eliminación real
                // ejemplo redirección:
                window.location.href = "../procedimiento/catdel.php?codigo=" + codigo;

                // o si usas AJAX también se puede
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

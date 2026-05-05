<!DOCTYPE html>
<html lang="es">

<head>
      <title>Registro de Actividades de Produccion</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';


$consulta = "
    SELECT 
      p.id,
      p.nombre,
      p.abreviatura,
      e.id   AS idestado,
      e.nom  AS nombreestado
    FROM prod_act_prod AS p
    INNER JOIN prod_estados AS e
      ON p.estado = e.id
    ORDER BY p.nombre ASC
";

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1>Actividades de produccion</h1>
  <div class="row justify-content-center">
    <!----------------------------->
  

<!------------------------>
<div class="container mt-2">
    <div class="row mb-3">
        <div class="col-2">
          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="fa-solid fa-square-plus" style="color: rgb(255, 255, 255);"></i> Agregar Actividad de Produccion</button>
        </div>
        <div class="col-4">
          <input type="text" id="Buscador" class="form-control mb-3" placeholder="Buscar actividad de producción...">
        </div>
    </div>

<table class="table mt-5 table-sm shadow table-bordered" id="tblcolab">
  <thead class="table-dark">
    <tr>
      <th>Nombre</th>
      <th>Sigla</th>
      <th>Estado</th>
      <th>Acciones</th>

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
            <td><?=  $row["nombreestado"]?></td>
            <td class="text-center">
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modaleditar"
                data-id=<?=$row["id"] ?> 
                data-nombre=<?=$row['nombre'] ?> 
                data-abrev=<?=$row['abreviatura'] ?>
                data-est=<?=$row['idestado']?>
              ><i class="bi bi-pencil-square"></i></button>
              
              <!-------BOTON ELIMINAR-------->
            <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $row['id'] ?>">
              <i class="fa-solid fa-trash-can" style="color: rgb(255, 255, 255);"></i>
            </button>

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
        <h5 class="modal-title">Registro de Actividad de Producción</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
         <div class="col-12 ">
 
          <form id="formNuevo">
            
              <div class="row mb-3">
                <div class="col-6">
                  <label class="form-label">Nombre</label>
                  <input type="text" class="form-control" name="act_nom">
                </div>
                <div class="col-6">
                  <label class="form-label">Abreviatura</label>
                  <input type="text" class="form-control" name="act_abreviatura">
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

<!--------------MODAL EDITAR--------------------->
<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Registro de Actividad de Producción</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="col-12 ">
 
          <form id="formEdit">
            <input type="hidden" name="iden" id="iden">
          <div class="row mb-3">
              <div class="col-8">
                  <label class="form-label">
                    Nombre
                  </label>
                  <input type="text" class="form-control" id="nombre" name="nombre_act">
                  <label class="form-label">
                    Abreviatura
                  </label>
                  <input type="text" class="form-control" id="abre" name="abrevia_act">
                  <label class="form-label">
                    Estado
                  </label>
                    <?php
                    $g=$conn->query("select id,nom from  prod_estados");
                    ?>
                  <select name="est" id="est" class="form-select">
                    <?php
                    while($r=$g->fetch_assoc()){
                    ?>
                    <option value="<?=$r['id'] ?>">
                      <?=$r['nom'] ?>
                    </option>
                    <?php } ?>
                  </select>
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


<!------------PAGINADOR--------------------->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!------SWEET ALERT DE ELIMINAR------------------------------>

<script>
document.getElementById('tblcolab').addEventListener('click', function (e) {

  const btn = e.target.closest('.btn-eliminar');
  if (!btn) return;

  const id = btn.dataset.id;
  const fila = btn.closest('tr');

  Swal.fire({
        title: "Confirmar eliminación",
        text: "Va a eliminar esta actividad. ¿Desea continuar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar"
  }).then((result) => {

    if (result.isConfirmed) {

      fetch('../procedimiento/delact.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + id
      })
      .then(res => res.json())
      .then(data => {

        if (data.success) {
          fila.remove();

          Swal.fire({
            icon: 'success',
            title: 'Eliminado',
            timer: 1200,
            showConfirmButton: false
          });

        } else {
          Swal.fire('Error', 'No se pudo eliminar', 'error');
        }

      });

    }
  });

});
</script>

<!---------GUARDA NUEVO REGISTRO SIN RECARGAR--------------------------->

<script>
document.getElementById('formNuevo').addEventListener('submit', function(e) {
  e.preventDefault(); // no recarga

  const form = this;
  const datos = new FormData(form);

  fetch('../procedimiento/guardar_act.php', {
    method: 'POST',
    body: datos
  })
  .then(res => res.json())
  .then(data => {

    if (data.success) {

      Swal.fire({
        icon: 'success',
        title: 'Guardado',
        text: 'Actividad registrada correctamente',
        timer: 1500,
        showConfirmButton: false
      });

      // cerrar modal
      bootstrap.Modal.getInstance(
        document.getElementById('modalnuevo')
      ).hide();

      // limpiar form
      form.reset();

      // agregar fila a la tabla 
      document.querySelector('#tblcolab tbody').insertAdjacentHTML('afterbegin', `
        <tr>
          <td>${data.nombre}</td>
          <td>${data.abreviatura}</td>
          <td>Activo</td>
          <td>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modaleditar"
              data-id="${data.id}"
              data-nombre="${data.nombre}"
              data-abrev="${data.abreviatura}">
              <i class="bi bi-pencil-square"></i>
            </button>

            <button class="btn btn-danger btn-sm btn-eliminar" data-id="${data.id}">
              <i class="bi bi-trash3"></i>
            </button>
          </td>
        </tr>
      `);

    } else {
      Swal.fire('Error', data.msg || 'No se pudo guardar', 'error');
    }

  });
});
</script>

<!-------------------------EDITAR------------------------------------->
<script>
let filaEditando = null;

const modaleditar = document.getElementById('modaleditar');

modaleditar.addEventListener('show.bs.modal', event => {

  const button = event.relatedTarget;
  filaEditando = button.closest('tr');

  document.getElementById('iden').value   = button.dataset.id;
  document.getElementById('nombre').value = button.dataset.nombre;
  document.getElementById('abre').value   = button.dataset.abrev;
  document.getElementById('est').value    = button.dataset.est;
});

document.getElementById('formEdit').addEventListener('submit', function (e) {
  e.preventDefault();

  const datos = new FormData(this);

  fetch('../procedimiento/edit_act.php', {
    method: 'POST',
    body: datos
  })
  .then(res => res.json())
  .then(data => {

    if (data.success) {

      Swal.fire({
        icon: 'success',
        title: 'Editado correctamente',
        timer: 1200,
        showConfirmButton: false
      });

    filaEditando.children[0].textContent = data.nombre;
    filaEditando.children[1].textContent = data.abreviatura;

// muestra ID del estado
    filaEditando.children[2].textContent =
      document.querySelector(`#est option[value="${data.estado_id}"]`).textContent;

    const btnEditar = filaEditando.querySelector('.btn-primary');
    btnEditar.dataset.nombre = data.nombre;
    btnEditar.dataset.abrev  = data.abreviatura;
    btnEditar.dataset.est    = data.estado_id;

      bootstrap.Modal.getInstance(modaleditar).hide();
    }
  });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

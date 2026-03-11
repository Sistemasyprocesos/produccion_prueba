<!DOCTYPE html>
<html lang="es">

<head>
      <title>Registro de Productos</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
  .modal-dialog {
  max-height: 95vh;
}

.modal-content {
  height: 95vh;
  display: flex;
  flex-direction: column;
}

.modal-body {
  overflow-y: auto;
}

.modal-footer {
  position: sticky;
  bottom: 0;
  background: white;   /* importante para que no sea transparente */
  z-index: 10;
}
</style>
</head>

<body>

<?php
  include '../complemento/sidebar.php';
  include '../connection/conexion.php';


$consulta="select 
p.nombre as nprod,
p.cat_prod as cat_prod,
p.tipo_prod,
p.envase,
p.peso_prod,
p.unds_cjsc,
e.nombre as env,
e.id as idenv,
p.tipo_embalaje,
p.und_pallet,
p.producto_base,
p.estado,
p.codigo_prod as codigo_prod,

p.id as idprod,
u.sigla,u.id,
p.estado as estado,
t.abreviatura as tprod,
t.cod as codtipoprod
from prod_productos as p 
inner join prod_envase as e on e.id=p.envase  
inner join prod_tipo_prod as t on t.cod=p.tipo_prod
inner join prod_udm as u on p.udm=u.id
order by p.nombre asc";

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1>Productos</h1>
  <div class="row justify-content-center">
    <!----------------------------->
<!------------------------>
<div class="container mt-2">
<div class="row mb-3">
<div class="col-2">
<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo"><i class="bi bi-plus-square"></i> Agregar Producto</button>
</div>
<div class="col-4">
<input  type="text"  id="Buscador"  class="form-control mb-3"  placeholder="Buscar producto...">
</div>
</div>

<table class="table mt-5 table-sm" id="tblcolab">
  <thead class="table-dark">
    <tr>
      <th>Codigo</th>
      <th>Producto</th>
      <th>Categoria</th>
      <th>Tipo</th>
      <th>Peso (Kg)</th>
      <th>Envase</th>
      <th>Unds(CJ/SC)</th>
      <th>Tipo Embalaje</th>
      <th>Unds(Pallet)</th>     
      <th>Producto Base</th>
      <th>Acciones</th>
      <th>Activar/Inactivar</th>
    </tr>
  </thead>
  <tbody>

    <?php
  
    $result = $conn->query($consulta);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { ?>
            <tr>
            <td><?= $row["codigo_prod"] ?></td>
            <td><?=  $row["nprod"]?></td>
            <td><?= $row["cat_prod"]?></td>
            <td><?= $row["tprod"]?></td>
            <td><?= $row["peso_prod"]?></td>
            <td><?=$row["env"] ?></td>
            <td><?= $row["unds_cjsc"] ?></td>
            <td><?=$row["env"] ?></td>
            <td><?= $row["und_pallet"]?></td>
            <td><?=$row["producto_base"] ?></td>
            <td>

            <!-------BOTON EDITAR------------->
              <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modaleditar"
              data-id="<?= $row["idprod"] ?>"
              data-codigo="<?= $row["codigo_prod"] ?>"
              data-nombre="<?= $row["nprod"] ?>"
              data-categoria="<?= $row["cat_prod"] ?>"
              data-tipo="<?= $row["codtipoprod"] ?>"
              data-peso="<?= $row["peso_prod"] ?>"
              data-envase="<?= $row["idenv"] ?>"
              data-unds_cjsc="<?= $row["unds_cjsc"] ?>"
              data-tipo_embalaje="<?= $row["tipo_embalaje"] ?>"
              data-und_pallet="<?= $row["und_pallet"] ?>"
             
              data-producto_base="<?= $row["producto_base"] ?>"
              data-estado="<?= $row["estado"] ?>"
              ><i class="bi bi-pencil-square"></i></button>

              <!-------BOTON ELIMINAR------------->
              <button class="btn btn-danger btn-sm btn-eliminar" data-cod="<?= $row["idprod"] ?>"><i class="bi bi-trash3" ></i></button>
            </td>

              <td><button class="btn btn-sm btn-warning"><i class="bi bi-check-circle text-white"></i>
</button></td>
            </tr>
     <?php   }
    } else {
      ?>
        <tr><td colspan='5'>No hay registros</td></tr>
   <?php }
    ?>
  </tbody>
</table>



<!-----------------------------PAGINADOR---------------------------------------->



<nav>
<?php include '../complemento/paginator.php' ?>
<ul class="pagination justify-content-center" id="pagination"></ul>
</nav>

</div>
  </div>
</main>

<!--------------MODAL AGREGAR NUEVO--------------------->

<div class="modal fade" id="modalnuevo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

     
<form id="formguarda">
        <div class="modal-header" style="background-color: #198754; color: white;">
          <h5 class="modal-title">Registro de nuevo producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <h6 class="text-decoration-underline">Datos del producto</h6>

          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label">Código</label>
              <input type="text" name="codigoprod" maxlength="10" class="form-control">
            </div>
            <div class="col-6">
              <label class="form-label">Nombre del producto</label>
              <input type="text" placeholder="Achochillo, Azucar Invertida...." name="nombreprod" id="nombreprod" class="form-control">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-8">
              <label class="form-label">Categoría</label>
              <input type="text" name="categoria" id="categoria" class="form-control">
            </div>
            <div class="col-4">
              <label class="form-label">Tipo</label>
              <select class="form-select" name="tipoprod" id="tipoprod">
                <option>Seleccione...</option>
                <?php 
                $h=$conn->query("select cod,nombre,abreviatura from prod_tipo_prod order by abreviatura asc");
                while($row = $h->fetch_assoc()) { ?>
                  <option value="<?= $row["cod"] ?>"><?= $row["abreviatura"] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-4">
              <label class="form-label">Peso</label>
              <input type="text" class="form-control" onkeypress=soloNumeros(event) name="pesoprod" id="pesoprod">
            </div>
           
            <div class="col-4"> 
              <label class="form-label">UDM</label>
              <select class="form-select" name="udm" id="udm">
                <?php
                $h=$conn->query("select id,nombre,sigla from prod_udm  order by sigla asc");
                while($row = $h->fetch_assoc()) { ?>
                  <option value="<?= $row["id"] ?>"><?= $row["sigla"] ?></option>
                <?php } ?>
              </select>
            </div>
           
            <div class="col-4"> 
              <label class="form-label">Envase</label>
              <select class="form-select" name="envase_prod" id="envase_prod">
                <?php
                $h=$conn->query("select id,nombre,abreviatura,estado from prod_envase where estado=1 order by abreviatura asc");
                while($row = $h->fetch_assoc()) { ?>
                  <option value="<?= $row["id"] ?>"><?= $row["nombre"].' ('.$row['abreviatura'].')' ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <hr>
          <h6><u>Datos pallet</u></h6>

          <div class="row mb-3">
            <div class="col-4">
              <label class="form-label">Unds(CJ/SC)</label>
              <input type="text" class="form-control" id="unds_cjsc" onkeypress=soloNumeros(event) name="unds_cjsc">
            </div>
            <div class="col-4">
              <label class="form-label">Embalaje</label>
              <select class="form-select" name="tipo_embalaje" id="tipo_embalaje">
                <?php
                $c=$conn->query("select id,nombre,abreviatura,estado from prod_envase where estado=1 order by abreviatura asc");
                while($row = $c->fetch_assoc()) { ?>
                  <option value="<?= $row["id"] ?>"><?= $row["nombre"].' ('.$row['abreviatura'].')' ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-4">
              <label class="form-label">Unds por pallet</label>
              <input type="text" class="form-control" onkeypress=soloNumeros(event) name="unds_pallet" id="unds_pallet">
            </div>
          </div>

          <hr>
          <h6><u>Unidades equivalentes</u></h6>

          <div class="row mb-3">
            <div class="col-4">
              <label class="form-label">Producto base</label>
              <select class="form-select" name="prod_base" id="prod_base">
                <option value="si">Si</option>
                <option value="no">No</option>
              </select>
            </div>

            
          </div>

        </div> <!-- ✅ CIERRE modal-body -->

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!--------------MODAL EDITAR--------------------->


<div class="modal fade" id="modaleditar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog  modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <form action="edprod.php" method="POST">

        <div class="modal-header" style="background-color: #198754; color: white;">
          <h5 class="modal-title">Editar producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <input type="hidden" name="iden" id="iden">
        <div class="modal-body" >

          <!-- Código -->
          <div class="row mb-3">
            <div class="col-12">
              <label class="form-label">Código</label>
              <input type="text" required class="form-control" id="cod" name="cod">
            </div>
          </div>

          <!-- Nombre / Tipo -->
          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label">Nombre</label>
              <input type="text" required class="form-control" id="nombre" name="nombre">
            </div>
            <div class="col-6">
              <label class="form-label">Tipo</label>
           <select class="form-select" required id="tipo" name="tipo">
                <?php
                $h=$conn->query("SELECT cod, abreviatura FROM prod_tipo_prod WHERE estado=1 ORDER BY abreviatura");
                while($row = $h->fetch_assoc()) { ?>
                  <option value="<?= $row['cod'] ?>"><?= $row['abreviatura'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <!-- Categoría / PVP / Peso -->
          <div class="row mb-3">
            <div class="col-4">
              <label class="form-label">Categoría</label>
              <input type="text" class="form-control" required name="cate" id="cate">
            </div>
            
            <div class="col-2">
              <label class="form-label">Peso (kg)</label>
              <input type="number"  step="0.01" min="0" onkeypress=soloNumeros(event) class="form-control" required id="peso" name="peso">
            </div>
             <div class="col-2">
              <label class="form-label">UDM</label>

              <select required class="form-select" name="udm" id="udm">
                <?php 
                $g=$conn->query("SELECT id,nombre,sigla FROM prod_udm  ORDER BY sigla");
                while($row = $g->fetch_assoc()) { ?>
                  <option value="<?= $row['id'] ?>">
                    <?= $row['sigla']?>
                  </option>
                <?php } ?>
              </select>
            </div>
           
          </div>

          <hr>

          <!-- Envase -->
          <div class="row mb-3">
            <div class="col-4">
              <label class="form-label">Envase</label>
              <select required class="form-select" name="env" id="env">
                <?php 
                $g=$conn->query("SELECT id,nombre,abreviatura FROM prod_envase WHERE estado=1 ORDER BY abreviatura");
                while($row = $g->fetch_assoc()) { ?>
                  <option value="<?= $row['id'] ?>">
                    <?= $row['nombre']." (".$row['abreviatura'].")" ?>
                  </option>
                <?php } ?>
              </select>
            </div>
           
            <div class="col-4">
              <label class="form-label">UDM envase</label>
              <select required class="form-select" name="udmenvase" id="udmenvase">
                <option value="KG">KG</option>
              </select>
            </div>
          </div>

          <hr>

<!-- Unidades -->
          <div class="row mb-3">
            <div class="col-4">
              <label class="form-label">Unidades x cj/sc</label>
              <input type="number" required min="0" onkeypress=soloNumeros(event) class="form-control" name="undscjsc" id="undscjsc">
            </div>
            <div class="col-4">
              <label class="form-label">Unidades en pallet</label>
              <input type="number" required min="0" onkeypress=soloNumeros(event) class="form-control" name="und_pallet" id="und_pallet">
            </div>
              <div class="col-4">
              <label class="form-label">Estado</label>
                <select name="estate" required id="estate" class="form-select">
                  <?php $f=$conn->query("select id,nom from prod_estados order by nom desc");
                  while($k=$f->fetch_assoc()){
                  ?>
                    <option value="<?=$k['id'] ?>"><?=$k['nom'] ?></option>
                  <?php } ?>
                </select>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
document.addEventListener("click", function(e) {

    if (e.target.closest(".btn-eliminar")) {

        const btn = e.target.closest(".btn-eliminar");
        const codigo = btn.dataset.cod;

        Swal.fire({
            title: '¿Eliminar producto?',
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
                window.location.href = "proddel.php?codigo=" + codigo;

                // o si usas AJAX también se puede
            }
        });
    }

});
</script>

<!-----------------------PASA LOS DATOS A EDITAR-------------------------->
<script>
  const modaleditar = document.getElementById('modaleditar');
  modaleditar.addEventListener('show.bs.modal', event => {
  const button = event.relatedTarget; // botón que abrió el modal

  // Extraer info desde data-*
  const id = button.getAttribute('data-id');//id
  const cod = button.getAttribute('data-codigo');
  const nombre = button.getAttribute('data-nombre');
  const categoria= button.getAttribute('data-categoria');
  const tipo = button.getAttribute('data-tipo');
  const peso= button.getAttribute('data-peso');
  const envase = button.getAttribute('data-envase');

  const unds_cjsc = button.getAttribute('data-unds_cjsc');
  //const tipo_embalaje = button.getAttribute('data-tipo_embalaje');
  const und_pallet = button.getAttribute('data-und_pallet');
  const estado = button.getAttribute('data-estado');

// Pasar info a los inputs del modal
  document.getElementById('iden').value = id;//id de identificacion
  document.getElementById('cod').value = cod;
  document.getElementById('nombre').value = nombre;
  document.getElementById('cate').value = categoria;
  document.getElementById('tipo').value = tipo;
  document.getElementById('peso').value = peso;
 
  document.getElementById('estate').value = parseInt(estado) || "";
  document.getElementById('env').value=envase;

  document.getElementById('und_pallet').value=und_pallet;
  document.getElementById('undscjsc').value=unds_cjsc;




});

  </script>



<!---------GUARDA NUEVO REGISTRO SIN RECARGAR--------------------------->


<script>
document.getElementById('formguarda').addEventListener('submit', function(e) {
  e.preventDefault(); // no recarga

  const form = this;
  const datos = new FormData(form);

  fetch('../procedimiento/nuevoprod.php', {
    method: 'POST',
    body: datos
  })
  .then(res => res.json())
  .then(data => {

    if (data.success) {

      Swal.fire({
        icon: 'success',
        title: 'Guardado',
        text: 'Producto registrado correctamente',
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
        <td>${data.codigo_prod}</td>
          <td>${data.nombre_completo}</td>
          <td>${data.cat_prod}</td>
          <td>${data.tipo_prod}</td>
          <td>${data.peso_prod}</td>
          <td>${data.envase}</td>
          <td>${data.unds_cjsc}</td>
          <td>${data.tipo_embalaje}</td>
          <td>${data.und_pallet}</td>
          <td>${data.producto_base}</td>
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



<script>

function soloNumeros(e){

if (!/[0-9.,]/.test(e.key)) {
e.preventDefault();
}

}

</script>



<!------------PAGINADOR--------------------->

<!------------------------------------------------------------------------------------->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>

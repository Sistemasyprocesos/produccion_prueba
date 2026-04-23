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


$consulta="select 
    c.id,
    c.identificacion,
    c.direccion,
    c.tipo_identi as tipo_doc,
    l.id as tipo,
    l.nombre as nombretipo,
    c.razon_social,
    e.nom as estado,
    e.id as estado_id
       from prod_clientes as c 
        inner join prod_tipo_cliente as l 
            on l.id=c.tipo
        inner join prod_estados as e 
            on e.id=c.estado   
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

<table class="table mt-5 table-sm shadow" id="tblcolab">
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
              data-tipodoc="<?= $row["tipo_doc"]?>"
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
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

<form action="../procedimiento/newclt.php" method="post" onsubmit="return validarIdentificacion('tipo_documento','iden')">

<div class="modal-header" style="background-color: #198754; color: white;">
  <h5 class="modal-title">Registro de cliente</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <div class="row mb-3">
    <div class="col-6">
      <label class="form-label">Razon Social</label>
      <input type="text" class="form-control" required name="razon_social" id="nuevoclient">
          <div class="invalid-feedback">
                   Este cliente ya existe!
          </div>
    </div>


<!------------------------->
    <div class="col-6">
      <label class="form-label">Tipo de documento</label>
        <select class="form-select" name="tipo_documento" id="tipo_documento" required>
            <option></option>
              <?php 
                $p=$conn->query("select idtipoidenti,tipo from prod_tipo_identi order by tipo asc");
                if ($p->num_rows > 0) {
                    while($row = $p->fetch_assoc()) { ?>
                        <option value="<?=$row['idtipoidenti'] ?>"><?= $row['tipo']?></option>
                    <?php   }
                }
              ?>
        </select>
    </div>
  </div>

  <div class="row mb-3">
<div class="col-6">
      <label class="form-label">Identificación</label>
      <input type="text" class="form-control"   required id="iden" name="iden">
    </div>

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

<div class="modal-footer justify-content-center">
  <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill"></i> Guardar</button>
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill"></i> Cancelar</button>
</div>

</form>

    </div>
  </div>
</div>



<!--------------MODAL EDITAR--------------------->

<div class="modal fade" tabindex="-1" id="modaleditar" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

<form action="../procedimiento/editclt.php" method="post" onsubmit="return validarIdentificacion('tipo_doc_edit','identi')">

<div class="modal-header" style="background-color: #198754; color: white;">
  <h5 class="modal-title">Editar informacion de cliente</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
  <div class="row mb-3">
    <div class="col-6">

    <input type="hidden" name="idclient" id="identcli">

      <label class="form-label">Razon Social</label>
      <input type="text" class="form-control" name="rs" id="rs">
    </div>
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
 
  </div>

  <div class="row mb-3">

    <div class="col-6">
      <label class="form-label">Tipo de Identificación</label>
    <select class="form-select" name="tipo_doc_edit" id="tipo_doc_edit">
        <?php
        $cl=$conn->query("select idtipoidenti,tipo from prod_tipo_identi order by tipo asc");
        if ($cl->num_rows > 0) {
            while($row = $cl->fetch_assoc()) { ?>
                <option value="<?=$row['idtipoidenti'] ?>"><?= $row['tipo']?></option>
            <?php   }
        }
        ?>
      </select>
    </div>
       <div class="col-6">
            <label class="form-label">Identificación</label>
            <input type="text" class="form-control" name="identi" id="identi">
      </div>

  </div>





  <div class="row mb-3">
    <div class="col-12">
      <label class="form-label">Direccion</label>
      <input type="text" class="form-control" name="dir" id="dir">
    </div>
  </div>


<div class="row mb-3">
  <div class="col-6">
      <label class="form-label">Estado</label>
      <select class="form-select" name="est" id="est">
        <?php
        $est=$conn->query("select id,nom from prod_estados order by nom asc");
        if ($est->num_rows > 0) {
            while($row = $est->fetch_assoc()) { ?>
                <option value="<?=$row['id'] ?>"><?= $row['nom']?></option>
            <?php   }
        }
        ?>
      </select>

   </div>
</div>
<div class="modal-footer justify-content-center">
  <button type="submit" class="btn btn-primary"><i class="bi bi-floppy-fill"></i> Guardar</button>
  <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle-fill"></i> Cancelar</button>
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
const tipodoc = button.getAttribute('data-tipodoc');
document.getElementById('tipo_doc_edit').value = tipodoc;
// Pasar info a los inputs del modal
  document.getElementById('identcli').value = id;//id de identificacion
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




<!---------------VERIFICA DISPONIBILIDAD DE LA CATEGORIA-------------------->
<script>
const inputCategoria = document.getElementById('nuevoclient');

inputCategoria.addEventListener('keyup', function () {

    const valor = this.value.trim();

    if (valor.length < 2) return; // evita spam

    fetch('verificar_cliente.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'cliente=' + encodeURIComponent(valor)
    })
    .then(res => res.json())
    .then(data => {

     
if (data.existe) {
    inputCategoria.classList.add('is-invalid');
} else {
    inputCategoria.classList.remove('is-invalid');
}

    });

});
</script>

<script>
document.getElementById("tipo_documento").addEventListener("change", function() {
    const tipo = this.value;
    const input = document.getElementById("iden");

    input.value = "";

    if (tipo === "1") {
        input.maxLength = 10;
    } else if (tipo === "2") {
        input.maxLength = 13;
    } else if (tipo === "3") {
        input.maxLength = 9;
    } else {
        input.removeAttribute("maxLength");
    }
});



document.getElementById("tipo_doc_edit").addEventListener("change", function() {
    const tipo = this.value;
    const input = document.getElementById("identi");

    input.value = "";

    if (tipo === "1") {
        input.maxLength = 10;
    } else if (tipo === "2") {
        input.maxLength = 13;
    } else if (tipo === "3") {
        input.maxLength = 9;
    } else {
        input.removeAttribute("maxLength");
    }
});
</script>


<script>
let validando = false;

function validarIdentificacion(tipoId, inputId) {

    const tipo  = document.getElementById(tipoId).value;
    const input = document.getElementById(inputId);
    const valor = input.value;

    if (tipo === "1" && valor.length !== 10) {
        Swal.fire("Error", "La cédula debe tener 10 dígitos", "warning");
        input.focus();
        return false;
    }

    if (tipo === "2") {
        if (valor.length !== 13) {
            Swal.fire("Error", "El RUC debe tener 13 dígitos", "warning");
            input.focus();
            return false;
        }
        if (!valor.endsWith("001")) {
            Swal.fire("Error", "El RUC debe terminar en 001", "warning");
            input.focus();
            return false;
        }
    }

    if (tipo === "3" && valor.length !== 9) {
        Swal.fire("Error", "El pasaporte debe tener 9 dígitos", "warning");
        input.focus();
        return false;
    }

    return true;
}
</script>
<!------------------------------------------------------------------------------------->
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php $conn->close(); ?>
</body>
</html>

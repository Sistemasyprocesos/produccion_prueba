


<!DOCTYPE html>
<html lang="es">

<head>
  <title>Registro de Fases de Produccion</title>
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


$sql="SELECT
    p.id AS idproducto,
    p.nombre AS producto,
    f.secuencia AS sec,
    t.total_fases,
    e.abreviatura as envase,
    p.peso_prod,
    f.unds AS kg,
    f.proceso_id AS proce,
    u.sigla as umed,
    GROUP_CONCAT(a.abreviatura ORDER BY a.abreviatura SEPARATOR '+') AS act

FROM prod_fases_prod f

INNER JOIN prod_act_prod a 
    ON a.id = f.actividad

INNER JOIN prod_productos p 
    ON p.id = f.producto

inner join prod_envase as e 
    on e.id=p.envase 

inner join prod_udm as u
on p.udm=u.id

INNER JOIN (
    SELECT
        producto,
        proceso_id,
        COUNT(DISTINCT secuencia) AS total_fases
    FROM prod_fases_prod
    GROUP BY producto, proceso_id
) t 
    ON t.producto = f.producto
    AND t.proceso_id = f.proceso_id

GROUP BY
    p.id,
    p.nombre,
    f.proceso_id,
    f.secuencia,
    f.unds,
    t.total_fases

ORDER BY
    p.nombre,
    f.proceso_id,
    f.secuencia;
";

$res=$conn->query($sql);
?>

<main class="container-fluid pt-5 mt-3">
  <h1>Fases de Producción</h1>

  <div class="container mt-2">
    <div class="row mb-3">
      <div class="col-2">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalnuevo">
          <i class="bi bi-plus-square"></i> Agregar Fases de Producción
        </button>
      </div>
      <div class="col-4">
        <input type="text" id="Buscador" class="form-control mb-3" placeholder="Buscar...">
      </div>
    </div>

<table class="table mt-5 table-bordered table-hover table-sm" id="tblcolab">
  <thead class="table-dark">
    <tr>
      <th>PRODUCTO PT</th>
      <th>ORDEN OP</th>
      <th>Produccion Estandar</th>
      
      <th></th>
    </tr>
  </thead>
  <tbody><?php if ($res && $res->num_rows > 0) { ?>

  <?php while ($f = $res->fetch_assoc()) { ?>
    <tr>
      <td><?=$f['producto']?></td>

      <td><?= $f['producto'].' ('.$f['sec'].'/'.$f['total_fases'].') '.$f['act'] ?></td>

      <td><?= $f['kg'].' KG' ?></td>
    <td>

<?php if($f['sec'] == 1){ ?>

  <!-- EDITAR -->
  <button 
    class="btn btn-primary btn-sm btnEditarProceso"
    data-proceso="<?=htmlspecialchars($f['proce'], ENT_QUOTES, 'UTF-8') ?>"
    data-bs-toggle="modal"
    data-bs-target="#modaleditar">
    <i class="bi bi-pencil-square"></i>
  </button>

  <!-- ELIMINAR -->
  <button 
    class="btn btn-danger btn-sm btnEliminarProceso"
    data-proceso="<?=$f['proce']?>">
    <i class="bi bi-trash3"></i>
  </button>

<?php } ?>

</td>
    </tr>
  <?php } ?>

<?php } else { ?>
  <tr>
    <td colspan="4" class="text-center">No hay registros</td>
  </tr>
<?php } ?>

  </tbody>
</table>

<nav>
  <?php include '../complemento/paginator.php' ?>
  <ul class="pagination justify-content-center" id="pagination"></ul>
</nav>

</div>
</main>

<!-- ================= MODAL NUEVO ================= -->
<div class="modal fade" tabindex="-1" id="modalnuevo">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header" style="background-color: #198754; color: white;">
        <h5 class="modal-title">Registro de Fases de Produccion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="formfases">

          <div class="row mb-3">
            
          
            <div class="col-6">
              <label class="form-label">Producto</label>
                <?php   $g=$conn->query("
                select 
                p.id,
                p.nombre,
                p.peso_prod, 
                u.sigla,
                e.abreviatura
                from  prod_productos as p inner join prod_udm as u 
                  on p.udm=u.id
                inner join prod_envase as e 
                  on e.id=p.envase
                order by nombre asc"
                 );   ?>
              <select name="producto" id="producto" class="form-select" required>
                <?php  while($r=$g->fetch_assoc()){   ?>
                  <option value="<?=htmlspecialchars($r['id'], ENT_QUOTES, 'UTF-8') ?>"><?=$r['nombre'] ?></option>
                <?php } ?>
              </select>
            </div>
        
          </div>
          <!----BOTON DE AGG FASE------->
          <div class="row mb-3">
          <div class="col-6">
            <button type="button" class="btn btn-primary btn-sm" id="btnAgregarFilaModal"><i class="bi bi-plus-circle-fill"></i> Agregar Fase</button>
          </div>
</div>
          <!---------->
          <table class="table table-bordered" id="tablaDescuentosModal">
            <thead class="table-dark">
              <tr>
                <th>Secuencia</th>
                <th>Tipo de Fase</th>
                <th>Area de Produccion</th>
                <th>Actividad</th>
                <th>Unidades</th>
                <th>Envase</th>
                <th>Peso Envase</th>
                <th>UDM envase</th>
                
                <th>HC std</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <!---SECUENCIA---->
               <td class="cuota">
                  <span class="num">1</span>
                  <input type="hidden" name="secuencia[]" value="1">
                </td>

      <td>
        <?php
            $g=$conn->query("select cod,nombre,abreviatura from  prod_tipo_prod order by abreviatura asc");
            ?>
<!---------TIPO------------>
          <select name="tipo[]" id="tipo" required class="form-select">
            <?php
            while($r=$g->fetch_assoc()){
            ?>
            <option value="<?=$r['cod'] ?>"><?=$r['abreviatura'] ?></option>
            <?php } ?>
          </select></td>
                </td>
                   <td> <?php
            $g=$conn->query("select id,nombre from  prod_area_prod order by nombre asc");
            ?>
<!----------------AREA--------------->
          <select name="area[]" required id="area" class="form-select">
            <?php
            while($r=$g->fetch_assoc()){
            ?>
            <option value="<?=$r['id'] ?>"><?=$r['nombre'] ?></option>
            <?php } ?>
          </select></td>
                
       <td>
  <div class="actividad-container">

    <div class="actividad-item input-group mb-2">
<!-----ACTIVIDAD------------>
      <select name="act[0][]" required class="form-select actividad-select">
        <?php
        $g=$conn->query("select id,nombre from prod_act_prod order by nombre asc");
        while($r=$g->fetch_assoc()){
        ?>
          <option value="<?=$r['id']?>"><?=$r['nombre']?></option>
        <?php } ?>
      </select>

      <button type="button" class="btn btn-success btnAgregarActividad">
        <i class="bi bi-plus"></i>
      </button>

    </div>

  </div>
</td>
          


          <!---------UNIDADES----------->
    <td>
      <input type="number" min="0" step="0.01" required onkeypress="return solonum(event)" class="form-control" name="kgstd[]">
    </td>

<!---------envase----------->
          <td>
            <select class="form-select" required name="envase[]">
            <?php 
            $v=$conn->query("select id,nombre,abreviatura,estado from prod_envase order by nombre asc");
            while($t=$v->fetch_assoc()){
            ?>
            <option value="<?=$t['id'] ?>"><?=$t['nombre'] ?></option>
            <?php } ?>
            </select>
          </td>
          <!--------PESO ENVASE---------------------->
          <td>
            <input type="number" min="0" step="0.1" class="form-control" onkeypress="return solonum(event)" name="pesoenv[]">

          </td>
          
          <!----------UDM ENVASE-------------------------->
          <td>
            <select class="form-select" required name="udmenva[]">
              <option selected>-seleccione--</option>
            <?php
              $h=$conn->query("select id,sigla from prod_udm order by sigla asc");
              while($l=$h->fetch_assoc()){
            ?>
              <option value=<?=$l['id'] ?>><?=$l['sigla'] ?></option>
            <?php }   ?>
            </select>
          </td>



          
          <!---------CANTIDAD DE PERSONAS ESTANDAR----------->
          <td>
             <input type="number" min="0" step="1.0" onkeypress="return solonum(event)" required class="form-control" name="personas[]">
          </td>
                <td><button type="button" class="btn btn-sm btn-danger eliminar-fila"><i class="bi bi-trash"></i></button></td>
              </tr>
            </tbody>
          </table>

          

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>



<!-- ================= MODAL EDITAR ================= -->


<div class="modal fade" tabindex="-1" id="modaleditar">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header" style="background-color: #0d6efd; color: white;">
        <h5 class="modal-title">Editar Fases de Producción</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="loadingEditar" class="text-center py-4">
          <div class="spinner-border text-primary"></div>
          <p class="mt-2">Cargando fases...</p>
        </div>

        <form id="formEditar" style="display:none;">
          <input type="hidden" name="proceso_id" id="edit_proceso_id">

          <div class="row mb-3">
            <div class="col-6">
              <label class="form-label fw-bold">Producto</label>
              <input type="text" class="form-control" id="edit_producto_nombre" readonly>
            </div>
          </div>

          <table class="table table-bordered table-sm" id="tablaEditar">
            <thead class="table-dark">
              <tr>
                <th>Sec.</th>
                <th>Tipo de Fase</th>
                <th>Área de Producción</th>
                <th>Actividad(es)</th>
                <th>Unidades</th>
                <th>Envase</th>
                <th>Peso Envase</th>
                <th>UDM Env</th>
                
                <th>Personas Estándar</th>
              </tr>
            </thead>
            <tbody id="tbodyEditar">
            </tbody>
          </table>

          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Guardar Cambios
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>
<!-- ================= SCRIPTS ================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(function(){

  const modal = $('#modalnuevo');

  /* =========================
     AGREGAR NUEVA FASE (FILA)
  ========================== */
  modal.on('click','#btnAgregarFilaModal',function(){

    const tbody = $('#tablaDescuentosModal tbody');
    const fila  = tbody.find('tr:first').clone();

    // limpiar inputs
    fila.find('input').val('');

    // limpiar actividades
    fila.find('.actividad-container').html('');

    tbody.append(fila);

    actualizarIndices();
  });


  /* =========================
     ACTUALIZAR INDICES DE FILA
  ========================== */
  function actualizarIndices(){

    $('#tablaDescuentosModal tbody tr').each(function(i){

      // numero visible
      $(this).find('.num').text(i + 1);

      // secuencia oculta
      $(this).find('input[name="secuencia[]"]').val(i + 1);

      // reset actividades de esa fila
      const cont = $(this).find('.actividad-container');

      cont.html(`
        <div class="actividad-item input-group mb-2">

          <select name="act[${i}][]" class="form-select actividad-select">
            <?php
            $g=$conn->query("select id,nombre from prod_act_prod order by nombre asc");
            while($r=$g->fetch_assoc()){
            ?>
              <option value="<?=$r['id']?>"><?=$r['nombre']?></option>
            <?php } ?>
          </select>

          <button type="button" class="btn btn-success btnAgregarActividad">
            <i class="bi bi-plus"></i>
          </button>

        </div>
      `);

    });
  }


  /* =========================
     AGREGAR ACTIVIDAD MISMA FASE
  ========================== */
  modal.on('click','.btnAgregarActividad',function(){

    const fila = $(this).closest('tr');
    const index = fila.index();

    const nuevaActividad = `
      <div class="actividad-item input-group mb-2">

        <select name="act[${index}][]" class="form-select">
          <?php
          $g=$conn->query("select id,nombre from prod_act_prod order by nombre asc");
          while($r=$g->fetch_assoc()){
          ?>
            <option value="<?=$r['id']?>"><?=$r['nombre']?></option>
          <?php } ?>
        </select>

        <button type="button" class="btn btn-danger eliminarActividad">
          <i class="bi bi-trash"></i>
        </button>

      </div>
    `;

    fila.find('.actividad-container').append(nuevaActividad);

  });


  /* =========================
     ELIMINAR ACTIVIDAD
  ========================== */
  modal.on('click','.eliminarActividad',function(){
    $(this).closest('.actividad-item').remove();
  });


  /* =========================
     ELIMINAR FILA COMPLETA
  ========================== */
  modal.on('click','.eliminar-fila',function(){

    const tbody = $('#tablaDescuentosModal tbody');

    if(tbody.find('tr').length > 1){
      $(this).closest('tr').remove();
      actualizarIndices();
    }

  });


  /* =========================
     ENVIAR FORMULARIO A PHP
  ========================== */
  modal.on('submit','#formfases',function(e){

    e.preventDefault();

    $.ajax({
      url: '../procedimiento/guardar_fase.php', // ajusta ruta si es necesario
      type: 'POST',
      data: $(this).serialize(),

     success: function(res){
  Swal.fire({ icon: 'success', title: 'Guardado', timer: 1500, showConfirmButton: false })
    .then(() => location.reload());
},

    error: function(xhr){
  Swal.fire('Error', 'No se pudo guardar. Intente nuevamente.', 'error');
  console.log(xhr.responseText);
}
    });

  });

});
</script>

<!------BTN EDITAR-------------->
<script>
/* =============================================
   MODAL EDITAR — carga fases por proceso_id
   ============================================= */
document.addEventListener('click', function(e) {
  const btn = e.target.closest('.btnEditarProceso');


  if (!btn) return;

  const procesoId = btn.dataset.proceso;

  // reset modal
  document.getElementById('loadingEditar').style.display = 'block';
  document.getElementById('formEditar').style.display   = 'none';
  document.getElementById('tbodyEditar').innerHTML = '';





  // AJAX: cargar fases
  fetch('../procedimiento/get_fases_proceso.php?proceso_id=' + procesoId)
    .then(r => r.json())
    .then(data => {
      window.catalogoActividades = data.actividades;
      document.getElementById('loadingEditar').style.display = 'none';
      document.getElementById('formEditar').style.display    = 'block';
      document.getElementById('edit_proceso_id').value       = procesoId;
const p = data.producto;
      if (data.fases && data.fases.length > 0) {
       document.getElementById('edit_producto_nombre').value = 
    p.nombre;
      }

      const tbody = document.getElementById('tbodyEditar');

      data.fases.forEach(function(fase) {
        const actividades = fase.actividades.map(a =>
          `<span class="badge bg-primary me-1">${a.nombre}</span>`
        ).join('');

        const tr = document.createElement('tr');

let actividadesHTML = '';

fase.actividades.forEach(function(act, i){

  actividadesHTML += `
  <div class="actividad-item input-group mb-1">

    <select name="actividad[${fase.secuencia}][]" class="form-select form-select-sm">

      ${data.actividades.map(a => {

        const selected = (a.nombre == act.nombre) ? 'selected' : '';

        return `<option value="${a.id}" ${selected}>${a.nombre}</option>`;

      }).join('')}

    </select>

    ${i === 0 
      ? `<button type="button" class="btn btn-success btnAgregarActividadEditar">
           <i class="bi bi-plus"></i>
         </button>`
      : `<button type="button" class="btn btn-danger eliminarActividadEditar">
           <i class="bi bi-trash"></i>
         </button>`
    }

  </div>
  `;
});

tr.innerHTML = `
<td class="text-center fw-bold">${fase.secuencia}</td>

<td>
<select name="tipo[${fase.secuencia}]" class="form-select form-select-sm">
${data.tipos.map(t =>
`<option value="${t.cod}" ${t.cod == fase.tipo_fase ? 'selected' : ''}>
${t.abreviatura}
</option>`).join('')}
</select>
</td>


<td>
<select name="area[${fase.secuencia}]" class="form-select form-select-sm">
${data.areas.map(a =>
`<option value="${a.id}" ${a.id == fase.area ? 'selected' : ''}>
${a.nombre}
</option>`).join('')}
</select>
</td>

<td>
<div class="actividad-container">
${actividadesHTML}
</div>
</td>

<td>
<input type="number" step="0.01" min="0"
name="kgstd[${fase.secuencia}]"
value="${fase.unds}"
class="form-control form-control-sm">
</td>



<td>
<select name="envase[${fase.secuencia}]" class="form-select form-select-sm">
${data.envases.map(v =>
`<option value="${v.id}" ${v.id == fase.envase ? 'selected' : ''}>
${v.nombre}
</option>`).join('')}
</select>
</td>

<td>
  <input type="number" step="0.1" min="0"
    name="pesoenv[${fase.secuencia}]"
    value="${fase.peso_env ?? ''}"
    class="form-control form-control-sm">
</td>

<td>
  <select name="udmenv[${fase.secuencia}]" class="form-select form-select-sm">
    ${data.udms.map(u =>
      `<option value="${u.id}" ${u.sigla == fase.sigla ? 'selected' : ''}>
        ${u.sigla}
      </option>`
    ).join('')}
  </select>
</td>


<td>
<input type="number" step="1" min="0"
name="personas[${fase.secuencia}]"
value="${fase.personas_std}"
class="form-control form-control-sm">
</td>
`;
        tbody.appendChild(tr);
      });
    })
    .catch(err => {
        document.getElementById('loadingEditar').style.display = 'none';
        Swal.fire('Error', 
        'No se pudieron cargar las fases. '
        + err,
        'error');
    });
});


/* =============================================
   SUBMIT EDITAR
   ============================================= */
document.addEventListener('submit', function(e) {
  if (e.target.id !== 'formEditar') return;
  e.preventDefault();

  const formData = new FormData(e.target);

  fetch('../procedimiento/actualizar_fase.php', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(res => {
    if (res.ok) {
      Swal.fire({ icon: 'success', title: 'Actualizado', timer: 1500, showConfirmButton: false })
        .then(() => location.reload());
    } else {
      Swal.fire('Error', res.msg || 'No se pudo actualizar.', 'error');
    }
  })
  .catch(() => Swal.fire('Error', 'Fallo de conexión.', 'error'));
});
</script>



<!------SCRIPT AGREGAR ACTIVIDAD---------------------->
<script>



/* =============================================
   AGREGAR ACTIVIDAD EN EDITAR
   ============================================= */

document.addEventListener("click", function(e){

  if(e.target.closest(".btnAgregarActividadEditar")){

    const btn = e.target.closest(".btnAgregarActividadEditar");

    const fila = btn.closest("tr");

    const contenedor = fila.querySelector(".actividad-container");

    const secuencia = fila.querySelector("td").innerText.trim();

    let opciones = "";

if(window.catalogoActividades){
  window.catalogoActividades.forEach(function(a){
    opciones += `<option value="${a.id}">${a.nombre}</option>`;
  });
}
    const nuevaActividad = document.createElement("div");

    nuevaActividad.className = "actividad-item input-group mb-1";

    nuevaActividad.innerHTML = `
      <select name="actividad[${secuencia}][]" class="form-select form-select-sm">
        ${opciones}
      </select>

      <button type="button" class="btn btn-danger eliminarActividadEditar">
        <i class="bi bi-trash"></i>
      </button>
    `;

    contenedor.appendChild(nuevaActividad);

  }

});


/* =============================================
   ELIMINAR ACTIVIDAD EN EDITAR
   ============================================= */

document.addEventListener("click", function(e){

  if(e.target.closest(".eliminarActividadEditar")){

    const btn = e.target.closest(".eliminarActividadEditar");

    const actividad = btn.closest(".actividad-item");

    actividad.remove();

  }

});


</script>
<!------------------------->
<!---------BTN ELIMINAR------------------>
<script>
document.addEventListener("click", function(e) {

  if (e.target.closest(".btnEliminarProceso")) {

    const btn = e.target.closest(".btnEliminarProceso");
    const codigo = btn.dataset.proceso;

    Swal.fire({
      title: 'Eliminar Fases de Producción',
      text: '¿Está seguro de eliminar las fases de producción de este producto?',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "fasesdel.php?codigo=" + codigo;
      }
    });

  }
});
</script>



<script>
function solonum(event) {
  const tecla = event.key;
  const input = event.target.value;

  // permitir números
  if (/[0-9]/.test(tecla)) {
    return true;
  }

  // permitir un solo punto decimal
  if (tecla === "." && !input.includes(".")) {
    return true;
  }

  return false;
}
  </script>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<!DOCTYPE html>
<html lang="es">

<head>
  <title>Reporte de planificacion</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
      
        /* Animación de entrada */
        .fila-animada {
          opacity: 0;
          transform: translateY(10px);
          animation: aparecer 0.4s ease forwards;
        }

        @keyframes aparecer {
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        /* Animación al desaparecer */
        .fade-out {
          opacity: 0;
          transform: translateY(-10px);
          transition: all 0.2s ease;
        }


        .listado-scroll {
          max-height: 350px;   /* puedes ajustar */
          overflow-y: auto;
        }

        /* Scroll más bonito (opcional) */
        .listado-scroll::-webkit-scrollbar {
          width: 6px;
        }

        .listado-scroll::-webkit-scrollbar-thumb {
          background: #ccc;
          border-radius: 10px;
        }

        .listado-scroll::-webkit-scrollbar-thumb:hover {
          background: #999;
        }
    </style>

</head>

<body>

<?php include '../complemento/sidebar.php';
  include '../connection/conexion.php';

?>

<!-- CONTENIDO -->
<main class="container-fluid pt-5 mt-3">
  <h1 class="mt-1"><i class="fa-solid fa-clipboard-list" style="color: rgb(0, 0, 0);"></i> Reporte por producto</h1>
  <div class="row mt-1 gx-0">

  
<div class="col-md-12 ps-3">

    <!-- CONTROLES -->
    <div class="row g-2 mb-2 justify-content-center">


      <!-- FILTRO -->
      <div class="col-md-6">
        <div class="card shadow border-0 rounded-4 h-100">
          <div class="card-body">
            <div class="justify-content-end"> 
                 <label class="form-label fw-semibold text-center w-100 d-block">
                    <i class="fa-solid fa-filter"></i> Rango de fechas
                  </label>
            </div>
                <div class="row">
                    <div class="col-md-5">
                            <input class="form-control mt-2" id="fechadesde" name="desde" required type="date" placeholder="Desde">
                    </div>
                    <div class="col-md-5">
                            <input class="form-control mt-2" id="fechahasta" name="hasta" type="date" placeholder="Hasta">
                    </div>
                    <div class="col-md-2 mt-2">
                        <button class="btn btn-success  rounded-3 btnBuscarFecha"><i class="fa-solid fa-magnifying-glass" style="color: rgb(255, 255, 255);"></i></button>
                    </div>
                </div>    
            </div>
        </div>


      </div>
        <div class="col-md-2">
  <div class="card shadow border-0 rounded-4 h-100">
    <div class="card-body d-flex flex-column justify-content-center align-items-center">

        <label class="form-label fw-semibold mb-3">Imprimir reporte</label>

      <button class="btn btn-danger rounded-3">
        <i class="bi bi-file-earmark-pdf-fill"></i>
      </button>
   </div>
  </div>
</div>
    </div>
        <table class="table mt-3 shadow table-sm table-hover table-bordered" id="tblcolab">
          <thead>
            <tr>
              <th>FECHA PLANIFICADA</th>
              <th>PEDIDO</th>
              <th>ORDEN DE PRODUCCION</th>
              <th>TURNO</th>
              <th>UNIDADES ESTANDAR</th>
              <th>OBJETIVO</th>
              <th>KG PRODUCIDOS</th>
              <th>CUMPLIMIENTO</th>
              <th>HC</th>
            </tr>
        </thead>
      
<!-- TBODY — arranca vacío -->
<tbody id="tbodyReporte" class="table-group-divider">
  <tr id="filaVacia">
    <td colspan="9" class="text-center text-muted py-4">
      <i class="fa-solid fa-calendar-days me-2"></i>
      Seleccione un rango de fechas y presione buscar <i class="fa-solid fa-magnifying-glass" style="color: rgb(19, 15, 15);"></i>
    </td>
  </tr>

      </tbody>

        </table>

    <!----PAGINADOR------->
          <nav>
            
              <ul class="pagination justify-content-center" id="pagination"></ul>
          </nav>

    </div>
      </div>
</main>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const rowsPerPage = 10;
    const pagination = document.getElementById("pagination");

    let currentPage = 1;
    let allRows = [];      // ← ahora se llena desde el fetch, no del DOM

  // ---------- PAGINADOR ----------
  function displayRows() {
    const tbody = document.getElementById("tbodyReporte");
    tbody.innerHTML = "";

    const start = (currentPage - 1) * rowsPerPage;
    const end   = start + rowsPerPage;

    allRows.slice(start, end).forEach((row, index) => {
      row.classList.add("fila-animada");
      row.style.animationDelay = (index * 0.02) + "s";
      tbody.appendChild(row);
    });
  }

  function createPagination() {
    pagination.innerHTML = "";
    const totalPages = Math.ceil(allRows.length / rowsPerPage);
    if (totalPages <= 1) return;

    pagination.innerHTML += `
      <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="prev">Anterior</a>
      </li>`;

    for (let i = 1; i <= totalPages; i++) {
      pagination.innerHTML += `
        <li class="page-item ${i === currentPage ? 'active' : ''}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }

    pagination.innerHTML += `
      <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="next">Siguiente</a>
      </li>`;
  }

  function update() {
    displayRows();
    createPagination();
  }

  pagination.addEventListener("click", function (e) {
    e.preventDefault();
    const page = e.target.dataset.page;
    const totalPages = Math.ceil(allRows.length / rowsPerPage);

    if (page === "prev" && currentPage > 1) currentPage--;
    else if (page === "next" && currentPage < totalPages) currentPage++;
    else if (!isNaN(page)) currentPage = parseInt(page);

    update();
  });

  // ---------- FETCH ----------
  $(document).on("click", ".btnBuscarFecha", function () {

    const desde = $("#fechadesde").val();
    const hasta  = $("#fechahasta").val();

    if (!desde || !hasta) {
      Swal.fire({ icon: 'warning', title: 'Fechas requeridas', text: 'Debe seleccionar ambas fechas' });
      return;
    }

    const tbody = document.getElementById("tbodyReporte");

    tbody.innerHTML = `
      <tr>
        <td colspan="9" class="text-center py-4">
          <div class="spinner-border text-success" role="status">
          </div>
          <p class="mt-2 text-muted">Buscando datos...</p>
        </td>
      </tr>`;

    pagination.innerHTML = ""; // limpiar paginador mientras carga

    fetch("ajax_reporte_planificacion.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `desde=${encodeURIComponent(desde)}&hasta=${encodeURIComponent(hasta)}`
    })
    .then(res => res.json())
    .then(data => {

      if (data.error || data.length === 0) {
        allRows = [];
        tbody.innerHTML = `
          <tr>
            <td colspan="9" class="text-center text-muted py-4">
              <i class="fa-solid fa-circle-info me-2"></i>
              No se encontraron registros en ese rango de fechas
            </td>
          </tr>`;
        pagination.innerHTML = "";
        return;
      }

      // Construir elementos <tr> reales y guardarlos en allRows
      allRows = data.map(row => {
        const cumplimiento = parseFloat(row.cumplimiento) || 0;
        const hue = Math.round((Math.min(cumplimiento, 100) * 120) / 100);
        const badgeColor = `hsl(${hue}, 70%, 40%)`;

        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${row.fecha_turno ?? '—'}</td>
          <td>${row.idenpedido ?? '—'}</td>
          <td>${row.orden}</td>
          <td>${row.turno ?? '—'}</td>
          <td>${row.undsstd ?? '—'}</td>
          <td>${row.objetivo ?? '—'}</td>
          <td>${row.reales ?? '—'}</td>
          <td>
            <span class="badge rounded-pill px-3 py-2"
                  style="background:${badgeColor}; font-size:.85rem;">
              ${cumplimiento >= 100 ? cumplimiento +'%' : cumplimiento + '%'}
            </span>
          </td>
          <td>${row.personas ?? '—'} </td>
          `;
        return tr;
      });

      currentPage = 1;  // ← resetear siempre al buscar
      update();         // ← aquí pinta las primeras 10 y crea el paginador
    })
    .catch(() => {
      tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error al conectar con el servidor</td></tr>`;
    });
  });

});
</script>


<script>

// Agrega esto dentro del DOMContentLoaded, junto al resto del JS
document.querySelector('.btn.btn-danger').addEventListener('click', function () {
    const desde = document.getElementById('fechadesde').value;
    const hasta  = document.getElementById('fechahasta').value;

    if (!desde || !hasta) {
        Swal.fire({ icon: 'warning', title: 'Fechas requeridas', text: 'Seleccione ambas fechas antes de imprimir' });
        return;
    }

    const url = `pdf_reporte_planificacion.php?desde=${encodeURIComponent(desde)}&hasta=${encodeURIComponent(hasta)}`;
    window.open(url, '_blank');
});

  </script>

<!---------------------------->
</body>
</html>

<?php
require_once __DIR__ . '/../complemento/config.php';
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<nav class="navbar navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
        <img src="<?= BASE_URL ?>resources/images/ceg.png" height="50px"/> 
          Modulo Producción Cegranecsa</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasDarkNavbar" aria-labelledby="offcanvasDarkNavbarLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasDarkNavbarLabel">Menú</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
    
      <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="<?= BASE_URL ?>index.php">Inicio</a>
          </li>
         
          <!-------------------------------------------------------->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Configuracion </a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/clts.php"><i class="fa-solid fa-user-tie" style="color: rgb(243, 234, 234);"></i> Clientes</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/prods.php"><i class="fa-solid fa-box-open" style="color: rgb(243, 234, 234);"></i> Productos</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/categorias.php"><i class="fa-solid fa-table-list" style="color: rgb(243, 234, 234);"></i> Categorias</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/act.php"><i class="fa-solid fa-list" style="color: rgb(243, 234, 234);"></i> Actividades de Produccion</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/area.php"><i class="fa-solid fa-location-dot" style="color: rgb(243, 234, 234);"></i> Areas de produccion</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/env.php"><i class="fa-solid fa-bag-shopping" style="color: rgb(243, 234, 234);"></i> Envases</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/udm.php"><i class="fa-solid fa-scale-unbalanced" style="color: rgb(243, 234, 234);"></i> Unidades de Medida</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/fases.php"><i class="fa-solid fa-diagram-project" style="color: rgb(243, 234, 234);"></i> Fases de Produccion</a></li>
              </ul>
          </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Transaccion </a>
                  <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>operations/pedidos.php"><i class="bi bi-list-check"></i> Pedidos</a></li>
                    
                  </ul>
              </li>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Reportes </a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/planprod.php"><i class="bi bi-file-earmark-text"></i> Planificación</a></li>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>registers/repprod.php"><i class="bi bi-file-earmark-bar-graph"></i> Por Producto</a></li>
                <li><a class="dropdown-item" href=""><i class="bi bi-graph-up"></i> Standar</a></li>
                <li><a class="dropdown-item" href=""><i class="bi bi-graph-up"></i> Producto</a></li>
              </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
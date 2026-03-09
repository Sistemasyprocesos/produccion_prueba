<?php
include '../connection/conexion.php';

$id = intval($_POST['id']);

// Datos del pedido
$q = $conn->query("
  SELECT p.*, c.razon_social, pr.nombre as nom_producto, u.sigla
  FROM prod_pedidos p
  INNER JOIN prod_clientes c ON c.id = p.id_cliente
  INNER JOIN prod_productos pr ON pr.id = p.producto
  INNER JOIN prod_udm u ON u.id = p.und_medida
  WHERE p.id_pedido = $id
");
$p = $q->fetch_assoc();

// Fases del producto con avance actual
$fases = $conn->query("
  SELECT 
    f.secuencia,
  
    COALESCE(a.kg_real, '') as kg_real,
    COALESCE(a.fecha_registro, '') as fecha_prod,
   
    a.id as id_avance
  FROM prod_fases_prod f
  LEFT JOIN prod_avance_pedido a 
    ON a.id_pedido = $id AND a.secuencia = f.secuencia
  WHERE f.producto = {$p['producto']}
  ORDER BY f.secuencia ASC
");
?>

<form id="formEditar">
  <input type="hidden" name="id_pedido" value="<?= $p['id_pedido'] ?>">

  <!-- DATOS GENERALES DEL PEDIDO -->
  <div class="card mb-3 border-primary">
    <div class="card-header bg-primary text-white">
      <i class="bi bi-info-circle"></i> Datos del pedido
    </div>
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-6">
          <label class="form-label fw-semibold"># Pedido</label>
          <input type="text"  class="form-control border-0 shadow-none" value="<?= $p['num_pedido'] ?>" readonly>
        </div>
        <div class="col-6">
          <label class="form-label fw-semibold">Cliente</label>
          <select name="clte" class="form-select" required>
            <?php
            $cl = $conn->query("SELECT id, razon_social FROM prod_clientes WHERE estado=1");
            while($c = $cl->fetch_assoc()){
              $sel = ($c['id'] == $p['id_cliente']) ? 'selected' : '';
              echo "<option value='{$c['id']}' $sel>{$c['razon_social']}</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-6">
          <label class="form-label fw-semibold">Producto</label>
          <select name="prod" class="form-select" required>
            <?php
            $pr = $conn->query("SELECT id, nombre FROM prod_productos WHERE estado=1 AND fase=2");
            while($r = $pr->fetch_assoc()){
              $sel = ($r['id'] == $p['producto']) ? 'selected' : '';
              echo "<option value='{$r['id']}' $sel>{$r['nombre']}</option>";
            }
            ?>
          </select>
        </div>
        <div class="col-3">
          <label class="form-label fw-semibold">Cantidad</label>
          <input type="text" name="cant" class="form-control" value="<?= $p['cantidad'] ?>" required>
        </div>
        <div class="col-3">
          <label class="form-label fw-semibold">UM</label>
          <select name="unds" class="form-select">
            <?php
            $um = $conn->query("SELECT id, sigla FROM prod_udm ORDER BY sigla DESC");
            while($u = $um->fetch_assoc()){
              $sel = ($u['id'] == $p['und_medida']) ? 'selected' : '';
              echo "<option value='{$u['id']}' $sel>{$u['sigla']}</option>";
            }
            ?>
          </select>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-6">
          <label class="form-label fw-semibold">Fecha de registro</label>
          <input type="date" name="fechreg" class="form-control" value="<?= $p['fecha_registro'] ?>" required>
        </div>
        <div class="col-6">
          <label class="form-label fw-semibold">Fecha de entrega</label>
          <input type="date" name="fentreg" class="form-control" value="<?= $p['fecha_entrega'] ?>" required>
        </div>
      </div>
    </div>
  </div>

  <!-- FASES DE PRODUCCIÓN --
  <div class="card border-success">
    <div class="card-header bg-success text-white">
      <i class="bi bi-diagram-3"></i> Fases de producción
    </div>
    <div class="card-body p-2">
      <?php
      $i = 0;
      while($f = $fases->fetch_assoc()):
        $i++;
        $color = ($i % 2 == 0) ? '#f8f9fa' : '#ffffff';
      ?>
      <div class="border rounded p-2 mb-2" style="background:<?= $color ?>">
        <input type="hidden" name="fases[<?= $i ?>][secuencia]" value="<?= $f['secuencia'] ?>">

       
        <div class="row g-2">
          <div class="col-4">
            <label class="form-label small mb-1">Producido (kg/und)</label>
            <input type="number" step="0.01"
              name="fases[<?= $i ?>][kg_real]"
              class="form-control form-control-sm"
              value="<?= $f['kg_real'] ?>"
              placeholder="0.00">
          </div>
          <div class="col-4">
            <label class="form-label small mb-1">Fecha producción</label>
            <input type="date"
              name="fases[<?= $i ?>][fecha_prod]"
              class="form-control form-control-sm"
              value="<?= $f['fecha_prod'] ?>">
          </div>
        
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
----->
</form>
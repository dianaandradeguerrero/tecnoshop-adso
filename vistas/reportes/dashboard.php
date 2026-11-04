<?php require_once __DIR__ . '/../../vistas/layout/header.php'; ?>
<div class="card">
  <div class="card-header"><h2>📈 Ventas por mes — <?= date('Y') ?></h2></div>
  <div class="card-body"><canvas id="graficaLinea" height="120"></canvas></div>
</div>

<div class="card" x-data="reportesApp()" x-init="init()">
  <div class="card-header"><h2>🏆 Top 5 productos más vendidos</h2></div>
  <div class="card-body">
    <div x-show="cargando" class="spinner-wrap"><div class="spinner"></div></div>
    <div x-show="!cargando" class="table-responsive">
      <table>
        <thead><tr><th>#</th><th>Producto</th><th>Unidades</th><th>Ingresos</th></tr></thead>
        <tbody>
          <template x-for="(p,i) in topProductos" :key="p.nombre">
            <tr>
              <td x-text="['🥇','🥈','🥉','4️⃣','5️⃣'][i]"></td>
              <td><strong x-text="p.nombre"></strong></td>
              <td x-text="p.total_vendido + ' uds.'"></td>
              <td><strong x-text="'$' + Number(p.total_ingresos).toLocaleString('es-CO')"></strong></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card" x-data="reportesApp()" x-init="init()">
  <div class="card-header"><h2>⚠️ Productos con stock bajo</h2></div>
  <div class="card-body">
    <div x-show="!cargando" class="table-responsive">
      <table>
        <thead><tr><th>#</th><th>Producto</th><th>Marca</th><th>Stock</th><th>Precio</th></tr></thead>
        <tbody>
          <template x-for="p in stockBajo" :key="p.id">
            <tr>
              <td x-text="p.id"></td>
              <td><strong x-text="p.nombre"></strong></td>
              <td x-text="p.marca"></td>
              <td><span class="stock-bajo" x-text="p.stock + ' uds.'"></span></td>
              <td x-text="'$' + Number(p.precio).toLocaleString('es-CO')"></td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
  try {
    const r = await fetch('/tecnoshop/?modulo=reportes&accion=ventasPorMes');
    const j = await r.json();
    new Chart(document.getElementById('graficaLinea'), {
      type: 'line',
      data: {
        labels: j.datos.map(d => d.mes),
        datasets: [{ label:'Ventas COP',
          data: j.datos.map(d => Number(d.total)),
          borderColor:'#39A900', backgroundColor:'rgba(57,169,0,0.1)',
          borderWidth:3, pointRadius:6, fill:true, tension:0.4 }]
      },
      options: { responsive:true,
        plugins:{legend:{display:false}},
        scales:{y:{ticks:{callback:v=>'$'+(v/1000).toFixed(0)+'K'}}} }
    });
  } catch(e) {}
});
</script>
<?php require_once __DIR__ . '/../../vistas/layout/footer.php'; ?>

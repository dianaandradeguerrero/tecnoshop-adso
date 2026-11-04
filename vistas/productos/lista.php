<?php require_once __DIR__ . '/../../vistas/layout/header.php'; ?>
<div class="card" x-data="productosApp()">
  <template x-if="mensaje">
    <div :class="`alert alert-${tipoMsg}`" x-text="mensaje"></div>
  </template>
  <div class="card-header">
    <h2>📦 Lista de Productos</h2>
    <button class="btn btn-primary" @click="abrirFormulario()">
      <i class="bi bi-plus-lg"></i> Nuevo Producto
    </button>
  </div>
  <div class="card-body">
    <div x-show="cargando" class="spinner-wrap"><div class="spinner"></div> Cargando...</div>
    <div x-show="!cargando" class="table-responsive">
      <table>
        <thead><tr><th>#</th><th>Nombre</th><th>Marca</th><th>Precio</th><th>Stock</th><th>Categoría</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <template x-for="p in productos" :key="p.id">
            <tr>
              <td x-text="p.id"></td>
              <td><strong x-text="p.nombre"></strong></td>
              <td x-text="p.marca"></td>
              <td x-text="'$' + Number(p.precio).toLocaleString('es-CO')"></td>
              <td><span :class="p.stock < 5 ? 'stock-bajo' : ''" x-text="p.stock + ' uds.'"></span></td>
              <td x-text="p.categoria || '—'"></td>
              <td><span class="badge badge-success">Activo</span></td>
              <td style="display:flex;gap:6px;">
                <button class="btn btn-secondary btn-sm" @click="editar(p)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-danger btn-sm" @click="eliminar(p.id)"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </template>
          <template x-if="!cargando && productos.length===0">
            <tr><td colspan="8"><div class="empty-state"><p>No hay productos.</p></div></td></tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal formulario -->
  <div class="modal-overlay" x-show="mostrarModal" x-cloak>
    <div class="modal">
      <div class="modal-header">
        <h3 x-text="modoEdicion ? '✏️ Editar Producto' : '➕ Nuevo Producto'"></h3>
        <button class="btn-close" @click="cerrarModal()">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-grid-2">
          <div class="form-group"><label>Nombre *</label><input type="text" x-model="form.nombre" placeholder="Nombre del producto"></div>
          <div class="form-group"><label>Marca</label><input type="text" x-model="form.marca" placeholder="Marca"></div>
          <div class="form-group"><label>Precio *</label><input type="number" x-model="form.precio" placeholder="0" min="0"></div>
          <div class="form-group"><label>Stock</label><input type="number" x-model="form.stock" placeholder="0" min="0"></div>
        </div>
        <div class="form-group"><label>Descripción</label><textarea x-model="form.descripcion" rows="2" placeholder="Descripción del producto"></textarea></div>
        <div class="form-group"><label>Categoría</label>
          <select x-model="form.categoria_id">
            <option value="">Seleccionar categoría</option>
            <template x-for="c in categorias" :key="c.id">
              <option :value="c.id" x-text="c.nombre"></option>
            </template>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="cerrarModal()">Cancelar</button>
        <button class="btn btn-primary" @click="guardar()" x-text="modoEdicion?'Actualizar':'Guardar'"></button>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../../vistas/layout/footer.php'; ?>

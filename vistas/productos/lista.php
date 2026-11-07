<div class="card" x-data="productosApp()">
  <template x-if="mensaje">
    <div class="alert" :class="'alert-' + tipoMsg" x-text="mensaje"></div>
  </template>

  <div class="card-header">
    <div>
      <h2>📦 Lista de Productos</h2>
      <small>Administra el inventario de TecnoShop.</small>
    </div>
    <button type="button" class="btn btn-primary" @click="abrirFormulario()">
      <i class="bi bi-plus-lg"></i> Nuevo Producto
    </button>
  </div>

  <div class="card-body">
    <div x-show="cargando" class="spinner-wrap">
      <div class="spinner"></div> Cargando productos...
    </div>

    <div x-show="!cargando" class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Marca</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="producto in productos" :key="producto.id">
            <tr>
              <td x-text="'#' + producto.id"></td>
              <td>
                <strong x-text="producto.nombre"></strong>
                <small
                  x-show="producto.descripcion"
                  style="display:block; color:#64748b; max-width:260px;"
                  x-text="producto.descripcion">
                </small>
              </td>
              <td x-text="producto.categoria || 'Sin categoría'"></td>
              <td x-text="producto.marca || '—'"></td>
              <td x-text="'$' + Number(producto.precio || 0).toLocaleString('es-CO')"></td>
              <td>
                <span
                  class="badge"
                  :class="Number(producto.stock) < 5 ? 'badge-danger' : 'badge-success'"
                  x-text="producto.stock">
                </span>
              </td>
              <td>
                <span class="badge badge-success" x-text="producto.estado || 'activo'"></span>
              </td>
              <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                  <button
                    type="button"
                    class="btn btn-secondary btn-sm"
                    @click="editar(producto)">
                    Editar
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click="eliminar(producto.id)">
                    Desactivar
                  </button>
                </div>
              </td>
            </tr>
          </template>

          <tr x-show="productos.length === 0 && !cargando">
            <td colspan="8" style="text-align:center; padding:20px;">
              No hay productos registrados.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal para crear o editar productos -->
  <div
    class="modal-overlay"
    x-show="mostrarModal"
    x-cloak
    style="display:none;"
    @click.self="cerrarModal()">

    <div class="modal">
      <div class="modal-header">
        <h3 x-text="modoEdicion ? 'Editar producto' : 'Nuevo producto'"></h3>
        <button type="button" class="btn-close" @click="cerrarModal()">✕</button>
      </div>

      <form @submit.prevent="guardar()">
        <div class="modal-body">
          <div class="form-group">
            <label for="producto_nombre">Nombre *</label>
            <input
              id="producto_nombre"
              type="text"
              x-model.trim="form.nombre"
              maxlength="150"
              required
              placeholder="Nombre del producto">
          </div>

          <div class="form-group">
            <label for="producto_descripcion">Descripción</label>
            <textarea
              id="producto_descripcion"
              x-model="form.descripcion"
              rows="3"
              placeholder="Descripción del producto"></textarea>
          </div>

          <div class="form-group">
            <label for="producto_categoria">Categoría</label>
            <select id="producto_categoria" x-model="form.categoria_id">
              <option value="">Selecciona una categoría</option>
              <template x-for="categoria in categorias" :key="categoria.id">
                <option
                  :value="String(categoria.id)"
                  x-text="categoria.nombre">
                </option>
              </template>
            </select>
          </div>

          <div class="form-group">
            <label for="producto_marca">Marca</label>
            <input
              id="producto_marca"
              type="text"
              x-model.trim="form.marca"
              maxlength="100"
              placeholder="Marca">
          </div>

          <div class="form-group">
            <label for="producto_precio">Precio *</label>
            <input
              id="producto_precio"
              type="number"
              x-model.number="form.precio"
              min="0.01"
              step="0.01"
              required
              placeholder="Ejemplo: 35000">
          </div>

          <div class="form-group">
            <label for="producto_stock">Stock</label>
            <input
              id="producto_stock"
              type="number"
              x-model.number="form.stock"
              min="0"
              step="1"
              placeholder="Cantidad disponible">
          </div>
        </div>

        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="cerrarModal()">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <span x-text="modoEdicion ? 'Guardar cambios' : 'Registrar producto'"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="card" x-data="ventasApp()">
  <template x-if="mensaje">
    <div class="alert" :class="'alert-' + tipoMsg" x-text="mensaje"></div>
  </template>

  <div class="card-header">
    <h2>🧾 Lista de Ventas</h2>
    <button type="button" class="btn btn-primary" @click="abrirFormulario()">
      <i class="bi bi-plus-lg"></i> Nueva Venta
    </button>
  </div>

  <div class="card-body">
    <div x-show="cargando" class="spinner-wrap">
      <div class="spinner"></div> Cargando...
    </div>

    <div x-show="!cargando" class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="v in ventas" :key="v.id">
            <tr>
              <td x-text="'#' + v.id"></td>
              <td x-text="v.cliente || '—'"></td>
              <td x-text="v.fecha || '—'"></td>
              <td x-text="'$' + Number(v.total).toLocaleString('es-CO')"></td>
              <td>
                <span
                  class="badge"
                  :class="v.estado === 'activa' ? 'badge-success' : 'badge-danger'"
                  x-text="v.estado">
                </span>
              </td>
              <td>
                <button
                  type="button"
                  class="btn btn-danger btn-sm"
                  x-show="v.estado === 'activa'"
                  @click="cancelar(v.id)">
                  Anular
                </button>
              </td>
            </tr>
          </template>

          <tr x-show="ventas.length === 0 && !cargando">
            <td colspan="6" style="text-align:center; padding:20px;">
              No hay ventas registradas.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal para registrar una venta con varios productos -->
  <div
    class="modal-overlay"
    x-show="mostrarModal"
    x-cloak
    style="display:none;"
    @click.self="cerrarModal()">

    <div class="modal">
      <div class="modal-header">
        <h3>➕ Nueva Venta</h3>
        <button type="button" class="btn-close" @click="cerrarModal()">✕</button>
      </div>

      <form @submit.prevent="guardar()">
        <div class="modal-body">
          <!-- Selección del cliente -->
          <div class="form-group">
            <label for="venta_cliente">Cliente *</label>
            <select
              id="venta_cliente"
              x-model="form.cliente_id"
              required>
              <option value="">Selecciona un cliente</option>
              <template x-for="c in clientes" :key="c.id">
                <option
                  :value="String(c.id)"
                  x-text="c.nombre + ' — ' + (c.documento || 'Sin documento')">
                </option>
              </template>
            </select>

            <small x-show="cargandoClientes">
              Cargando clientes...
            </small>
          </div>

          <!-- Productos de la venta -->
          <div class="form-group" style="margin-top:18px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:10px;">
              <label style="margin:0;">Productos *</label>
              <button
                type="button"
                class="btn btn-secondary btn-sm"
                @click="agregarProducto()"
                :disabled="cargandoProductos || productos.length === 0">
                <i class="bi bi-plus-lg"></i> Agregar producto
              </button>
            </div>

            <small x-show="cargandoProductos">
              Cargando productos...
            </small>

            <small
              x-show="!cargandoProductos && productos.length === 0"
              style="display:block; color:#b42318;">
              No hay productos disponibles.
            </small>

            <div class="table-responsive" style="margin-top:8px;">
              <table>
                <thead>
                  <tr>
                    <th style="min-width:220px;">Producto</th>
                    <th>Precio unitario</th>
                    <th style="width:110px;">Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acción</th>
                  </tr>
                </thead>

                <tbody>
                  <template x-for="(detalle, indice) in detalles" :key="indice">
                    <tr>
                      <td>
                        <select
                          :id="'venta_producto_' + indice"
                          x-model="detalle.producto_id"
                          required
                          style="min-width:210px;">
                          <option value="">Selecciona un producto</option>
                          <template x-for="p in productos" :key="p.id">
                            <option
                              :value="String(p.id)"
                              :disabled="Number(p.stock) < 1"
                              x-text="p.nombre + ' — Stock: ' + p.stock">
                            </option>
                          </template>
                        </select>
                      </td>

                      <td>
                        <span
                          x-text="detalle.producto_id
                            ? '$' + Number((productos.find(p => Number(p.id) === Number(detalle.producto_id)) || {}).precio || 0).toLocaleString('es-CO')
                            : '—'">
                        </span>
                      </td>

                      <td>
                        <input
                          type="number"
                          min="1"
                          step="1"
                          x-model.number="detalle.cantidad"
                          required
                          style="width:90px;">
                      </td>

                      <td>
                        <strong
                          x-text="'$' + Number(subtotalDetalle(detalle)).toLocaleString('es-CO')">
                        </strong>
                      </td>

                      <td>
                        <button
                          type="button"
                          class="btn btn-danger btn-sm"
                          @click="quitarProducto(indice)"
                          :disabled="detalles.length <= 1"
                          title="Quitar producto">
                          Quitar
                        </button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Total calculado automáticamente -->
          <div
            style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding:14px 16px; margin-top:16px; border-radius:8px; background:#f4f6f8;">
            <strong>Total de la venta</strong>
            <strong
              style="font-size:1.25rem;"
              x-text="'$' + Number(totalVenta).toLocaleString('es-CO')">
            </strong>
          </div>

          <p style="margin-top:12px;">
            La venta se registrará con estado activa. El inventario se actualizará al confirmar la venta.
          </p>
        </div>

        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="cerrarModal()">
            Cancelar
          </button>

          <button
            type="submit"
            class="btn btn-primary"
            :disabled="guardando || clientes.length === 0 || productos.length === 0 || detalles.length === 0">
            <span x-text="guardando ? 'Guardando...' : 'Registrar venta'"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

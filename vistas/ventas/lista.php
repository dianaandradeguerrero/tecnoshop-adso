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
                <span class="badge"
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

  <!-- Modal para registrar una venta -->
  <div class="modal-overlay"
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
          <div class="form-group">
            <label for="venta_cliente">Cliente *</label>
            <select id="venta_cliente"
              x-model="form.cliente_id"
              required>
              <option value="">Selecciona un cliente</option>
              <template x-for="c in clientes" :key="c.id">
                <option :value="String(c.id)"
                  x-text="c.nombre + ' — ' + c.documento">
                </option>
              </template>
            </select>

            <small x-show="cargandoClientes">
              Cargando clientes...
            </small>
          </div>

          <div class="form-group">
            <label for="venta_total">Total de la venta *</label>
            <input id="venta_total"
              type="number"
              min="1"
              step="0.01"
              x-model="form.total"
              placeholder="Ejemplo: 20000"
              required>
          </div>

          <p>La venta se registrará con estado activa.</p>
        </div>

        <div class="modal-footer">
          <button type="button"
            class="btn btn-secondary"
            @click="cerrarModal()">
            Cancelar
          </button>

          <button type="submit"
            class="btn btn-primary"
            :disabled="guardando || clientes.length === 0">
            <span x-text="guardando ? 'Guardando...' : 'Registrar venta'"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
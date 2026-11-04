<?php require_once __DIR__ . '/../../vistas/layout/header.php'; ?>
<div class="card" x-data="clientesApp()">
  <template x-if="mensaje">
    <div :class="`alert alert-${tipoMsg}`" x-text="mensaje"></div>
  </template>
  <div class="card-header">
    <h2>👥 Lista de Clientes</h2>
    <button class="btn btn-primary" @click="abrirFormulario()"><i class="bi bi-plus-lg"></i> Nuevo Cliente</button>
  </div>
  <div class="card-body">
    <div x-show="cargando" class="spinner-wrap"><div class="spinner"></div> Cargando...</div>
    <div x-show="!cargando" class="table-responsive">
      <table>
        <thead><tr><th>#</th><th>Nombre</th><th>Documento</th><th>Email</th><th>Teléfono</th><th>Estado</th><th>Acciones</th></tr></thead>
        <tbody>
          <template x-for="c in clientes" :key="c.id">
            <tr>
              <td x-text="c.id"></td>
              <td><strong x-text="c.nombre + ' ' + c.apellido"></strong></td>
              <td x-text="c.documento"></td>
              <td x-text="c.email || '—'"></td>
              <td x-text="c.telefono || '—'"></td>
              <td><span :class="c.estado==='activo'?'badge badge-success':'badge badge-danger'" x-text="c.estado"></span></td>
              <td style="display:flex;gap:6px;flex-wrap:wrap;">
                <button class="btn btn-secondary btn-sm" @click="editar(c)"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-primary btn-sm" @click="verSugerenciasIA(c)" title="Sugerencias IA">🤖</button>
                <button class="btn btn-danger btn-sm" @click="eliminar(c.id)"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal IA -->
  <div class="modal-overlay" x-show="mostrarIA" x-cloak>
    <div class="modal">
      <div class="modal-header">
        <h3>🤖 Sugerencias IA — <span x-text="clienteSeleccionado?.nombre"></span></h3>
        <button class="btn-close" @click="mostrarIA=false">✕</button>
      </div>
      <div class="modal-body">
        <div x-show="cargandoIA" class="spinner-wrap"><div class="spinner"></div> Consultando Gemini IA...</div>
        <template x-if="!cargandoIA">
          <div>
            <p style="margin-bottom:12px;font-size:13px;color:#666;">Basado en el historial, la IA sugiere:</p>
            <template x-for="(s,i) in sugerencias" :key="i">
              <div class="sugerencia-item"><span x-text="(i+1)+'. '+s"></span></div>
            </template>
            <p style="margin-top:12px;font-size:11px;color:#888;font-style:italic;">💡 Generado por Gemini IA. El criterio final es del vendedor.</p>
          </div>
        </template>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" @click="mostrarIA=false">Cerrar</button></div>
    </div>
  </div>

  <!-- Modal formulario -->
  <div class="modal-overlay" x-show="mostrarModal" x-cloak>
    <div class="modal">
      <div class="modal-header">
        <h3 x-text="modoEdicion ? '✏️ Editar Cliente' : '➕ Nuevo Cliente'"></h3>
        <button class="btn-close" @click="cerrarModal()">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-grid-2">
          <div class="form-group"><label>Nombre *</label><input type="text" x-model="form.nombre"></div>
          <div class="form-group"><label>Apellido *</label><input type="text" x-model="form.apellido"></div>
          <div class="form-group"><label>Documento *</label><input type="text" x-model="form.documento"></div>
          <div class="form-group"><label>Teléfono</label><input type="text" x-model="form.telefono"></div>
          <div class="form-group"><label>Email</label><input type="email" x-model="form.email"></div>
          <div class="form-group"><label>Estado</label>
            <select x-model="form.estado"><option value="activo">Activo</option><option value="inactivo">Inactivo</option></select>
          </div>
        </div>
        <div class="form-group"><label>Dirección</label><input type="text" x-model="form.direccion"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="cerrarModal()">Cancelar</button>
        <button class="btn btn-primary" @click="guardar()" x-text="modoEdicion?'Actualizar':'Guardar'"></button>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../../vistas/layout/footer.php'; ?>

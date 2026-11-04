/**
 * app.js — Funciones Alpine.js TecnoShop
 * CRUD + IA + Reportes
 * TecnoShop · SENA ADSO · Juan Carlos Huertas Niño · 2026
 */

var API_URL_URL = '/tecnoshop_vscode/index.php';

/** Helper para mostrar alertas */
function crearAlerta(ctx, mensaje, tipo = 'success') {
  ctx.mensaje = mensaje;
  ctx.tipoMsg = tipo;
  setTimeout(() => {
    ctx.mensaje = '';
  }, 4000);
}

// ══════════════════════════════════════════════════════
// PRODUCTOS
// ══════════════════════════════════════════════════════
function productosApp() {
  return {
    productos: [],
    categorias: [],
    cargando: true,
    mostrarModal: false,
    modoEdicion: false,
    mensaje: '',
    tipoMsg: 'success',
    form: {
      id: null,
      nombre: '',
      descripcion: '',
      precio: '',
      stock: '',
      marca: '',
      categoria_id: '',
    },

    async init() {
      await this.cargar();
      await this.cargarCategorias();
    },

    async cargar() {
      this.cargando = true;
      try {
        const res = await fetch(`${API_URL_URL_URL}?modulo=productos&accion=obtenerTodos`);
        const json = await res.json();
        this.productos = json.ok ? json.datos : [];
      } catch (e) {
        crearAlerta(this, 'Error de conexión.', 'danger');
      } finally {
        this.cargando = false;
      }
    },

    async cargarCategorias() {
      const res = await fetch(`${API_URL_URL_URL}?modulo=productos&accion=categorias`);
      const json = await res.json();
      this.categorias = json.ok ? json.datos : [];
    },

    abrirFormulario() {
      this.modoEdicion = false;
      this.form = {
        id: null,
        nombre: '',
        descripcion: '',
        precio: '',
        stock: '',
        marca: '',
        categoria_id: '',
      };
      this.mostrarModal = true;
    },

    editar(p) {
      this.modoEdicion = true;
      this.form = { ...p };
      this.mostrarModal = true;
    },

    cerrarModal() {
      this.mostrarModal = false;
    },

    async guardar() {
      if (!this.form.nombre) {
        crearAlerta(this, 'El nombre es obligatorio.', 'warning');
        return;
      }
      if (this.form.precio <= 0) {
        crearAlerta(this, 'El precio debe ser mayor a 0.', 'warning');
        return;
      }
      const accion = this.modoEdicion ? 'actualizar' : 'guardar';
      try {
        const res = await fetch(`${API_URL_URL_URL}?modulo=productos&accion=${accion}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        });
        const json = await res.json();
        crearAlerta(this, json.mensaje, json.ok ? 'success' : 'danger');
        if (json.ok) {
          this.cerrarModal();
          await this.cargar();
        }
      } catch (e) {
        crearAlerta(this, 'Error al guardar.', 'danger');
      }
    },

    async eliminar(id) {
      if (!confirm('¿Eliminar este producto?')) return;
      try {
        const res = await fetch(`${API_URL_URL_URL}?modulo=productos&accion=eliminar&id=${id}`, {
          method: 'DELETE',
        });
        const json = await res.json();
        crearAlerta(this, json.mensaje, json.ok ? 'success' : 'danger');
        if (json.ok) await this.cargar();
      } catch (e) {
        crearAlerta(this, 'Error al eliminar.', 'danger');
      }
    },
  };
}

// ══════════════════════════════════════════════════════
// CLIENTES con IA
// ══════════════════════════════════════════════════════
function clientesApp() {
  return {
    clientes: [],
    cargando: true,
    mostrarModal: false,
    mostrarIA: false,
    cargandoIA: false,
    sugerencias: [],
    clienteSeleccionado: null,
    modoEdicion: false,
    mensaje: '',
    tipoMsg: 'success',
    form: {
      id: null,
      nombre: '',
      apellido: '',
      documento: '',
      telefono: '',
      email: '',
      direccion: '',
      estado: 'activo',
    },

    async init() {
      await this.cargar();
    },

    async cargar() {
      this.cargando = true;
      try {
        const res = await fetch(`${API_URL_URL_URL}?modulo=clientes&accion=obtenerTodos`);
        const json = await res.json();
        this.clientes = json.ok ? json.datos : [];
      } catch (e) {
        crearAlerta(this, 'Error de conexión.', 'danger');
      } finally {
        this.cargando = false;
      }
    },

    abrirFormulario() {
      this.modoEdicion = false;
      this.form = {
        id: null,
        nombre: '',
        apellido: '',
        documento: '',
        telefono: '',
        email: '',
        direccion: '',
        estado: 'activo',
      };
      this.mostrarModal = true;
    },

    editar(c) {
      this.modoEdicion = true;
      this.form = { ...c };
      this.mostrarModal = true;
    },
    cerrarModal() {
      this.mostrarModal = false;
    },

    async guardar() {
      if (!this.form.nombre || !this.form.apellido || !this.form.documento) {
        crearAlerta(this, 'Nombre, apellido y documento son obligatorios.', 'warning');
        return;
      }
      const accion = this.modoEdicion ? 'actualizar' : 'guardar';
      try {
        const res = await fetch(`${API_URL_URL_URL}?modulo=clientes&accion=${accion}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        });
        const json = await res.json();
        crearAlerta(this, json.mensaje, json.ok ? 'success' : 'danger');
        if (json.ok) {
          this.cerrarModal();
          await this.cargar();
        }
      } catch (e) {
        crearAlerta(this, 'Error al guardar.', 'danger');
      }
    },

    async eliminar(id) {
      if (!confirm('¿Eliminar este cliente?')) return;
      try {
        const res = await fetch(`${API_URL_URL_URL}?modulo=clientes&accion=eliminar&id=${id}`, {
          method: 'DELETE',
        });
        const json = await res.json();
        crearAlerta(this, json.mensaje, json.ok ? 'success' : 'danger');
        if (json.ok) await this.cargar();
      } catch (e) {
        crearAlerta(this, 'Error al eliminar.', 'danger');
      }
    },

    // 🤖 Sugerencias IA para un cliente
    async verSugerenciasIA(cliente) {
      this.clienteSeleccionado = cliente;
      this.sugerencias = [];
      this.cargandoIA = true;
      this.mostrarIA = true;
      try {
        const res = await fetch(
          `${API_URL_URL_URL}?modulo=ia&accion=sugerirProductos&id=${cliente.id}`,
        );
        const json = await res.json();
        this.sugerencias = json.datos?.sugerencias ?? [];
      } catch (e) {
        this.sugerencias = [
          'Cable USB-C certificado',
          'Funda protectora premium',
          'Cargador rápido 65W',
        ];
      } finally {
        this.cargandoIA = false;
      }
    },
  };
}

// ══════════════════════════════════════════════════════
// VENTAS
// ══════════════════════════════════════════════════════

function ventasApp() {
  return {
    ventas: [],
    clientes: [],
    cargando: true,
    cargandoClientes: false,
    guardando: false,
    mostrarModal: false,
    mensaje: '',
    tipoMsg: 'success',

    form: {
      cliente_id: '',
      total: '',
      estado: 'activa',
    },

    async init() {
      await Promise.all([this.cargar(), this.cargarClientes()]);
    },

    async cargar() {
      this.cargando = true;

      try {
        const res = await fetch(`${API_URL_URL}?modulo=ventas&accion=obtenerTodos`);

        if (!res.ok) {
          throw new Error('No fue posible consultar las ventas.');
        }

        const json = await res.json();

        if (!json.ok) {
          throw new Error(json.mensaje || 'Error al cargar las ventas.');
        }

        this.ventas = Array.isArray(json.datos) ? json.datos : [];
      } catch (e) {
        console.error('Error al cargar ventas:', e);
        this.ventas = [];
        crearAlerta(this, e.message || 'Error de conexión al cargar las ventas.', 'danger');
      } finally {
        this.cargando = false;
      }
    },

    async cargarClientes() {
      this.cargandoClientes = true;

      try {
        const res = await fetch(`${API_URL_URL}?modulo=clientes&accion=obtenerTodos`);

        if (!res.ok) {
          throw new Error('No fue posible consultar los clientes.');
        }

        const json = await res.json();

        if (!json.ok) {
          throw new Error(json.mensaje || 'Error al cargar los clientes.');
        }

        this.clientes = Array.isArray(json.datos) ? json.datos : [];
      } catch (e) {
        console.error('Error al cargar clientes:', e);
        this.clientes = [];
        crearAlerta(this, 'No se pudieron cargar los clientes para el formulario.', 'danger');
      } finally {
        this.cargandoClientes = false;
      }
    },

    abrirFormulario() {
      if (this.clientes.length === 0) {
        crearAlerta(this, 'Primero debes tener al menos un cliente registrado.', 'warning');
        return;
      }

      this.form = {
        cliente_id: '',
        total: '',
        estado: 'activa',
      };

      this.mostrarModal = true;
    },

    cerrarModal() {
      this.mostrarModal = false;
    },

    async guardar() {
      if (this.guardando) return;

      if (!this.form.cliente_id) {
        crearAlerta(this, 'Selecciona un cliente.', 'warning');
        return;
      }

      if (!this.form.total || Number(this.form.total) <= 0) {
        crearAlerta(this, 'Ingresa un total mayor que cero.', 'warning');
        return;
      }

      this.guardando = true;

      try {
        const res = await fetch(`${API_URL_URL}?modulo=ventas&accion=guardar`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            cliente_id: Number(this.form.cliente_id),
            total: Number(this.form.total),
            estado: this.form.estado,
          }),
        });

        const json = await res.json();

        crearAlerta(
          this,
          json.mensaje || (json.ok ? 'Venta registrada.' : 'No se pudo registrar la venta.'),
          json.ok ? 'success' : 'danger',
        );

        if (json.ok) {
          this.cerrarModal();
          await this.cargar();
        }
      } catch (e) {
        console.error('Error al guardar venta:', e);
        crearAlerta(this, 'Error al registrar la venta.', 'danger');
      } finally {
        this.guardando = false;
      }
    },

    async cancelar(id) {
      if (!confirm('¿Deseas anular esta venta?')) return;

      try {
        const res = await fetch(
          `${API_URL_URL}?modulo=ventas&accion=cancelar&id=${encodeURIComponent(id)}`,
          { method: 'DELETE' },
        );

        const json = await res.json();

        crearAlerta(
          this,
          json.mensaje || (json.ok ? 'Venta anulada.' : 'No se pudo anular la venta.'),
          json.ok ? 'success' : 'danger',
        );

        if (json.ok) {
          await this.cargar();
        }
      } catch (e) {
        console.error('Error al anular venta:', e);
        crearAlerta(this, 'Error al anular la venta.', 'danger');
      }
    },
  };
}

// ══════════════════════════════════════════════════════
// REPORTES
// ══════════════════════════════════════════════════════
function reportesApp() {
  return {
    topProductos: [],
    stockBajo: [],
    cargando: true,

    async init() {
      this.cargando = true;
      try {
        const [r1, r2] = await Promise.all([
          fetch(`${API_URL_URL}?modulo=reportes&accion=topProductos`).then((r) => r.json()),
          fetch(`${API_URL_URL}?modulo=reportes&accion=stockBajo`).then((r) => r.json()),
        ]);
        this.topProductos = r1.ok ? r1.datos : [];
        this.stockBajo = r2.ok ? r2.datos : [];
      } catch (e) {
        console.error('Error reportes:', e);
      } finally {
        this.cargando = false;
      }
    },
  };
}

// ══════════════════════════════════════════════════════
// IA — RESUMEN INTELIGENTE
// ══════════════════════════════════════════════════════
function resumenIAApp() {
  return {
    datos: null,
    cargando: true,
    async cargar() {
      this.cargando = true;
      try {
        const res = await fetch(`${API_URL_URL}?modulo=ia&accion=resumenNegocio`);
        const json = await res.json();
        this.datos = json.ok ? json.datos : null;
      } catch (e) {
        console.error('IA no disponible:', e);
      } finally {
        this.cargando = false;
      }
    },
  };
}

// ══════════════════════════════════════════════════════
// IA — PREDICCIÓN DE AGOTAMIENTO
// ══════════════════════════════════════════════════════
function prediccionApp() {
  return {
    predicciones: [],
    cargando: true,
    async cargar() {
      this.cargando = true;
      try {
        const res = await fetch(`${API_URL_URL}?modulo=ia&accion=predecirAgotamiento`);
        const json = await res.json();
        this.predicciones = json.ok ? json.datos : [];
      } catch (e) {
        console.error('Error predicción:', e);
      } finally {
        this.cargando = false;
      }
    },
  };
}

/**
 * app.js — Funciones Alpine.js TecnoShop
 * CRUD + IA + Reportes
 * TecnoShop · SENA ADSO · Juan Carlos Huertas Niño · 2026
 */

const API_URL = "/tecnoshop_vscode/index.php";
/** Helper para mostrar alertas */

/**
 * Muestra una alerta temporal en la interfaz.
 * @param {Object} ctx - Contexto del componente que recibe la alerta.
 * @param {string} mensaje - Texto que se mostrará al usuario.
 * @param {string} tipo - Tipo de alerta: success, danger o warning.
 * @returns {void} No devuelve ningún valor.
 */

function crearAlerta(ctx, mensaje, tipo = "success") {
  ctx.mensaje = mensaje;
  ctx.tipoMsg = tipo;
  setTimeout(() => {
    ctx.mensaje = "";
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
    mensaje: "",
    tipoMsg: "success",

    form: {
      id: null,
      nombre: "",
      descripcion: "",
      precio: "",
      stock: "",
      marca: "",
      categoria_id: "",
    },

    async init() {
      await this.cargar();
      await this.cargarCategorias();
    },

    async cargar() {
      this.cargando = true;

      try {
        const res = await fetch(`${API}?modulo=productos&accion=obtenerTodos`);
        const json = await res.json();
        this.productos = json.ok ? json.datos : [];
      } catch (e) {
        crearAlerta(this, "Error de conexión.", "danger");
      } finally {
        this.cargando = false;
      }
    },

    async cargarCategorias() {
      const res = await fetch(`${API}?modulo=productos&accion=categorias`);
      const json = await res.json();
      this.categorias = json.ok ? json.datos : [];
    },

    abrirFormulario() {
      this.modoEdicion = false;
      this.form = this.crearFormularioVacio();
      this.mostrarModal = true;
    },

    crearFormularioVacio() {
      return {
        id: null,
        nombre: "",
        descripcion: "",
        precio: "",
        stock: "",
        marca: "",
        categoria_id: "",
      };
    },

    editar(producto) {
      this.modoEdicion = true;
      this.form = { ...producto };
      this.mostrarModal = true;
    },

    cerrarModal() {
      this.mostrarModal = false;
    },

    validarFormulario() {
      if (!this.form.nombre) {
        crearAlerta(this, "El nombre es obligatorio.", "warning");
        return false;
      }

      if (this.form.precio <= 0) {
        crearAlerta(this, "El precio debe ser mayor a 0.", "warning");
        return false;
      }

      return true;
    },

    obtenerAccion() {
      return this.modoEdicion ? "actualizar" : "guardar";
    },

    async enviarFormulario() {
      const accion = this.obtenerAccion();

      const res = await fetch(`${API}?modulo=productos&accion=${accion}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(this.form),
      });

      return await res.json();
    },

    procesarRespuesta(json) {
      crearAlerta(this, json.mensaje, json.ok ? "success" : "danger");

      return json.ok;
    },

    async guardar() {
      if (!this.validarFormulario()) return;

      try {
        const json = await this.enviarFormulario();

        if (this.procesarRespuesta(json)) {
          this.cerrarModal();
          await this.cargar();
        }
      } catch (e) {
        crearAlerta(this, "Error al guardar.", "danger");
      }
    },

    async eliminar(id) {
      if (!confirm("¿Eliminar este producto?")) return;

      try {
        const json = await this.eliminarProducto(id);

        if (this.procesarRespuesta(json)) {
          await this.cargar();
        }
      } catch (e) {
        crearAlerta(this, "Error al eliminar.", "danger");
      }
    },

    /**
     * Envía una solicitud para eliminar un producto.
     * @param {number|string} id - Identificador del producto que se eliminará.
     * @returns {Promise<Object>} Respuesta JSON del servidor.
     */

    async eliminarProducto(id) {
      const res = await fetch(
        `${API}?modulo=productos&accion=eliminar&id=${id}`,
        { method: "DELETE" },
      );

      return await res.json();
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
    mensaje: "",
    tipoMsg: "success",
    form: {
      id: null,
      nombre: "",
      apellido: "",
      documento: "",
      telefono: "",
      email: "",
      direccion: "",
      estado: "activo",
    },

    async init() {
      await this.cargar();
    },

    async cargar() {
      this.cargando = true;
      try {
        const res = await fetch(`${API}?modulo=clientes&accion=obtenerTodos`);
        const json = await res.json();
        this.clientes = json.ok ? json.datos : [];
      } catch (e) {
        crearAlerta(this, "Error de conexión.", "danger");
      } finally {
        this.cargando = false;
      }
    },

    abrirFormulario() {
      this.modoEdicion = false;
      this.form = {
        id: null,
        nombre: "",
        apellido: "",
        documento: "",
        telefono: "",
        email: "",
        direccion: "",
        estado: "activo",
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
        crearAlerta(
          this,
          "Nombre, apellido y documento son obligatorios.",
          "warning",
        );
        return;
      }
      const accion = this.modoEdicion ? "actualizar" : "guardar";
      try {
        const res = await fetch(`${API}?modulo=clientes&accion=${accion}`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.form),
        });
        const json = await res.json();
        crearAlerta(this, json.mensaje, json.ok ? "success" : "danger");
        if (json.ok) {
          this.cerrarModal();
          await this.cargar();
        }
      } catch (e) {
        crearAlerta(this, "Error al guardar.", "danger");
      }
    },

    async eliminar(id) {
      if (!confirm("¿Eliminar este cliente?")) return;
      try {
        const res = await fetch(
          `${API}?modulo=clientes&accion=eliminar&id=${id}`,
          { method: "DELETE" },
        );
        const json = await res.json();
        crearAlerta(this, json.mensaje, json.ok ? "success" : "danger");
        if (json.ok) await this.cargar();
      } catch (e) {
        crearAlerta(this, "Error al eliminar.", "danger");
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
          `${API}?modulo=ia&accion=sugerirProductos&id=${cliente.id}`,
        );
        const json = await res.json();
        this.sugerencias = json.datos?.sugerencias ?? [];
      } catch (e) {
        this.sugerencias = [
          "Cable USB-C certificado",
          "Funda protectora premium",
          "Cargador rápido 65W",
        ];
      } finally {
        this.cargandoIA = false;
      }
    },
  };
}

// ══════════════════════════════════════════════════════
// VENTAS
/**
 * Ejecuta una consulta a la API y devuelve los datos JSON.
 * @param {string} url - Dirección del servicio solicitado.
 * @returns {Promise<Object>} Respuesta recibida del servidor.
 */
/**
 * Realiza una petición HTTP y procesa la respuesta JSON.
 * @param {string} url - Dirección del servicio solicitado.
 * @returns {Promise<Object>} Datos JSON recibidos del servidor.
 */

async function consultarApi(url) {
  const response = await fetch(url);

  if (!response.ok) {
    throw new Error("No fue posible completar la consulta.");
  }

  return await response.json();
}

// ══════════════════════════════════════════════════════

function ventasApp() {
  return {
    ventas: [],
    clientes: [],
    cargando: true,
    cargandoClientes: false,
    guardando: false,
    mostrarModal: false,
    mensaje: "",
    tipoMsg: "success",

    form: {
      cliente_id: "",
      total: "",
      estado: "activa",
    },

    async init() {
      await Promise.all([this.cargar(), this.cargarClientes()]);
    },

    async cargar() {
      this.cargando = true;

      try {
        const json = await consultarApi(
          `${API}?modulo=ventas&accion=obtenerTodos`,
        );

        if (!json.ok) {
          throw new Error(json.mensaje || "Error al cargar las ventas.");
        }

        this.ventas = Array.isArray(json.datos) ? json.datos : [];
      } catch (e) {
        console.error("Error al cargar ventas:", e);
        this.ventas = [];
        crearAlerta(this, e.message, "danger");
      } finally {
        this.cargando = false;
      }
    },

    async cargarClientes() {
      this.cargandoClientes = true;

      try {
        const json = await consultarApi(
          `${API}?modulo=clientes&accion=obtenerTodos`,
        );

        if (!json.ok) {
          throw new Error(json.mensaje || "Error al cargar los clientes.");
        }

        this.clientes = Array.isArray(json.datos) ? json.datos : [];
      } catch (e) {
        console.error("Error al cargar clientes:", e);
        this.clientes = [];
        crearAlerta(
          this,
          "No se pudieron cargar los clientes para el formulario.",
          "danger",
        );
      } finally {
        this.cargandoClientes = false;
      }
    },

    abrirFormulario() {
      if (this.clientes.length === 0) {
        crearAlerta(
          this,
          "Primero debes tener al menos un cliente registrado.",
          "warning",
        );
        return;
      }

      this.form = {
        cliente_id: "",
        total: "",
        estado: "activa",
      };

      this.mostrarModal = true;
    },

    cerrarModal() {
      this.mostrarModal = false;
    },

    async guardar() {
      if (this.guardando) return;

      if (!this.form.cliente_id) {
        crearAlerta(this, "Selecciona un cliente.", "warning");
        return;
      }

      if (!this.form.total || Number(this.form.total) <= 0) {
        crearAlerta(this, "Ingresa un total mayor que cero.", "warning");
        return;
      }

      this.guardando = true;

      try {
        const res = await fetch(`${API}?modulo=ventas&accion=guardar`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
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
          json.mensaje ||
            (json.ok ? "Venta registrada." : "No se pudo registrar la venta."),
          json.ok ? "success" : "danger",
        );

        if (json.ok) {
          this.cerrarModal();
          await this.cargar();
        }
      } catch (e) {
        console.error("Error al guardar venta:", e);
        crearAlerta(this, "Error al registrar la venta.", "danger");
      } finally {
        this.guardando = false;
      }
    },

    async cancelar(id) {
      if (!confirm("¿Deseas anular esta venta?")) return;

      try {
        const res = await fetch(
          `${API}?modulo=ventas&accion=cancelar&id=${encodeURIComponent(id)}`,
          { method: "DELETE" },
        );

        const json = await res.json();

        crearAlerta(
          this,
          json.mensaje ||
            (json.ok ? "Venta anulada." : "No se pudo anular la venta."),
          json.ok ? "success" : "danger",
        );

        if (json.ok) {
          await this.cargar();
        }
      } catch (e) {
        console.error("Error al anular venta:", e);
        crearAlerta(this, "Error al anular la venta.", "danger");
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
          fetch(`${API}?modulo=reportes&accion=topProductos`).then((r) =>
            r.json(),
          ),
          fetch(`${API}?modulo=reportes&accion=stockBajo`).then((r) =>
            r.json(),
          ),
        ]);
        this.topProductos = r1.ok ? r1.datos : [];
        this.stockBajo = r2.ok ? r2.datos : [];
      } catch (e) {
        console.error("Error reportes:", e);
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
        const res = await fetch(`${API}?modulo=ia&accion=resumenNegocio`);
        const json = await res.json();
        this.datos = json.ok ? json.datos : null;
      } catch (e) {
        console.error("IA no disponible:", e);
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
        const res = await fetch(`${API}?modulo=ia&accion=predecirAgotamiento`);
        const json = await res.json();
        this.predicciones = json.ok ? json.datos : [];
      } catch (e) {
        console.error("Error predicción:", e);
      } finally {
        this.cargando = false;
      }
    },
  };
}

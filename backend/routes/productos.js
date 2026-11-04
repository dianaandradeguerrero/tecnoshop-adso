const express = require('express');
const router = express.Router();

// Conexión a MySQL ubicada en la carpeta backend
const db = require('../db');

// ======================================================
// GET /api/productos
// Listar todos los productos
// ======================================================
router.get('/', async (req, res) => {
  try {
    const [productos] = await db.query('SELECT * FROM productos');

    res.status(200).json({
      mensaje: 'Listado de productos',
      datos: productos,
    });
  } catch (error) {
    console.error('Error al consultar productos:', error);

    res.status(500).json({
      error: true,
      message: 'Error al consultar los productos',
    });
  }
});

// ======================================================
// GET /api/productos/:id
// Obtener un producto por ID
// ======================================================
router.get('/:id', async (req, res) => {
  try {
    const [productos] = await db.query('SELECT * FROM productos WHERE id = ?', [req.params.id]);

    if (productos.length === 0) {
      return res.status(404).json({
        error: true,
        message: 'Producto no encontrado',
      });
    }

    res.status(200).json({
      mensaje: 'Producto encontrado',
      datos: productos[0],
    });
  } catch (error) {
    console.error('Error al consultar producto:', error);

    res.status(500).json({
      error: true,
      message: 'Error al consultar el producto',
    });
  }
});

// ======================================================
// POST /api/productos
// Crear un producto
// ======================================================
router.post('/', async (req, res) => {
  try {
    const { nombre, descripcion, categoria_id, precio, stock, marca, estado } = req.body;

    if (
      !nombre ||
      !descripcion ||
      !categoria_id ||
      precio === undefined ||
      stock === undefined ||
      !marca
    ) {
      return res.status(400).json({
        error: true,
        message: 'Todos los datos del producto son obligatorios',
      });
    }

    const [resultado] = await db.query(
      `INSERT INTO productos (
                nombre,
                descripcion,
                categoria_id,
                precio,
                stock,
                marca,
                estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [nombre, descripcion, categoria_id, precio, stock, marca, estado || 'activo'],
    );

    res.status(201).json({
      mensaje: 'Producto creado correctamente',
      id: resultado.insertId,
    });
  } catch (error) {
    console.error('Error al crear producto:', error);

    res.status(500).json({
      error: true,
      message: 'Error al crear el producto',
    });
  }
});

// ======================================================
// PUT /api/productos/:id
// Actualizar un producto
// ======================================================
router.put('/:id', async (req, res) => {
  try {
    const { nombre, descripcion, categoria_id, precio, stock, marca, estado } = req.body;

    if (
      !nombre ||
      !descripcion ||
      !categoria_id ||
      precio === undefined ||
      stock === undefined ||
      !marca ||
      !estado
    ) {
      return res.status(400).json({
        error: true,
        message: 'Todos los datos del producto son obligatorios',
      });
    }

    const [resultado] = await db.query(
      `UPDATE productos
             SET nombre = ?,
                 descripcion = ?,
                 categoria_id = ?,
                 precio = ?,
                 stock = ?,
                 marca = ?,
                 estado = ?
             WHERE id = ?`,
      [nombre, descripcion, categoria_id, precio, stock, marca, estado, req.params.id],
    );

    if (resultado.affectedRows === 0) {
      return res.status(404).json({
        error: true,
        message: 'Producto no encontrado',
      });
    }

    res.status(200).json({
      mensaje: 'Producto actualizado correctamente',
    });
  } catch (error) {
    console.error('Error al actualizar producto:', error);

    res.status(500).json({
      error: true,
      message: 'Error al actualizar el producto',
    });
  }
});

// ======================================================
// DELETE /api/productos/:id
// Eliminar un producto
// ======================================================
router.delete('/:id', async (req, res) => {
  try {
    const [resultado] = await db.query('DELETE FROM productos WHERE id = ?', [req.params.id]);

    if (resultado.affectedRows === 0) {
      return res.status(404).json({
        error: true,
        message: 'Producto no encontrado',
      });
    }

    res.status(200).json({
      mensaje: 'Producto eliminado correctamente',
    });
  } catch (error) {
    console.error('Error al eliminar producto:', error);

    res.status(500).json({
      error: true,
      message: 'Error al eliminar el producto',
    });
  }
});

module.exports = router;

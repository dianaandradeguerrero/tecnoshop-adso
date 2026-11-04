const express = require("express");
const bcrypt = require("bcryptjs");
const jwt = require("jsonwebtoken");

const db = require("../db");

const router = express.Router();

// ======================================================
// POST /api/auth/login
// Iniciar sesión
// ======================================================
router.post("/login", async (req, res) => {
  try {
    const { email, password } = req.body;

    // Validar datos obligatorios
    if (!email || !password) {
      return res.status(400).json({
        error: true,
        message: "Email y contraseña son obligatorios",
      });
    }

    // Verificar que exista el secreto JWT
    if (!process.env.JWT_SECRET) {
      console.error("JWT_SECRET no está configurado en backend/.env");

      return res.status(500).json({
        error: true,
        message: "Error de configuración del servidor",
      });
    }

    // Buscar usuario por email
    const [usuarios] = await db.query(
      "SELECT * FROM clientes WHERE email = ?",
      [email],
    );

    // Usuario no encontrado
    if (usuarios.length === 0) {
      return res.status(401).json({
        error: true,
        message: "Email o contraseña incorrectos",
      });
    }

    const usuario = usuarios[0];

    // Comparar contraseña con el hash almacenado
    const passwordValida = await bcrypt.compare(password, usuario.password);

    // Contraseña incorrecta
    if (!passwordValida) {
      return res.status(401).json({
        error: true,
        message: "Email o contraseña incorrectos",
      });
    }

    // Crear token JWT
    const token = jwt.sign(
      {
        id: usuario.id,
        email: usuario.email,
      },
      process.env.JWT_SECRET,
      {
        expiresIn: "1h",
      },
    );

    // No enviar la contraseña al cliente
    const { password: passwordUsuario, ...usuarioSinPassword } = usuario;

    // Respuesta exitosa
    return res.status(200).json({
      token: token,
      user: usuarioSinPassword,
    });
  } catch (error) {
    console.error("Error en login:", error);

    return res.status(500).json({
      error: true,
      message: "Error interno del servidor",
    });
  }
});

module.exports = router;

const express = require("express");
const cors = require("cors");
const path = require("path");

// Cargar las variables de backend/.env
require("dotenv").config({
  path: path.join(__dirname, ".env"),
});

const productosRoutes = require("./routes/productos");
const authRoutes = require("./routes/auth");
const verifyToken = require("./middleware/verifyToken");

const app = express();

app.use(cors());
app.use(express.json());

// Ruta principal para comprobar que la API funciona
app.get("/", (req, res) => {
  res.status(200).json({
    ok: true,
    mensaje: "API REST de TecnoShop funcionando correctamente",
    baseDatos: process.env.DB_NAME || "No configurada",
  });
});

// Autenticación
app.use("/api/auth", authRoutes);

// Productos protegidos con JWT
app.use("/api/productos", verifyToken, productosRoutes);

// Ruta para endpoints inexistentes
app.use((req, res) => {
  res.status(404).json({
    ok: false,
    mensaje: "Ruta no encontrada",
  });
});

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
  console.log(`Servidor ejecutándose en http://localhost:${PORT}`);
  console.log(
    `Base de datos configurada: ${process.env.DB_NAME || "No configurada"}`,
  );
});

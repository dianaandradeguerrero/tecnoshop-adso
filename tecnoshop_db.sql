-- ══════════════════════════════════════════════════════
-- TecnoShop — Base de datos completa
-- Caso de estudio SENA ADSO · Fase de Ejecución 2026
-- Instructor: Juan Carlos Huertas Niño
-- ══════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS tecnoshop_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tecnoshop_db;

-- ─── CATEGORÍAS ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS categorias (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
);

-- ─── PRODUCTOS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS productos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(200) NOT NULL,
  descripcion  TEXT,
  precio       DECIMAL(12,2) NOT NULL DEFAULT 0,
  stock        INT NOT NULL DEFAULT 0,
  marca        VARCHAR(100),
  estado       ENUM('activo','inactivo') DEFAULT 'activo',
  categoria_id INT,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- ─── CLIENTES ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS clientes (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(100) NOT NULL,
  apellido  VARCHAR(100) NOT NULL,
  documento VARCHAR(20) UNIQUE NOT NULL,
  telefono  VARCHAR(20),
  email     VARCHAR(150),
  direccion TEXT,
  estado    ENUM('activo','inactivo') DEFAULT 'activo'
);

-- ─── VENTAS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS ventas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente  INT NOT NULL,
  fecha       DATETIME DEFAULT NOW(),
  total       DECIMAL(14,2) DEFAULT 0,
  estado      ENUM('pendiente','completada','cancelada') DEFAULT 'pendiente',
  observacion TEXT,
  FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

-- ─── DETALLE VENTAS ───────────────────────────────────
CREATE TABLE IF NOT EXISTS detalle_ventas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  id_venta    INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad    INT NOT NULL,
  precio_unit DECIMAL(12,2) NOT NULL,
  subtotal    DECIMAL(14,2) NOT NULL,
  FOREIGN KEY (id_venta)    REFERENCES ventas(id),
  FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- ─── DATOS DE PRUEBA ──────────────────────────────────
INSERT INTO categorias (nombre) VALUES
  ('Celulares'),('Computadores'),('Accesorios'),('Periféricos');

INSERT INTO productos (nombre,descripcion,precio,stock,marca,categoria_id) VALUES
  ('iPhone 15','Apple iPhone 15 128GB',3999000,8,'Apple',1),
  ('Samsung A55','Samsung Galaxy A55 256GB',1299000,12,'Samsung',1),
  ('iPhone 15 Pro','Apple iPhone 15 Pro 256GB',5499000,4,'Apple',1),
  ('MacBook Air M2','MacBook Air chip M2 8GB RAM',6499000,4,'Apple',2),
  ('Dell Inspiron 15','Dell Inspiron i5 8GB 512GB SSD',2899000,6,'Dell',2),
  ('HP Pavilion 15','HP Pavilion i7 16GB 1TB',3499000,3,'HP',2),
  ('Audífonos Sony WH','Sony WH-1000XM5 Bluetooth',899000,15,'Sony',3),
  ('Funda iPhone 15','Funda silicona premium iPhone 15',89000,30,'Genérico',3),
  ('Cable USB-C 2m','Cable carga rápida 65W 2 metros',45000,50,'Genérico',3),
  ('Mouse Logitech MX','Mouse inalámbrico Logitech MX Master 3',399000,10,'Logitech',4),
  ('Teclado Mecánico','Teclado mecánico RGB switches blue',289000,8,'Redragon',4),
  ('Monitor LG 24','Monitor LG 24 pulgadas Full HD IPS',899000,5,'LG',4);

INSERT INTO clientes (nombre,apellido,documento,telefono,email,direccion) VALUES
  ('Roberto','García','12345678','3001234567','roberto@email.com','Calle 10 # 5-20, Bogotá'),
  ('María','López','87654321','3109876543','maria@email.com','Carrera 7 # 45-30, Bogotá'),
  ('Carlos','Martínez','11223344','3201122334','carlos@email.com','Av. 68 # 23-15, Bogotá'),
  ('Ana','Rodríguez','55667788','3151234567','ana@email.com','Calle 80 # 12-40, Bogotá'),
  ('Pedro','Sánchez','99887766','3187654321','pedro@email.com','Carrera 15 # 88-50, Bogotá');

INSERT INTO ventas (id_cliente,total,estado,observacion) VALUES
  (1,5298000,'completada','Compra iPhone 15 + Funda'),
  (2,1299000,'completada','Samsung A55'),
  (3,7398000,'completada','MacBook Air + Mouse'),
  (1,45000,'pendiente','Cable USB-C'),
  (4,1188000,'completada','Audífonos + Funda');

INSERT INTO detalle_ventas (id_venta,id_producto,cantidad,precio_unit,subtotal) VALUES
  (1,1,1,3999000,3999000),(1,8,1,89000,89000),(1,2,1,1299000,1299000),
  (2,2,1,1299000,1299000),
  (3,4,1,6499000,6499000),(3,10,1,399000,399000),
  (4,9,1,45000,45000),
  (5,7,1,899000,899000),(5,8,1,89000,89000);

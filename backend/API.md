# API REST - TecnoShop

## Información general

API REST desarrollada con Node.js, Express y MySQL para gestionar los productos de TecnoShop.

**URL base:**

http://localhost:3000

La API utiliza autenticación JWT para proteger los endpoints de productos.

---

# 1. Autenticación

## POST /api/auth/login

Permite iniciar sesión y obtener un token JWT.

### URL

```text
POST http://localhost:3000/api/auth/login
```

### Headers

```text
Content-Type: application/json
```

### Body

```json
{
  "email": "ana@correo.com",
  "password": "123456"
}
```

### Respuesta exitosa

**HTTP 200 OK**

```json
{
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "user": {
    "id": 1,
    "nombre": "Ana Sofía Gómez",
    "email": "ana@correo.com",
    "telefono": "3001234567",
    "documento": "1098765432"
  }
}
```

### Errores posibles

**400 Bad Request**

```json
{
  "error": true,
  "message": "Email y contraseña son obligatorios"
}
```

**401 Unauthorized**

```json
{
  "error": true,
  "message": "Email o contraseña incorrectos"
}
```

**500 Internal Server Error**

```json
{
  "error": true,
  "message": "Error interno del servidor"
}
```

---

# 2. Productos

Todos los endpoints de productos requieren autenticación JWT.

### Header requerido

```text
Authorization: Bearer TOKEN
```

---

## GET /api/productos

Obtiene todos los productos.

### URL

```text
GET http://localhost:3000/api/productos
```

### Headers

```text
Authorization: Bearer TOKEN
```

### Body

No requiere body.

### Respuesta exitosa

**HTTP 200 OK**

```json
{
  "mensaje": "Listado de productos",
  "datos": [
    {
      "id": 1,
      "nombre": "Samsung Galaxy A55",
      "descripcion": "Pantalla AMOLED 6.6 pulgadas",
      "categoria_id": 1,
      "precio": "2299000.00",
      "stock": 12,
      "marca": "Samsung",
      "estado": "activo"
    }
  ]
}
```

### Errores posibles

**401 Unauthorized**

```json
{
  "error": true,
  "message": "Token no proporcionado"
}
```

**500 Internal Server Error**

```json
{
  "error": true,
  "message": "Error al consultar los productos"
}
```

---

# GET /api/productos/:id

Obtiene un producto específico.

### URL

```text
GET http://localhost:3000/api/productos/1
```

### Headers

```text
Authorization: Bearer TOKEN
```

### Body

No requiere body.

### Respuesta exitosa

**HTTP 200 OK**

```json
{
  "mensaje": "Producto encontrado",
  "datos": {
    "id": 1,
    "nombre": "Samsung Galaxy A55",
    "descripcion": "Pantalla AMOLED 6.6 pulgadas",
    "categoria_id": 1,
    "precio": "2299000.00",
    "stock": 12,
    "marca": "Samsung",
    "estado": "activo"
  }
}
```

### Errores posibles

**401 Unauthorized**

Token no proporcionado o inválido.

**404 Not Found**

```json
{
  "error": true,
  "message": "Producto no encontrado"
}
```

**500 Internal Server Error**

Error interno al consultar la base de datos.

---

# POST /api/productos

Crea un nuevo producto.

### URL

```text
POST http://localhost:3000/api/productos
```

### Headers

```text
Content-Type: application/json
Authorization: Bearer TOKEN
```

### Body

```json
{
  "nombre": "Mouse inalámbrico",
  "descripcion": "Mouse óptico USB",
  "categoria_id": 1,
  "precio": 45000,
  "stock": 10,
  "marca": "Logitech",
  "estado": "activo"
}
```

### Respuesta exitosa

**HTTP 201 Created**

```json
{
  "mensaje": "Producto creado correctamente",
  "id": 7
}
```

### Errores posibles

**400 Bad Request**

Datos de producto inválidos o incompletos.

**401 Unauthorized**

Token no proporcionado o inválido.

**500 Internal Server Error**

Error al guardar el producto.

---

# PUT /api/productos/:id

Actualiza un producto existente.

### URL

```text
PUT http://localhost:3000/api/productos/7
```

### Headers

```text
Content-Type: application/json
Authorization: Bearer TOKEN
```

### Body

```json
{
  "nombre": "Mouse inalámbrico actualizado",
  "descripcion": "Mouse óptico USB actualizado",
  "categoria_id": 1,
  "precio": 50000,
  "stock": 15,
  "marca": "Logitech",
  "estado": "activo"
}
```

### Respuesta exitosa

**HTTP 200 OK**

```json
{
  "mensaje": "Producto actualizado correctamente"
}
```

### Errores posibles

**401 Unauthorized**

Token no proporcionado o inválido.

**404 Not Found**

```json
{
  "error": true,
  "message": "Producto no encontrado"
}
```

**500 Internal Server Error**

Error al actualizar el producto.

---

# DELETE /api/productos/:id

Elimina un producto.

### URL

```text
DELETE http://localhost:3000/api/productos/7
```

### Headers

```text
Authorization: Bearer TOKEN
```

### Body

No requiere body.

### Respuesta exitosa

**HTTP 200 OK**

```json
{
  "mensaje": "Producto eliminado correctamente"
}
```

### Errores posibles

**401 Unauthorized**

Token no proporcionado o inválido.

**404 Not Found**

```json
{
  "error": true,
  "message": "Producto no encontrado"
}
```

**500 Internal Server Error**

Error al eliminar el producto.

---

# Códigos HTTP utilizados

| Código | Descripción                       |
| ------ | --------------------------------- |
| 200    | Operación realizada correctamente |
| 201    | Recurso creado correctamente      |
| 400    | Solicitud incorrecta              |
| 401    | No autorizado o token inválido    |
| 404    | Recurso no encontrado             |
| 500    | Error interno del servidor        |

---

# Autenticación JWT

Para acceder a los endpoints de productos se debe enviar el token obtenido mediante:

```text
POST /api/auth/login
```

El token debe enviarse en el header:

```text
Authorization: Bearer TOKEN
```

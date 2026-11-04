# ESTÁNDARES DE CODIFICACIÓN - TECNOSHOP

## 1. Objetivo

Establecer normas de programación para garantizar un código legible, organizado, reutilizable y fácil de mantener.

## 2. Convenciones de nombres

- CSS: utilizar kebab-case. Ejemplo: formulario-login.
- JavaScript: utilizar camelCase. Ejemplo: nombreUsuario.
- Constantes: utilizar UPPER_SNAKE_CASE. Ejemplo: MAX_INTENTOS.
- Archivos: utilizar kebab-case. Ejemplo: gestion-productos.js.
- Funciones: utilizar camelCase. Ejemplo: registrarProducto().
- Clases PHP: utilizar PascalCase. Ejemplo: ProductoControlador.

## 3. Estructura de carpetas

- /publico/css: hojas de estilos.
- /publico/js: archivos JavaScript.
- /publico/img: imágenes e iconos.
- /publico/fonts: fuentes tipográficas.
- /publico/components: componentes reutilizables.
- /vistas: interfaces del sistema.
- /controladores: lógica de control.
- /modelos: acceso y gestión de datos.
- /backend: servicios y API.

## 4. Comentarios

Los comentarios deben ser breves, claros y explicar la finalidad del código.

JavaScript:
// Validar los datos del formulario.

CSS:
/_ Estilos del formulario de productos _/

PHP:
// Consultar los productos registrados.

## 5. Longitud de funciones

Las funciones deben tener máximo 20 líneas de código. Si superan este límite, deben dividirse en funciones más pequeñas.

## 6. Principios Clean Code

- Utilizar nombres descriptivos.
- Evitar código duplicado.
- Mantener funciones pequeñas.
- Eliminar código innecesario.
- Mantener una correcta indentación.

## 7. Principios SOLID

- S: Responsabilidad única.
- O: Abierto/cerrado.
- L: Sustitución de Liskov.
- I: Segregación de interfaces.
- D: Inversión de dependencias.

## 8. Cumplimiento

Todos los integrantes del proyecto TecnoShop deben respetar estos estándares durante el desarrollo y mantenimiento del software.

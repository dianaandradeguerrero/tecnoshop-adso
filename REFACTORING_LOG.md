# REGISTRO DE REFACTORIZACIÓN - TECNOSHOP

## 1. Objetivo

Identificar y corregir problemas de calidad del código mediante técnicas de Clean Code para mejorar la legibilidad, reutilización y mantenimiento del sistema.

## 2. Problemas identificados antes de la refactorización

### Problema 1: Función guardar() de productos

- Archivo: publico/js/app.js
- Problema: La función concentraba validaciones, envío de información, procesamiento de respuestas y actualización de datos.
- Solución: Dividir las responsabilidades en funciones auxiliares.

### Problema 2: Código duplicado en ventasApp()

- Archivo: publico/js/app.js
- Problema: Las funciones cargar() y cargarClientes() repetían operaciones de consulta HTTP, lectura de respuestas y manejo de errores.
- Solución: Extraer la lógica común a una función reutilizable.

### Problema 3: Nombre poco descriptivo de la variable API

- Archivo: publico/js/app.js
- Problema: La variable API no describía claramente su finalidad y estaba declarada con var.
- Solución: Renombrarla como API_URL y utilizar const.

## 3. Resultados esperados

- Código más legible.
- Menor duplicación.
- Funciones con responsabilidades específicas.
- Nombres descriptivos.
- Mayor facilidad de mantenimiento.

## 4. Control de versiones

Cada refactorización se registrará mediante un commit independiente en Git.

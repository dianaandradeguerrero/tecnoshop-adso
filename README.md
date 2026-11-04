# 📱 TecnoShop — Sistema de Gestión Integral

**Proyecto académico SENA — Tecnólogo en Análisis y Desarrollo de Software**  
**Fase de ejecución — Integración, pruebas y documentación**

## 1. Descripción del proyecto

TecnoShop es una aplicación web para gestionar los productos, clientes, ventas y reportes de una tienda.

El proyecto utiliza PHP para el funcionamiento de la aplicación web, MySQL para almacenar la información y una API REST desarrollada con Node.js y Express para las operaciones de integración que utilizan sus endpoints.

La aplicación también cuenta con una interfaz web con elementos interactivos y un dashboard para consultar información general del negocio.

## 2. Tecnologías utilizadas

- **Backend web:** PHP.
- **API REST:** Node.js y Express.
- **Frontend:** HTML, CSS y JavaScript.
- **Interactividad:** Alpine.js, según los módulos implementados.
- **Base de datos:** MySQL.
- **Servidor local:** Apache y MySQL mediante XAMPP.
- **Autenticación de la API:** JSON Web Tokens (JWT).
- **Entorno de desarrollo:** Visual Studio Code.
- **Pruebas HTTP:** navegador y PowerShell.

## 3. Requisitos previos

Antes de ejecutar el proyecto, se necesita:

1. XAMPP con Apache y MySQL.
2. Node.js y npm.
3. Visual Studio Code o un editor de código equivalente.
4. Un navegador web actualizado.
5. La base de datos `tecnoshop_db_restaurada`, con las tablas y los datos necesarios.
6. Las dependencias de Node.js especificadas en `backend/package.json`.
7. Los archivos de configuración correspondientes al entorno local.

## 4. Ubicación del proyecto

La carpeta principal del proyecto es:

```text
C:\xampp\htdocs\tecnoshop_vscode
```

La API REST se encuentra en:

```text
C:\xampp\htdocs\tecnoshop_vscode\backend
```

La base de datos utilizada es:

```text
tecnoshop_db_restaurada
```

## 5. Paso 1: iniciar Apache y MySQL

1. Abre el Panel de Control de XAMPP.
2. Haz clic en **Start** en la fila de Apache.
3. Haz clic en **Start** en la fila de MySQL.
4. Comprueba que ambos servicios estén funcionando.

Apache permite acceder a la aplicación PHP desde el navegador y MySQL administra los datos del sistema.

## 6. Paso 2: verificar la base de datos

1. Abre el navegador.
2. Ingresa a:

   `http://localhost/phpmyadmin/`

3. Busca la base de datos `tecnoshop_db_restaurada`.
4. Comprueba que existan las tablas necesarias para el funcionamiento del sistema.

Las tablas principales identificadas en el proyecto son:

- `categorias`
- `clientes`
- `detalle_ventas`
- `productos`
- `ventas`

Si la base de datos no existe, importa el respaldo SQL correspondiente a la versión restaurada del proyecto.

**Importante:** no crees una base de datos vacía con el nombre `tecnoshop_db_restaurada` si ya existe una base de datos con información. Tampoco importes un respaldo sin verificar primero su contenido.

## 7. Paso 3: configurar las variables de entorno de la API

La API utiliza un archivo `.env` ubicado en:

```text
C:\xampp\htdocs\tecnoshop_vscode\backend\.env
```

Este archivo contiene la configuración de conexión a MySQL, el puerto del servidor y el secreto utilizado para JWT.

Comprueba que la configuración utilice la base de datos correcta:

```text
DB_NAME=tecnoshop_db_restaurada
```

Las demás variables deben coincidir con los nombres que utiliza el código de conexión y autenticación del proyecto.

No publiques el archivo `.env`, las contraseñas ni el secreto JWT. El archivo `.env.example` puede utilizarse como plantilla si está actualizado.

## 8. Paso 4: instalar las dependencias de la API

Abre Visual Studio Code y selecciona **Terminal → Nueva terminal**.

Ejecuta:

```powershell
cd C:\xampp\htdocs\tecnoshop_vscode\backend
npm install
```

Si PowerShell presenta problemas al ejecutar npm, utiliza:

```powershell
npm.cmd install
```

Este comando instala las dependencias declaradas en `package.json`.

La instalación normalmente se realiza una vez y se repite cuando sea necesario recuperar o actualizar las dependencias del proyecto.

## 9. Paso 5: iniciar la API REST

En una terminal de Visual Studio Code, ejecuta:

```powershell
cd C:\xampp\htdocs\tecnoshop_vscode\backend
node server.js
```

Si el servidor inicia correctamente, debe mostrar mensajes similares a estos:

```text
Servidor ejecutándose en http://localhost:3000
Base de datos configurada: tecnoshop_db_restaurada
```

Mantén abierta esta terminal mientras utilizas la API.

Para comprobar su funcionamiento, abre:

`http://localhost:3000/`

La respuesta debe indicar que la API REST está funcionando correctamente y mostrar el nombre de la base de datos configurada.

### Rutas principales

- `GET /`: comprueba el funcionamiento de la API.
- `/api/auth`: contiene las rutas de autenticación.
- `/api/productos`: contiene las rutas de productos protegidas mediante el middleware de autenticación.

Consulta los archivos de rutas dentro de `backend/routes` para conocer los métodos HTTP y las operaciones disponibles.

Las rutas protegidas requieren un token JWT válido cuando así lo establece el middleware.

## 10. Paso 6: abrir la aplicación web

Con Apache y MySQL funcionando, abre el navegador e ingresa a:

`http://localhost/tecnoshop_vscode/`

Inicia sesión con las credenciales configuradas para el entorno de pruebas.

Desde el menú principal puedes acceder a los módulos disponibles, como:

- Dashboard.
- Productos.
- Clientes.
- Ventas.
- Reportes.

La disponibilidad de cada módulo depende de la implementación actual del proyecto.

## 11. Paso 7: verificar el funcionamiento integrado

Para comprobar el funcionamiento de TecnoShop:

1. Inicia sesión en la aplicación.
2. Abre el módulo de productos.
3. Consulta el listado de productos.
4. Registra un producto de prueba.
5. Verifica que aparezca en el listado.
6. Modifica sus datos.
7. Comprueba que los cambios se hayan guardado.
8. Elimina el producto de prueba.
9. Confirma que ya no aparezca en el listado.
10. Verifica los cambios en MySQL mediante phpMyAdmin o MySQL Workbench.
11. Comprueba que la API responda correctamente en `http://localhost:3000/`.

Para las pruebas de rendimiento, abre las herramientas de desarrollador del navegador con `F12` y selecciona **Network**. El filtro **Fetch/XHR** permite observar las peticiones de datos y la columna **Time** muestra sus tiempos.

## 12. Pruebas y manejo de errores

Durante las pruebas del proyecto se verificaron los siguientes escenarios:

- **API disponible:** la ruta principal devolvió una respuesta correcta.
- **Token inválido o expirado:** la API respondió con HTTP 401.
- **Error interno de prueba:** se obtuvo HTTP 500 mediante una ruta temporal.
- **Ruta temporal retirada:** la API respondió HTTP 404 al solicitarla después de su eliminación.
- **API detenida:** se comprobó el comportamiento de la aplicación web mientras Node.js no estaba ejecutándose.

Durante las pruebas se observó que algunas operaciones del módulo PHP podían seguir consultando o modificando datos directamente en MySQL aun cuando la API de Node.js estaba detenida.

El comportamiento depende de la ruta utilizada por cada módulo.

## 13. Solución de problemas

### La aplicación PHP no abre

- Comprueba que Apache esté iniciado.
- Verifica que la carpeta esté ubicada dentro de `htdocs`.
- Confirma que la URL sea `http://localhost/tecnoshop_vscode/`.
- Revisa los mensajes de error de PHP y Apache.

### MySQL no inicia

- Revisa el estado del servicio en XAMPP.
- Comprueba si otro servicio está utilizando el puerto configurado.
- Consulta los registros de error antes de cambiar la configuración.

### La API no responde

- Comprueba que Node.js esté instalado.
- Abre una terminal en la carpeta `backend`.
- Ejecuta `node server.js`.
- Verifica que el puerto 3000 esté disponible.
- Revisa la configuración del archivo `.env`.

### Error de conexión con la base de datos

- Comprueba que MySQL esté iniciado.
- Verifica el host, el usuario y la contraseña.
- Confirma que el nombre de la base de datos sea `tecnoshop_db_restaurada`.
- Revisa los mensajes de error de la aplicación o de la API.

### Error de autenticación

- Comprueba que las credenciales sean correctas.
- Verifica que el token JWT se genere y se envíe correctamente.
- Para las rutas protegidas, utiliza el encabezado `Authorization` con el formato `Bearer <token>`.

## 14. Seguridad

- No publiques credenciales ni contraseñas.
- No compartas tokens JWT válidos.
- No publiques el archivo `.env`.
- Utiliza datos de prueba para validar operaciones de eliminación.
- No incluyas secretos de servicios externos en el repositorio.
- Revisa las dependencias antes de actualizarlas.

## 15. Información académica

**Proyecto:** TecnoShop  
**Programa:** Tecnólogo en Análisis y Desarrollo de Software  
**Institución:** SENA  
**Tipo de proyecto:** Aplicación web con base de datos MySQL y API REST.

Este README describe los pasos básicos para configurar, iniciar y probar el proyecto en un entorno local.

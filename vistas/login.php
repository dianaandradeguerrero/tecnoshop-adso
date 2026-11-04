<?php
$baseUrl = '/tecnoshop_vscode/'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión | TecnoShop</title>
  <link rel="stylesheet" href="<?= $baseUrl ?>publico/css/style.css">

  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      background: #f4f7fb;
      font-family: Arial, sans-serif;
    }

    .login-card {
      width: min(420px, calc(100% - 40px));
      box-sizing: border-box;
      padding: 32px;
      background: white;
      border-radius: 16px;
      box-shadow: 0 8px 28px #00000012;
    }

    .login-card h1 {
      margin-top: 0;
      color: #1e293b;
    }

    .login-card p {
      color: #64748b;
    }

    .login-card label {
      display: block;
      margin: 18px 0 7px;
      font-weight: bold;
    }

    .login-card input {
      width: 100%;
      box-sizing: border-box;
      padding: 12px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
    }

    .login-card button {
      width: 100%;
      margin-top: 22px;
      padding: 13px;
      border: 0;
      border-radius: 8px;
      background: #38a800;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    .login-card button:disabled {
      opacity: 0.65;
      cursor: wait;
    }

    #mensaje {
      margin-top: 15px;
    }
  </style>
</head>

<body>
  <main class="login-card">
    <h1>📱 TecnoShop</h1>
    <p>Inicia sesión para acceder al sistema de gestión.</p>

    <form id="formLogin">
      <label for="email">Correo electrónico</label>
      <input
        id="email"
        name="email"
        type="email"
        autocomplete="username"
        required
        value="ana@correo.com">

      <label for="password">Contraseña</label>
      <input
        id="password"
        name="password"
        type="password"
        autocomplete="current-password"
        required>

      <button id="btnLogin" type="submit">Iniciar sesión</button>
      <div id="mensaje" role="alert" aria-live="polite"></div>
    </form>
  </main>

  <script>
    (() => {
      const API_LOGIN = 'http://localhost:3000/api/auth/login';
      const URL_SESION =
        '/tecnoshop_vscode/index.php?modulo=login&accion=establecerSesion';
      const DESTINO = '/tecnoshop_vscode/';

      const form = document.getElementById('formLogin');
      const mensaje = document.getElementById('mensaje');
      const boton = document.getElementById('btnLogin');

      // No reutilizar tokens antiguos para entrar automáticamente.
      localStorage.removeItem('tecnoshop_token');
      localStorage.removeItem('tecnoshop_user');

      form.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        mensaje.textContent = '';
        mensaje.style.color = '#b91c1c';
        boton.disabled = true;
        boton.textContent = 'Validando...';

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        try {
          const respuesta = await fetch(API_LOGIN, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              email,
              password
            })
          });

          const datos = await respuesta.json();

          if (!respuesta.ok || !datos.token || !datos.user) {
            throw new Error(
              datos.mensaje ||
              datos.message ||
              'Correo electrónico o contraseña incorrectos.'
            );
          }

          // Solicitar a PHP que valide el token y cree la sesión.
          const respuestaSesion = await fetch(URL_SESION, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              token: datos.token
            })
          });

          const sesion = await respuestaSesion.json();

          if (!respuestaSesion.ok || !sesion.ok) {
            throw new Error(
              sesion.mensaje ||
              'No fue posible establecer la sesión del sistema.'
            );
          }

          localStorage.setItem('tecnoshop_token', datos.token);
          localStorage.setItem(
            'tecnoshop_user',
            JSON.stringify(datos.user)
          );

          window.location.replace(DESTINO);

        } catch (error) {
          mensaje.textContent =
            error.message === 'Failed to fetch' ?
            'No se pudo conectar con la API. Comprueba que esté ejecutándose.' :
            error.message;
        } finally {
          boton.disabled = false;
          boton.textContent = 'Iniciar sesión';
        }
      });
    })();
  </script>
</body>

</html>
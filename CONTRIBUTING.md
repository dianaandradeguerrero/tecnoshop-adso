# Guía de contribución — TecnoShop

Este documento define el flujo de trabajo para colaborar en el proyecto **TecnoShop ADSO**. Su propósito es mantener el código organizado, facilitar la integración de cambios y conservar un historial de Git claro.

Repositorio: https://github.com/dianaandradeguerrero/tecnoshop-adso

## 1. Organización de ramas

Se utilizan tres tipos de ramas:

| Rama | Propósito |
|---|---|
| `main` | Contiene la versión principal y estable del proyecto. Los cambios deben llegar aquí cuando estén revisados y listos para integrarse como versión estable. |
| `develop` | Rama de integración. Recibe los cambios terminados de las ramas de trabajo mediante Pull Request. |
| `feature/*` | Ramas temporales para desarrollar funcionalidades o módulos específicos sin modificar directamente `develop` o `main`. |

### Crear una rama para una funcionalidad

Antes de comenzar una tarea, actualizar la rama `develop` y crear una rama nueva basada en ella:

```bash
git checkout develop
git pull origin develop
git checkout -b feature/nombre-del-modulo
```

Ejemplo utilizado en el proyecto:

```bash
git checkout -b feature/modulo-principal
```

Los nombres de las ramas deben ser claros, en minúsculas y separados por guiones. Para otros tipos de trabajo se pueden utilizar prefijos como `fix/` para correcciones y `docs/` para documentación.

## 2. Convención para los mensajes de commit

Los mensajes deben indicar de forma breve y clara el tipo de cambio realizado. Se utiliza el formato:

```text
 tipo: descripción breve en minúsculas
```

Tipos principales:

| Prefijo | Cuándo utilizarlo | Ejemplo |
|---|---|---|
| `feat:` | Cuando se agrega una funcionalidad. | `feat: implementar interfaz inicial del sistema` |
| `fix:` | Cuando se corrige un error. | `fix: corregir validación del inventario` |
| `docs:` | Cuando se crea o actualiza documentación. | `docs: agregar guía de contribución` |
| `style:` | Cuando se aplican cambios de formato que no modifican la lógica. | `style: aplicar formato al código` |
| `refactor:` | Cuando se reorganiza o mejora el código sin cambiar su comportamiento esperado. | `refactor: reutilizar consultas del módulo de ventas` |

Cada commit debe representar un cambio concreto. Evita mensajes genéricos como `cambios`, `avance` o `actualización`.

## 3. Crear y publicar cambios

Cuando el trabajo de la rama esté listo:

1. Revisar los archivos modificados con `git status`.
2. Preparar los archivos que se van a incluir.
3. Crear un commit con un mensaje que siga la convención anterior.
4. Publicar la rama en GitHub.

Ejemplo:

```bash
git add .
git commit -m "feat: completar modulo principal"
git push -u origin feature/modulo-principal
```

Antes de preparar los archivos, verificar que no se incluyan credenciales, contraseñas, archivos `.env`, dependencias descargadas (`node_modules`) ni archivos temporales o copias de seguridad.

## 4. Crear un Pull Request (PR)

Los cambios de una rama `feature/*` se integran en `develop` mediante Pull Request en GitHub:

1. Publicar la rama de trabajo en GitHub con `git push`.
2. Abrir el repositorio y seleccionar **Pull requests → New pull request**.
3. Configurar `develop` como rama base (**base**) y la rama de trabajo como comparación (**compare**). Por ejemplo: `feature/modulo-principal`.
4. Escribir un título claro, preferiblemente con el prefijo convencional, y describir qué se modificó y para qué.
5. Revisar los archivos y comprobar que GitHub indique que los cambios se pueden fusionar sin conflictos.
6. Crear el Pull Request y revisar su contenido.
7. Cuando esté listo, seleccionar **Merge pull request** y confirmar la fusión.

Después de la fusión, actualizar la rama local `develop`:

```bash
git checkout develop
git pull origin develop
```

La rama `feature/*` puede conservarse mientras se necesite para el trabajo o para las evidencias del proyecto. Su eliminación, cuando corresponda, se realiza después de confirmar que el PR quedó fusionado.

## 5. Cinco comandos Git de uso diario

| Comando | Uso en el trabajo diario de TecnoShop |
|---|---|
| `git status` | Muestra la rama actual y los archivos modificados, preparados o sin seguimiento. Permite revisar qué se va a guardar antes de un commit. |
| `git add .` | Prepara los cambios del directorio de trabajo para incluirlos en el siguiente commit. Antes de ejecutarlo, se deben revisar los archivos para evitar agregar información privada o archivos innecesarios. |
| `git commit -m "tipo: descripción"` | Guarda un conjunto de cambios en el historial local con un mensaje claro y convencional. |
| `git push` | Envía los commits locales de la rama actual al repositorio remoto de GitHub para compartir el avance. |
| `git pull` | Descarga e integra los cambios publicados en la rama remota correspondiente, para mantener la copia local actualizada. |

## 6. Recomendaciones para trabajar en equipo

- Crear una rama independiente para cada funcionalidad, corrección o tarea documental.
- Evitar trabajar directamente sobre `main` y `develop`.
- Actualizar `develop` antes de iniciar una nueva rama de trabajo.
- Hacer commits pequeños, relacionados con un solo propósito y con mensajes descriptivos.
- Revisar el destino del Pull Request: las ramas `feature/*` deben proponer sus cambios hacia `develop`.
- Comprobar el resultado de la fusión y actualizar la rama local después del Merge.
- No publicar contraseñas, tokens, claves privadas ni datos de conexión en GitHub.

---

**Proyecto:** TecnoShop ADSO  
**Repositorio:** dianaandradeguerrero/tecnoshop-adso  
**Documento:** CONTRIBUTING.md

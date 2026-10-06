# Gestor de Resoluciones CORPONOR

Proyecto base para PHP 8.1 + CodeIgniter 3.1.13 + MySQL + AdminLTE 4 + Bootstrap 5.

## Funcionalidades
- Login por documento y clave.
- Recuperación de clave por token de un solo uso enviado por correo, válido durante 1 hora.
- Usuarios administradores: creación de usuarios y asignación de rol administrador.
- Grupos de resoluciones con consecutivo automático.
- Carga de resoluciones: título, fecha, PDF o URL, imagen previa opcional y grupo.
- Administración/edición de resoluciones.
- Consulta pública sin autenticación, separada por grupos y con filtros por título, fecha y grupo.
- AdminLTE configurado con header, sidebar y footer.
- CSRF, sesiones HttpOnly/SameSite, password_hash, tokens hasheados y validación de carga.

## Instalación
1. Requiere PHP 8.1, Composer, MySQL/MariaDB y extensiones mysqli, mbstring, openssl, fileinfo.
2. Ejecute `composer install --no-dev --optimize-autoloader` en la raíz. Esto instala CodeIgniter 3.1.13 en `vendor/`.

En producción, `vendor/` no se versiona. Después de cada despliegue por Git, ejecute Composer desde la carpeta del proyecto:

```bash
composer install --no-dev --optimize-autoloader
```

Si el servidor usa varias versiones de PHP, confirme que Composer se ejecute con PHP 8.1.
3. Importe `database/corponor_resoluciones.sql`.
4. Para una base existente, ejecute `database/migracion_estadisticas_permisos.sql`. Crea los eventos de visitas/descargas y la asignación de usuarios a grupos sin eliminar datos.
4. Configure variables de entorno del servidor o ajuste `application/config/database.php`:
   - DB_HOST, DB_USER, DB_PASS, DB_NAME
   - APP_URL y APP_KEY
5. Configure SMTP:
   - MAIL_HOST, MAIL_USER, MAIL_PASS, MAIL_PORT, MAIL_CRYPTO, MAIL_FROM
6. Dé permisos de escritura a `application/cache`, `application/logs`, `uploads/resoluciones` y `uploads/imagenes`.
7. En Apache habilite mod_rewrite y AllowOverride All. Para Nginx redirija rutas inexistentes a `/index.php`.

## Usuario inicial
- Documento: `1000000000`
- Clave: `Admin123*`
- Correo: `admin@corponor.gov.co`

**Cambie inmediatamente la clave y el correo del administrador en producción.**

## Notas visuales
Se aplican los colores corporativos publicados por CORPONOR: #3A66AC, #AECA49, #7CC4F0 y #111E2A y Montserrat como tipografía complementaria oficial. Ezra ExtraBold se reserva en el manual para el logotipo. El isotipo local incluido es un marcador estilizado para desarrollo; antes de producción sustituya `brand-mark` por el archivo vectorial/logotipo oficial provisto por Comunicaciones de CORPONOR para conservar sus proporciones exactas.

## Dependencias front-end
Bootstrap 5.3.8, Bootstrap Icons y AdminLTE 4 rc7 se cargan por CDN. Si el entorno no tiene salida a Internet, descargue esos activos a `assets/vendor/` y cambie las referencias de los layouts.

## Actualizacion de usuarios y permisos

Antes de desplegar estos cambios sobre una base existente, ejecutar una sola vez
`database/migracion_roles_usuarios.sql`. Los esquemas de instalacion nueva ya incluyen
el rol inicial. `es_admin` utiliza 0 para usuario, 1 para administrador y 2 para
superadministrador. La migracion conserva como superadministrador la cuenta inicial.
Los permisos se actualizan desde la base de datos en cada solicitud autenticada.

En Usuarios se pueden editar nombres, documento alfanumerico, correo y rol.
Solo el superadministrador puede conceder el rol de superadministrador; nadie puede
cambiar su propio rol. Administradores pueden restablecer cualquier clave y los
usuarios regulares disponen de Mi clave para restablecer solo la propia.

Eliminar resoluciones y grupos requiere acceso al grupo (o ser superadministrador).
Solo se eliminan grupos sin resoluciones, incluidas las inactivas. Los archivos
fisicos se conservan al eliminar una resolucion; el registro deja de publicarse.
La descarga directa se ofrece para archivos PDF cargados, no para URLs externas.

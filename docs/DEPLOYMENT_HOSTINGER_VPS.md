# Despliegue en Hostinger VPS

## Configuración recomendada

- Ubuntu 24.04 LTS.
- Mínimo: 2 vCPU, 4 GB de RAM y 50 GB NVMe.
- Nginx.
- PHP 8.3-FPM.
- MySQL 8.
- Node.js 22.
- Composer 2.
- HTTPS mediante Certbot.
- Backups automáticos del VPS.

## Información necesaria

Antes del despliegue debemos tener:

- IP pública del VPS.
- Dominio o subdominio definitivo.
- Acceso SSH.
- Contraseña segura para MySQL.
- Correo y contraseña del administrador inicial.
- Client ID de Google Calendar.
- Client Secret de Google Calendar.
- Refresh Token de Google Calendar.
- Calendar ID.

Nunca deben guardarse contraseñas, tokens ni el archivo `.env` real en Git.

## Preparar dominio y servidor

Crear registros DNS dirigidos a la IP del VPS:

```text
A    @      IP_DEL_VPS
A    www    IP_DEL_VPS

```

## Instalar servicios del servidor

Conectarse al VPS mediante SSH y actualizar Ubuntu:

```bash
sudo apt update
sudo apt upgrade -y
```

Instalar los servicios principales:

```bash
sudo apt install -y nginx mysql-server git unzip curl certbot python3-certbot-nginx
```

Instalar PHP 8.3 y sus extensiones:

```bash
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl php8.3-xml php8.3-mbstring php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath
```

Verificar las instalaciones:

```bash
php -v
nginx -v
mysql --version
```

## Instalar Composer, Node.js y pnpm

Instalar Composer:

```bash
sudo apt install -y composer
composer --version
```

Instalar Node.js 22:

```bash
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt install -y nodejs
sudo corepack enable
```

Verificar:

```bash
node --version
pnpm --version
```

Node debe ser versión 22 o superior porque el proyecto utiliza Vite 7.

## Configurar firewall

```bash
sudo ufw allow OpenSSH
sudo ufw allow "Nginx Full"
sudo ufw enable
sudo ufw status
```

El puerto 3306 de MySQL no debe exponerse públicamente.

## Crear la base de datos nueva

Entrar a MySQL:

```bash
sudo mysql
```

Ejecutar las siguientes instrucciones, reemplazando la contraseña:

```sql
CREATE DATABASE medicina_regenerativa
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER 'medicina_app'@'localhost'
IDENTIFIED BY 'CONTRASENA_MYSQL_SEGURA';

GRANT ALL PRIVILEGES
ON medicina_regenerativa.*
TO 'medicina_app'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

La base comenzará completamente vacía. No se importarán pacientes, fotografías ni datos de ejemplo de la instalación local.

## Descargar e instalar el proyecto

Crear el directorio:

```bash
sudo mkdir -p /var/www/medicina-regenerativa
sudo chown -R "$USER":www-data /var/www/medicina-regenerativa
cd /var/www/medicina-regenerativa
```

Clonar la rama principal:

```bash
git clone https://github.com/chaiLuna2003/Medicina-Regenerativa.git .
git checkout main
git log -1 --oneline --decorate
```

Instalar dependencias PHP de producción:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
```

Compilar los recursos frontend:

```bash
pnpm install --frozen-lockfile
pnpm run build
```

En producción no deben ejecutarse `composer update` ni `pnpm update`.

## Configurar Laravel

Crear el archivo privado de entorno:

```bash
cp .env.production.example .env
nano .env
```

Completar en `.env`:

- Dominio definitivo en `APP_URL`.
- Contraseña de MySQL.
- Correo y contraseña del administrador inicial.
- Credenciales de Google Calendar.

Activar temporalmente el administrador inicial:

```dotenv
INITIAL_ADMIN_ENABLED=true
```

Generar una clave exclusiva:

```bash
php artisan key:generate
```

Confirmar que Laravel apunta a la base nueva:

```bash
php artisan about
```

Crear todas las tablas y ejecutar los seeders:

```bash
php artisan migrate --seed --force
```

Después de crear correctamente el administrador, editar `.env` y dejar:

```dotenv
INITIAL_ADMIN_ENABLED=false
INITIAL_ADMIN_EMAIL=
INITIAL_ADMIN_PASSWORD=
```

Nunca dejar la contraseña inicial guardada permanentemente en `.env`.

## Configurar permisos

```bash
sudo chown -R "$USER":www-data /var/www/medicina-regenerativa
sudo find /var/www/medicina-regenerativa -type d -exec chmod 750 {} \;
sudo find /var/www/medicina-regenerativa -type f -exec chmod 640 {} \;
sudo chmod -R ug+rwx storage bootstrap/cache
```

Las fotografías y expedientes se almacenan de forma privada. Actualmente no es necesario ejecutar `php artisan storage:link`.

## Optimizar Laravel

Después de desactivar y retirar las credenciales del administrador inicial:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Configurar Nginx

Crear el archivo del sitio:

```bash
sudo nano /etc/nginx/sites-available/medicina-regenerativa
```

Contenido:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name tu-dominio.com www.tu-dominio.com;

    root /var/www/medicina-regenerativa/public;
    index index.php;

    charset utf-8;
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activar el sitio:

```bash
sudo ln -s /etc/nginx/sites-available/medicina-regenerativa /etc/nginx/sites-enabled/medicina-regenerativa
sudo nginx -t
sudo systemctl reload nginx
```

Si aparece la página predeterminada de Nginx:

```bash
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

## Configurar PHP para archivos y PDF

Editar:

```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

Valores recomendados:

```ini
upload_max_filesize = 20M
post_max_size = 25M
memory_limit = 512M
max_execution_time = 120
```

Aplicar cambios:

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

## Activar HTTPS

Cuando el dominio ya apunte al VPS:

```bash
sudo certbot --nginx -d tu-dominio.com -d www.tu-dominio.com
```

Verificar la renovación automática:

```bash
sudo certbot renew --dry-run
```

Confirmar que HTTP redirige a HTTPS y que el certificado es válido.

## Worker y cron

Actualmente el proyecto no contiene trabajos en cola ni tareas programadas.

Se utilizará:

```dotenv
QUEUE_CONNECTION=sync
```

No es necesario configurar Supervisor ni cron en el primer despliegue.

## Backups obligatorios

Respaldar periódicamente:

- La base MySQL.
- El directorio `storage/app/private`.
- El archivo `.env` en una ubicación segura.
- La configuración de Nginx.

Ejemplo de respaldo manual:

```bash
mysqldump -u medicina_app -p medicina_regenerativa > respaldo.sql
```

También deben activarse los backups automáticos o snapshots de Hostinger.

## Pruebas de humo en producción

Comprobar mediante HTTPS:

- Login y cierre de sesión.
- Bloqueo del registro público.
- Enlace de soporte por WhatsApp.
- Usuarios y permisos.
- Alta, edición y desactivación de médicos.
- Alta y edición de pacientes.
- Fotografías privadas.
- Citas presenciales.
- Videoconsultas.
- Creación y cancelación en Google Calendar.
- Signos vitales.
- Historia clínica.
- Exploración física.
- Casos clínicos y evoluciones.
- Recetas.
- Estudios.
- Hoja diaria.
- Todos los PDF.
- Accesos de administración, recepción, médico y enfermería.

## Verificación técnica final

```bash
php artisan about
php artisan migrate:status
php artisan route:list
composer audit --locked
git status
git log -1 --oneline --decorate
```

El árbol de Git debe quedar limpio.

## Actualizaciones posteriores

Antes de actualizar, generar un backup. Después ejecutar:

```bash
cd /var/www/medicina-regenerativa
git pull origin main
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
pnpm install --frozen-lockfile
pnpm run build
php artisan migrate --force
php artisan optimize
sudo systemctl reload php8.3-fpm
```
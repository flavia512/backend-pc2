#!/bin/sh
set -e

echo "==> Preparando entorno Laravel..."

# Copiar .env si no existe (usa .env.docker como base para Docker)
if [ ! -f /var/www/.env ]; then
    if [ -f /var/www/.env.docker ]; then
        cp /var/www/.env.docker /var/www/.env
        echo "    .env creado desde .env.docker"
    else
        cp /var/www/.env.example /var/www/.env
        echo "    .env creado desde .env.example"
    fi
fi

# Generar APP_KEY si no está configurada
if grep -q "APP_KEY=$" /var/www/.env || grep -q "APP_KEY=base64:$" /var/www/.env; then
    echo "==> Generando APP_KEY..."
    php artisan key:generate --no-interaction --force
fi

# Generar JWT secret si no está configurado
if ! grep -q "JWT_SECRET=" /var/www/.env || grep -q "JWT_SECRET=$" /var/www/.env; then
    echo "==> Generando JWT_SECRET..."
    php artisan jwt:secret --no-interaction --force
fi

# Limpiar solo config (no cache, porque la tabla puede no existir aún)
echo "==> Limpiando caché de configuración..."
php artisan config:clear

# Esperar a que MySQL esté listo
echo "==> Esperando a que la base de datos esté lista..."
until php -r "new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" > /dev/null 2>&1; do
    sleep 2
done

# Ejecutar migraciones
echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# Limpiar cache DESPUÉS de las migraciones (la tabla ya existe)
echo "==> Limpiando caché de aplicación..."
php artisan cache:clear

# Optimizar para producción
echo "==> Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Listo. Iniciando PHP-FPM..."
exec "$@"

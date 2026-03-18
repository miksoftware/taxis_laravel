#!/bin/bash
set -e

echo "=== Taxi Diamantes - Iniciando ==="

# Esperar a que MySQL esté listo (extra safety además del healthcheck)
echo "Verificando conexión a base de datos..."
MAX_RETRIES=30
RETRY=0
until php artisan db:monitor --databases=mysql 2>/dev/null || [ $RETRY -eq $MAX_RETRIES ]; do
    echo "Esperando base de datos... intento $((RETRY+1))/$MAX_RETRIES"
    sleep 2
    RETRY=$((RETRY+1))
done

if [ $RETRY -eq $MAX_RETRIES ]; then
    echo "ADVERTENCIA: No se pudo verificar la BD, continuando de todas formas..."
fi

# Generar key si no existe
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo "Generando APP_KEY..."
    php artisan key:generate --force
fi

# Crear directorios de storage si no existen
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views storage/framework/cache/laravel-excel
chown -R www-data:www-data storage bootstrap/cache

# Migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Seed (solo si la tabla usuarios está vacía)
USUARIO_COUNT=$(php artisan tinker --execute="echo \App\Models\Usuario::count();" 2>/dev/null || echo "0")
if [ "$USUARIO_COUNT" = "0" ]; then
    echo "Ejecutando seeders..."
    php artisan db:seed --force
fi

# Cache de configuración y rutas para producción
echo "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear link de storage si no existe
php artisan storage:link 2>/dev/null || true

echo "=== Iniciando servicios con Supervisor ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

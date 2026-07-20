#!/bin/sh
set -e
cd /var/www/html

DB_HOST="${DB_HOST:-mysql}"
DB_DATABASE="${DB_DATABASE:-insurance_db}"
DB_USER="${DB_USER:-insurance}"
DB_PASSWORD="${DB_PASSWORD:-insurance}"

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing dependencies..."
    for i in 1 2 3; do
        composer update --no-interaction --prefer-dist && break
        echo "Retry $i..."; sleep 3
    done
fi

[ ! -f ".env" ] && cp .env.example .env 2>/dev/null || true

echo "Waiting for MySQL at ${DB_HOST}..."
RETRIES=0
until php -r "try{new PDO('mysql:host=${DB_HOST};dbname=${DB_DATABASE}','${DB_USER}','${DB_PASSWORD}');echo 'ok';}catch(Exception \$e){exit(1);}" 2>/dev/null; do
    RETRIES=$((RETRIES+1))
    [ $RETRIES -ge 40 ] && echo "MySQL not ready after retries, continuing..." && break
    sleep 2
done

echo "Generating JWT keys if needed..."
mkdir -p config/jwt
if [ ! -f "config/jwt/private.pem" ]; then
    openssl genrsa -passout pass:insurance_jwt_passphrase -out config/jwt/private.pem 4096 2>/dev/null
    openssl rsa -passin pass:insurance_jwt_passphrase -in config/jwt/private.pem -pubout -out config/jwt/public.pem 2>/dev/null
fi

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction 2>/dev/null || true

chmod -R 775 var/ 2>/dev/null || true

if [ "$#" -gt 0 ]; then
    exec "$@"
fi

exec php-fpm

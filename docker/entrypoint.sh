#!/bin/sh
cd /var/www/html

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing dependencies..."
    for i in 1 2 3; do
        composer update --no-interaction --prefer-dist --no-security-blocking && break
        echo "Retry $i..."; sleep 3
    done
fi

[ ! -f ".env" ] && cp .env.example .env

echo "Waiting for MySQL..."
RETRIES=0
until php -r "try{new PDO('mysql:host='.getenv('DB_HOST','mysql').';dbname='.getenv('DB_DATABASE','insurance_db'),getenv('DB_USER','insurance'),getenv('DB_PASSWORD','insurance'));echo 'ok';}catch(Exception \$e){exit(1);}" 2>/dev/null; do
    RETRIES=$((RETRIES+1)); [ $RETRIES -ge 40 ] && break; sleep 2
done

echo "Generating JWT keys..."
mkdir -p config/jwt
if [ ! -f "config/jwt/private.pem" ]; then
    openssl genrsa -passout pass:insurance_jwt_passphrase -out config/jwt/private.pem 4096 2>/dev/null
    openssl rsa -passin pass:insurance_jwt_passphrase -in config/jwt/private.pem -pubout -out config/jwt/public.pem 2>/dev/null
fi

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction 2>/dev/null || true

chmod -R 775 var/ 2>/dev/null || true

php-fpm

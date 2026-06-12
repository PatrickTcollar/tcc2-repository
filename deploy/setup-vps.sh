#!/bin/bash
# ============================================================
# PulmoEspir — Setup completo VPS Ubuntu 24.04
# Domínio: ibitsintelligence.com.br | IP: 2.25.189.124
# ============================================================
set -e
export DEBIAN_FRONTEND=noninteractive

DOMAIN="ibitsintelligence.com.br"
REPO="https://github.com/PatrickTcollar/tcc2-repository.git"
APP_DIR="/var/www/pulmoespir"
DB_NAME="pulmoespir"
DB_USER="pulmoespir"
DB_PASS="2qlCZUg9vWhJ0jpJKS8wcPyWLClAVcj"
GEMINI_KEY="AIzaSyAh-y2etWUdRcyaHD5lQLUAaK3vd0Jsz14"

echo "=========================================="
echo " PulmoEspir — Deploy VPS"
echo " $(date)"
echo "=========================================="

# ── 1. Atualizar sistema ───────────────────────────────────
echo "[1/9] Atualizando sistema..."
apt-get update -qq && apt-get upgrade -y -qq

# ── 2. Instalar dependências ───────────────────────────────
echo "[2/9] Instalando Nginx, PHP 8.3, PostgreSQL, Node.js 20..."

# Nginx
apt-get install -y -qq nginx

# PHP 8.3 + extensões Laravel
apt-get install -y -qq software-properties-common
add-apt-repository ppa:ondrej/php -y
apt-get update -qq
apt-get install -y -qq \
  php8.3 php8.3-fpm php8.3-cli php8.3-common \
  php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-zip \
  php8.3-curl php8.3-bcmath php8.3-gd php8.3-intl \
  php8.3-tokenizer php8.3-fileinfo php8.3-opcache

# PostgreSQL
apt-get install -y -qq postgresql postgresql-contrib

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node.js 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y -qq nodejs

# Git, Certbot, unzip
apt-get install -y -qq git unzip certbot python3-certbot-nginx

echo "   PHP: $(php8.3 --version | head -1)"
echo "   Node: $(node --version)"
echo "   Composer: $(composer --version --no-ansi 2>&1 | head -1)"

# ── 3. Configurar PostgreSQL ───────────────────────────────
echo "[3/9] Configurando PostgreSQL..."
sudo -u postgres psql -c "CREATE USER $DB_USER WITH PASSWORD '$DB_PASS';" 2>/dev/null || true
sudo -u postgres psql -c "CREATE DATABASE $DB_NAME OWNER $DB_USER;" 2>/dev/null || true
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE $DB_NAME TO $DB_USER;" 2>/dev/null || true
echo "   Banco '$DB_NAME' criado para usuário '$DB_USER'"

# ── 4. Clonar repositório ──────────────────────────────────
echo "[4/9] Clonando repositório..."
if [ -d "$APP_DIR" ]; then
  cd "$APP_DIR" && git pull origin main
else
  git clone "$REPO" "$APP_DIR"
fi
cd "$APP_DIR"

# ── 5. Configurar .env de produção ─────────────────────────
echo "[5/9] Configurando .env de produção..."
cat > "$APP_DIR/.env" <<EOF
APP_NAME=PulmoEspir
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$DOMAIN
APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS

FILESYSTEM_DISK=local
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

GEMINI_API_KEY=$GEMINI_KEY

MAIL_MAILER=log
EOF

# ── 6. Instalar dependências PHP e Node ────────────────────
echo "[6/9] Instalando dependências (Composer + NPM)..."
cd "$APP_DIR"
composer install --no-dev --optimize-autoloader --no-interaction --quiet
npm ci --quiet
npm run build

# Gerar APP_KEY
php artisan key:generate --force
echo "   APP_KEY gerada"

# ── 7. Permissões e storage ────────────────────────────────
echo "[7/9] Configurando permissões..."
chown -R www-data:www-data "$APP_DIR"
chmod -R 755 "$APP_DIR"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
php artisan storage:link --force 2>/dev/null || true

# ── 8. Migração do banco ───────────────────────────────────
echo "[8/9] Executando migrations..."
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "   Migrations OK"

# ── 9. Configurar Nginx ────────────────────────────────────
echo "[9/9] Configurando Nginx..."

cat > /etc/nginx/sites-available/pulmoespir <<NGINX
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $APP_DIR/public;
    index index.php;

    client_max_body_size 20M;
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* { deny all; }

    error_log  /var/log/nginx/pulmoespir_error.log;
    access_log /var/log/nginx/pulmoespir_access.log;
}
NGINX

ln -sf /etc/nginx/sites-available/pulmoespir /etc/nginx/sites-enabled/pulmoespir
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
systemctl enable nginx php8.3-fpm
systemctl restart php8.3-fpm

echo ""
echo "=========================================="
echo " Setup concluído — iniciando SSL..."
echo "=========================================="

# ── SSL com Let's Encrypt ──────────────────────────────────
certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" \
  --non-interactive --agree-tos \
  --email ibits.intelligence@gmail.com \
  --redirect

echo ""
echo "=========================================="
echo " ✅ PulmoEspir está no ar!"
echo "    https://$DOMAIN"
echo "=========================================="

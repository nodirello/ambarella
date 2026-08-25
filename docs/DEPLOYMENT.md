# AMBARELLA — Production deploy qo‘llanmasi

> Ushbu qo‘llanma Ubuntu 22.04+ + Nginx + MySQL uchun mo‘ljallangan.

---

## 1. Talablar

| Komponent | Versiya |
|---|---|
| PHP | 8.4+ (`cli`, `fpm`, `mysql|pdo_sqlite`, `gd`, `mbstring`, `xml`, `zip`, `curl`, `openssl`, `intl` ixtiyoriy) |
| Composer | 2.x |
| Node.js | 20+ (faqat build uchun) |
| MySQL | 8.0+ |
| Nginx | 1.24+ |

---

## 2. Server tayyorlash

```bash
sudo apt update && sudo apt upgrade -y
sudo adduser deploy
sudo usermod -aG sudo deploy

# Nginx + PHP
sudo apt install -y nginx mysql-server php8.4-cli php8.4-fpm php8.4-mysql \
  php8.4-gd php8.4-mbstring php8.4-xml php8.4-zip php8.4-curl php8.4-bcmath

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node (build uchun)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Firewall
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

---

## 3. Loyihani joylash

```bash
sudo mkdir -p /var/www/ambarella.uz
sudo chown -R $USER:www-data /var/www/ambarella.uz
git clone <repo> /var/www/ambarella.uz
cd /var/www/ambarella.uz

# Dastlabki sozlash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
npm ci && npm run build

# Ruxsatlar
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 4. Ma’lumotlar bazasi

```sql
CREATE DATABASE ambarella CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ambarella'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON ambarella.* TO 'ambarella'@'localhost';
FLUSH PRIVILEGES;
```

`.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ambarella.uz

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ambarella
DB_USERNAME=ambarella
DB_PASSWORD=STRONG_PASSWORD

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Keyin:
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

---

## 5. Nginx

`nginx.conf` (repo ichida tayyor):
```nginx
server {
    listen 80;
    server_name ambarella.uz www.ambarella.uz;
    root /var/www/ambarella.uz/public;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(?:css|js|svg|png|jpg|webp|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
sudo cp nginx.conf /etc/nginx/sites-available/ambarella.uz
sudo ln -s /etc/nginx/sites-available/ambarella.uz /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 6. SSL

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d ambarella.uz -d www.ambarella.uz
```

---

## 7. Cron (schedule)

```bash
crontab -e
# har daqiqada
* * * * * cd /var/www/ambarella.uz && php artisan schedule:run >> /dev/null 2>&1
```

Queues (agar `QUEUE_CONNECTION=database`):
```bash
sudo tee /etc/systemd/system/ambarella-worker.service <<'EOF'
[Unit]
Description=AMBARELLA queue worker
After=network.target

[Service]
User=www-data
WorkingDirectory=/var/www/ambarella.uz
ExecStart=/usr/bin/php artisan queue:work --sleep=2 --tries=3 --timeout=90
Restart=always

[Install]
WantedBy=multi-user.target
EOF
sudo systemctl enable --now ambarella-worker
```

---

## 8. Yangilash

```bash
cd /var/www/ambarella.uz
php artisan down --retry=60

git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize

php artisan up
```

---

## 9. Telegram sozlash

```bash
# .env
TELEGRAM_BOT_TOKEN=123456:ABC...
TELEGRAM_WEBHOOK_SECRET=$(openssl rand -hex 24)
TELEGRAM_WEBHOOK_URL=/webhook/telegram

php artisan telegram:set-webhook
```

---

## 10. Tekshiruv ro‘yxati

- [ ] `https://ambarella.uz/up` → `{"status":"ok"}`
- [ ] `/sitemap.xml` 200
- [ ] Ro‘yxatdan o‘tish → onboarding → dashboard
- [ ] Admin: `/admin` (2FA bilan)
- [ ] Cron buyruqlari logda ko‘rinadi
- [ ] Storage link ishlaydi (`storage/app/public` → `public/storage`)
- [ ] `robots.txt` to‘g‘ri

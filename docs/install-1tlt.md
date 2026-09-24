# Установка на чистый Ubuntu — домен 1tlt.ru

Отдельный VPS, каноникал `https://1tlt.ru`.  
Тот же репозиторий, что и bizdevops.site; домен задаётся через `.env` + `settings.public_url`.

## 0. DNS

В панели домена / Cloudflare:

| Тип | Имя | Значение | Proxy |
|-----|-----|----------|-------|
| A | `@` | IP_НОВОГО_VPS | сначала **DNS only** |
| A | `www` | IP_НОВОГО_VPS | DNS only |

```bash
dig +short 1tlt.ru
# должен показать IP нового сервера
```

## 1. Пакеты (Ubuntu 22.04/24.04)

```bash
sudo apt update
sudo apt install -y apache2 mysql-server git curl unzip \
  php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip \
  certbot python3-certbot-apache

# если php8.2 нет в репозитории — включите ondrej/php:
# sudo apt install -y software-properties-common
# sudo add-apt-repository -y ppa:ondrej/php
# sudo apt update && sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip

sudo a2enmod rewrite proxy proxy_fcgi headers expires deflate ssl
sudo systemctl enable --now apache2 mysql php8.2-fpm
```

Проверка PHP-FPM:

```bash
ls /run/php/php8.2-fpm.sock
# или
ss -lntp | grep php
```

## 2. Код

```bash
sudo mkdir -p /ssd/www
sudo chown -R $USER:www-data /ssd/www
cd /ssd/www
git clone https://github.com/alexevil1979/proflanding.git
cd proflanding
```

Если `dubious ownership`:

```bash
git config --global --add safe.directory /ssd/www/proflanding
```

## 3. .env

```bash
cp .env.example .env
nano .env
```

Минимум для **1tlt.ru**:

```
APP_ENV=production
APP_URL=https://1tlt.ru
APP_KEY=СГЕНЕРИРОВАТЬ
CANONICAL_REDIRECT_FROM=

DB_HOST=127.0.0.1
DB_NAME=proflanding
DB_USER=proflanding
DB_PASS=СИЛЬНЫЙ_ПАРОЛЬ
DB_CHARSET=utf8mb4
```

Ключ:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

На **этом** сервере `CANONICAL_REDIRECT_FROM` оставьте **пустым**.  
(На bizdevops.site можно поставить `CANONICAL_REDIRECT_FROM=proflanding.1tlt.ru`.)

## 4. MySQL

Рекомендуется отдельный пользователь (не root через socket):

```bash
sudo mysql
```

```sql
CREATE DATABASE proflanding CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'proflanding'@'localhost' IDENTIFIED BY 'СИЛЬНЫЙ_ПАРОЛЬ';
GRANT ALL PRIVILEGES ON proflanding.* TO 'proflanding'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Импорт схемы и seed:

```bash
cd /ssd/www/proflanding
mysql -u proflanding -p proflanding < database/schema.sql
mysql -u proflanding -p proflanding < database/seed.sql
mysql -u proflanding -p proflanding < database/migrate_optimize.sql
mysql -u proflanding -p proflanding < database/migrate_seo_analytics.sql
mysql -u proflanding -p proflanding < database/migrate_promo_banner.sql
```

Каноникал в БД:

```bash
mysql -u proflanding -p proflanding -e "INSERT INTO settings (k,v) VALUES ('public_url','https://1tlt.ru') ON DUPLICATE KEY UPDATE v=VALUES(v);"
```

## 5. Права

```bash
sudo mkdir -p /ssd/www/proflanding/public/uploads /ssd/www/proflanding/storage/logs
sudo chown -R www-data:www-data /ssd/www/proflanding
sudo chmod -R 775 /ssd/www/proflanding/storage /ssd/www/proflanding/public/uploads
sudo chmod 640 /ssd/www/proflanding/.env
```

## 6. Apache vhost

```bash
sudo cp /ssd/www/proflanding/deploy/apache-vhost-1tlt.conf.example \
  /etc/apache2/sites-available/1tlt.conf

# ВАЖНО: если php8.2-fpm через sock — в конфиге раскомментируйте вариант B
sudo nano /etc/apache2/sites-available/1tlt.conf

sudo a2dissite 000-default.conf 2>/dev/null || true
sudo a2ensite 1tlt.conf
sudo apache2ctl configtest
sudo systemctl reload apache2
```

Проверка по IP до SSL:

```bash
curl -sI -H "Host: 1tlt.ru" http://127.0.0.1/ | head
curl -s -H "Host: 1tlt.ru" http://127.0.0.1/ | head -5
# должен быть HTML, не <?php
```

Если снова исходник PHP — неверный `SetHandler` (sock vs :9000).

## 7. SSL

```bash
sudo certbot --apache -d 1tlt.ru -d www.1tlt.ru
sudo systemctl reload php8.2-fpm
sudo systemctl reload apache2
```

## 8. Проверки

```bash
curl -sI https://1tlt.ru/ | head -15
curl -s https://1tlt.ru/robots.txt
curl -s https://1tlt.ru/sitemap.xml | head -15
# в loc только https://1tlt.ru/...
```

Админка: `https://1tlt.ru/admin/login`  
Seed: `admin` / `ChangeMe123!` → сразу сменить пароль.

В админке:

- **Контент** → `public_url` = `https://1tlt.ru`
- **Аналитика** — свои счётчики (отдельные от bizdevops)
- **Уведомления** — SMTP / Telegram

## 9. На сервере bizdevops.site (по желанию)

Чтобы старое зеркало `proflanding.1tlt.ru` вело на bizdevops:

```
# в .env bizdevops
CANONICAL_REDIRECT_FROM=proflanding.1tlt.ru
```

```bash
sudo systemctl reload php8.2-fpm 2>/dev/null || sudo systemctl reload php82-fpm
```

Не добавляйте сюда голый `1tlt.ru`, если на другом VPS это основной домен лендинга.

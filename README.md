# Proflanding — продающий лендинг IT-специалиста

Одностраничный лендинг с админкой, заявками и уведомлениями в Telegram + Gmail SMTP.

Стек: **PHP 8.2**, **MySQL 5.7**, **Apache 2.4**, чистый MVC без Laravel/Symfony.

## Возможности

- Публичный лендинг с услугами/пакетами из БД
- Мультиязычность как на hiddifysales.com: `ru` `/`, `en`, `fa`, `zh`, `tr`, `ar` (+ RTL для fa/ar)
- Цены в ₽ и ~$ по курсу ЦБ РФ (кэш в settings, кнопка обновления в админке)
- Форма заявок (CSRF + honeypot + rate limit)
- Админка: заявки, услуги, пакеты, контент, SEO, уведомления, смена пароля
- Уведомления: Telegram Bot API + SMTP Gmail (STARTTLS)
- SEO: Open Graph, JSON-LD, hreflang, sitemap.xml, robots.txt

## Требования

- PHP 8.2+ (на VPS — из сорцов, **php82-fpm** на `127.0.0.1:9000`) с расширениями: `pdo_mysql`, `mbstring`, `openssl`, `json`
- MySQL 5.7+ (InnoDB, utf8mb4)
- Apache 2.4: `mod_rewrite`, `mod_proxy`, `mod_proxy_fcgi`, желательно `mod_expires`, `mod_deflate`, `mod_headers`
- SSL (Let's Encrypt) на уровне хоста

## Установка на VPS

```bash
cd /ssd/www
git clone https://github.com/alexevil1979/proflanding.git
cd proflanding
cp .env.example .env
nano .env
```

### База данных

```bash
mysql -u root -p -e "CREATE DATABASE proflanding CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p proflanding < database/schema.sql
mysql -u root -p proflanding < database/seed.sql
```

Создайте пользователя БД и пропишите его в `.env`.

### .env (обязательно)

- `APP_URL` — https://proflanding.1tlt.ru
- `APP_KEY` — случайная строка 32+ символов
- `DB_*` — доступ к MySQL
- `SMTP_*` — Gmail + **App Password**
- `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`

Сгенерировать `APP_KEY`:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Сгенерировать новый хеш пароля админа:

```bash
php -r "echo password_hash('ВашПароль', PASSWORD_DEFAULT), PHP_EOL;"
```

Хеш можно положить в `users.password_hash` или использовать seed-пароль ниже.

### Права

```bash
chown -R www-data:www-data /ssd/www/proflanding
chmod -R 775 /ssd/www/proflanding/storage
```

### Apache + PHP-FPM (как на сервере)

PHP из сорцов, через FPM:

```apache
<FilesMatch "\.php$">
    SetHandler "proxy:fcgi://127.0.0.1:9000"
</FilesMatch>
```

DocumentRoot:

```
DocumentRoot /ssd/www/proflanding/public
AllowOverride All
DirectoryIndex index.php
```

Пример: `deploy/apache-vhost.conf.example`

```bash
sudo a2enmod rewrite proxy proxy_fcgi headers expires deflate ssl
# Сначала только HTTP (:80). SSL-пути в конфиге до certbot — нельзя.
sudo cp /ssd/www/proflanding/deploy/apache-vhost.conf.example /etc/apache2/sites-available/proflanding.conf
sudo a2ensite proflanding.conf
sudo apache2ctl configtest && sudo systemctl reload apache2

# DNS A-запись proflanding.1tlt.ru → IP сервера уже должна указывать сюда
sudo certbot --apache -d proflanding.1tlt.ru

# PHP-FPM (сервис из сорцов):
sudo systemctl reload php82-fpm
sudo systemctl reload apache2
```

Если DocumentRoot нельзя сменить на `public/`, корневые `index.php` + `.htaccess` проксируют в `public/`.

## Первый вход в админку

URL: `https://proflanding.1tlt.ru/admin/login`

Из seed:

- Логин: `admin`
- Пароль: `ChangeMe123!`

**Сразу смените пароль** в разделе «Пароль».

## Telegram chat_id

1. Создайте бота у [@BotFather](https://t.me/BotFather), получите token.
2. Напишите боту любое сообщение (или добавьте в группу).
3. Откройте: `https://api.telegram.org/bot<TOKEN>/getUpdates`
4. Возьмите `message.chat.id` (для группы часто отрицательный).
5. Пропишите `TELEGRAM_BOT_TOKEN` и `TELEGRAM_CHAT_ID` в `.env`.
6. В админке → Уведомления → «Тест Telegram».

## Gmail App Password

1. Создайте отдельный Gmail (или используйте рабочий).
2. Включите 2FA в аккаунте Google.
3. Google Account → Security → App passwords → создайте пароль для Mail.
4. В `.env`:
   - `SMTP_HOST=smtp.gmail.com`
   - `SMTP_PORT=587`
   - `SMTP_SECURE=tls`
   - `SMTP_USER` / `SMTP_FROM` = ваш Gmail
   - `SMTP_PASS` = App Password (не обычный пароль)
   - `SMTP_TO` = куда слать заявки
5. Админка → Уведомления → «Тест SMTP».

## Чеклист запуска

- [ ] `.env` заполнен, не в git
- [ ] schema + seed импортированы
- [ ] `storage/` доступен на запись www-data
- [ ] DocumentRoot = `.../public`
- [ ] HTTPS работает, HTTP → HTTPS
- [ ] `/` открывается, услуги видны
- [ ] заявка пишется в `leads`
- [ ] тест Telegram / SMTP в админке
- [ ] пароль админа сменён
- [ ] `robots.txt` и `/sitemap.xml` доступны

## Структура

```
public/          # DocumentRoot
app/             # Core, Controllers, Models, Views, Services
config/          # config.php, routes.php
database/        # schema.sql, seed.sql
storage/         # logs, cache, uploads
deploy/          # пример Apache vhost
```

## Безопасность

- Секреты только в `.env` (чтение через Apache запрещено)
- PDO prepared statements
- CSRF на POST, session httponly/samesite
- Блокировка брутфорса логина
- Security headers в приложении

## Git

```bash
cd /ssd/www/proflanding
git remote -v
# origin → https://github.com/alexevil1979/proflanding.git
git pull origin main
```

Локальная разработка:

```bash
git clone https://github.com/alexevil1979/proflanding.git
cd proflanding
cp .env.example .env
# поднять PHP built-in с router:
php -S localhost:8080 -t public
```

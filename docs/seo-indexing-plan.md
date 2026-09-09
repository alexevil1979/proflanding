# План размещения в поисковиках — bizdevops.site

Канонический сайт: **https://bizdevops.site**  
Устаревшее зеркало: `proflanding.1tlt.ru` (только 301, не индексировать).

Цель: попасть в индекс Яндекс и Google, закрепить каноникал, не плодить дубли локалей и не терять заявки из органики.

---

## 0. Быстрая проверка перед подачей (1 день)

Сделайте на VPS / с локальной машины:

```bash
curl -sI https://bizdevops.site/ | head -20
curl -s https://bizdevops.site/sitemap.xml | grep -i 1tlt || echo "sitemap OK"
curl -sI https://proflanding.1tlt.ru/ | grep -i Location
curl -s https://bizdevops.site/robots.txt
```

Чеклист:

| Проверка | Ожидание |
|----------|----------|
| HTTPS + HSTS | 200 на bizdevops.site |
| Старый домен | 301 → тот же путь на bizdevops.site |
| `sitemap.xml` | только `https://bizdevops.site/...` |
| `robots.txt` | `Allow: /`, `Disallow: /admin`, Sitemap на bizdevops |
| Canonical / hreflang | в `<head>` главной и `/en/` и т.д. |
| Privacy / offer | контакты из settings, без example.com |
| Форма заявки | пишет в БД + TG/SMTP |

В `.env` и админке (Контент):

- `APP_URL=https://bizdevops.site`
- `public_url=https://bizdevops.site`

---

## 1. Яндекс (приоритет для RU)

### 1.1. Яндекс Вебмастер
1. Зайти: https://webmaster.yandex.ru/
2. Добавить сайт: `https://bizdevops.site`
3. Подтвердить владельца (HTML-файл / meta / DNS — любой удобный способ).
4. Раздел **Индексирование → Файлы Sitemap** → добавить:  
   `https://bizdevops.site/sitemap.xml`
5. **Индексирование → Переобход страниц** → отправить главную `/` и ключевые:
   - `/`
   - `/privacy`
   - `/offer`
   - `/en/` (если EN доведён)
6. **Настройки индексирования → Главное зеркало**: указать `bizdevops.site` (без www, если так в DNS).
7. Если `www.bizdevops.site` открывается — настроить 301 на без www (или наоборот) и то же зеркало в Вебмастере.

### 1.2. Яндекс.Метрика
1. Создать счётчик на `bizdevops.site`.
2. Вставить код в админке → **Контент → Яндекс.Метрика**.
3. Цели (минимум):
   - отправка формы `#leadForm` / успешный toast / событие `lead_ok`;
   - клик по Telegram / WhatsApp (float и chips).

### 1.3. Регион и коммерция
- В Вебмастере указать регион (если спрашивает) — с учётом `city` в settings (Bangkok / remote / Москва).
- Не заявлять «локальный бизнес только Москва», если оффер remote — лучше «Россия / удалённо».

---

## 2. Google

### 2.1. Google Search Console
1. https://search.google.com/search-console
2. Ресурс типа ** Domеn** `bizdevops.site` (DNS TXT) — предпочтительно, покрывает http/https/www.
   Либо URL-префикс `https://bizdevops.site`.
3. **Sitemaps** → `https://bizdevops.site/sitemap.xml`
4. **URL Inspection** → запросить индексирование:
   - `https://bizdevops.site/`
   - `https://bizdevops.site/en/` (после проверки качества перевода)
5. Проверить отчёт **Страницы** / **Международное таргетирование** (hreflang): ошибки `ru` / `en` / `zh-Hans` / `x-default`.

### 2.2. Google Analytics (по желанию)
- GA4 → код в админке **Google Analytics**.
- Событие `generate_lead` при успешной заявке (через `main.js` или GTM позже).

---

## 3. Мультиязычность: как не навредить индексу

Сейчас локали: `ru` `/`, `en`, `fa`, `zh`, `tr`, `ar`.

| Политика | Действие |
|----------|----------|
| RU — основной | `x-default` = RU, каноникал `/` |
| EN — если контент полный | index,follow |
| Слабые / машинные локали | `noindex,follow` в `pages_seo` или robots для префикса, пока не доведены hero / услуги / FAQ / форма |
| Дубли | один H1, уникальный title ≤ 60, description ≤ 160 на язык |

Не подавать в Search Console/Вебмастер слабые локали как «важные», пока нет нормального перевода.

---

## 4. Контент и сниппеты (после индексации)

Минимум для коммерческого запроса «IT-специалист / DevOps / сопровождение сайтов»:

1. **Title / H1 согласованы** (уже в админке SEO + settings).
2. **Реальные кейсы** в `/admin/portfolio` (не шаблоны) — секция скрыта, если пусто — ок.
3. **FAQ** — живые вопросы клиентов (уже JSON-LD FAQPage).
4. **OG image** 1200×630 (загрузить в settings / uploads) — шаринг и часть CTR.
5. Страницы `/privacy` и `/offer` — для доверия и E-E-A-T, не для трафика.

Раз в 2–4 недели: 1 обновление кейса или услуги → sitemap `lastmod` обновится → можно «переобход» в Вебмастере.

---

## 5. Внешние сигналы (лёгкий старт, без серого SEO)

Не покупать ссылки пачками. Достаточно:

1. Профиль / портфолио: Telegram-канал, LinkedIn, HH (если есть), GitHub.
2. Упоминание `bizdevops.site` в подписи писем и коммерческих предложениях.
3. 1–2 отраслевых каталога / справочника (только белые, с модерацией).
4. Отзывы клиентов (скрин/цитата в кейсе) — когда появятся.

---

## 6. Календарь на 30 дней

| День | Действие |
|------|----------|
| 1 | Проверка чеклиста §0, `git pull`, APP_URL / public_url |
| 1–2 | Яндекс Вебмастер + Sitemap + Метрика |
| 2–3 | Google Search Console + Sitemap + запрос индекса главной |
| 3–7 | Добить OG/аватар, 1–2 реальных кейса |
| 7 | Проверить статус индекса: `site:bizdevops.site` в Яндекс/Google |
| 14 | Разбор ошибок hreflang / «Исключённые страницы» |
| 21 | Цели Метрики + 1 улучшение FAQ/оффера по отказам |
| 30 | Решение по `noindex` слабых локалей или доводка EN |

---

## 7. Мониторинг (что смотреть)

**Яндекс Вебмастер**

- ИКС / качество сайта (если появится)
- «Страницы в поиске»
- «Проблемы безопасности / вирусы»
- Склейка зеркал: не должно быть отдельного индекса `1tlt.ru`

**Google Search Console**

- Coverage / Pages
- Experience (Core Web Vitals — мобильный LCP/CLS)
- Queries: первые показы по бренду и «IT специалист PHP», «сопровождение сайта» и т.п.

**Метрика**

- Конверсия формы с органики
- Отказы с мобильных (шапка / float / форма)

---

## 8. Чего не делать

- Не оставлять в индексе `proflanding.1tlt.ru` (только 301).
- Не индексировать `/admin`, thank-you, UTM-дубли.
- Не плодить 6 тонких языковых копий ради «международности».
- Не менять домен снова без 301 и смены Sitemap в кабинетах.
- Не закрывать весь сайт в `robots` «на время правок».

---

## 9. Команды после деплоя (шпаргалка)

```bash
cd /ssd/www/proflanding
git pull origin main
# .env: APP_URL=https://bizdevops.site
sudo systemctl reload php82-fpm
curl -s https://bizdevops.site/sitemap.xml | head -20
curl -sI https://proflanding.1tlt.ru/en/ | grep -i Location
```

Ожидаемый Location: `https://bizdevops.site/en/`

---

## 10. Ответственные поля в админке

| Где | Зачем |
|-----|--------|
| Контент → `public_url` | каноникал / sitemap / JSON-LD |
| Контент → phone, email, city | сниппет, legal, schema |
| Контент → avatar / og_image | OG + Person image |
| SEO → home title/description/h1 | сниппет выдачи |
| Кейсы | контент для доверия |
| Уведомления | не SEO, но конверсия с органики |

Документ обновлять при смене домена, появлении блога или отключении локалей.

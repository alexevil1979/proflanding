-- SEO / analytics settings
INSERT INTO settings (k, v) VALUES
('index_locales', 'ru,en'),
('yandex_metrika_id', ''),
('google_analytics_id', ''),
('google_tag_manager_id', ''),
('yandex_verification', ''),
('google_site_verification', ''),
('yandex_goal_lead', 'lead'),
('ga_event_lead', 'generate_lead'),
('head_custom', ''),
('body_custom', '')
ON DUPLICATE KEY UPDATE k = k;

UPDATE pages_seo SET
  title = 'IT-решения для бизнеса — BizDevOps',
  description = 'Сайты, VPS, DevOps, безопасность и поддержка под ключ. Прямая работа, смета до старта, ответ за 1–2 часа.',
  h1 = 'IT-решения для бизнеса без лишней бюрократии',
  og_title = 'IT-решения для бизнеса — BizDevOps',
  og_description = 'Разработка, администрирование и сопровождение — один ответственный специалист.',
  robots = 'index,follow'
WHERE page_key = 'home'
  AND (title = '' OR title LIKE '%IT-специалист под ключ%' OR CHAR_LENGTH(title) > 60);

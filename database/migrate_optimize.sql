-- Канонический URL и правки контента
INSERT INTO settings (k, v) VALUES
('public_url', 'https://bizdevops.site'),
('work_format', 'Bangkok / remote; выезд Москва — по согласованию'),
('response_sla', 'Ответ по заявке обычно 1–2 часа в рабочие дни'),
('not_doing', 'Не беру: 1С, мобильную разработку с нуля, массовый SMM, «сделать как у Apple за 3 дня»')
ON DUPLICATE KEY UPDATE v = VALUES(v);

UPDATE settings SET v = '25+' WHERE k = 'experience_years' AND (v = '25++' OR v = '25' OR v = '12');
UPDATE settings SET v = '1800+' WHERE k = 'projects_count' AND (v = '180+' OR v = '180');
UPDATE settings SET v = 'hello@bizdevops.site' WHERE k = 'email' AND (v LIKE '%example.com%' OR v = '');
UPDATE settings SET v = 'Bangkok / remote · выезд Москва' WHERE k = 'city' AND (v LIKE '%Москва / удалённо%' OR v = '');
UPDATE settings SET v = 'IT-решения для бизнеса без лишней бюрократии' WHERE k = 'hero_offer';
UPDATE settings SET v = 'Сайты, VPS, DevOps, безопасность и поддержка. Прямая работа, смета до старта, ответственность за результат.' WHERE k = 'hero_sub';

UPDATE portfolio SET is_active = 0
WHERE title IN (
  'Лендинг + заявки в Telegram/почту',
  'Миграция сайта на VPS',
  'Интеграция CRM и платежей'
);

UPDATE pages_seo SET
  title = 'IT-решения для бизнеса — BizDevOps',
  description = 'Сайты, VPS, DevOps, безопасность и поддержка под ключ. Прямая работа, смета до старта, ответ за 1–2 часа.',
  h1 = 'IT-решения для бизнеса без лишней бюрократии',
  og_title = 'IT-решения для бизнеса — BizDevOps',
  og_description = 'Разработка, администрирование и сопровождение — один ответственный специалист.'
WHERE page_key = 'home';

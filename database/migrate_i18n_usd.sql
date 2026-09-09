-- Курс USD для уже развёрнутой БД
INSERT INTO settings (k, v) VALUES
('usd_rate', '90'),
('usd_rate_updated_at', '')
ON DUPLICATE KEY UPDATE v = VALUES(v);

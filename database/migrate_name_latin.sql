INSERT INTO settings (k, v) VALUES
('site_name_latin', 'Alexander M.')
ON DUPLICATE KEY UPDATE v = VALUES(v);

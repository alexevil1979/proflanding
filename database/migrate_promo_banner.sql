-- Promo banner defaults (вкл. скидка 30% до конца месяца)
INSERT INTO settings (k, v) VALUES
('promo_banner_enabled', '1'),
('promo_banner_style', '1'),
('promo_banner_dismissible', '1'),
('promo_banner_cta_url', '#lead'),
('promo_banner_until', DATE_FORMAT(LAST_DAY(CURDATE()), '%Y-%m-%d')),
('promo_banner_texts', '{"ru":{"title":"Скидка 30% на услуги до конца месяца","sub":"Успейте зафиксировать цену — акция действует до последнего дня месяца","cta":"Успеть со скидкой"},"en":{"title":"30% off services until month end","sub":"Lock in the price — offer lasts through the last day of the month","cta":"Claim the discount"},"fa":{"title":"۳۰٪ تخفیف خدمات تا پایان ماه","sub":"قیمت را قفل کنید — پیشنهاد تا آخرین روز ماه معتبر است","cta":"دریافت تخفیف"},"zh":{"title":"本月底前服务享 30% 折扣","sub":"锁定价格——优惠有效至本月最后一天","cta":"立即领取"},"tr":{"title":"Ay sonuna kadar hizmetlerde %30 indirim","sub":"Fiyatı sabitleyin — teklif ayın son gününe kadar geçerli","cta":"İndirimi al"},"ar":{"title":"خصم 30٪ على الخدمات حتى نهاية الشهر","sub":"ثبّت السعر — العرض ساري حتى آخر يوم في الشهر","cta":"احصل على الخصم"}}')
ON DUPLICATE KEY UPDATE v = VALUES(v);

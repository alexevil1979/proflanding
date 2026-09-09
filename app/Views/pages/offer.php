<?php /** @var array $seo */ ?>
<section class="section legal legal-page">
    <div class="container narrow">
        <article class="legal-card">
            <p class="legal-brand"><bdi dir="ltr"><?= e(brand_name()) ?></bdi> · <?= e(setting('site_role')) ?></p>
            <h1><?= e($seo['h1'] ?? 'Публичная оферта') ?></h1>
            <p>Настоящий документ является предложением <bdi dir="ltr"><?= e(brand_name()) ?></bdi> заключить договор на оказание IT-услуг на условиях ниже. Сайт: <a href="<?= e(app_url()) ?>"><?= e(rtrim(app_url(), '/')) ?></a>.</p>
            <h2>1. Предмет</h2>
            <p>Исполнитель оказывает услуги по разработке, администрированию, интеграциям, безопасности, SEO и поддержке IT-систем по заявке Заказчика.</p>
            <h2>2. Порядок заключения</h2>
            <p>Заявка на сайте не является автоматическим договором. Договор считается согласованным после подтверждения объёма, сроков и стоимости сторонами (в переписке/счёте/договоре).</p>
            <h2>3. Оплата</h2>
            <p>Стоимость определяется индивидуально либо по выбранному пакету. Оплата производится на реквизиты, указанные в счёте.</p>
            <h2>4. Ответственность</h2>
            <p>Исполнитель отвечает за качество работ в согласованном объёме. Заказчик обеспечивает доступы и достоверность исходных данных.</p>
            <h2>5. Контакты</h2>
            <div class="legal-contacts">
                <p><bdi dir="ltr"><?= e(brand_name()) ?></bdi><?php if (setting('city')): ?>, <?= e(setting('city')) ?><?php endif; ?></p>
                <?php if (setting('email')): ?><p>Email: <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p><?php endif; ?>
                <?php if (setting('phone')): ?><p>Телефон: <a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p><?php endif; ?>
            </div>
        </article>
    </div>
</section>

(function () {
  const root = document.documentElement;
  const saved = localStorage.getItem('theme');
  if (saved === 'dark' || saved === 'light') {
    root.setAttribute('data-theme', saved);
  } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    root.setAttribute('data-theme', 'dark');
  }

  const themeBtn = document.getElementById('themeToggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      localStorage.setItem('theme', next);
    });
  }

  const navToggle = document.getElementById('navToggle');
  const nav = document.querySelector('.nav');
  if (navToggle && nav) {
    navToggle.addEventListener('click', function () {
      const open = nav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  function qs(name) {
    return new URLSearchParams(window.location.search).get(name) || '';
  }
  ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'].forEach(function (k) {
    const el = document.getElementById(k);
    if (el) el.value = qs(k);
  });

  document.querySelectorAll('.js-order').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const service = btn.getAttribute('data-service') || '';
      const pack = btn.getAttribute('data-package') || '';
      const s = document.getElementById('service_id');
      const p = document.getElementById('package_id');
      if (s && service) s.value = service;
      if (p && pack) p.value = pack;
      const lead = document.getElementById('lead');
      if (lead) lead.scrollIntoView({ behavior: 'smooth', block: 'start' });
      const name = document.getElementById('name');
      if (name) name.focus();
    });
  });

  const toast = document.getElementById('toast');
  function showToast(msg, isErr) {
    if (!toast) return;
    toast.hidden = false;
    toast.textContent = msg;
    toast.classList.toggle('err', !!isErr);
    clearTimeout(showToast._t);
    showToast._t = setTimeout(function () { toast.hidden = true; }, 4500);
  }

  const form = document.getElementById('leadForm');
  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();
      const submit = document.getElementById('leadSubmit');
      if (submit) submit.disabled = true;
      try {
        const fd = new FormData(form);
        const res = await fetch((window.PL_I18N && window.PL_I18N.leadUrl) || '/lead', {
          method: 'POST',
          body: fd,
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const data = await res.json().catch(function () { return {}; });
        if (res.ok && data.ok) {
          showToast(data.message || (window.PL_I18N && window.PL_I18N.leadOk) || 'OK');
          form.reset();
        } else {
          showToast(data.error || (window.PL_I18N && window.PL_I18N.leadErr) || 'Error', true);
        }
      } catch (err) {
        showToast((window.PL_I18N && window.PL_I18N.leadNetwork) || 'Network error', true);
      } finally {
        if (submit) submit.disabled = false;
      }
    });
  }

  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const items = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('in');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12 });
      items.forEach(function (el) { io.observe(el); });
    } else {
      items.forEach(function (el) { el.classList.add('in'); });
    }
  } else {
    document.querySelectorAll('.reveal').forEach(function (el) { el.classList.add('in'); });
  }
})();

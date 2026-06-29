/* =============================================================
   VESTA — Global front-end behaviour
   ============================================================= */
(function () {
  'use strict';

  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from((ctx || document).querySelectorAll(sel));

  /* ---------- Header: transparent -> solid on scroll ---------- */
  const header = $('[data-header]');
  const topBtn = $('[data-scroll-top]');
  const onScroll = () => {
    const y = window.scrollY;
    if (header) header.classList.toggle('is-solid', y > 40);
    if (topBtn) topBtn.classList.toggle('is-visible', y > 600);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
  if (topBtn) topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* ---------- Mobile nav drawer ---------- */
  const nav      = $('[data-nav]');
  const toggle   = $('[data-nav-toggle]');
  const closeBtn = $('[data-nav-close]');
  const backdrop = $('[data-nav-backdrop]');
  const setNav = (open) => {
    if (!nav) return;
    nav.classList.toggle('is-open', open);
    if (backdrop) backdrop.classList.toggle('is-open', open);
    if (toggle) toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  };
  if (toggle)   toggle.addEventListener('click', () => setNav(true));
  if (closeBtn) closeBtn.addEventListener('click', () => setNav(false));
  if (backdrop) backdrop.addEventListener('click', () => setNav(false));
  $$('[data-nav-link]').forEach((l) => l.addEventListener('click', () => setNav(false)));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setNav(false); });

  /* ---------- AOS scroll animations ---------- */
  window.addEventListener('load', () => {
    if (window.AOS) window.AOS.init({ duration: 700, once: true, offset: 60, easing: 'ease-out-cubic' });
  });

  /* ---------- Save / favourite hearts (localStorage) ---------- */
  const SAVED_KEY = 'vesta_saved';
  const getSaved = () => { try { return JSON.parse(localStorage.getItem(SAVED_KEY)) || []; } catch (e) { return []; } };
  const setSaved = (arr) => localStorage.setItem(SAVED_KEY, JSON.stringify(arr));
  const syncHearts = () => {
    const saved = getSaved();
    $$('[data-save]').forEach((btn) => {
      btn.classList.toggle('is-saved', saved.includes(btn.dataset.save));
    });
  };
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-save]');
    if (!btn) return;
    e.preventDefault();
    const id = btn.dataset.save;
    let saved = getSaved();
    saved = saved.includes(id) ? saved.filter((x) => x !== id) : [...saved, id];
    setSaved(saved);
    syncHearts();
  });
  syncHearts();
  window.syncSavedHearts = syncHearts; // re-applied after AJAX renders cards

  /* ---------- Animated stat counters ---------- */
  const animateCount = (el) => {
    const target   = parseFloat(el.dataset.count || '0');
    const decimals = parseInt(el.dataset.decimals || '0', 10);
    const prefix   = el.dataset.prefix || '';
    const suffix   = el.dataset.suffix || '';
    const dur      = 1800;
    const start    = performance.now();
    const tick = (now) => {
      const p = Math.min((now - start) / dur, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      const val = (target * eased).toFixed(decimals);
      el.textContent = prefix + Number(val).toLocaleString('en-US', { minimumFractionDigits: decimals }) + suffix;
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };
  const counters = $$('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) { animateCount(entry.target); obs.unobserve(entry.target); }
      });
    }, { threshold: 0.4 });
    counters.forEach((c) => io.observe(c));
  } else {
    counters.forEach(animateCount);
  }

  /* ---------- Cycling search placeholder ---------- */
  const cycleInput = $('[data-placeholder-cycle]');
  if (cycleInput) {
    const phrases = JSON.parse(cycleInput.dataset.placeholderCycle || '[]');
    let i = 0;
    if (phrases.length) {
      cycleInput.setAttribute('placeholder', phrases[0]);
      setInterval(() => {
        if (document.activeElement === cycleInput) return;
        i = (i + 1) % phrases.length;
        cycleInput.setAttribute('placeholder', phrases[i]);
      }, 2800);
    }
  }

  /* ---------- Search tabs (buy / rent) ---------- */
  $$('[data-search-tabs] .search-tab').forEach((tab) => {
    tab.addEventListener('click', () => {
      const wrap = tab.closest('[data-search-tabs]');
      $$('.search-tab', wrap).forEach((t) => t.classList.remove('is-active'));
      tab.classList.add('is-active');
      const hidden = $('[data-status-input]', wrap.closest('form'));
      if (hidden) hidden.value = tab.dataset.status || '';
    });
  });

  /* ---------- Exit-intent modal ---------- */
  const exitModal = $('[data-exit-modal]');
  if (exitModal) {
    const SEEN = 'vesta_exit_seen';
    const open = () => {
      if (sessionStorage.getItem(SEEN)) return;
      exitModal.classList.add('is-open');
      exitModal.setAttribute('aria-hidden', 'false');
      sessionStorage.setItem(SEEN, '1');
    };
    const close = () => { exitModal.classList.remove('is-open'); exitModal.setAttribute('aria-hidden', 'true'); };
    document.addEventListener('mouseout', (e) => { if (e.clientY <= 0 && !e.relatedTarget) open(); });
    // Mobile fallback: trigger after 25s of engagement
    setTimeout(() => { if (window.innerWidth < 768) open(); }, 25000);
    $$('[data-exit-close]', exitModal).forEach((b) => b.addEventListener('click', close));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
  }

  /* ---------- Generic AJAX form handler ---------- */
  window.VestaForm = {
    /** Bind any <form data-ajax-form>. Honeypot field "company" must stay empty. */
    bind(form) {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const alertBox = form.querySelector('[data-alert]');
        const submit = form.querySelector('[type="submit"]');
        const hp = form.querySelector('.honeypot');
        if (hp && hp.value) return; // bot
        const originalLabel = submit ? submit.innerHTML : '';
        if (submit) { submit.disabled = true; submit.innerHTML = 'Sending…'; }
        if (alertBox) alertBox.className = 'form-alert';

        // Refresh CSRF token right before submit so a stale/cached page never 403s.
        if (window.VESTA && window.VESTA.csrfUrl) {
          try {
            const t = await fetch(window.VESTA.csrfUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then((r) => r.json());
            const tokenField = form.querySelector('input[name="' + t.name + '"]');
            if (tokenField) tokenField.value = t.hash;
          } catch (e) { /* fall back to embedded token */ }
        }

        try {
          const res = await fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            body: new FormData(form),
          });
          const data = await res.json().catch(() => ({}));
          // refresh CSRF token if returned
          if (data.csrf) {
            const tokenField = form.querySelector('input[name="' + data.csrf.name + '"]');
            if (tokenField) tokenField.value = data.csrf.hash;
          }
          if (res.ok && data.status === 'success') {
            if (alertBox) { alertBox.textContent = data.message || 'Thank you! We will be in touch shortly.'; alertBox.classList.add('form-alert--success', 'is-visible'); }
            form.reset();
          } else {
            const msg = data.message || (data.errors ? Object.values(data.errors)[0] : 'Something went wrong. Please try again.');
            if (alertBox) { alertBox.textContent = msg; alertBox.classList.add('form-alert--error', 'is-visible'); }
          }
        } catch (err) {
          if (alertBox) { alertBox.textContent = 'Network error. Please try again.'; alertBox.classList.add('form-alert--error', 'is-visible'); }
        } finally {
          if (submit) { submit.disabled = false; submit.innerHTML = originalLabel; }
        }
      });
    },
  };
  $$('[data-ajax-form]').forEach((f) => window.VestaForm.bind(f));

  /* ---------- Current year stamps ---------- */
  $$('[data-year]').forEach((el) => { el.textContent = new Date().getFullYear(); });
})();

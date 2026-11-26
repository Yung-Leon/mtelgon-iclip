/* Mt. Elgon-ICLIP — front-end behaviour (no dependencies) */
(() => {
  'use strict';

  const $  = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];
  const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Images: fall back to the small WordPress size if the full-size file 404s ------------- */
  $$('img[data-fallback]').forEach(img => {
    img.addEventListener('error', () => {
      const fb = img.dataset.fallback;
      if (fb && img.src !== fb) { img.src = fb; } else { img.removeAttribute('data-fallback'); }
    }, { once: false });
  });

  /* Header: solid background once the page scrolls; mobile menu -------------------------- */
  const header = $('#siteHeader');
  const menuBtn = $('#menuBtn');
  const nav = $('#primaryNav');
  const onScrollHeader = () => header.classList.toggle('is-solid', scrollY > 24);
  addEventListener('scroll', onScrollHeader, { passive: true });
  onScrollHeader();

  const setMenu = open => {
    menuBtn.setAttribute('aria-expanded', String(open));
    nav.classList.toggle('open', open);
    header.classList.toggle('menu-open', open);
  };
  menuBtn.addEventListener('click', () => setMenu(menuBtn.getAttribute('aria-expanded') !== 'true'));
  nav.addEventListener('click', e => { if (e.target.closest('a')) setMenu(false); });
  addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });

  /* Hero: live topographic contour map --------------------------------------------------- */
  const canvas = $('#contours');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    const LEVELS = 17;
    const CELL = 11;                       // grid cell size in CSS pixels
    let W = 0, H = 0, cols = 0, rows = 0, dpr = 1, field = new Float32Array(0);
    let running = false, visible = true, t0 = performance.now();
    const pointer = { x: -1e4, y: -1e4, tx: -1e4, ty: -1e4 };

    // Smooth 2-D value noise
    const hash = (x, y) => {
      let h = Math.imul(x, 374761393) + Math.imul(y, 668265263);
      h = Math.imul(h ^ (h >>> 13), 1274126177);
      return ((h ^ (h >>> 16)) >>> 0) / 4294967295;
    };
    const smooth = v => v * v * (3 - 2 * v);
    const vnoise = (x, y) => {
      const xi = Math.floor(x), yi = Math.floor(y);
      const xf = smooth(x - xi), yf = smooth(y - yi);
      const a = hash(xi, yi), b = hash(xi + 1, yi), c = hash(xi, yi + 1), d = hash(xi + 1, yi + 1);
      return a + (b - a) * xf + (c - a) * yf + (a - b - c + d) * xf * yf;
    };
    const fbm = (x, y) => vnoise(x, y) * .55 + vnoise(x * 2.1, y * 2.1) * .3 + vnoise(x * 4.3, y * 4.3) * .15;

    const resize = () => {
      const r = canvas.getBoundingClientRect();
      dpr = Math.min(devicePixelRatio || 1, 2);
      W = r.width; H = r.height;
      canvas.width = W * dpr; canvas.height = H * dpr;
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      cols = Math.ceil(W / CELL) + 1;
      rows = Math.ceil(H / CELL) + 1;
      field = new Float32Array(cols * rows);
    };

    const compute = time => {
      // caldera-like massif on the right, drifting noise over it, small bump under the cursor
      const peakX = W * (W > 900 ? .78 : .55), peakY = H * .55, spread = Math.max(W, H) * .5;
      const drift = time * .00005;
      for (let j = 0; j < rows; j++) {
        for (let i = 0; i < cols; i++) {
          const x = i * CELL, y = j * CELL;
          const dx = (x - peakX) / spread, dy = (y - peakY) / (spread * .8);
          let h = Math.exp(-(dx * dx + dy * dy) * 1.6);
          h += (fbm(x * .004 + drift * 3, y * .004 - drift * 2) - .5) * .55;
          const px = x - pointer.x, py = y - pointer.y;
          h += .16 * Math.exp(-(px * px + py * py) / 26000);
          field[j * cols + i] = h;
        }
      }
    };

    const lerp = (a, b, l) => (l - a) / (b - a);

    const draw = time => {
      compute(time);
      ctx.clearRect(0, 0, W, H);
      ctx.lineJoin = ctx.lineCap = 'round';
      const lo = -.12, hi = 1.0;
      for (let k = 0; k < LEVELS; k++) {
        const l = lo + (hi - lo) * (k / (LEVELS - 1));
        const major = k % 4 === 0;
        ctx.beginPath();
        for (let j = 0; j < rows - 1; j++) {
          for (let i = 0; i < cols - 1; i++) {
            const a = field[j * cols + i], b = field[j * cols + i + 1];
            const d = field[(j + 1) * cols + i], c = field[(j + 1) * cols + i + 1];
            const idx = (a > l ? 8 : 0) | (b > l ? 4 : 0) | (c > l ? 2 : 0) | (d > l ? 1 : 0);
            if (idx === 0 || idx === 15) continue;
            const x = i * CELL, y = j * CELL;
            const T = [x + lerp(a, b, l) * CELL, y];
            const R = [x + CELL, y + lerp(b, c, l) * CELL];
            const B = [x + lerp(d, c, l) * CELL, y + CELL];
            const L = [x, y + lerp(a, d, l) * CELL];
            const seg = (p, q) => { ctx.moveTo(p[0], p[1]); ctx.lineTo(q[0], q[1]); };
            switch (idx) {
              case 1: case 14: seg(L, B); break;
              case 2: case 13: seg(B, R); break;
              case 3: case 12: seg(L, R); break;
              case 4: case 11: seg(T, R); break;
              case 5: seg(T, L); seg(B, R); break;
              case 6: case 9: seg(T, B); break;
              case 7: case 8: seg(T, L); break;
              case 10: seg(T, R); seg(L, B); break;
            }
          }
        }
        ctx.lineWidth = major ? 1.6 : .9;
        ctx.strokeStyle = major ? 'rgba(62,107,63,.55)' : 'rgba(62,107,63,.26)';
        ctx.stroke();
      }
    };

    const frame = now => {
      pointer.x += (pointer.tx - pointer.x) * .08;
      pointer.y += (pointer.ty - pointer.y) * .08;
      draw(now - t0);
      if (running && visible) requestAnimationFrame(frame); else running = false;
    };
    const start = () => { if (!running && !reduceMotion) { running = true; requestAnimationFrame(frame); } };

    resize();
    draw(0);
    if (!reduceMotion) start();

    let rz;
    addEventListener('resize', () => { clearTimeout(rz); rz = setTimeout(() => { resize(); draw(performance.now() - t0); }, 120); });
    $('.hero').addEventListener('pointermove', e => {
      const r = canvas.getBoundingClientRect();
      pointer.tx = e.clientX - r.left; pointer.ty = e.clientY - r.top;
      if (pointer.x < -1e3) { pointer.x = pointer.tx; pointer.y = pointer.ty; }
    });
    $('.hero').addEventListener('pointerleave', () => { pointer.tx = pointer.ty = -1e4; });
    new IntersectionObserver(([en]) => { visible = en.isIntersecting; if (visible) start(); }).observe(canvas);
  }

  /* Elevation gauge: scroll progress as altitude + section navigation -------------------- */
  const gauge = $('#gauge');
  if (gauge) {
    const marker = $('#gaugeMarker'), value = $('#gaugeValue');
    const links = $$('a[data-target]', gauge);
    const sections = links.map(a => document.getElementById(a.dataset.target));
    const line = $('.gauge-line', gauge);
    const BASE = 2000, SUMMIT = 4321;
    const fmt = n => n.toLocaleString('en-US') + ' m';

    const placeTicks = () => {
      const max = document.documentElement.scrollHeight - innerHeight;
      links.forEach((a, i) => {
        const pct = Math.min(100, Math.max(0, (sections[i].offsetTop / max) * 100));
        a.closest('li').style.top = pct + '%';
      });
    };
    const update = () => {
      const max = document.documentElement.scrollHeight - innerHeight;
      const p = Math.min(1, Math.max(0, scrollY / max));
      marker.style.top = (p * 100) + '%';
      value.textContent = fmt(Math.round(BASE + p * (SUMMIT - BASE)));
      const mid = scrollY + innerHeight * .4;
      let active = 0;
      sections.forEach((s, i) => { if (s.offsetTop <= mid) active = i; });
      links.forEach((a, i) => a.classList.toggle('is-active', i === active));
      links[active].setAttribute('aria-current', 'true');
      links.forEach((a, i) => { if (i !== active) a.removeAttribute('aria-current'); });
      gauge.dataset.tone = sections[active].dataset.tone || 'light';
    };
    addEventListener('scroll', update, { passive: true });
    addEventListener('resize', () => { placeTicks(); update(); });
    addEventListener('load', () => { placeTicks(); update(); });
    placeTicks(); update();
  }

  /* Project tabs (WAI-ARIA tabs pattern, vertical) -------------------------------------- */
  $$('[data-tabs]').forEach(root => {
    const tabs = $$('[role="tab"]', root);
    const panels = $$('[role="tabpanel"]', root);
    const select = (tab, focus) => {
      tabs.forEach((t, i) => {
        const on = t === tab;
        t.setAttribute('aria-selected', on);
        t.tabIndex = on ? 0 : -1;
        panels[i].hidden = !on;
        panels[i].classList.toggle('is-enter', on);
      });
      if (focus) tab.focus();
    };
    tabs.forEach((tab, i) => {
      tab.addEventListener('click', () => select(tab, false));
      tab.addEventListener('keydown', e => {
        const keys = { ArrowDown: 1, ArrowRight: 1, ArrowUp: -1, ArrowLeft: -1 };
        if (e.key in keys) { e.preventDefault(); select(tabs[(i + keys[e.key] + tabs.length) % tabs.length], true); }
        if (e.key === 'Home') { e.preventDefault(); select(tabs[0], true); }
        if (e.key === 'End')  { e.preventDefault(); select(tabs[tabs.length - 1], true); }
      });
    });
  });

  /* Forms: progressive enhancement; they still POST normally without JS ------------------ */
  const csrf = $('meta[name="csrf-token"]')?.content || '';

  const wireForm = (form, statusEl) => {
    if (!form) return;
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const btn = $('button[type="submit"]', form);
      $$('.field', form).forEach(f => f.classList.remove('invalid'));
      $$('.err', form).forEach(el => el.textContent = '');
      statusEl.className = 'status';
      statusEl.textContent = 'Sending…';
      btn.disabled = true;

      const data = new FormData(form);
      // radio inputs live outside the <form> element but are bound with form="inquiryForm", so FormData already includes them
      data.set('csrf', csrf);

      try {
        const res = await fetch(form.action, { method: 'POST', body: data, headers: { 'Accept': 'application/json' } });
        const json = await res.json().catch(() => ({ ok: false, message: 'Unexpected response from the server.' }));
        statusEl.textContent = json.message || '';
        statusEl.classList.add(json.ok ? 'ok' : 'bad');
        if (json.ok) { form.reset(); }
        if (json.errors) {
          Object.entries(json.errors).forEach(([name, msg]) => {
            const err = $(`[data-err="${name}"]`, form);
            if (err) { err.textContent = msg; err.closest('.field')?.classList.add('invalid'); }
          });
        }
      } catch {
        statusEl.textContent = 'Network problem. Check your connection and try again.';
        statusEl.classList.add('bad');
      } finally {
        btn.disabled = false;
      }
    });
  };
  wireForm($('#inquiryForm'), $('#inquiryStatus'));
  wireForm($('#newsletterForm'), $('#newsletterStatus'));
})();

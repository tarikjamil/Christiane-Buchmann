(function () {
  'use strict';

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var desktop = window.matchMedia('(min-width: 900px)').matches;

  /* —— Lenis smooth scroll (A24) —— */
  var lenis = null;
  if (!reduce && typeof Lenis !== 'undefined') {
    lenis = new Lenis({
      duration: 1.2,
      easing: function (t) {
        return Math.min(1, 1.001 - Math.pow(2, -10 * t));
      },
      smoothWheel: true,
      touchMultiplier: 1.6,
      wheelMultiplier: 0.95
    });
    document.documentElement.classList.add('lenis');

    function raf(time) {
      lenis.raf(time);
      requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var id = a.getAttribute('href');
        if (!id || id === '#') return;
        var target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        lenis.scrollTo(target, { offset: -20 });
      });
    });
  }

  function scrollToY(y, opts) {
    if (lenis) lenis.scrollTo(y, opts || {});
    else window.scrollTo({ top: y, behavior: (opts && opts.immediate) ? 'auto' : 'smooth' });
  }

  /* —— Pause Lenis while theatre player is open —— */
  if (lenis) {
    var tpOpen = false;
    function syncLenisWithTrailer() {
      var open = document.documentElement.classList.contains('tp-open');
      if (open === tpOpen) return;
      tpOpen = open;
      if (open) lenis.stop();
      else lenis.start();
    }
    var tpObs = new MutationObserver(syncLenisWithTrailer);
    tpObs.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
  }

  /* —— Sticky nav: light bar after hero —— */
  var nav = document.querySelector('.pv-nav');
  var navHero = document.querySelector(
    '.pv-panel--hero, .pv-ahero, .pv-whero, .pv-phero, [data-pv-panel], .pv-contact__title, .pv-ptitle'
  );

  function updateNavSolid() {
    if (!nav) return;
    var past = false;
    if (navHero) {
      var heroBottom = navHero.getBoundingClientRect().bottom + window.scrollY;
      past = window.scrollY + 12 >= heroBottom - nav.offsetHeight;
    } else {
      past = window.scrollY > 24;
    }
    nav.classList.toggle('is-solid', past);
  }

  /* —— Menu drawer (Wolverine-like drop-down) —— */
  var menu = document.querySelector('[data-pv-menu]');
  var drawer = document.getElementById('pv-drawer');
  var menuOpen = false;
  var menuCloseTimer = null;

  function setMenuOpen(open) {
    if (!menu || !drawer) return;
    if (open === menuOpen) return;
    menuOpen = open;
    if (menuCloseTimer) {
      clearTimeout(menuCloseTimer);
      menuCloseTimer = null;
    }
    menu.setAttribute('aria-expanded', open ? 'true' : 'false');
    var label = menu.querySelector('.pv-nav__menu-label');
    if (label) {
      label.textContent = open
        ? (label.getAttribute('data-label-close') || 'Close')
        : (label.getAttribute('data-label-open') || 'Menu');
    }
    if (open) {
      drawer.removeAttribute('hidden');
      document.documentElement.style.overflow = 'hidden';
      if (lenis) lenis.stop();
      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          if (!menuOpen) return;
          document.documentElement.classList.add('is-menu-open');
          drawer.classList.add('is-open');
        });
      });
    } else {
      document.documentElement.classList.remove('is-menu-open');
      drawer.classList.remove('is-open');
      document.documentElement.style.overflow = '';
      if (lenis) lenis.start();
      menuCloseTimer = setTimeout(function () {
        if (!menuOpen) drawer.setAttribute('hidden', '');
      }, reduce ? 0 : 720);
    }
  }

  if (menu && drawer) {
    menu.addEventListener('click', function () {
      setMenuOpen(!menuOpen);
    });
    drawer.querySelectorAll('[data-pv-drawer-close]').forEach(function (el) {
      el.addEventListener('click', function () {
        setMenuOpen(false);
      });
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menuOpen) setMenuOpen(false);
    });
    window.matchMedia('(min-width: 900px)').addEventListener('change', function (mq) {
      if (mq.matches) setMenuOpen(false);
    });
  }

  /* —— Home: section snap + scroll-scrubbed media (Siena-like) —— */
  var panels = Array.prototype.slice.call(document.querySelectorAll('[data-pv-panel]'));
  var homeScroll = document.querySelector('[data-pv-scroll]');
  var snapping = false;
  var snapIndex = 0;

  function panelTop(el) {
    return el.getBoundingClientRect().top + window.scrollY;
  }

  function nearestPanel() {
    var mid = window.scrollY + window.innerHeight * 0.35;
    var best = 0;
    var bestDist = Infinity;
    panels.forEach(function (p, i) {
      if (p.classList.contains('pv-panel--showcase')) return;
      var d = Math.abs(panelTop(p) - mid + window.innerHeight * 0.15);
      if (d < bestDist) { bestDist = d; best = i; }
    });
    return best;
  }

  function updatePanelMotion() {
    var vh = window.innerHeight;
    var sy = window.scrollY;
    panels.forEach(function (panel) {
      var top = panelTop(panel);
      var h = panel.offsetHeight || vh;
      var progress = (sy + vh * 0.5 - top) / h; // 0 at top-center, 1 at bottom
      var content = panel.querySelector('.pv-panel__content');
      var inView = progress > -0.15 && progress < 1.15;
      panel.classList.toggle('is-inview', inView);

      if (content && !reduce) {
        var visibility = Math.max(0, 1 - Math.abs(progress - 0.42) * 1.35);
        var ty = (0.42 - progress) * 28;
        content.style.opacity = String(0.35 + visibility * 0.65);
        content.style.transform = 'translate3d(0,' + ty + 'px,0)';
      }
    });
  }

  /* —— Cassandra Vale–style scroll parallax (heroes + gallery) —— */
  function updateParallax() {
    if (reduce) return;
    var vh = window.innerHeight;
    document.querySelectorAll('[data-pv-parallax]').forEach(function (root) {
      var layer = root.querySelector('[data-pv-parallax-layer]');
      if (!layer) return;
      var rect = root.getBoundingClientRect();
      if (rect.bottom < -80 || rect.top > vh + 80) return;
      var mid = rect.top + rect.height * 0.5;
      var progress = (vh * 0.5 - mid) / (vh * 0.5 + rect.height * 0.5);
      progress = Math.max(-1, Math.min(1, progress));
      var travel = parseFloat(root.getAttribute('data-pv-parallax-travel') || '14', 10);
      if (isNaN(travel)) travel = 14;
      layer.style.transform = 'translate3d(0,' + (progress * travel).toFixed(3) + '%,0)';
    });
  }

  function onScrollFrame() {
    updatePanelMotion();
    updateParallax();
    updateNavSolid();
  }

  if (lenis) lenis.on('scroll', onScrollFrame);
  window.addEventListener('scroll', onScrollFrame, { passive: true });
  window.addEventListener('resize', onScrollFrame);
  onScrollFrame();
  if (panels.length) panels[0].classList.add('is-inview');

  // Scrollify-style: one full panel per wheel gesture (desktop home)
  if (homeScroll && desktop && !reduce && panels.length > 1) {
    var fullPanels = panels.filter(function (p) {
      return !p.classList.contains('pv-panel--showcase');
    });
    var showcase = panels[panels.length - 1];
    snapIndex = nearestPanel();
    if (snapIndex > fullPanels.length - 1) snapIndex = fullPanels.length - 1;

    function inShowcase() {
      return showcase && window.scrollY + 80 >= panelTop(showcase) - window.innerHeight * 0.15;
    }

    function goPanel(i, duration) {
      i = Math.max(0, Math.min(fullPanels.length - 1, i));
      if (snapping && i === snapIndex) return;
      snapIndex = i;
      snapping = true;
      scrollToY(panelTop(fullPanels[i]), {
        duration: duration || 1.15,
        easing: function (t) {
          return 1 - Math.pow(1 - t, 3);
        },
        force: true,
        onComplete: function () { snapping = false; }
      });
      setTimeout(function () { snapping = false; }, ((duration || 1.15) * 1000) + 120);
    }

    window.addEventListener('wheel', function (e) {
      if (inShowcase()) {
        if (e.deltaY < 0 && window.scrollY <= panelTop(showcase) + 4) {
          e.preventDefault();
          goPanel(fullPanels.length - 1, 1.0);
        }
        return;
      }
      if (snapping) {
        e.preventDefault();
        return;
      }
      if (Math.abs(e.deltaY) < 6) return;
      var dir = e.deltaY > 0 ? 1 : -1;
      var next = snapIndex + dir;
      if (next >= fullPanels.length) {
        e.preventDefault();
        snapIndex = fullPanels.length - 1;
        snapping = true;
        scrollToY(panelTop(showcase), {
          duration: 1.15,
          force: true,
          onComplete: function () { snapping = false; }
        });
        setTimeout(function () { snapping = false; }, 1300);
        return;
      }
      if (next < 0) {
        e.preventDefault();
        return;
      }
      e.preventDefault();
      goPanel(next, 1.15);
    }, { passive: false });

    window.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp' && e.key !== 'PageDown' && e.key !== 'PageUp' && e.key !== ' ') return;
      if (inShowcase() && (e.key === 'ArrowDown' || e.key === 'PageDown' || e.key === ' ')) return;
      var dir = (e.key === 'ArrowUp' || e.key === 'PageUp') ? -1 : 1;
      e.preventDefault();
      goPanel(snapIndex + dir, 1.15);
    });
  }

  /* —— Ticket hover: slight parallax toward cursor (Siena) —— */
  if (!reduce && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    document.querySelectorAll('.pv-ticket').forEach(function (btn) {
      btn.addEventListener('pointermove', function (e) {
        var r = btn.getBoundingClientRect();
        var x = ((e.clientX - r.left) / r.width - 0.5) * 10;
        var y = ((e.clientY - r.top) / r.height - 0.5) * 8;
        btn.style.setProperty('--pv-tx', x.toFixed(2) + 'px');
        btn.style.setProperty('--pv-ty', y.toFixed(2) + 'px');
      });
      btn.addEventListener('pointerleave', function () {
        btn.style.setProperty('--pv-tx', '0px');
        btn.style.setProperty('--pv-ty', '0px');
      });
    });
  }

  /* —— Showcase slider (Filmbot-style) —— */
  (function () {
    var root = document.querySelector('[data-pv-showcase]');
    if (!root) return;
    var rail = root.querySelector('[data-pv-showcase-rail]');
    var prev = root.querySelector('[data-pv-showcase-prev]');
    var next = root.querySelector('[data-pv-showcase-next]');
    if (!rail) return;

    function cardStep() {
      var card = rail.querySelector('.pv-showcase__card');
      if (!card) return rail.clientWidth * 0.8;
      var style = window.getComputedStyle(rail);
      var gap = parseFloat(style.columnGap || style.gap) || 0;
      return card.getBoundingClientRect().width + gap;
    }

    function syncButtons() {
      var max = Math.max(0, rail.scrollWidth - rail.clientWidth - 2);
      if (prev) prev.disabled = rail.scrollLeft <= 2;
      if (next) next.disabled = rail.scrollLeft >= max;
    }

    function scrollByDir(dir) {
      rail.scrollBy({ left: dir * cardStep(), behavior: reduce ? 'auto' : 'smooth' });
    }

    if (prev) prev.addEventListener('click', function () { scrollByDir(-1); });
    if (next) next.addEventListener('click', function () { scrollByDir(1); });
    rail.addEventListener('scroll', syncButtons, { passive: true });
    window.addEventListener('resize', syncButtons);
    rail.scrollLeft = 0;
    syncButtons();

    /* mouse / pen drag-to-swipe — click still opens the project */
    var tracking = false;
    var dragging = false;
    var startX = 0;
    var startScroll = 0;
    var lastX = 0;
    var lastT = 0;
    var velocity = 0;
    var activePointer = null;

    rail.addEventListener('dragstart', function (e) { e.preventDefault(); });

    rail.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return; /* native touch scroll */
      if (e.button !== 0) return;
      tracking = true;
      dragging = false;
      activePointer = e.pointerId;
      startX = lastX = e.clientX;
      startScroll = rail.scrollLeft;
      lastT = performance.now();
      velocity = 0;
    });

    rail.addEventListener('pointermove', function (e) {
      if (!tracking || e.pointerId !== activePointer) return;
      var dx = e.clientX - startX;
      if (!dragging) {
        if (Math.abs(dx) < 6) return;
        dragging = true;
        rail.classList.add('is-dragging');
        try { rail.setPointerCapture(e.pointerId); } catch (err) {}
      }
      var now = performance.now();
      rail.scrollLeft = startScroll - dx;
      var dt = now - lastT;
      if (dt > 0) velocity = (e.clientX - lastX) / dt;
      lastX = e.clientX;
      lastT = now;
      e.preventDefault();
    });

    function endDrag(e) {
      if (!tracking) return;
      if (e && activePointer != null && e.pointerId !== activePointer) return;
      var wasDragging = dragging;
      tracking = false;
      dragging = false;
      activePointer = null;
      rail.classList.remove('is-dragging');

      if (wasDragging) {
        if (!reduce && Math.abs(velocity) > 0.05) {
          rail.scrollBy({ left: -(velocity * 180), behavior: 'smooth' });
        }
        syncButtons();
        /* swallow only the click from this gesture, then clear */
        var block = function (ev) {
          ev.preventDefault();
          ev.stopPropagation();
        };
        rail.addEventListener('click', block, true);
        setTimeout(function () {
          rail.removeEventListener('click', block, true);
        }, 80);
      }
    }

    rail.addEventListener('pointerup', endDrag);
    rail.addEventListener('pointercancel', endDrag);
  })();

  /* —— About awards: dual-column infinite loops (opposite directions) —— */
  (function () {
    var root = document.querySelector('[data-pv-awards]');
    if (!root) return;
    var tracks = Array.prototype.slice.call(root.querySelectorAll('[data-pv-awards-track]'));
    if (!tracks.length || reduce) return;

    function syncDuration() {
      var tallest = 0;
      tracks.forEach(function (track) {
        var group = track.querySelector('.pv-areviews__group');
        if (group) tallest = Math.max(tallest, group.offsetHeight);
      });
      if (!tallest) return;
      var seconds = Math.max(14, Math.round(tallest / 60));
      tracks.forEach(function (track) {
        track.style.setProperty('--pv-awards-duration', seconds + 's');
        track.classList.add('is-ready');
      });
    }

    syncDuration();
    window.addEventListener('resize', syncDuration);
    if (typeof ResizeObserver !== 'undefined') {
      tracks.forEach(function (track) {
        var group = track.querySelector('.pv-areviews__group');
        if (group) new ResizeObserver(syncDuration).observe(group);
      });
    }
  })();

  /* —— Portfolio filters —— */
  var filters = document.querySelector('[data-pv-filters]');
  var search = document.querySelector('[data-pv-search]');
  var cards = Array.prototype.slice.call(document.querySelectorAll('[data-pv-grid] .pv-wcard'));
  var empty = document.querySelector('[data-pv-empty]');
  var active = 'all';
  var filterTimers = new WeakMap();

  function cardMatches(card) {
    var cat = card.getAttribute('data-category') || '';
    var hay = card.getAttribute('data-search') || '';
    var q = (search && search.value ? search.value : '').trim().toLowerCase();
    var okCat = active === 'all' || cat === active;
    var okQ = !q || hay.indexOf(q) !== -1;
    return okCat && okQ;
  }

  function applyFilter(animate) {
    var doAnim = animate !== false && !reduce;
    var shown = 0;
    var enterDelay = 0;

    cards.forEach(function (card) {
      var show = cardMatches(card);
      var hidden = card.classList.contains('is-hidden');
      var prev = filterTimers.get(card);
      if (prev) {
        clearTimeout(prev);
        filterTimers.delete(card);
      }

      if (show) {
        shown += 1;
        card.classList.remove('is-filtering-out');
        if (hidden) {
          card.classList.remove('is-hidden');
          if (doAnim) {
            card.classList.remove('is-filtering-in');
            void card.offsetWidth;
            card.style.animationDelay = enterDelay + 'ms';
            enterDelay += 45;
            card.classList.add('is-filtering-in');
            var onEnd = function () {
              card.classList.remove('is-filtering-in');
              card.style.animationDelay = '';
              card.removeEventListener('animationend', onEnd);
            };
            card.addEventListener('animationend', onEnd);
          }
        }
      } else if (!hidden) {
        if (doAnim) {
          card.classList.add('is-filtering-out');
          var t = setTimeout(function () {
            card.classList.add('is-hidden');
            card.classList.remove('is-filtering-out', 'is-filtering-in');
            card.style.animationDelay = '';
            filterTimers.delete(card);
          }, 420);
          filterTimers.set(card, t);
        } else {
          card.classList.add('is-hidden');
          card.classList.remove('is-filtering-out', 'is-filtering-in');
        }
      }
    });

    if (empty) empty.hidden = shown > 0;
  }

  if (filters) {
    filters.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-filter]');
      if (!btn) return;
      active = btn.getAttribute('data-filter');
      filters.querySelectorAll('[data-filter]').forEach(function (b) {
        var on = b === btn;
        b.classList.toggle('is-active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
      });
      applyFilter(true);
    });
    var hash = (location.hash || '').replace(/^#/, '');
    if (hash) {
      var target = filters.querySelector('[data-filter="' + hash + '"]');
      if (target) target.click();
    }
  }
  if (search) {
    var searchTimer = null;
    search.addEventListener('input', function () {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(function () {
        applyFilter(true);
      }, 80);
    });
  }

  /* —— External links open in a new tab —— */
  document.querySelectorAll('a[href]').forEach(function (a) {
    var href = a.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#' || href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return;
    try {
      var url = new URL(href, window.location.href);
      if (url.origin !== window.location.origin && !a.hasAttribute('target')) {
        a.setAttribute('target', '_blank');
        a.setAttribute('rel', 'noopener');
      }
    } catch (e) { /* ignore bad hrefs */ }
  });
})();

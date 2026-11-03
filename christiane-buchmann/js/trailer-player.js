/*
 * Custom trailer player for Vimeo (chromeless, Player SDK) and YouTube (IFrame API).
 * Each .trailer link opens a theatre dialog; without JavaScript the link goes to the video page.
 * The provider scripts load only after the visitor clicks play.
 */
(function () {
  'use strict';

  var triggers = document.querySelectorAll('[data-trailer]');
  if (!triggers.length) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var EASE_OUT = 'cubic-bezier(0.16, 1, 0.3, 1)';
  var IDLE_MS = 2500;
  var LOAD_TIMEOUT_MS = 15000;
  var scripts = {};

  function loadScript(src) {
    if (!scripts[src]) {
      scripts[src] = new Promise(function (resolve, reject) {
        var s = document.createElement('script');
        s.src = src;
        s.async = true;
        s.onload = resolve;
        s.onerror = reject;
        document.head.appendChild(s);
      });
    }
    return scripts[src];
  }

  function youtubeApi() {
    if (window.YT && window.YT.Player) return Promise.resolve();
    return new Promise(function (resolve, reject) {
      var previous = window.onYouTubeIframeAPIReady;
      window.onYouTubeIframeAPIReady = function () {
        if (previous) previous();
        resolve();
      };
      loadScript('https://www.youtube.com/iframe_api').catch(reject);
    });
  }

  function formatTime(s) {
    s = Math.max(0, Math.floor(s || 0));
    return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
  }

  var ICONS = {
    play: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4.5v15l12.5-7.5z" fill="currentColor"/></svg>',
    pause: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4.5h3v15H7zM14 4.5h3v15h-3z" fill="currentColor"/></svg>',
    close: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>',
    expand: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9V4h5M15 4h5v5M20 15v5h-5M9 20H4v-5" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>',
    shrink: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4v5H4M20 9h-5V4M15 20v-5h5M4 15h5v5" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>'
  };

  // One dialog, reused for every trailer on the page.
  var dialog = document.createElement('div');
  dialog.className = 'tp';
  dialog.setAttribute('role', 'dialog');
  dialog.setAttribute('aria-modal', 'true');
  dialog.tabIndex = -1;
  dialog.hidden = true;
  dialog.innerHTML =
    '<div class="tp_backdrop"></div>' +
    '<div class="tp_frame">' +
      '<div class="tp_still" aria-hidden="true"></div>' +
      '<div class="tp_media"></div>' +
      '<div class="tp_hit" aria-hidden="true"></div>' +
      '<p class="tp_status" role="status"></p>' +
      '<button type="button" class="tp_close" aria-label="Close">' + ICONS.close + '</button>' +
      '<div class="tp_bar">' +
        '<button type="button" class="tp_btn tp_toggle" aria-label="Pause">' + ICONS.pause + '</button>' +
        '<span class="tp_time tp_current">0:00</span>' +
        '<input class="tp_scrub" type="range" min="0" max="1000" step="1" value="0" aria-label="Seek">' +
        '<span class="tp_time tp_total">0:00</span>' +
        '<button type="button" class="tp_sound" aria-pressed="true">Sound off</button>' +
        '<button type="button" class="tp_btn tp_full" aria-label="Fullscreen">' + ICONS.expand + '</button>' +
      '</div>' +
    '</div>';
  document.body.appendChild(dialog);

  var el = {
    frame: dialog.querySelector('.tp_frame'),
    backdrop: dialog.querySelector('.tp_backdrop'),
    still: dialog.querySelector('.tp_still'),
    media: dialog.querySelector('.tp_media'),
    hit: dialog.querySelector('.tp_hit'),
    status: dialog.querySelector('.tp_status'),
    close: dialog.querySelector('.tp_close'),
    toggle: dialog.querySelector('.tp_toggle'),
    current: dialog.querySelector('.tp_current'),
    total: dialog.querySelector('.tp_total'),
    scrub: dialog.querySelector('.tp_scrub'),
    sound: dialog.querySelector('.tp_sound'),
    full: dialog.querySelector('.tp_full')
  };
  if (!document.fullscreenEnabled) el.full.hidden = true;

  var state = null;

  function setStatus(kind, html) {
    dialog.classList.toggle('tp--loading', kind === 'loading');
    dialog.classList.toggle('tp--error', kind === 'error');
    el.status.innerHTML = html || '';
  }

  function render() {
    if (!state) return;
    var d = state.duration || 0;
    el.current.textContent = formatTime(state.time);
    el.total.textContent = formatTime(d);
    if (!state.scrubbing) el.scrub.value = d ? Math.round((state.time / d) * 1000) : 0;
    el.scrub.style.setProperty('--tp-progress', (el.scrub.value / 10) + '%');
    el.scrub.setAttribute('aria-valuetext', formatTime(state.time) + ' of ' + formatTime(d));
    el.toggle.innerHTML = state.playing ? ICONS.pause : ICONS.play;
    el.toggle.setAttribute('aria-label', state.playing ? 'Pause' : 'Play');
    el.sound.textContent = state.muted ? 'Sound on' : 'Sound off';
    el.sound.setAttribute('aria-pressed', String(!state.muted));
    dialog.classList.toggle('tp--paused', !state.playing);
  }

  // Provider adapters expose: play(), pause(), seek(seconds), setMuted(bool), destroy().
  function vimeoAdapter(id, onUpdate) {
    return loadScript('https://player.vimeo.com/api/player.js').then(function () {
      var iframe = document.createElement('iframe');
      iframe.src = 'https://player.vimeo.com/video/' + encodeURIComponent(id) + '?controls=0&autoplay=1&autopause=0&playsinline=1&dnt=1&title=0&byline=0&portrait=0';
      iframe.allow = 'autoplay; fullscreen; picture-in-picture';
      iframe.title = state.title;
      el.media.appendChild(iframe);
      var player = new window.Vimeo.Player(iframe);
      player.on('timeupdate', function (d) { onUpdate({ time: d.seconds, duration: d.duration }); });
      player.on('play', function () { player.getMuted().then(function (m) { onUpdate({ playing: true, started: true, muted: m }); }); });
      player.on('pause', function () { onUpdate({ playing: false }); });
      player.on('ended', function () { onUpdate({ playing: false, ended: true }); });
      player.on('error', function () { onUpdate({ error: true }); });
      return player.ready().then(function () {
        player.getDuration().then(function (d) { onUpdate({ duration: d }); });
        player.play().catch(function () {
          return player.setMuted(true).then(function () { return player.play(); });
        }).catch(function () { onUpdate({ playing: false, started: true }); });
        return {
          play: function () { return player.play(); },
          pause: function () { return player.pause(); },
          seek: function (t) { return player.setCurrentTime(t); },
          setMuted: function (m) { return player.setMuted(m).then(function () { onUpdate({ muted: m }); }); },
          destroy: function () { player.destroy().catch(function () {}); }
        };
      });
    });
  }

  function youtubeAdapter(id, onUpdate) {
    return youtubeApi().then(function () {
      return new Promise(function (resolve, reject) {
        var mount = document.createElement('div');
        el.media.appendChild(mount);
        var timer = null;
        var player = new window.YT.Player(mount, {
          host: 'https://www.youtube-nocookie.com',
          videoId: id,
          playerVars: { autoplay: 1, controls: 0, rel: 0, playsinline: 1, iv_load_policy: 3, fs: 0, disablekb: 1, modestbranding: 1 },
          events: {
            onReady: function () {
              player.getIframe().title = state.title;
              onUpdate({ duration: player.getDuration() });
              player.playVideo();
              timer = setInterval(function () {
                onUpdate({ time: player.getCurrentTime(), duration: player.getDuration(), muted: player.isMuted() });
              }, 250);
              resolve({
                play: function () { player.playVideo(); },
                pause: function () { player.pauseVideo(); },
                seek: function (t) { player.seekTo(t, true); },
                setMuted: function (m) { if (m) player.mute(); else player.unMute(); onUpdate({ muted: m }); },
                destroy: function () { clearInterval(timer); player.destroy(); }
              });
            },
            onStateChange: function (e) {
              if (e.data === 1 && player.unloadModule) player.unloadModule('captions');
              if (e.data === 1) onUpdate({ playing: true, started: true, muted: player.isMuted() });
              if (e.data === 2) onUpdate({ playing: false });
              if (e.data === 0) onUpdate({ playing: false, ended: true });
            },
            onError: function () { onUpdate({ error: true }); reject(new Error('youtube')); }
          }
        });
      });
    });
  }

  function update(patch) {
    if (!state) return;
    Object.keys(patch).forEach(function (k) { state[k] = patch[k]; });
    if (patch.error) return showError();
    if (patch.started && !state.shown) {
      state.shown = true;
      clearTimeout(state.loadTimer);
      setStatus(null);
      dialog.classList.add('tp--playing');
    }
    if (patch.ended) showControls();
    render();
  }

  function showError() {
    if (!state) return;
    clearTimeout(state.loadTimer);
    var where = state.provider === 'youtube' ? 'YouTube' : 'Vimeo';
    setStatus('error', 'This video can’t be played here. <a href="' + state.watchUrl + '" target="_blank" rel="noopener">Watch it on ' + where + '</a>');
  }

  // Controls hide after a moment of stillness while playing, and come back on any movement.
  function showControls() {
    dialog.classList.remove('tp--idle');
    if (!state) return;
    clearTimeout(state.idleTimer);
    if (state.playing) {
      state.idleTimer = setTimeout(function () {
        var active = document.activeElement;
        if (state && state.playing && !(active && active.closest && active.closest('.tp_bar'))) dialog.classList.add('tp--idle');
      }, IDLE_MS);
    }
  }

  function frameRect() {
    var vw = window.innerWidth;
    var vh = window.innerHeight;
    var pad = vw < 768 ? 0 : 48;
    var w = Math.min(vw - pad * 2, ((vh - pad * 2) * 16) / 9);
    var h = (w * 9) / 16;
    return { left: (vw - w) / 2, top: (vh - h) / 2, width: w, height: h };
  }

  function placeFrame() {
    var r = frameRect();
    el.frame.style.left = r.left + 'px';
    el.frame.style.top = r.top + 'px';
    el.frame.style.width = r.width + 'px';
    el.frame.style.height = r.height + 'px';
    return r;
  }

  function flipFrom(source, target) {
    var s = source.getBoundingClientRect();
    return [
      'translate(' + (s.left - target.left) + 'px,' + (s.top - target.top) + 'px) scale(' + s.width / target.width + ',' + s.height / target.height + ')',
      'none'
    ];
  }

  function open(trigger) {
    if (state) return;
    var poster = trigger.querySelector('.trailer_poster');
    state = {
      trigger: trigger,
      provider: trigger.getAttribute('data-trailer'),
      id: trigger.getAttribute('data-trailer-id'),
      title: trigger.getAttribute('data-trailer-title') || 'Trailer',
      watchUrl: trigger.href,
      time: 0,
      duration: parseFloat(trigger.getAttribute('data-trailer-duration')) || 0,
      playing: true,
      muted: false
    };
    dialog.setAttribute('aria-label', state.title);
    el.still.style.backgroundImage = poster ? 'url("' + (poster.currentSrc || poster.src) + '")' : 'none';
    el.close.setAttribute('aria-label', 'Close ' + state.title);
    setStatus('loading', 'Loading');
    dialog.classList.remove('tp--playing', 'tp--idle', 'tp--paused');
    document.documentElement.classList.add('tp-open');
    dialog.hidden = false;
    var target = placeFrame();
    trigger.classList.add('trailer--open');
    render();

    if (!reduceMotion && poster && el.frame.animate) {
      el.frame.animate({ transform: flipFrom(poster, target) }, { duration: 750, easing: EASE_OUT });
      el.backdrop.animate({ opacity: [0, 1] }, { duration: 450, easing: 'ease-out' });
    } else if (dialog.animate) {
      dialog.animate({ opacity: [0, 1] }, { duration: 200 });
    }
    dialog.focus({ preventScroll: true });

    state.loadTimer = setTimeout(showError, LOAD_TIMEOUT_MS);
    var adapter = state.provider === 'youtube' ? youtubeAdapter : vimeoAdapter;
    var session = state;
    adapter(state.id, update).then(function (api) {
      if (state !== session) return api.destroy();
      state.api = api;
    }).catch(function () { if (state === session) showError(); });
  }

  function close() {
    if (!state) return;
    var session = state;
    state = null;
    clearTimeout(session.loadTimer);
    clearTimeout(session.idleTimer);
    if (document.fullscreenElement) document.exitFullscreen().catch(function () {});
    var finish = function () {
      if (session.api) session.api.destroy();
      el.media.innerHTML = '';
      dialog.hidden = true;
      document.documentElement.classList.remove('tp-open');
      session.trigger.classList.remove('trailer--open');
      session.trigger.focus({ preventScroll: true });
    };
    var poster = session.trigger.querySelector('.trailer_poster');
    var r = poster && poster.getBoundingClientRect();
    var onScreen = r && r.bottom > 0 && r.top < window.innerHeight;
    if (!reduceMotion && el.frame.animate && onScreen) {
      if (session.api) session.api.pause();
      var target = el.frame.getBoundingClientRect();
      var frameAnim = el.frame.animate({ transform: flipFrom(poster, target).reverse() }, { duration: 500, easing: EASE_OUT, fill: 'forwards' });
      var backdropAnim = el.backdrop.animate({ opacity: [1, 0] }, { duration: 450, easing: 'ease-out', fill: 'forwards' });
      frameAnim.finished.then(function () {
        finish();
        frameAnim.cancel();
        backdropAnim.cancel();
      });
    } else {
      finish();
    }
  }

  function togglePlay() {
    if (!state || !state.api) return;
    if (state.ended) { state.api.seek(0); state.ended = false; }
    if (state.playing) state.api.pause(); else state.api.play();
    showControls();
  }

  function seekBy(delta) {
    if (!state || !state.api || !state.duration) return;
    var t = Math.min(state.duration, Math.max(0, state.time + delta));
    state.api.seek(t);
    update({ time: t });
    showControls();
  }

  function toggleFullscreen() {
    if (document.fullscreenElement) document.exitFullscreen();
    else el.frame.requestFullscreen().catch(function () {});
  }

  Array.prototype.forEach.call(triggers, function (trigger) {
    trigger.addEventListener('click', function (e) {
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
      e.preventDefault();
      open(trigger);
    });
  });

  el.close.addEventListener('click', close);
  el.backdrop.addEventListener('click', close);
  el.hit.addEventListener('click', togglePlay);
  el.toggle.addEventListener('click', togglePlay);
  el.full.addEventListener('click', toggleFullscreen);
  el.sound.addEventListener('click', function () {
    if (state && state.api) state.api.setMuted(!state.muted);
    showControls();
  });

  el.scrub.addEventListener('input', function () {
    if (!state || !state.duration) return;
    state.scrubbing = true;
    var t = (el.scrub.value / 1000) * state.duration;
    el.current.textContent = formatTime(t);
    el.scrub.style.setProperty('--tp-progress', (el.scrub.value / 10) + '%');
    showControls();
  });
  el.scrub.addEventListener('change', function () {
    if (!state || !state.api || !state.duration) return;
    var t = (el.scrub.value / 1000) * state.duration;
    state.scrubbing = false;
    state.api.seek(t);
    update({ time: t, ended: false });
  });

  document.addEventListener('fullscreenchange', function () {
    var on = document.fullscreenElement === el.frame;
    dialog.classList.toggle('tp--fullscreen', on);
    el.full.innerHTML = on ? ICONS.shrink : ICONS.expand;
    el.full.setAttribute('aria-label', on ? 'Exit fullscreen' : 'Fullscreen');
  });

  dialog.addEventListener('pointermove', showControls);
  dialog.addEventListener('focusin', showControls);
  window.addEventListener('resize', function () { if (state) placeFrame(); });

  dialog.addEventListener('keydown', function (e) {
    if (!state) return;
    var onScrub = e.target === el.scrub;
    if (e.key === 'Escape') { e.preventDefault(); close(); return; }
    if (e.key === 'Tab') {
      var items = Array.prototype.filter.call(dialog.querySelectorAll('button, input, a[href]'), function (n) { return !n.hidden && n.offsetParent !== null; });
      var first = items[0];
      var last = items[items.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      return;
    }
    if (e.target.tagName === 'BUTTON' && (e.key === ' ' || e.key === 'Enter')) return;
    if (e.key === ' ' || e.key === 'k') { e.preventDefault(); togglePlay(); }
    else if (!onScrub && e.key === 'ArrowRight') { e.preventDefault(); seekBy(5); }
    else if (!onScrub && e.key === 'ArrowLeft') { e.preventDefault(); seekBy(-5); }
    else if (e.key === 'm') { el.sound.click(); }
    else if (e.key === 'f' && !el.full.hidden) { toggleFullscreen(); }
  });
})();

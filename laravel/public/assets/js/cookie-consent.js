/* SSTN cookie consent.
   - No optional cookie or script before consent: such scripts are <script type="text/plain" data-cookie-category="…">.
   - Rejecting is as easy as accepting; consent can be changed or withdrawn any time ([data-cc-open]).
   - The choice is stored in a first-party cookie and logged on the server as proof (random ID, categories, time). */
(() => {
  const root = document.querySelector('[data-cookie-consent]');
  if (!root) return;

  const cfg = JSON.parse(root.dataset.config);
  const banner = root.querySelector('[data-cc-banner]');
  const dialog = root.querySelector('[data-cc-dialog]');
  const switches = [...root.querySelectorAll('[data-cc-category]')];

  /* Stored choice, or null when missing, unreadable or from an older policy version. */
  const read = () => {
    const raw = document.cookie.split('; ').find((c) => c.startsWith(`${cfg.cookie}=`));
    if (!raw) return null;
    try {
      const value = JSON.parse(decodeURIComponent(raw.slice(cfg.cookie.length + 1)));
      return value && value.v === cfg.version && Array.isArray(value.c) ? value : null;
    } catch {
      return null;
    }
  };

  const granted = (consent, category) => Boolean(consent) && consent.c.includes(category);

  const uuid = () => (crypto.randomUUID
    ? crypto.randomUUID()
    : '10000000-1000-4000-8000-100000000000'.replace(/[018]/g, (c) =>
      (c ^ (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))).toString(16)));

  /* Turn blocked scripts of accepted categories into real ones. */
  const activate = (consent) => {
    document.querySelectorAll('script[type="text/plain"][data-cookie-category]').forEach((blocked) => {
      if (!granted(consent, blocked.dataset.cookieCategory)) return;
      const script = document.createElement('script');
      [...blocked.attributes].forEach(({ name, value }) => {
        if (!['type', 'data-src', 'data-cookie-category'].includes(name)) script.setAttribute(name, value);
      });
      if (blocked.dataset.src) script.src = blocked.dataset.src;
      else script.textContent = blocked.textContent;
      blocked.replaceWith(script);
    });
  };

  /* Stop the category's services, then delete its cookies ("_ga_*" = prefix) on this host and its parent domains. */
  const removeCookies = (category) => {
    (cfg.disable[category] || []).forEach((flag) => { window[flag] = true; });
    const patterns = cfg.cookies[category] || [];
    const matches = (name) => patterns.some((p) => (p.endsWith('*') ? name.startsWith(p.slice(0, -1)) : name === p));
    const parts = location.hostname.split('.');
    const domains = [''].concat(parts.slice(0, -1).map((_, i) => `; domain=.${parts.slice(i).join('.')}`));
    document.cookie.split('; ').map((c) => c.split('=')[0]).filter(matches).forEach((name) => {
      domains.forEach((domain) => { document.cookie = `${name}=; Max-Age=0; path=/${domain}`; });
    });
  };

  const save = (categories) => {
    const previous = read();
    const consent = {
      v: cfg.version,
      id: previous?.id || uuid(),
      t: new Date().toISOString(),
      c: categories.filter((c) => cfg.categories.includes(c)),
    };
    const secure = location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = `${cfg.cookie}=${encodeURIComponent(JSON.stringify(consent))}; Max-Age=${cfg.days * 86400}; Path=/; SameSite=Lax${secure}`;

    // Proof of consent for the controller (GDPR art. 7(1)). The choice works even if this request fails.
    fetch(cfg.endpoint, {
      method: 'POST',
      keepalive: true,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({ consent_id: consent.id, version: consent.v, categories: consent.c }),
    }).catch(() => {});

    if (banner) banner.hidden = true;
    if (dialog.open) dialog.close();

    // Scripts that already run can only be stopped by reloading the page.
    const withdrawn = (previous?.c || []).filter((c) => !consent.c.includes(c));
    if (withdrawn.length) {
      withdrawn.forEach(removeCookies);
      location.reload();
      return;
    }
    activate(consent);
  };

  const openSettings = () => {
    const consent = read();
    switches.forEach((s) => { s.checked = granted(consent, s.dataset.ccCategory); });
    dialog.showModal();
  };

  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-cc-accept], [data-cc-reject], [data-cc-save], [data-cc-open]');
    if (!button) return;
    event.preventDefault();
    if (button.hasAttribute('data-cc-accept')) save(cfg.categories);
    else if (button.hasAttribute('data-cc-reject')) save([]);
    else if (button.hasAttribute('data-cc-save')) save(switches.filter((s) => s.checked).map((s) => s.dataset.ccCategory));
    else openSettings();
  });

  const consent = read();
  if (consent) {
    // Clear leftovers of refused categories, e.g. a cookie a service rewrote while the page was unloading.
    cfg.categories.filter((c) => !granted(consent, c)).forEach(removeCookies);
    activate(consent);
  } else if (banner && cfg.categories.length) {
    banner.hidden = false;
  }
})();

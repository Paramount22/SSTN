/* SSTN landing page – small progressive enhancements. The page works without this file. */
(() => {
  const header = document.querySelector('[data-header]');
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Header gets a stronger background once the page scrolls */
  const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 12);
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* Mobile menu */
  const toggle = document.querySelector('[data-menu-toggle]');
  const menu = document.querySelector('[data-menu]');
  const setMenu = (open) => {
    toggle.setAttribute('aria-expanded', String(open));
    menu.hidden = !open;
  };
  toggle.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
  menu.addEventListener('click', (e) => { if (e.target.closest('a')) setMenu(false); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setMenu(false); });
  window.matchMedia('(min-width: 961px)').addEventListener('change', (e) => { if (e.matches) setMenu(false); });

  /* Active section: sliding white pill in the navigation */
  const links = [...document.querySelectorAll('.nav__link')];
  const indicator = document.querySelector('[data-nav-indicator]');
  const sections = links.map((l) => document.querySelector(l.getAttribute('href')));
  let active = null;

  const placeIndicator = () => {
    links.forEach((l) => l.classList.toggle('is-active', l === active));
    if (!active) { indicator.style.opacity = '0'; return; }
    indicator.style.width = `${active.offsetWidth}px`;
    indicator.style.transform = `translateX(${active.offsetLeft}px)`;
    indicator.style.opacity = '1';
  };

  const updateActive = () => {
    const line = window.innerHeight * 0.4;
    let current = null;
    sections.forEach((s, i) => { if (s && s.getBoundingClientRect().top <= line) current = links[i]; });
    const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4;
    if (atBottom) current = links[links.length - 1];
    if (current !== active) { active = current; placeIndicator(); }
  };

  let ticking = false;
  window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => { updateActive(); ticking = false; });
  }, { passive: true });
  window.addEventListener('resize', placeIndicator);
  updateActive();

  /* Scroll reveal */
  const revealEls = document.querySelectorAll('[data-reveal]');
  if ('IntersectionObserver' in window && !reduceMotion) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.1 });
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
  }

  /* Days until each event ("O 11 dní"), Slovak plural forms */
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  document.querySelectorAll('[data-countdown]').forEach((el) => {
    const [y, m, d] = el.dataset.countdown.split('-').map(Number);
    const days = Math.round((new Date(y, m - 1, d) - today) / 864e5);
    let text;
    if (days < 0) { text = 'Uskutočnené'; el.classList.add('is-past'); }
    else if (days === 0) text = 'Dnes';
    else if (days === 1) text = 'Zajtra';
    else if (days < 5) text = `O ${days} dni`;
    else text = `O ${days} dní`;
    el.textContent = text;
  });

  /* Copy IBAN */
  document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const value = btn.dataset.copy;
      try {
        await navigator.clipboard.writeText(value);
      } catch {
        const tmp = document.createElement('textarea');
        tmp.value = value;
        document.body.append(tmp);
        tmp.select();
        document.execCommand('copy');
        tmp.remove();
      }
      btn.textContent = 'Skopírované';
      btn.classList.add('is-done');
      setTimeout(() => { btn.textContent = 'Kopírovať'; btn.classList.remove('is-done'); }, 2000);
    });
  });
})();

(() => {
  const burger = document.querySelector('[data-burger]');
  const nav = document.getElementById('mainNav');
  if (burger && nav) {
    burger.addEventListener('click', () => {
      nav.classList.toggle('nav--open');
      burger.classList.toggle('burger--open');
    });
  }

  // Tab switch (login/register etc.)
  document.querySelectorAll('[data-tabs]').forEach(tabs => {
    const buttons = tabs.querySelectorAll('[data-tab]');
    const panels = tabs.querySelectorAll('[data-panel]');
    const activate = (name) => {
      buttons.forEach(b => b.classList.toggle('tab__btn--active', b.dataset.tab === name));
      panels.forEach(p => p.hidden = (p.dataset.panel !== name));
    };
    buttons.forEach(btn => btn.addEventListener('click', () => activate(btn.dataset.tab)));
    const first = buttons[0]?.dataset.tab;
    if (first) activate(first);
  });

  // confirm delete buttons
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
      const msg = el.getAttribute('data-confirm') || 'Are you sure?';
      if (!confirm(msg)) e.preventDefault();
    });
  });
})();

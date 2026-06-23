(function () {
  'use strict';

  const header = document.querySelector('[data-site-header]');
  const toggle = document.querySelector('.kechoo-menu-toggle');
  const navigation = document.getElementById('primary-navigation');

  if (header && toggle && navigation) {
    const closeMenu = () => {
      toggle.setAttribute('aria-expanded', 'false');
      navigation.classList.remove('is-open');
      document.body.classList.remove('kechoo-menu-open');
    };

    toggle.addEventListener('click', () => {
      const opening = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(opening));
      navigation.classList.toggle('is-open', opening);
      document.body.classList.toggle('kechoo-menu-open', opening);
    });

    navigation.addEventListener('click', (event) => {
      if (event.target.closest('a')) {
        closeMenu();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
        closeMenu();
        toggle.focus();
      }
    });

    window.matchMedia('(min-width: 64rem)').addEventListener('change', (event) => {
      if (event.matches) {
        closeMenu();
      }
    });
  }

  document.querySelectorAll('[data-kechoo-selector]').forEach((form) => {
    const selects = Array.from(form.querySelectorAll('select'));
    const submit = form.querySelector('[type="submit"]');

    const updateState = () => {
      selects.forEach((select, index) => {
        if (index === 0) return;
        const previousComplete = selects.slice(0, index).every((item) => item.value !== '');
        select.disabled = !previousComplete;
      });

      if (submit) {
        submit.disabled = !selects.some((select) => select.value !== '');
      }
    };

    selects.forEach((select) => select.addEventListener('change', updateState));
    updateState();
  });

  document.querySelectorAll('form[data-kechoo-prevent-double-submit]').forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('[type="submit"]');
      if (!button) return;
      button.disabled = true;
      button.dataset.originalLabel = button.textContent;
      button.textContent = button.dataset.loadingLabel || 'Sending request…';
    });
  });
})();


(function () {
  const header = document.querySelector('[data-site-header]');
  const toggle = document.querySelector('.menu-toggle');
  const navigation = document.getElementById('primary-navigation');

  if (header && toggle && navigation) {
    const closeMenu = () => {
      toggle.setAttribute('aria-expanded', 'false');
      navigation.classList.remove('is-open');
      document.body.classList.remove('menu-open');
    };

    toggle.addEventListener('click', () => {
      const opening = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(opening));
      navigation.classList.toggle('is-open', opening);
      document.body.classList.toggle('menu-open', opening);
    });

    navigation.addEventListener('click', (event) => {
      if (event.target.closest('a')) closeMenu();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
        closeMenu();
        toggle.focus();
      }
    });

    window.matchMedia('(min-width: 64rem)').addEventListener('change', (event) => {
      if (event.matches) closeMenu();
    });
  }

  document.querySelectorAll('[data-rfq-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const data = new FormData(form);
      const sku = String(data.get('sku') || '').trim();
      const contact = String(data.get('contact') || '').trim();
      const message = String(data.get('message') || '').trim();
      const output = form.querySelector('[data-rfq-output]');

      if (!contact || !message) {
        if (output) output.textContent = 'Please add your contact and requirement before sending.';
        return;
      }

      const body = [
        'Hello Band Saw Blade Supply, please quote the following bandsaw blade requirement:',
        sku ? `SKU/Product: ${sku}` : '',
        `My contact: ${contact}`,
        `Requirement: ${message}`
      ].filter(Boolean).join('\n');

      const mailto = `mailto:info@bandsawbladesupply.com?subject=${encodeURIComponent('Bandsaw blade quotation request')}&body=${encodeURIComponent(body)}`;
      window.location.href = mailto;
      if (output) output.textContent = 'Your email app should open with the quotation request prepared.';
    });
  });
})();

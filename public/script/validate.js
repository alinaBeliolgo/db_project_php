(function () {
  'use strict';
  // Базовая клиентская защита от XSS (дополнение к серверной)
  // Блокируем ввод опасных последовательностей в текстовые поля форм
  const isDangerous = (val) => {
    if (!val) return false;
    const s = String(val);
    // простые эвристики: html-теги, javascript: URI, on* обработчики
    return /<\s*\/?\s*script\b/i.test(s)
      || /<\s*\/?\s*iframe\b/i.test(s)
      || /javascript\s*:/i.test(s)
      || /on\w+\s*=/.test(s)
      || /<|>/.test(s);
  };

  const warn = (input) => {
    input.setCustomValidity('Ввод содержит недопустимые символы или теги.');
    input.reportValidity();
  };

  const clearValidity = (input) => input.setCustomValidity('');

  document.addEventListener('input', (ev) => {
    const el = ev.target;
    if (!(el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement)) return;
    if (['text', 'search', 'email', 'url', 'tel', 'password'].includes(el.type) || el.tagName === 'TEXTAREA') {
      if (isDangerous(el.value)) {
        warn(el);
      } else {
        clearValidity(el);
      }
    }
  });

  document.addEventListener('submit', (ev) => {
    const form = ev.target;
    if (!(form instanceof HTMLFormElement)) return;
    const fields = Array.from(form.elements).filter(el => el instanceof HTMLInputElement || el instanceof HTMLTextAreaElement);
    for (const el of fields) {
      if (isDangerous(el.value)) {
        ev.preventDefault();
        warn(el);
        break;
      }
    }
  });

  // Подтверждение удаления (замена inline onclick)
  document.addEventListener('click', (ev) => {
    const el = ev.target;
    if (el instanceof HTMLAnchorElement && el.classList.contains('delete')) {
      const ok = window.confirm('Точно удалить?');
      if (!ok) {
        ev.preventDefault();
      }
    }
  });
})();

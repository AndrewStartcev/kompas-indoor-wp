document.addEventListener('DOMContentLoaded', () => {
  const successModal = document.querySelector('.success-modal');

  document.addEventListener('wpcf7mailsent', (event) => {
    if (!successModal) return;
    successModal.hidden = false;
    document.body.classList.add('modal-open');
    event.target.reset();
  });

  document.addEventListener('wpcf7submit', (event) => {
    const button = event.target.querySelector('.wpcf7-submit');
    if (button) button.disabled = false;
  });

  document.addEventListener('submit', (event) => {
    if (!event.target.matches('.wpcf7-form')) return;
    const button = event.target.querySelector('.wpcf7-submit');
    if (button) button.disabled = true;
  });

  document.addEventListener('click', (event) => {
    if (!successModal) return;
    if (event.target === successModal || event.target.closest('.success-modal .modal__close')) {
      successModal.hidden = true;
      document.body.classList.remove('modal-open');
    }
  });
});

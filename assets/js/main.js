const menuButton=document.querySelector('.menu-button');const nav=document.querySelector('.nav');menuButton?.addEventListener('click',()=>{const open=nav.classList.toggle('nav--open');menuButton.setAttribute('aria-expanded',String(open))});
const modal=document.querySelector('.modal');const closeModal=()=>{modal.hidden=true;document.body.classList.remove('modal-open')};document.querySelectorAll('[data-estimate]').forEach(button=>button.addEventListener('click',()=>{modal.hidden=false;document.body.classList.add('modal-open');modal.querySelector('input')?.focus()}));modal?.querySelector('.modal__close')?.addEventListener('click',closeModal);modal?.addEventListener('mousedown',event=>{if(event.target===modal)closeModal()});document.addEventListener('keydown',event=>{if(event.key==='Escape'&&!modal.hidden)closeModal()});
// Contact Form 7 owns submissions and dispatches wpcf7mailsent; do not fake a successful submission.
const cookie=document.querySelector('.cookie-banner');if(localStorage.getItem('kompas-cookie-choice'))cookie.hidden=true;document.querySelectorAll('[data-cookie]').forEach(button=>button.addEventListener('click',()=>{localStorage.setItem('kompas-cookie-choice',button.dataset.cookie);cookie.hidden=true}));
document.querySelectorAll('.faq-item button').forEach(button=>button.addEventListener('click',()=>{const answer=button.parentElement.querySelector('p');const open=button.getAttribute('aria-expanded')==='true';button.setAttribute('aria-expanded',String(!open));button.querySelector('b').textContent=open?'+':'−';answer.hidden=open}));
const topButton=document.querySelector('.back-to-top');const updateTop=()=>topButton.classList.toggle('is-visible',scrollY>640);addEventListener('scroll',updateTop,{passive:true});updateTop();topButton.addEventListener('click',()=>scrollTo({top:0,behavior:'smooth'}));
const reveal=document.querySelectorAll('main > section,main > .breadcrumbs,.footer');if(matchMedia('(prefers-reduced-motion: reduce)').matches)reveal.forEach(el=>el.classList.add('is-visible'));else{const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-visible');observer.unobserve(entry.target)}}),{threshold:.08,rootMargin:'0px 0px -50px'});reveal.forEach(el=>{el.classList.add('reveal');observer.observe(el)})}


// Accessible nested navigation: the link opens the catalog, the separate button opens the submenu.
(() => {
  const navigation = document.querySelector('.header .nav');
  const mobileButton = document.querySelector('.header .menu-button');
  if (!navigation) return;

  const mobileQuery = window.matchMedia('(max-width: 1360px)');
  const submenuControls = [];

  navigation.querySelectorAll('.menu-item-has-children').forEach((item, index) => {
    const submenu = Array.from(item.children).find(el => el.matches('.sub-menu'));
    const label = Array.from(item.children).find(el => el.matches('a'));
    if (!submenu || !label) return;

    const id = 'kompas-nav-submenu-' + (index + 1);
    submenu.id = id;

    const toggle = document.createElement('button');
    toggle.className = 'nav__submenu-toggle';
    toggle.type = 'button';
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-controls', id);
    toggle.setAttribute('aria-label', 'Показать подразделы: ' + label.textContent.trim());
    toggle.innerHTML = '<span aria-hidden="true">⌄</span>';
    label.insertAdjacentElement('afterend', toggle);

    const close = () => {
      item.classList.remove('is-submenu-open');
      toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', () => {
      const shouldOpen = !item.classList.contains('is-submenu-open');
      submenuControls.forEach(control => control.close());
      item.classList.toggle('is-submenu-open', shouldOpen);
      toggle.setAttribute('aria-expanded', String(shouldOpen));
    });

    submenuControls.push({ item, toggle, close });
  });

  function closeAll() {
    submenuControls.forEach(control => control.close());
  }

  document.addEventListener('click', event => {
    if (!navigation.contains(event.target)) closeAll();
  });

  document.addEventListener('keydown', event => {
    if (event.key !== 'Escape') return;
    const opened = submenuControls.find(control => control.item.classList.contains('is-submenu-open'));
    if (opened) {
      opened.close();
      opened.toggle.focus();
      event.stopPropagation();
    } else if (mobileQuery.matches && navigation.classList.contains('nav--open')) {
      navigation.classList.remove('nav--open');
      mobileButton?.setAttribute('aria-expanded', 'false');
      mobileButton?.focus();
    }
  });

  navigation.addEventListener('click', event => {
    if (!mobileQuery.matches || !event.target.closest('a')) return;
    navigation.classList.remove('nav--open');
    mobileButton?.setAttribute('aria-expanded', 'false');
    closeAll();
  });

  const onDesktop = () => {
    if (mobileQuery.matches) return;
    navigation.classList.remove('nav--open');
    mobileButton?.setAttribute('aria-expanded', 'false');
    closeAll();
  };
  mobileQuery.addEventListener?.('change', onDesktop);
})();

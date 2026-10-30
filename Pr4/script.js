// Practical 4: DOM manipulation, events, and UI interactivity
const menuToggle = document.querySelector('.menu-toggle');
const mainNav = document.querySelector('#main-nav');
if (menuToggle && mainNav) {
  menuToggle.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
  });
}

const themeToggle = document.querySelector('#theme-toggle');
const savedTheme = localStorage.getItem('studenthub-theme');
if (savedTheme === 'dark') document.body.classList.add('dark-theme');
function updateThemeButton() {
  if (!themeToggle) return;
  const dark = document.body.classList.contains('dark-theme');
  themeToggle.textContent = dark ? 'Switch to Light Theme' : 'Switch to Dark Theme';
  themeToggle.setAttribute('aria-pressed', String(dark));
}
updateThemeButton();
if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-theme');
    localStorage.setItem('studenthub-theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
    updateThemeButton();
  });
}

const notice = document.querySelector('#notice-banner');
const dismissNotice = document.querySelector('#dismiss-notice');
if (notice && dismissNotice) dismissNotice.addEventListener('click', () => { notice.hidden = true; });

// Collapsible FAQ answers
const faqButtons = document.querySelectorAll('.faq-toggle');
faqButtons.forEach(button => {
  button.addEventListener('click', () => {
    const expanded = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', String(!expanded));
    const answer = button.closest('article')?.querySelector('.faq-answer');
    if (answer) answer.hidden = expanded;
  });
});

// Content slider on Home and Events pages
const slides = Array.from(document.querySelectorAll('.slide'));
let currentSlide = 0;
function showSlide(index) {
  if (!slides.length) return;
  currentSlide = (index + slides.length) % slides.length;
  slides.forEach((slide, i) => slide.classList.toggle('active', i === currentSlide));
  const status = document.querySelector('#slide-status');
  if (status) status.textContent = `Slide ${currentSlide + 1} of ${slides.length}`;
}
document.querySelector('#previous-slide')?.addEventListener('click', () => showSlide(currentSlide - 1));
document.querySelector('#next-slide')?.addEventListener('click', () => showSlide(currentSlide + 1));

// Accessible modal popup
const modal = document.querySelector('#info-modal');
const openModal = document.querySelector('#open-modal');
const closeModal = document.querySelector('#close-modal');
const modalDone = document.querySelector('#modal-done');
function closeInfoModal() {
  if (!modal) return;
  modal.hidden = true;
  openModal?.focus();
}
openModal?.addEventListener('click', () => {
  if (!modal) return;
  modal.hidden = false;
  closeModal?.focus();
});
closeModal?.addEventListener('click', closeInfoModal);
modalDone?.addEventListener('click', closeInfoModal);
modal?.addEventListener('click', event => { if (event.target === modal) closeInfoModal(); });
document.addEventListener('keydown', event => { if (event.key === 'Escape' && modal && !modal.hidden) closeInfoModal(); });

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

// Practical 5: Registration form validation
const registrationForm = document.querySelector('#registration-form');
if (registrationForm) {
  const fields = ['name', 'email', 'student-id', 'password', 'confirm-password', 'course'];
  const formStatus = document.querySelector('#form-status');

  function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const error = document.getElementById(`${fieldId}-error`);
    if (error) error.textContent = message;
    if (field) {
      field.setAttribute('aria-invalid', message ? 'true' : 'false');
      field.classList.toggle('invalid-field', Boolean(message));
    }
  }

  function validateRegistrationField(fieldId) {
    const field = document.getElementById(fieldId);
    const value = field.value.trim();
    let message = '';

    if (!value) {
      message = 'This field is required.';
    } else if (fieldId === 'name' && value.length < 2) {
      message = 'Please enter at least 2 characters.';
    } else if (fieldId === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      message = 'Enter a valid email address, for example name@example.com.';
    } else if (fieldId === 'password' && (!/(?=.*[A-Za-z])(?=.*\d).{8,}/.test(value))) {
      message = 'Password must be at least 8 characters and include a letter and a number.';
    } else if (fieldId === 'confirm-password' && value !== document.getElementById('password').value) {
      message = 'Passwords do not match.';
    }

    showFieldError(fieldId, message);
    return !message;
  }

  fields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    field.addEventListener('blur', () => validateRegistrationField(fieldId));
    field.addEventListener('input', () => {
      if (field.getAttribute('aria-invalid') === 'true') validateRegistrationField(fieldId);
      if (fieldId === 'password' && document.getElementById('confirm-password').value) {
        validateRegistrationField('confirm-password');
      }
    });
    field.addEventListener('change', () => validateRegistrationField(fieldId));
  });

  registrationForm.addEventListener('submit', event => {
    event.preventDefault();
    const valid = fields.map(validateRegistrationField).every(Boolean);
    if (!valid) {
      formStatus.textContent = 'Please correct the errors above and try again.';
      const firstInvalid = registrationForm.querySelector('[aria-invalid="true"]');
      firstInvalid?.focus();
      return;
    }
    formStatus.textContent = 'Registration form validated successfully! (Demo only; no data was sent.)';
  });
}


// Practical 6: Fetch JSON data, search, and filter students
const studentGrid = document.querySelector('#student-grid');
if (studentGrid) {
  const searchInput = document.querySelector('#student-search');
  const courseFilter = document.querySelector('#course-filter');
  const dataStatus = document.querySelector('#data-status');
  let students = [];

  function renderStudents() {
    const query = searchInput.value.trim().toLowerCase();
    const course = courseFilter.value;
    const filtered = students.filter(student => {
      const matchesQuery = `${student.name} ${student.email} ${student.course} ${student.year}`.toLowerCase().includes(query);
      const matchesCourse = !course || student.course === course;
      return matchesQuery && matchesCourse;
    });

    studentGrid.replaceChildren();
    if (!filtered.length) {
      studentGrid.innerHTML = '<p>No students match your search.</p>';
    } else {
      filtered.forEach(student => {
        const card = document.createElement('article');
        card.className = 'student-card';
        const name = document.createElement('h3');
        name.textContent = student.name;
        const email = document.createElement('p');
        email.textContent = `Email: ${student.email}`;
        const studentCourse = document.createElement('p');
        studentCourse.textContent = `Course: ${student.course}`;
        const year = document.createElement('p');
        year.textContent = `Year: ${student.year}`;
        card.append(name, email, studentCourse, year);
        studentGrid.append(card);
      });
    }
    dataStatus.textContent = `Showing ${filtered.length} of ${students.length} students`;
  }

  async function loadStudents() {
    dataStatus.textContent = 'Loading student data...';
    try {
      const response = await fetch('students.json');
      if (!response.ok) throw new Error('Could not load student data.');
      students = await response.json();
      const courses = [...new Set(students.map(student => student.course))].sort();
      courses.forEach(course => {
        const option = document.createElement('option');
        option.value = course;
        option.textContent = course;
        courseFilter.append(option);
      });
      renderStudents();
    } catch (error) {
      dataStatus.textContent = 'Unable to load student data. Run this page using VS Code Live Server.';
      studentGrid.innerHTML = '<p>Student data could not be loaded. Please start Live Server and refresh the page.</p>';
    }
  }

  searchInput.addEventListener('input', renderStudents);
  courseFilter.addEventListener('change', renderStudents);
  loadStudents();
}

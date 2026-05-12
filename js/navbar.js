function toggleMobileMenu(e) {
  const menu = document.querySelector('.nav-menu');
  const hamburger = document.querySelector('.hamburger');
  if (!menu || !hamburger) return;

  if (e && typeof e.stopPropagation === 'function') e.stopPropagation();

  menu.classList.toggle('active');
  hamburger.classList.toggle('active');

  const isExpanded = hamburger.classList.contains('active');
  hamburger.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
}

// Close the menu
function closeMenu() {
  const menu = document.querySelector('.nav-menu');
  const hamburger = document.querySelector('.hamburger');
  if (!menu || !hamburger) return;
  menu.classList.remove('active');
  hamburger.classList.remove('active');
  hamburger.setAttribute('aria-expanded', 'false');
}

// Add smooth page transition
function addPageTransition() {
  document.body.style.opacity = '0';
  document.body.style.transition = 'opacity 0.5s ease-in-out';
  
  window.addEventListener('load', () => {
    document.body.style.opacity = '1';
  });
}

function setupMenu() {
  const hamburger = document.querySelector('.hamburger');
  const menu = document.querySelector('.nav-menu');

  // Button already has inline onclick handler in HTML.
  // Avoid duplicate listeners that can toggle menu multiple times per click.

  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', (e) => {
      closeMenu();
      
      // Add smooth transition for page navigation
      const href = link.getAttribute('href');
      if (href && !href.startsWith('#')) {
        e.preventDefault();
        document.body.style.opacity = '0';
        setTimeout(() => {
          window.location.href = href;
        }, 500);
      }
    });
  });

  // outside click
  document.addEventListener('click', (e) => {
    if (!menu || !hamburger) return;
    if (menu.classList.contains('active') && !menu.contains(e.target) && !hamburger.contains(e.target)) {
      closeMenu();
    }
  });

  // escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });

  // add a helper class when orientation is landscape (for CSS)
  function updateLandscapeClass() {
    if (window.matchMedia && window.matchMedia('(orientation: landscape)').matches) {
      document.body.classList.add('landscape');
    } else {
      document.body.classList.remove('landscape');
    }
  }
  window.addEventListener('orientationchange', updateLandscapeClass);
  window.addEventListener('resize', updateLandscapeClass);
  updateLandscapeClass();
  
  // Add page transition on load
  addPageTransition();
}

document.addEventListener('DOMContentLoaded', setupMenu);


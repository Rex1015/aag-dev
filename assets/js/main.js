/* ============================================================
   AERO ASSIST GLOBAL — Main JavaScript
   ============================================================ */

(function () {
  'use strict';

  /* ──────────────────────────────────────────
     1. MOBILE MENU TOGGLE
  ────────────────────────────────────────── */
  const menuBtn = document.querySelector('[data-menu-btn]');
  const navLinks = document.querySelector('[data-nav-links]');

  if (menuBtn && navLinks) {
    menuBtn.addEventListener('click', function () {
      const isOpen = navLinks.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', isOpen);
      menuBtn.textContent = isOpen ? '✕' : '☰';
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (e) {
      if (!menuBtn.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('open');
        menuBtn.setAttribute('aria-expanded', false);
        menuBtn.textContent = '☰';
        // Close all open dropdowns
        document.querySelectorAll('.nav-item.open').forEach(function (item) {
          item.classList.remove('open');
        });
      }
    });
  }

  /* ──────────────────────────────────────────
     2. MOBILE DROPDOWN TOGGLE (tap to open)
  ────────────────────────────────────────── */
  const navItems = document.querySelectorAll('.nav-item[data-dropdown]');

  navItems.forEach(function (item) {
    const trigger = item.querySelector('.nav-link');
    if (!trigger) return;

    trigger.addEventListener('click', function (e) {
      // Only intercept on mobile
      if (window.innerWidth <= 900) {
        e.preventDefault();
        const isOpen = item.classList.toggle('open');
        // Close other open dropdowns
        navItems.forEach(function (other) {
          if (other !== item) other.classList.remove('open');
        });
      }
    });
  });

  /* ──────────────────────────────────────────
     3. ACTIVE NAV LINK HIGHLIGHT
  ────────────────────────────────────────── */
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';

  document.querySelectorAll('.nav-link[data-page]').forEach(function (link) {
    if (link.getAttribute('data-page') === currentPage) {
      link.classList.add('active');
    }
  });

  /* ──────────────────────────────────────────
     4. SCROLL REVEAL ANIMATION
  ────────────────────────────────────────── */
  const revealEls = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window && revealEls.length > 0) {
    const observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    revealEls.forEach(function (el) {
      observer.observe(el);
    });
  } else {
    // Fallback: just show everything
    revealEls.forEach(function (el) {
      el.classList.add('visible');
    });
  }

  /* ──────────────────────────────────────────
     5. HERO VIDEO FALLBACK
  ────────────────────────────────────────── */
  const heroVideo = document.querySelector('.hero-video');

  if (heroVideo) {
    heroVideo.addEventListener('error', function () {
      heroVideo.style.display = 'none';
      const hero = document.querySelector('.hero');
      if (hero) {
        hero.style.background =
          'linear-gradient(135deg, #07080b 0%, #1a1d24 50%, #07080b 100%)';
      }
    });

    // Pause video when tab is not visible (saves resources)
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        heroVideo.pause();
      } else {
        heroVideo.play().catch(function () {});
      }
    });
  }

  /* ──────────────────────────────────────────
     6. ANIMATED COUNTER (stats on home page)
  ────────────────────────────────────────── */
  function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-target'), 10);
    const duration = 1800;
    const start = performance.now();

    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      // Ease out cubic
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(update);
      else el.textContent = target.toLocaleString() + (el.getAttribute('data-suffix') || '');
    }

    requestAnimationFrame(update);
  }

  const counters = document.querySelectorAll('[data-target]');

  if ('IntersectionObserver' in window && counters.length > 0) {
    const counterObserver = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            animateCounter(entry.target);
            counterObserver.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.5 }
    );

    counters.forEach(function (counter) {
      counterObserver.observe(counter);
    });
  }

  /* ──────────────────────────────────────────
     7. FAQ SMOOTH OPEN / CLOSE
  ────────────────────────────────────────── */
  document.querySelectorAll('.faq details').forEach(function (detail) {
    detail.addEventListener('toggle', function () {
      if (detail.open) {
        // Close all other open FAQ items
        document.querySelectorAll('.faq details').forEach(function (other) {
          if (other !== detail && other.open) {
            other.removeAttribute('open');
          }
        });
      }
    });
  });

  /* ──────────────────────────────────────────
     8. SUGGESTION / INQUIRY FORM HANDLER
  ────────────────────────────────────────── */
  const inquiryForm = document.querySelector('[data-inquiry-form]');

  if (inquiryForm) {
    inquiryForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const btn = inquiryForm.querySelector('[data-submit-btn]');
      const originalText = btn ? btn.textContent : '';

      // Show loading state
      if (btn) {
        btn.textContent = 'Sending...';
        btn.disabled = true;
      }

      // Simulate sending (replace with real backend / Formspree later)
      setTimeout(function () {
        showToast('✅ Message sent! We will get back to you shortly.');
        inquiryForm.reset();
        if (btn) {
          btn.textContent = originalText;
          btn.disabled = false;
        }
      }, 1800);
    });
  }

  /* ──────────────────────────────────────────
     9. TOAST NOTIFICATION
  ────────────────────────────────────────── */
  function showToast(message) {
    // Remove existing toast
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      bottom: 28px;
      right: 28px;
      background: rgba(10, 12, 18, 0.96);
      border: 1px solid rgba(255,255,255,0.18);
      color: rgba(255,255,255,0.92);
      padding: 14px 20px;
      border-radius: 14px;
      font-size: 14px;
      font-family: ui-sans-serif, system-ui, sans-serif;
      box-shadow: 0 14px 40px rgba(0,0,0,0.55);
      z-index: 9999;
      opacity: 0;
      transform: translateY(10px);
      transition: opacity 300ms ease, transform 300ms ease;
      max-width: 320px;
    `;

    document.body.appendChild(toast);

    // Trigger animation
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
      });
    });

    // Auto remove after 4 seconds
    setTimeout(function () {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(function () { toast.remove(); }, 320);
    }, 4000);
  }

  /* ──────────────────────────────────────────
     10. STICKY HEADER SHADOW ON SCROLL
  ────────────────────────────────────────── */
  const header = document.querySelector('.header');

  if (header) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 10) {
        header.style.boxShadow = '0 4px 30px rgba(0,0,0,0.45)';
        header.style.background = 'rgba(7, 8, 11, 0.80)';
      } else {
        header.style.boxShadow = 'none';
        header.style.background = 'rgba(7, 8, 11, 0.60)';
      }
    }, { passive: true });
  }

  /* ──────────────────────────────────────────
     11. SMOOTH SCROLL FOR ANCHOR LINKS
  ────────────────────────────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /* ──────────────────────────────────────────
     12. CURRENT YEAR IN FOOTER
  ────────────────────────────────────────── */
  const yearEl = document.querySelector('[data-year]');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

})();

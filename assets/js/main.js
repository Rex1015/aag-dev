/* ============================================================
   AERO ASSIST GLOBAL — Main JavaScript
   ============================================================ */

(function () {
  'use strict';

  /* ──────────────────────────────────────────
     1. MOBILE MENU TOGGLE
  ────────────────────────────────────────── */
  const menuBtn  = document.querySelector('[data-menu-btn]');
  const navLinks = document.querySelector('[data-nav-links]');

  if (menuBtn && navLinks) {
    menuBtn.addEventListener('click', function () {
      const isOpen = navLinks.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', isOpen);
      menuBtn.textContent = isOpen ? '✕' : '☰';
    });

    document.addEventListener('click', function (e) {
      if (!menuBtn.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('open');
        menuBtn.setAttribute('aria-expanded', false);
        menuBtn.textContent = '☰';
        document.querySelectorAll('.nav-item.open').forEach(function (item) {
          item.classList.remove('open');
        });
      }
    });
  }

  /* ──────────────────────────────────────────
     2. MOBILE DROPDOWN TOGGLE
  ────────────────────────────────────────── */
  const navItems = document.querySelectorAll('.nav-item[data-dropdown]');

  navItems.forEach(function (item) {
    const trigger = item.querySelector('.nav-link');
    if (!trigger) return;

    trigger.addEventListener('click', function (e) {
      if (window.innerWidth <= 900) {
        e.preventDefault();
        const isOpen = item.classList.toggle('open');
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
    revealEls.forEach(function (el) { observer.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('visible'); });
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

    document.addEventListener('visibilitychange', function () {
      if (document.hidden) {
        heroVideo.pause();
      } else {
        heroVideo.play().catch(function () {});
      }
    });
  }

  /* ──────────────────────────────────────────
     6. ANIMATED COUNTER
  ────────────────────────────────────────── */
  function animateCounter(el) {
    const target   = parseInt(el.getAttribute('data-target'), 10);
    const duration = 1800;
    const start    = performance.now();

    function update(now) {
      const elapsed  = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased    = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (progress < 1) {
        requestAnimationFrame(update);
      } else {
        el.textContent = target.toLocaleString() +
                         (el.getAttribute('data-suffix') || '');
      }
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
        document.querySelectorAll('.faq details').forEach(function (other) {
          if (other !== detail && other.open) {
            other.removeAttribute('open');
          }
        });
      }
    });
  });

  /* ──────────────────────────────────────────
     8. INQUIRY FORM — REAL EMAIL SUBMISSION
  ────────────────────────────────────────── */
  const inquiryForm = document.querySelector('[data-inquiry-form]');

  if (inquiryForm) {

    // ── Live character counter for message ──
    const messageField = document.getElementById('message');
    if (messageField) {
      // Create counter element
      const counter = document.createElement('p');
      counter.style.cssText = `
        font-size: 12px;
        color: rgba(255,255,255,0.40);
        text-align: right;
        margin-top: 4px;
      `;
      counter.textContent = '0 / 5000 characters';
      messageField.parentNode.appendChild(counter);

      messageField.addEventListener('input', function () {
        const len = messageField.value.length;
        counter.textContent = len + ' / 5000 characters';
        counter.style.color = len > 4500
          ? 'rgba(255, 100, 100, 0.80)'
          : 'rgba(255,255,255,0.40)';
      });
    }

    // ── Form submit handler ──────────────────
    inquiryForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const btn          = inquiryForm.querySelector('[data-submit-btn]');
      const originalText = btn ? btn.textContent : '';

      // ── Client-side validation ─────────────
      const firstName = document.getElementById('first-name').value.trim();
      const lastName  = document.getElementById('last-name').value.trim();
      const email     = document.getElementById('email').value.trim();
      const service   = document.getElementById('service').value;
      const subject   = document.getElementById('subject').value.trim();
      const message   = document.getElementById('message').value.trim();

      if (!firstName || !lastName) {
        showToast('❌ Please enter your full name.', 'error');
        return;
      }
      if (!email || !isValidEmail(email)) {
        showToast('❌ Please enter a valid email address.', 'error');
        return;
      }
      if (!service) {
        showToast('❌ Please select a service type.', 'error');
        return;
      }
      if (!subject) {
        showToast('❌ Please enter a subject.', 'error');
        return;
      }
      if (!message || message.length < 10) {
        showToast('❌ Please enter a message (minimum 10 characters).', 'error');
        return;
      }
      if (message.length > 5000) {
        showToast('❌ Message is too long (maximum 5000 characters).', 'error');
        return;
      }

      // ── Show loading state ─────────────────
      if (btn) {
        btn.textContent = '⏳ Sending...';
        btn.disabled    = true;
        btn.style.opacity = '0.7';
      }

      // ── Build FormData ─────────────────────
      const formData = new FormData(inquiryForm);

      // ── Send to PHP backend ────────────────
      fetch('send-mail.php', {
        method: 'POST',
        body:   formData
      })
      .then(function (response) {
        return response.json().then(function (data) {
          return { status: response.status, data: data };
        });
      })
      .then(function (result) {
        if (result.data.success) {
          // ── Success ────────────────────────
          showToast('✅ ' + result.data.message, 'success');
          inquiryForm.reset();

          // Reset character counter
          if (messageField) {
            const counter = messageField.parentNode.querySelector('p');
            if (counter) counter.textContent = '0 / 5000 characters';
          }

          // Scroll to top of form
          inquiryForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

        } else {
          // ── Server validation error ────────
          showToast('❌ ' + result.data.message, 'error');
        }
      })
      .catch(function (error) {
        // ── Network error ──────────────────
        console.error('Form submission error:', error);
        showToast(
          '❌ Network error. Please check your connection and try again.',
          'error'
        );
      })
      .finally(function () {
        // ── Restore button ─────────────────
        if (btn) {
          btn.textContent  = originalText;
          btn.disabled     = false;
          btn.style.opacity = '1';
        }
      });
    });
  }

  /* ──────────────────────────────────────────
     9. EMAIL VALIDATOR HELPER
  ────────────────────────────────────────── */
  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  /* ──────────────────────────────────────────
     10. TOAST NOTIFICATION
  ────────────────────────────────────────── */
  function showToast(message, type) {
    const existing = document.querySelector('.toast');
    if (existing) existing.remove();

    const borderColor = type === 'success'
      ? 'rgba(100, 220, 130, 0.35)'
      : type === 'error'
      ? 'rgba(220, 100, 100, 0.35)'
      : 'rgba(255,255,255,0.18)';

    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.cssText = `
      position: fixed;
      bottom: 28px;
      right: 28px;
      background: rgba(10, 12, 18, 0.97);
      border: 1px solid ${borderColor};
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
      max-width: 360px;
      line-height: 1.5;
    `;

    document.body.appendChild(toast);

    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        toast.style.opacity   = '1';
        toast.style.transform = 'translateY(0)';
      });
    });

    const duration = type === 'error' ? 6000 : 5000;

    setTimeout(function () {
      toast.style.opacity   = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(function () { toast.remove(); }, 320);
    }, duration);
  }

  /* ──────────────────────────────────────────
     11. STICKY HEADER SHADOW ON SCROLL
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
     12. SMOOTH SCROLL FOR ANCHOR LINKS
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
     13. CURRENT YEAR IN FOOTER
  ────────────────────────────────────────── */
  const yearEl = document.querySelector('[data-year]');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

})();

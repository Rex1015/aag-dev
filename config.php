<?php
// ============================================================
// AERO ASSIST GLOBAL — Email Configuration
// !! NEVER commit this file to GitHub !!
// !! Already protected by .gitignore !!
// ============================================================

// ── SMTP Settings (Hostinger) ──────────────────────────────
define('SMTP_HOST',     'smtp.hostinger.com');
define('SMTP_PORT',     587);
define('SMTP_SECURE',   'tls');

// ── Sender Account ─────────────────────────────────────────
// This is the email account that SENDS all emails
// Create this in Hostinger Email panel
define('SMTP_USER',     'noreply@yourdomain.com');
define('SMTP_PASS',     'your-email-password-here');
define('SMTP_FROM',     'noreply@yourdomain.com');
define('SMTP_FROM_NAME','Aero Assist Global');

// ── 5 Department Emails ────────────────────────────────────
// Create all 5 of these in Hostinger Email panel
define('EMAIL_GROUND',   'ground@yourdomain.com');
define('EMAIL_PERMITS',  'permits@yourdomain.com');
define('EMAIL_CHARTER',  'charter@yourdomain.com');
define('EMAIL_FUEL',     'fuel@yourdomain.com');
define('EMAIL_OPS',      'ops@yourdomain.com');

// ── Rate Limiting ──────────────────────────────────────────
define('MAX_REQUESTS',   5);    // max submissions
define('TIME_WINDOW',    900);  // per 15 minutes (seconds)

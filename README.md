# ✈️ Aero Assist Global — Website

![Status](https://img.shields.io/badge/Status-In%20Development-yellow)
![HTML](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
![CSS](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)

> **Aero Assist Global** is a professional aviation support services
> company offering ground handling, permits, charter solutions, and
> fuel services worldwide. This repository contains the source code
> for the official company website.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Pages](#pages)
- [Features](#features)
- [File Structure](#file-structure)
- [Email System](#email-system)
- [Setup Guide](#setup-guide)
- [Deployment](#deployment)
- [Company Info](#company-info)
- [Status](#status)

---

## 🌐 Overview
Project
Aero Assist Global — Official Website

Purpose
Business website for aviation services

Stack
HTML5, CSS3, JavaScript, PHP

Hosting
Hostinger (pending)

Domain
Pending purchase

Preview
GitHub Pages (development preview)

📄 Pages
Home
index.html
Landing page with hero video

Services
services.html
Services overview and selection

Ground Handling
ground-handling.html
Ground handling services detail

Permits
permits.html
Aviation permits services detail

Charter
charter.html
Charter solutions detail

Fuel Services
fuel.html
Fuel services detail

Inquiry
inquiry.html
FAQ and contact/inquiry form

✨ Features
🎨 Design & UI
Dark aviation-themed design (Black + Silver)
Fully responsive — mobile, tablet, and desktop
Smooth scroll reveal animations
Animated statistics counter on homepage
Hero background video with fallback poster
Sticky navigation header with blur effect
Mobile hamburger menu with dropdown support
Toast notification system
📩 Inquiry Form
Service-based email routing to 5 departments
Client-side validation before submission
Live character counter (5000 max)
Priority level selection (Normal / Urgent / Critical)
Auto-reply email sent to customer
Rate limiting (5 submissions per 15 minutes)
Spam protection and input sanitization
🔍 FAQ System
Filterable FAQ by service category
Smooth accordion open/close
Categories: General, Ground Handling, Permits, Charter, Fuel
♿ Accessibility
Skip to main content link
ARIA labels and roles throughout
Semantic HTML structure
Keyboard navigable
Reduced motion support

📁 File Structure
aag-dev/
│
├── 📄 index.html                  # Home page
├── 📄 services.html               # Services overview
├── 📄 ground-handling.html        # Ground handling page
├── 📄 permits.html                # Permits page
├── 📄 charter.html                # Charter page
├── 📄 fuel.html                   # Fuel services page
├── 📄 inquiry.html                # Inquiry & FAQ page
│
├── 📄 send-mail.php               # Email handler (backend)
├── 📄 config.php                  # Email config (PRIVATE - not on GitHub)
├── 📄 .htaccess                   # Apache security rules
├── 📄 .gitignore                  # Git ignore rules
│
├── 📁 phpmailer/                  # PHPMailer library
│   ├── PHPMailer.php
│   ├── SMTP.php
│   └── Exception.php
│
└── 📁 assets/
    ├── 📁 css/
    │   └── styles.css             # Global stylesheet
    ├── 📁 js/
    │   └── main.js                # Global JavaScript
    ├── 📁 img/
    │   └── logo.png               # Company logo
    └── 📁 media/
        ├── hero.mp4               # Hero background video
        └── hero-poster.jpg        # Video fallback poster

📧 Email System
How It Works
User submits inquiry form
        │
        ▼
main.js validates (client-side)
        │
        ▼
fetch() POST → send-mail.php
        │
        ▼
PHP sanitizes & validates
        │
        ▼
Routes to correct department email
        │
        ├── ✈️  Ground Handling → ground@yourdomain.com
        ├── 📋  Permits         → permits@yourdomain.com
        ├── 🛩  Charter         → charter@yourdomain.com
        ├── ⛽  Fuel Services   → fuel@yourdomain.com
        └── 📡  General/Other   → ops@yourdomain.com
        │
        ▼
Auto-reply sent to customer ✅

5 Department Emails
Ground Handling
ground@yourdomain.com
Ground handling inquiries

Permits
permits@yourdomain.com
Permit processing requests

Charter
charter@yourdomain.com
Charter flight requests

Fuel Services
fuel@yourdomain.com
Fuel coordination requests

Operations
ops@yourdomain.com
General & multiple service

⚙️ Setup Guide
Prerequisites
- Web server with PHP 7.4+ support
- PHP mail / SMTP access
- PHPMailer library
- Hostinger hosting account (or similar)

Step 1 — Clone the Repository
git clone https://github.com/YOURUSERNAME/aag-dev.git
cd aag-dev

Step 2 — Install PHPMailer
Option A — Composer (Recommended)
composer require phpmailer/phpmailer

Option B — Manual
1. Download from github.com/PHPMailer/PHPMailer
2. Copy PHPMailer.php, SMTP.php, Exception.php
3. Place in /phpmailer/ folder

Step 3 — Configure Email Settings
# Create config.php (copy from template)
# Fill in your real SMTP credentials
// config.php
define('SMTP_HOST',     'smtp.hostinger.com');
define('SMTP_PORT',     587);
define('SMTP_SECURE',   'tls');
define('SMTP_USER',     'noreply@yourdomain.com');
define('SMTP_PASS',     'your-password-here');
define('SMTP_FROM',     'noreply@yourdomain.com');
define('SMTP_FROM_NAME','Aero Assist Global');

define('EMAIL_GROUND',  'ground@yourdomain.com');
define('EMAIL_PERMITS', 'permits@yourdomain.com');
define('EMAIL_CHARTER', 'charter@yourdomain.com');
define('EMAIL_FUEL',    'fuel@yourdomain.com');
define('EMAIL_OPS',     'ops@yourdomain.com');

Step 4 — Update Company Info
Find and replace in all HTML files:

ops@aeroassistglobal.com  →  your-real-email@yourdomain.com
+1 (000) 000-0000         →  your-real-phone-number

Step 5 — Set File Permissions (Hostinger)
config.php    → 600  (owner read/write only)
send-mail.php → 644
.htaccess     → 644

🚀 Deployment
GitHub Pages (Preview Only)
1. Go to repo Settings → Pages
2. Source: Deploy from branch
3. Branch: main / root
4. Save → visit:
   https://YOURUSERNAME.github.io/aag-dev/

⚠️ Note: Email form does NOT work on
   GitHub Pages (no PHP support)
   Use only for visual preview

Hostinger (Live Production)
1. Login to hPanel
2. Files → File Manager → public_html
3. Upload all project files
4. Create 5 email accounts in hPanel
5. Update config.php with real credentials
6. Set config.php permissions to 600
7. Test form submissions
8. Go live! ✅

🔒 Security Notes
⚠️  config.php is listed in .gitignore
    NEVER push config.php to GitHub
    It contains sensitive SMTP credentials

⚠️  .htaccess blocks direct access to config.php

⚠️  Rate limiting prevents form spam
    (5 submissions per 15 minutes per user)

⚠️  All form inputs are sanitized
    before processing


👨‍💻 Developer Notes
- aag-main  →  Original backup (DO NOT EDIT)
- aag-dev   →  Working development copy

Always work in aag-dev.
Push to GitHub after every change.
Transfer to Hostinger when domain is ready.


---

# 💾 How to Save It

``bash
# In your aag-dev folder
# Delete old README.md
# Create new README.md with content above
# Then push to GitHub:

git add README.md
git commit -m "Add professional README"
git push


© 2025 Aero Assist Global. All rights reserved. Built for business use. Confidential.

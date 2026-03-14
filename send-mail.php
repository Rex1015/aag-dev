<?php
// ============================================================
// AERO ASSIST GLOBAL — Email Handler
// Requires PHPMailer (installed via Composer or manual)
// ============================================================

// ── Security Headers ───────────────────────────────────────
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

// ── Only allow POST requests ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
    exit;
}

// ── Load config ────────────────────────────────────────────
require_once 'config.php';

// ── Rate Limiting ──────────────────────────────────────────
session_start();

$now = time();
$ip  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = ['count' => 0, 'start' => $now];
}

// Reset window if expired
if (($now - $_SESSION['rate_limit']['start']) > TIME_WINDOW) {
    $_SESSION['rate_limit'] = ['count' => 0, 'start' => $now];
}

// Block if over limit
if ($_SESSION['rate_limit']['count'] >= MAX_REQUESTS) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Too many requests. Please wait 15 minutes and try again.'
    ]);
    exit;
}

$_SESSION['rate_limit']['count']++;

// ── Sanitize & Validate Input ──────────────────────────────
function clean($value) {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

$firstName = clean($_POST['first-name']  ?? '');
$lastName  = clean($_POST['last-name']   ?? '');
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone     = clean($_POST['phone']       ?? '');
$company   = clean($_POST['company']     ?? '');
$service   = clean($_POST['service']     ?? '');
$subject   = clean($_POST['subject']     ?? '');
$message   = clean($_POST['message']     ?? '');
$priority  = clean($_POST['priority']    ?? 'normal');
$source    = clean($_POST['source']      ?? '');

// ── Required field validation ──────────────────────────────
$errors = [];

if (empty($firstName))               $errors[] = 'First name is required.';
if (empty($lastName))                $errors[] = 'Last name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email address is required.';
if (empty($service))                 $errors[] = 'Please select a service.';
if (empty($subject))                 $errors[] = 'Subject is required.';
if (empty($message))                 $errors[] = 'Message is required.';
if (strlen($message) < 10)          $errors[] = 'Message is too short.';
if (strlen($message) > 5000)        $errors[] = 'Message is too long (max 5000 characters).';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    exit;
}

// ── Map service to department email ───────────────────────
$serviceEmailMap = [
    'ground-handling' => EMAIL_GROUND,
    'permits'         => EMAIL_PERMITS,
    'charter'         => EMAIL_CHARTER,
    'fuel'            => EMAIL_FUEL,
    'trip-support'    => EMAIL_OPS,
    'multiple'        => EMAIL_OPS,
    'suggestion'      => EMAIL_OPS,
    'other'           => EMAIL_OPS,
];

$recipientEmail = $serviceEmailMap[$service] ?? EMAIL_OPS;

// ── Service & Priority Labels ──────────────────────────────
$serviceLabels = [
    'ground-handling' => '✈️ Ground Handling',
    'permits'         => '📋 Permits',
    'charter'         => '🛩 Charter',
    'fuel'            => '⛽ Fuel Services',
    'trip-support'    => '🗺 Trip Support',
    'multiple'        => '🔄 Multiple Services',
    'suggestion'      => '💡 General Suggestion',
    'other'           => '📝 Other',
];

$priorityLabels = [
    'normal'   => '🟢 Normal — Response within 24 hours',
    'urgent'   => '🟡 Urgent — Response within 4 hours',
    'critical' => '🔴 Critical — Immediate response required',
];

$serviceLabel  = $serviceLabels[$service]   ?? $service;
$priorityLabel = $priorityLabels[$priority] ?? $priority;
$fullName      = $firstName . ' ' . $lastName;
$timestamp     = date('F j, Y \a\t g:i A T');

// ── Load PHPMailer ─────────────────────────────────────────
// Option A: Via Composer (recommended)
// require_once 'vendor/autoload.php';

// Option B: Manual download (place PHPMailer files in /phpmailer/)
require_once 'phpmailer/PHPMailer.php';
require_once 'phpmailer/SMTP.php';
require_once 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ── Build Email HTML Template ──────────────────────────────
function buildEmailHTML($fullName, $email, $phone, $company,
                         $serviceLabel, $subject, $message,
                         $priorityLabel, $source, $timestamp, $ip) {
    $phone   = $phone   ?: 'Not provided';
    $company = $company ?: 'Not provided';
    $source  = $source  ?: 'Not provided';

    return "
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width,initial-scale=1.0'>
<title>New Inquiry — Aero Assist Global</title>
</head>
<body style='margin:0;padding:0;background:#07080b;font-family:Arial,sans-serif;'>

  <table width='100%' cellpadding='0' cellspacing='0'
         style='background:#07080b;padding:32px 16px;'>
    <tr>
      <td align='center'>
        <table width='600' cellpadding='0' cellspacing='0'
               style='max-width:600px;width:100%;'>

          <!-- Header -->
          <tr>
            <td style='
              background:linear-gradient(135deg,#1a1d24,#0d0f14);
              border:1px solid rgba(255,255,255,0.13);
              border-radius:16px 16px 0 0;
              padding:28px 32px;
              text-align:center;'>
              <p style='margin:0 0 4px;font-size:13px;
                        color:rgba(255,255,255,0.5);
                        letter-spacing:1.5px;
                        text-transform:uppercase;'>
                New Inquiry Received
              </p>
              <h1 style='margin:0;font-size:24px;color:#ffffff;
                         font-weight:700;letter-spacing:-0.3px;'>
                Aero Assist Global
              </h1>
              <p style='margin:4px 0 0;font-size:12px;
                        color:rgba(255,255,255,0.45);'>
                Aviation Support Services
              </p>
            </td>
          </tr>

          <!-- Priority Banner -->
          <tr>
            <td style='
              background:rgba(255,255,255,0.06);
              border-left:1px solid rgba(255,255,255,0.13);
              border-right:1px solid rgba(255,255,255,0.13);
              padding:14px 32px;
              text-align:center;'>
              <p style='margin:0;font-size:14px;color:rgba(255,255,255,0.85);'>
                <strong>Priority:</strong> {$priorityLabel}
              </p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style='
              background:#0d0f14;
              border:1px solid rgba(255,255,255,0.13);
              border-top:none;
              border-radius:0 0 16px 16px;
              padding:32px;'>

              <!-- Service Badge -->
              <div style='
                display:inline-block;
                background:rgba(255,255,255,0.08);
                border:1px solid rgba(255,255,255,0.15);
                border-radius:999px;
                padding:6px 16px;
                font-size:13px;
                color:#c7cbd3;
                margin-bottom:24px;'>
                {$serviceLabel}
              </div>

              <!-- Contact Details Table -->
              <table width='100%' cellpadding='0' cellspacing='0'
                     style='margin-bottom:24px;'>

                <tr>
                  <td style='padding:10px 14px;
                             background:rgba(255,255,255,0.04);
                             border:1px solid rgba(255,255,255,0.10);
                             border-radius:10px 10px 0 0;'>
                    <span style='font-size:12px;color:rgba(255,255,255,0.45);
                                 text-transform:uppercase;letter-spacing:1px;'>
                      Full Name
                    </span><br>
                    <span style='font-size:15px;color:#ffffff;font-weight:600;'>
                      {$fullName}
                    </span>
                  </td>
                </tr>

                <tr>
                  <td style='padding:10px 14px;
                             background:rgba(255,255,255,0.04);
                             border:1px solid rgba(255,255,255,0.10);
                             border-top:none;'>
                    <span style='font-size:12px;color:rgba(255,255,255,0.45);
                                 text-transform:uppercase;letter-spacing:1px;'>
                      Email Address
                    </span><br>
                    <a href='mailto:{$email}'
                       style='font-size:15px;color:#c7cbd3;font-weight:600;
                              text-decoration:none;'>
                      {$email}
                    </a>
                  </td>
                </tr>

                <tr>
                  <td style='padding:10px 14px;
                             background:rgba(255,255,255,0.04);
                             border:1px solid rgba(255,255,255,0.10);
                             border-top:none;'>
                    <span style='font-size:12px;color:rgba(255,255,255,0.45);
                                 text-transform:uppercase;letter-spacing:1px;'>
                      Phone Number
                    </span><br>
                    <span style='font-size:15px;color:#ffffff;'>
                      {$phone}
                    </span>
                  </td>
                </tr>

                <tr>
                  <td style='padding:10px 14px;
                             background:rgba(255,255,255,0.04);
                             border:1px solid rgba(255,255,255,0.10);
                             border-top:none;
                             border-radius:0 0 10px 10px;'>
                    <span style='font-size:12px;color:rgba(255,255,255,0.45);
                                 text-transform:uppercase;letter-spacing:1px;'>
                      Company / Organization
                    </span><br>
                    <span style='font-size:15px;color:#ffffff;'>
                      {$company}
                    </span>
                  </td>
                </tr>

              </table>

              <!-- Subject -->
              <div style='margin-bottom:20px;'>
                <p style='margin:0 0 6px;font-size:12px;
                           color:rgba(255,255,255,0.45);
                           text-transform:uppercase;letter-spacing:1px;'>
                  Subject
                </p>
                <p style='margin:0;font-size:16px;color:#ffffff;
                           font-weight:600;line-height:1.4;'>
                  {$subject}
                </p>
              </div>

              <!-- Message -->
              <div style='margin-bottom:24px;'>
                <p style='margin:0 0 8px;font-size:12px;
                           color:rgba(255,255,255,0.45);
                           text-transform:uppercase;letter-spacing:1px;'>
                  Message
                </p>
                <div style='
                  background:rgba(255,255,255,0.04);
                  border:1px solid rgba(255,255,255,0.10);
                  border-radius:12px;
                  padding:16px;
                  font-size:14px;
                  color:rgba(255,255,255,0.80);
                  line-height:1.7;
                  white-space:pre-wrap;'>
                  {$message}
                </div>
              </div>

              <!-- Reply Button -->
              <div style='text-align:center;margin-bottom:28px;'>
                <a href='mailto:{$email}?subject=Re: {$subject}'
                   style='
                     display:inline-block;
                     background:linear-gradient(135deg,
                       rgba(199,203,211,0.24),
                       rgba(255,255,255,0.06));
                     border:1px solid rgba(255,255,255,0.18);
                     border-radius:12px;
                     padding:13px 28px;
                     color:#ffffff;
                     font-size:14px;
                     font-weight:600;
                     text-decoration:none;
                     letter-spacing:0.3px;'>
                  📩 Reply to {$firstName}
                </a>
              </div>

              <!-- Meta Info -->
              <table width='100%' cellpadding='0' cellspacing='0'>
                <tr>
                  <td style='
                    background:rgba(255,255,255,0.03);
                    border:1px solid rgba(255,255,255,0.08);
                    border-radius:10px;
                    padding:14px 16px;'>
                    <p style='margin:0 0 6px;font-size:12px;
                               color:rgba(255,255,255,0.35);
                               text-transform:uppercase;letter-spacing:1px;'>
                      Submission Details
                    </p>
                    <p style='margin:0;font-size:12px;
                               color:rgba(255,255,255,0.45);
                               line-height:1.8;'>
                      🕐 <strong style='color:rgba(255,255,255,0.60);'>
                        Submitted:</strong> {$timestamp}<br>
                      🌍 <strong style='color:rgba(255,255,255,0.60);'>
                        Source:</strong> {$source}<br>
                      🔒 <strong style='color:rgba(255,255,255,0.60);'>
                        IP Address:</strong> {$ip}
                    </p>
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style='padding:20px 0;text-align:center;'>
              <p style='margin:0;font-size:12px;
                         color:rgba(255,255,255,0.25);
                         line-height:1.6;'>
                This email was automatically generated by the<br>
                Aero Assist Global inquiry system.<br>
                © " . date('Y') . " Aero Assist Global. All rights reserved.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>";
}

// ── Build Auto-Reply HTML for Customer ────────────────────
function buildAutoReplyHTML($firstName, $serviceLabel, $subject,
                             $priorityLabel, $timestamp) {
    return "
<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width,initial-scale=1.0'>
<title>We Received Your Inquiry — Aero Assist Global</title>
</head>
<body style='margin:0;padding:0;background:#07080b;font-family:Arial,sans-serif;'>

  <table width='100%' cellpadding='0' cellspacing='0'
         style='background:#07080b;padding:32px 16px;'>
    <tr>
      <td align='center'>
        <table width='600' cellpadding='0' cellspacing='0'
               style='max-width:600px;width:100%;'>

          <!-- Header -->
          <tr>
            <td style='
              background:linear-gradient(135deg,#1a1d24,#0d0f14);
              border:1px solid rgba(255,255,255,0.13);
              border-radius:16px 16px 0 0;
              padding:28px 32px;
              text-align:center;'>
              <p style='margin:0 0 4px;font-size:13px;
                        color:rgba(255,255,255,0.5);
                        letter-spacing:1.5px;
                        text-transform:uppercase;'>
                Inquiry Confirmation
              </p>
              <h1 style='margin:0;font-size:24px;color:#ffffff;
                         font-weight:700;letter-spacing:-0.3px;'>
                Aero Assist Global
              </h1>
              <p style='margin:4px 0 0;font-size:12px;
                        color:rgba(255,255,255,0.45);'>
                Aviation Support Services
              </p>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style='
              background:#0d0f14;
              border:1px solid rgba(255,255,255,0.13);
              border-top:none;
              border-radius:0 0 16px 16px;
              padding:32px;'>

              <!-- Greeting -->
              <h2 style='margin:0 0 12px;font-size:20px;
                          color:#ffffff;font-weight:600;'>
                Hello, {$firstName}! 👋
              </h2>
              <p style='margin:0 0 24px;font-size:14px;
                         color:rgba(255,255,255,0.70);
                         line-height:1.7;'>
                Thank you for reaching out to Aero Assist Global.
                We have successfully received your inquiry and our
                team will get back to you as soon as possible.
              </p>

              <!-- Inquiry Summary -->
              <div style='
                background:rgba(255,255,255,0.04);
                border:1px solid rgba(255,255,255,0.10);
                border-radius:12px;
                padding:20px;
                margin-bottom:24px;'>
                <p style='margin:0 0 12px;font-size:12px;
                           color:rgba(255,255,255,0.45);
                           text-transform:uppercase;letter-spacing:1px;'>
                  Your Inquiry Summary
                </p>
                <table width='100%' cellpadding='0' cellspacing='0'>
                  <tr>
                    <td style='padding:6px 0;font-size:13px;
                               color:rgba(255,255,255,0.50);width:40%;'>
                      Service Requested:
                    </td>
                    <td style='padding:6px 0;font-size:13px;
                               color:#ffffff;font-weight:600;'>
                      {$serviceLabel}
                    </td>
                  </tr>
                  <tr>
                    <td style='padding:6px 0;font-size:13px;
                               color:rgba(255,255,255,0.50);'>
                      Subject:
                    </td>
                    <td style='padding:6px 0;font-size:13px;
                               color:#ffffff;font-weight:600;'>
                      {$subject}
                    </td>
                  </tr>
                  <tr>
                    <td style='padding:6px 0;font-size:13px;
                               color:rgba(255,255,255,0.50);'>
                      Priority:
                    </td>
                    <td style='padding:6px 0;font-size:13px;
                               color:#ffffff;font-weight:600;'>
                      {$priorityLabel}
                    </td>
                  </tr>
                  <tr>
                    <td style='padding:6px 0;font-size:13px;
                               color:rgba(255,255,255,0.50);'>
                      Submitted:
                    </td>
                    <td style='padding:6px 0;font-size:13px;
                               color:#ffffff;font-weight:600;'>
                      {$timestamp}
                    </td>
                  </tr>
                </table>
              </div>

              <!-- Response Times -->
              <div style='
                background:rgba(255,255,255,0.04);
                border:1px solid rgba(255,255,255,0.10);
                border-radius:12px;
                padding:20px;
                margin-bottom:24px;'>
                <p style='margin:0 0 12px;font-size:12px;
                           color:rgba(255,255,255,0.45);
                           text-transform:uppercase;letter-spacing:1px;'>
                  Expected Response Time
                </p>
                <p style='margin:0 0 8px;font-size:13px;
                           color:rgba(255,255,255,0.60);'>
                  🔴 Critical — Immediate response
                </p>
                <p style='margin:0 0 8px;font-size:13px;
                           color:rgba(255,255,255,0.60);'>
                  🟡 Urgent — Within 4 hours
                </p>
                <p style='margin:0;font-size:13px;
                           color:rgba(255,255,255,0.60);'>
                  🟢 Normal — Within 24 hours
                </p>
              </div>

              <!-- Contact Info -->
              <div style='
                background:rgba(255,255,255,0.04);
                border:1px solid rgba(255,255,255,0.10);
                border-radius:12px;
                padding:20px;
                margin-bottom:24px;'>
                <p style='margin:0 0 12px;font-size:12px;
                           color:rgba(255,255,255,0.45);
                           text-transform:uppercase;letter-spacing:1px;'>
                  Need Urgent Assistance?
                </p>
                <p style='margin:0 0 6px;font-size:13px;
                           color:rgba(255,255,255,0.70);'>
                  📞 Call us: <strong style='color:#ffffff;'>
                    +1 (000) 000-0000
                  </strong>
                </p>
                <p style='margin:0;font-size:13px;
                           color:rgba(255,255,255,0.70);'>
                  📧 Email: <strong style='color:#ffffff;'>
                    ops@yourdomain.com
                  </strong>
                </p>
              </div>

              <p style='margin:0;font-size:13px;
                         color:rgba(255,255,255,0.45);
                         line-height:1.6;text-align:center;'>
                Please do not reply to this email directly.<br>
                Our team will contact you using your provided email address.
              </p>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style='padding:20px 0;text-align:center;'>
              <p style='margin:0;font-size:12px;
                         color:rgba(255,255,255,0.25);
                         line-height:1.6;'>
                © " . date('Y') . " Aero Assist Global.
                All rights reserved.<br>
                Your trusted partner for global aviation support.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>";
}

// ── Send Emails via PHPMailer ──────────────────────────────
try {

    // ── EMAIL 1: To Department ─────────────────────────────
    $mail = new PHPMailer(true);

    // SMTP Config
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;

    // From
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);

    // Reply-To (customer's email)
    $mail->addReplyTo($email, $fullName);

    // To (department email)
    $mail->addAddress($recipientEmail);

    // Subject
    $priorityPrefix = [
        'critical' => '[CRITICAL] ',
        'urgent'   => '[URGENT] ',
        'normal'   => '',
    ];
    $emailSubject = ($priorityPrefix[$priority] ?? '') .
                    'New Inquiry: ' . $serviceLabel .
                    ' — ' . $fullName;

    $mail->Subject = $emailSubject;
    $mail->isHTML(true);
    $mail->Body    = buildEmailHTML(
        $fullName, $email, $phone, $company,
        $serviceLabel, $subject, $message,
        $priorityLabel, $source, $timestamp, $ip
    );
    $mail->AltBody = "New inquiry from {$fullName}\n" .
                     "Email: {$email}\n" .
                     "Phone: {$phone}\n" .
                     "Company: {$company}\n" .
                     "Service: {$serviceLabel}\n" .
                     "Priority: {$priorityLabel}\n" .
                     "Subject: {$subject}\n\n" .
                     "Message:\n{$message}\n\n" .
                     "Submitted: {$timestamp}";

    $mail->send();

    // ── EMAIL 2: Auto-Reply to Customer ───────────────────
    $mail2 = new PHPMailer(true);

    $mail2->isSMTP();
    $mail2->Host       = SMTP_HOST;
    $mail2->SMTPAuth   = true;
    $mail2->Username   = SMTP_USER;
    $mail2->Password   = SMTP_PASS;
    $mail2->SMTPSecure = SMTP_SECURE;
    $mail2->Port       = SMTP_PORT;

    $mail2->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail2->addAddress($email, $fullName);
    $mail2->Subject = 'We Received Your Inquiry — Aero Assist Global';
    $mail2->isHTML
        $mail2->isHTML(true);
    $mail2->Body    = buildAutoReplyHTML(
        $firstName, $serviceLabel, $subject,
        $priorityLabel, $timestamp
    );
    $mail2->AltBody = "Hello {$firstName},\n\n" .
                      "Thank you for contacting Aero Assist Global.\n" .
                      "We have received your inquiry and will get back\n" .
                      "to you as soon as possible.\n\n" .
                      "Inquiry Summary:\n" .
                      "Service: {$serviceLabel}\n" .
                      "Subject: {$subject}\n" .
                      "Priority: {$priorityLabel}\n" .
                      "Submitted: {$timestamp}\n\n" .
                      "For urgent matters call: +1 (000) 000-0000\n\n" .
                      "© " . date('Y') . " Aero Assist Global.\n" .
                      "All rights reserved.";

    $mail2->send();

    // ── Success Response ───────────────────────────────────
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Your inquiry has been sent successfully! ' .
                     'We will get back to you shortly. ' .
                     'Please check your email for confirmation.'
    ]);

} catch (Exception $e) {

    // Log error privately (never expose to user)
    error_log('[AAG Mailer Error] ' . date('Y-m-d H:i:s') .
              ' | Service: ' . $service .
              ' | Email: ' . $email .
              ' | Error: ' . $mail->ErrorInfo);

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, there was an error sending your message. ' .
                     'Please try again or contact us directly at ' .
                     'ops@yourdomain.com'
    ]);
}
?>

<?php
// Handles Contact Us form submissions: validates, stores to DB, and emails admin.

// Redirect helper
function redirect_with($params = []) {
    $base = 'contact-us.php';
    if (!headers_sent()) {
        $qs = http_build_query($params);
        header('Location: ' . $base . ($qs ? ('?' . $qs) : ''));
    }
    exit;
}

// Ensure POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with(['error' => 'invalid_method']);
}

// Include DB connection
// Expecting connection.php to define $conn (mysqli). If it defines $con instead, alias it.
require_once __DIR__ . '/connection.php';
if (!isset($conn) && isset($con)) {
    $conn = $con; // alias common variable name
}
if (!isset($conn)) {
    redirect_with(['error' => 'db_connection_missing']);
}

// Prepare input
$name    = isset($_POST['name'])    ? trim($_POST['name'])    : '';
$email   = isset($_POST['email'])   ? trim($_POST['email'])   : '';
$phone   = isset($_POST['phone'])   ? trim($_POST['phone'])   : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate
$errors = [];
if ($name === '')   { $errors[] = 'name_required'; }
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'email_invalid'; }
if ($subject === '') { $errors[] = 'subject_required'; }
if ($message === '') { $errors[] = 'message_required'; }

// Basic length guards
if (mb_strlen($name) > 100)      { $errors[] = 'name_too_long'; }
if (mb_strlen($email) > 255)     { $errors[] = 'email_too_long'; }
if (mb_strlen($phone) > 30)      { $errors[] = 'phone_too_long'; }
if (mb_strlen($subject) > 150)   { $errors[] = 'subject_too_long'; }
if (mb_strlen($message) > 5000)  { $errors[] = 'message_too_long'; }

if ($errors) {
    redirect_with(['error' => implode(',', $errors)]);
}

// Derive optional user_id from session if available
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$userId = null;
if (isset($_SESSION)) {
    if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        $userId = (int) $_SESSION['user_id'];
    } elseif (isset($_SESSION['id']) && is_numeric($_SESSION['id'])) {
        $userId = (int) $_SESSION['id'];
    }
}

// Capture request meta
$ip        = $_SERVER['REMOTE_ADDR'] ?? '';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Insert into DB using prepared statement
try {
    if (!($conn instanceof mysqli)) {
        throw new Exception('db_not_mysqli');
    }

    $stmt = $conn->prepare(
        'INSERT INTO contact_messages (user_id, name, email, phone, subject, message, status, ip, user_agent) VALUES (?, ?, ?, ?, ?, ?, "new", ?, ?)'
    );
    if (!$stmt) {
        throw new Exception('stmt_prepare_failed');
    }

    $uid = $userId !== null ? $userId : null;
    $stmt->bind_param(
        'isssssss',
        $uid,
        $name,
        $email,
        $phone,
        $subject,
        $message,
        $ip,
        $userAgent
    );

    if (!$stmt->execute()) {
        throw new Exception('stmt_execute_failed');
    }

    $insertId = $stmt->insert_id;
    $stmt->close();
} catch (Throwable $e) {
    redirect_with(['error' => 'db_error']);
}

// Attempt to email admin (best-effort)
$adminEmail = getenv('ADMIN_EMAIL');
if (!$adminEmail || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
    // Optional: set this in environment or replace default
    $adminEmail = 'admin@example.com';
}

$emailSubject = '[Contact] ' . $subject;
$emailBody  = "A new contact message was received:\n\n";
$emailBody .= "ID: #{$insertId}\n";
$emailBody .= "From: {$name} <{$email}>\n";
if ($phone) { $emailBody .= "Phone: {$phone}\n"; }
$emailBody .= "IP: {$ip}\n";
$emailBody .= "User-Agent: {$userAgent}\n\n";
$emailBody .= "Message:\n{$message}\n";

// Try to include mailer.php if present
$mailerIncluded = false;
$mailerPath = __DIR__ . '/mailer.php';
if (is_file($mailerPath)) {
    $mailerIncluded = @include_once $mailerPath;
}

$mailed = false;
try {
    if (function_exists('sendMail')) {
        // Assume sendMail($to, $subject, $body) signature
        $mailed = (bool) @sendMail($adminEmail, $emailSubject, nl2br($emailBody));
    } else {
        // Fallback to PHP mail()
        $headers = "From: no-reply@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n" .
                   "Reply-To: {$email}\r\n" .
                   "Content-Type: text/plain; charset=UTF-8\r\n";
        $mailed = @mail($adminEmail, $emailSubject, $emailBody, $headers);
    }
} catch (Throwable $e) {
    // Ignore mail errors; message is saved in DB
}

// Redirect success regardless of email result (DB insert is the source of truth)
redirect_with(['success' => 1]);

?>


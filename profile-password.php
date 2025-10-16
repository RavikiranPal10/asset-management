<?php
// profile-password.php — handle password reset
session_start();
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: profile.php#account');
  exit;
}

/* CSRF check */
if (empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf_token'] ?? '')) {
  $_SESSION['flash_error'] = 'Invalid request (CSRF).';
  header('Location: profile.php#account');
  exit;
}

/* Login guard */
$currentUserId =
  $_SESSION['auth_user_id'] ??
  $_SESSION['user_id'] ??
  ($_SESSION['user']['userid'] ?? null);

if (!$currentUserId) {
  $_SESSION['flash_error'] = 'Please login again.';
  header('Location: login.php?next=' . urlencode('profile.php#account'));
  exit;
}

$old = $_POST['old_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$cnf = $_POST['confirm_password'] ?? '';

if ($new !== $cnf || strlen($new) < 6) {
  $_SESSION['flash_error'] = 'Passwords must match and be at least 6 characters.';
  header('Location: profile.php#account');
  exit;
}

/* Fetch current hash */
$stmt = $mysqli->prepare("SELECT password_hash FROM users WHERE user_id=? LIMIT 1");
$stmt->bind_param('s', $currentUserId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row || !password_verify($old, $row['password_hash'])) {
  $_SESSION['flash_error'] = 'Current password is incorrect.';
  header('Location: profile.php#account');
  exit;
}

/* Update hash */
$newHash = password_hash($new, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("UPDATE users SET password_hash=? WHERE user_id=? LIMIT 1");
$stmt->bind_param('ss', $newHash, $currentUserId);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
  // For safety, log the user out to require re-login
  session_regenerate_id(true);
  $_SESSION['flash_success'] = 'Password updated. Please login again.';
  header('Location: logout.php');
  exit;
} else {
  $_SESSION['flash_error'] = 'Could not update password. Try again.';
  header('Location: profile.php#account');
  exit;
}

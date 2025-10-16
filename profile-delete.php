<?php
// profile-delete.php — delete account + related data
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

/* Acknowledge checkbox */
if (empty($_POST['ack'])) {
  $_SESSION['flash_error'] = 'Please confirm account deletion.';
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

/* Load email for cascading deletes where applicable */
$email = '';
$stmt = $mysqli->prepare("SELECT email FROM users WHERE user_id=? LIMIT 1");
$stmt->bind_param('s', $currentUserId);
$stmt->execute();
$res = $stmt->get_result();
if ($u = $res->fetch_assoc()) { $email = $u['email'] ?? ''; }
$stmt->close();

/* Transaction: delete related rows then user */
$mysqli->begin_transaction();
try {
  // Adjust table names/columns to your schema as needed
  $tables = [
    ['visits',           'user_id', 'email'],
    ['orders',           'user_id', 'email'],
    ['bills',            'user_id', 'email'],
    ['support_tickets',  'user_id', 'email'],
    ['complaints',       'user_id', 'email'],
    ['feedback',         'user_id', 'email'],
  ];

  foreach ($tables as [$tbl, $colUser, $colEmail]) {
    if ($stmt = $mysqli->prepare("DELETE FROM {$tbl} WHERE {$colUser}=? OR {$colEmail}=?")) {
      $stmt->bind_param('ss', $currentUserId, $email);
      $stmt->execute();
      $stmt->close();
    }
  }

  // Finally remove user
  if ($stmt = $mysqli->prepare("DELETE FROM users WHERE user_id=? LIMIT 1")) {
    $stmt->bind_param('s', $currentUserId);
    $stmt->execute();
    $stmt->close();
  }

  $mysqli->commit();

  // Destroy session
  $_SESSION = [];
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
      $params["path"], $params["domain"],
      $params["secure"], $params["httponly"]
    );
  }
  session_destroy();

  // Redirect home
  header('Location: index.php');
  exit;

} catch (Throwable $e) {
  $mysqli->rollback();
  $_SESSION['flash_error'] = 'Could not delete account. Please try again later.';
  header('Location: profile.php#account');
  exit;
}

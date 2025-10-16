<?php
// login-process.php — verifies credentials, sets session, and redirects (supports ?next=...)
session_start();
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $_SESSION['flash_error'] = 'Invalid request.';
  header('Location: login.php');
  exit;
}

if (!isset($_POST['userid'], $_POST['password'])) {
  $_SESSION['flash_error'] = 'Please enter User ID and Password.';
  header('Location: login.php');
  exit;
}

$user_id = trim((string)$_POST['userid']);
$password = (string)$_POST['password'];

// Optional: capture redirect target
$next = isset($_POST['next']) ? trim((string)$_POST['next'])
      : (isset($_GET['next']) ? trim((string)$_GET['next']) : '');

// Look up user
$stmt = $mysqli->prepare("SELECT id, user_id, username, password_hash FROM users WHERE user_id = ? LIMIT 1");
if (!$stmt) {
  $_SESSION['flash_error'] = 'Unexpected error. Please try again.';
  header('Location: login.php');
  exit;
}

$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if ($user && password_verify($password, $user['password_hash'])) {
  // Good login — regenerate session ID to prevent fixation
  session_regenerate_id(true);

  // Store canonical session keys
  $_SESSION['user_id']   = $user['user_id'];
  $_SESSION['user_name'] = $user['username'];

  // Keep your legacy keys too (if other pages rely on them)
  $_SESSION['auth_user_id'] = $user['user_id'];
  $_SESSION['auth_username'] = $user['username'];

  $_SESSION['flash_success'] = 'Welcome back, ' . htmlspecialchars($user['username']) . '!';

  // -------- Redirect handling with allow-list --------
  $redirect = 'dashboard.php';
  if ($next !== '') {
    // Allow only local relative paths and only certain endpoints
    $allowed = [
      'dashboard.php',
      'product.php',
      'visits.php',
      'visit.php',
      'checkout.php',
      'profile.php',
      'viewed.php',
      'shopping.php'
    ];

    // Ensure $next is relative (no host)
    $host = parse_url($next, PHP_URL_HOST);
    if ($host === null) {
      // Base file without query
      $base = strtok($next, '?');
      if (in_array($base, $allowed, true)) {
        $redirect = $next; // keep its query string (e.g., item=P001)
      }
    }
  }

  header('Location: ' . $redirect);
  exit;

} else {
  // Bad login
  $_SESSION['flash_error'] = 'Invalid User ID or Password.';
  header('Location: login.php' . ($next ? ('?next=' . urlencode($next)) : ''));
  exit;
}

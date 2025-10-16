<?php
session_start();
require __DIR__ . '/db.php';

function required($key) {
  return isset($_POST[$key]) && trim($_POST[$key]) !== '';
}

if (!required('userid') || !required('username') || !required('password') || !required('email')) {
  $_SESSION['flash_error'] = 'Please fill all required fields.';
  header('Location: new-user.php');
  exit;
}

$user_id  = trim($_POST['userid']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$sex      = $_POST['sex'] ?? null;
$dob      = $_POST['dob'] ?? null;
$address  = $_POST['address'] ?? null;
$email    = trim($_POST['email']);
$contact  = $_POST['contact'] ?? null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['flash_error'] = 'Invalid email address.';
  header('Location: new-user.php');
  exit;
}

if (strlen($password) < 6) {
  $_SESSION['flash_error'] = 'Password must be at least 6 characters.';
  header('Location: new-user.php');
  exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$dup = $mysqli->prepare("SELECT 1 FROM users WHERE user_id=? OR email=? LIMIT 1");
$dup->bind_param('ss', $user_id, $email);
$dup->execute();
$dup->store_result();
if ($dup->num_rows > 0) {
  $_SESSION['flash_error'] = 'User ID or Email already exists.';
  $dup->close();
  header('Location: new-user.php');
  exit;
}
$dup->close();

$stmt = $mysqli->prepare("
  INSERT INTO users (user_id, username, password_hash, sex, dob, address, email, contact)
  VALUES (?,?,?,?,?,?,?,?)
");
$stmt->bind_param('ssssssss', $user_id, $username, $hash, $sex, $dob, $address, $email, $contact);

if ($stmt->execute()) {
  $_SESSION['flash_success'] = 'Registration successful! You can now log in.';
  header('Location: login.php');
  exit;
} else {
  $_SESSION['flash_error'] = 'Database error: ' . $stmt->error;
  header('Location: new-user.php');
  exit;
}
?>

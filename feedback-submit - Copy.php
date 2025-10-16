<?php
session_start();
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/mailer.php';

function uniq_code(mysqli $c, string $prefix): string {
  $rand = function($a,$b){ return function_exists('random_int') ? random_int($a,$b) : mt_rand($a,$b); };
  $y = date('Y');
  do {
    $n = $rand(100000, 999999);
    $code = "{$prefix}-{$y}-{$n}";
    $stmt = $c->prepare("SELECT id FROM feedbacks WHERE feedback_no=?");
    $stmt->bind_param('s', $code);
    $stmt->execute(); $stmt->store_result();
    $exists = $stmt->num_rows > 0; $stmt->close();
  } while ($exists);
  return $code;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: feedback.php'); exit; }

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$rating  = (int)($_POST['rating'] ?? 0);
$message = trim($_POST['message'] ?? '');

$errors=[];
if($name==='') $errors[]='Name is required.';
if($email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Valid email is required.';
if($contact==='') $errors[]='Contact is required.';
if($rating < 1 || $rating > 5) $errors[]='Rating must be 1–5.';
if(strlen($message) < 20) $errors[]='Feedback message must be at least 20 characters.';

if($errors){ $_SESSION['flash_error']=implode(' ',$errors); header('Location: feedback.php'); exit; }

$feedback_no = uniq_code($conn,'FBK');

$sql="INSERT INTO feedbacks (feedback_no,name,email,contact_no,rating,message) VALUES (?,?,?,?,?,?)";
$stmt=$conn->prepare($sql);
$stmt->bind_param('ssssss',$feedback_no,$name,$email,$contact,$rating,$message);
$ok=$stmt->execute(); $stmt->close();

if(!$ok){ $_SESSION['flash_error']='Database error while saving feedback.'; header('Location: feedback.php'); exit; }

$subject="Thanks for your feedback ({$feedback_no}) — Asset Management";
$body="
  <p>Dear {$name},</p>
  <p>Thank you for sharing feedback. We’ve recorded it and will use it to improve our service.</p>
  <p><strong>Reference:</strong> {$feedback_no}<br>
     <strong>Rating:</strong> {$rating} / 5</p>
  <p><strong>Message:</strong><br>".nl2br(htmlentities($message))."</p>
  <p>— Asset Management Team</p>
";
$email_ok = send_email($email,$name,$subject,$body);

$_SESSION['flash_success']="Thank you! Your feedback was submitted with reference <strong>{$feedback_no}</strong>."
  .($email_ok ? " A copy was emailed to {$email}." : " Email could not be sent (SMTP not configured).");

header('Location: feedback.php'); exit;

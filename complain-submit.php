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
    $stmt = $c->prepare("SELECT id FROM complaints WHERE complaint_no=?");
    $stmt->bind_param('s', $code);
    $stmt->execute(); $stmt->store_result();
    $exists = $stmt->num_rows > 0; $stmt->close();
  } while ($exists);
  return $code;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: complain.php'); exit; }

$name     = trim($_POST['name'] ?? '');
$date     = trim($_POST['date'] ?? '');
$contact  = trim($_POST['contact'] ?? '');
$email    = trim($_POST['email'] ?? '');
$category = trim($_POST['category'] ?? '');
$priority = trim($_POST['priority'] ?? 'Normal');
$details  = trim($_POST['details'] ?? '');

$errors=[];
if($name==='') $errors[]='Name is required.';
if($date==='') $errors[]='Date is required.';
if($contact==='') $errors[]='Contact is required.';
if($email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Valid email is required.';
if($category==='') $errors[]='Category is required.';
if(!in_array($priority,['Normal','High','Urgent'])) $errors[]='Invalid priority.';
if(strlen($details)<20) $errors[]='Details must be at least 20 characters.';

if($errors){ $_SESSION['flash_error']=implode(' ',$errors); header('Location: complain.php'); exit; }

$complaint_no = uniq_code($conn,'CMP');

$sql="INSERT INTO complaints (complaint_no,name,date,contact_no,email,category,priority,details)
      VALUES (?,?,?,?,?,?,?,?)";
$stmt=$conn->prepare($sql);
$stmt->bind_param('ssssssss',$complaint_no,$name,$date,$contact,$email,$category,$priority,$details);
$ok=$stmt->execute(); $stmt->close();

if(!$ok){ $_SESSION['flash_error']='Database error while saving complaint.'; header('Location: complain.php'); exit; }

$subject="Complaint {$complaint_no} received — Asset Management";
$body="
  <p>Dear {$name},</p>
  <p>We’ve received your complaint and opened a case.</p>
  <p><strong>Complaint No:</strong> {$complaint_no}<br>
     <strong>Category:</strong> {$category}<br>
     <strong>Priority:</strong> {$priority}<br>
     <strong>Date:</strong> {$date}</p>
  <p><strong>Details:</strong><br>".nl2br(htmlentities($details))."</p>
  <p>We’ll keep you informed by email. — Asset Management Team</p>
";
$email_ok = send_email($email,$name,$subject,$body);

$_SESSION['flash_success']="Complaint submitted successfully. Your number is <strong>{$complaint_no}</strong>."
  .($email_ok ? " A confirmation email was sent to {$email}." : " Email could not be sent (SMTP not configured).");

header('Location: complain.php'); exit;

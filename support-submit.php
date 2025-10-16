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
    $stmt = $c->prepare("SELECT id FROM support_tickets WHERE ticket_no=?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
  } while ($exists);
  return $code;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: support.php'); exit; }

$support_type = trim($_POST['support_type'] ?? '');
$priority     = trim($_POST['priority'] ?? 'Normal');
$date         = trim($_POST['date'] ?? '');
$contact      = trim($_POST['contact'] ?? '');
$email        = trim($_POST['email'] ?? '');
$ticket_ref   = trim($_POST['ticket_ref'] ?? '');
$message      = trim($_POST['message'] ?? '');
$user_id      = $_SESSION['user'] ?? null;

$errors = [];
if ($support_type==='') $errors[]='Support type is required.';
if (!in_array($priority,['Normal','High','Urgent'])) $errors[]='Invalid priority.';
if ($date==='') $errors[]='Date is required.';
if ($contact==='') $errors[]='Contact number is required.';
if ($email==='' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Valid email is required.';
if (strlen($message)<20) $errors[]='Message must be at least 20 characters.';

if ($errors){ $_SESSION['flash_error']=implode(' ',$errors); header('Location: support.php'); exit; }

$ticket_no = uniq_code($conn, 'SUP');

$sql = "INSERT INTO support_tickets
(ticket_no,user_id,support_type,priority,date_of_issue,contact_no,email,ticket_ref,message)
VALUES (?,?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssss',$ticket_no,$user_id,$support_type,$priority,$date,$contact,$email,$ticket_ref,$message);
$ok = $stmt->execute();
$stmt->close();

if (!$ok){ $_SESSION['flash_error']='Database error while creating ticket.'; header('Location: support.php'); exit; }

$subject = "Ticket {$ticket_no} received — Asset Management";
$body = "
  <p>Dear Customer,</p>
  <p>Your support ticket has been created. We will get back to you shortly.</p>
  <p><strong>Ticket No:</strong> {$ticket_no}<br>
     <strong>Type:</strong> {$support_type}<br>
     <strong>Priority:</strong> {$priority}<br>
     <strong>Date:</strong> {$date}</p>
  <p><strong>Message:</strong><br>".nl2br(htmlentities($message))."</p>
  <p>— Asset Management Team</p>
";
$email_ok = send_email($email, '', $subject, $body);

$_SESSION['flash_success'] = "Ticket created successfully. Your ticket number is <strong>{$ticket_no}</strong>." .
  ($email_ok ? " A confirmation email was sent to {$email}." : " Email could not be sent (SMTP not configured).");

header('Location: support.php'); exit;

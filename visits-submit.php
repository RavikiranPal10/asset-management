<?php
// visits-submit.php — stores visit requests and (optionally) emails an acknowledgement
session_start();
require __DIR__ . '/db.php'; // $mysqli connection

function generate_ref() {
  return 'VIS-' . date('Y') . '-' . mt_rand(100000, 999999);
}

try {
  // Basic validation
  $user_id        = trim($_POST['user_id'] ?? '');
  $item_id        = trim($_POST['item_id'] ?? '');
  $property_label = trim($_POST['property_label'] ?? '');
  $city           = trim($_POST['city'] ?? '');
  $preferred_date = trim($_POST['preferred_date'] ?? '');
  $time_slot      = trim($_POST['time_slot'] ?? '');
  $contact        = trim($_POST['contact'] ?? '');
  $email          = trim($_POST['email'] ?? '');
  $notes          = trim($_POST['notes'] ?? '');

  if ($user_id === '' || $city === '' || $preferred_date === '' || $time_slot === '' || $contact === '' || $email === '') {
    throw new Exception('Please fill all required fields.');
  }

  $ref = generate_ref();
  $status = 'Pending';

  // Create table if not exists (safe for local dev)
  $mysqli->query("
    CREATE TABLE IF NOT EXISTS visit_requests (
      id INT AUTO_INCREMENT PRIMARY KEY,
      ref_no VARCHAR(30) NOT NULL,
      user_id VARCHAR(100) NOT NULL,
      item_id VARCHAR(50) NULL,
      property_label VARCHAR(255) NULL,
      city VARCHAR(50) NOT NULL,
      preferred_date DATE NOT NULL,
      time_slot VARCHAR(30) NOT NULL,
      contact VARCHAR(30) NOT NULL,
      email VARCHAR(120) NOT NULL,
      notes TEXT NULL,
      status VARCHAR(20) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

  $stmt = $mysqli->prepare("
    INSERT INTO visit_requests
      (ref_no, user_id, item_id, property_label, city, preferred_date, time_slot, contact, email, notes, status)
    VALUES (?,?,?,?,?,?,?,?,?,?,?)
  ");
  $stmt->bind_param(
    'sssssssssss',
    $ref, $user_id, $item_id, $property_label, $city, $preferred_date, $time_slot, $contact, $email, $notes, $status
  );
  $stmt->execute();

  // --- OPTIONAL EMAIL (same approach as other pages; fine if SMTP not configured) ---
  $subject = "Visit Request Received ($ref)";
  $body = "Dear User,\n\nWe’ve received your visit request.\n"
        . "Reference: $ref\n"
        . "Property: " . ($property_label ?: $item_id) . "\n"
        . "City/Slot: $city, $preferred_date, $time_slot\n\n"
        . "We’ll confirm your slot shortly.\n\nThanks,\nSupport Team";

  @mail($email, $subject, $body, "From: no-reply@example.com\r\n");

  $_SESSION['flash_ok'] = "Thank you! Your visit request was submitted with reference <strong>$ref</strong>.";
} catch (Throwable $e) {
  $_SESSION['flash_err'] = "Could not submit request: " . htmlspecialchars($e->getMessage());
}

// Return to the form (preserve item param if any)
$back = 'visits.php';
if (!empty($_POST['item_id'])) { $back .= '?item=' . urlencode($_POST['item_id']); }
header("Location: $back");
exit;

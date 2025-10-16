<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}
$item = $_GET['item'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html>
<head><title>Visit Scheduled</title></head>
<body style="font-family: Bookman Old Style;">
  <h2>Visit Confirmation</h2>
  <p>Thank you, <?php echo htmlspecialchars($_SESSION['user']); ?>! We’ve scheduled your visit for property <strong><?php echo htmlspecialchars($item); ?></strong>.</p>
  <a href="product.php">Back to Properties</a>
</body>
</html>

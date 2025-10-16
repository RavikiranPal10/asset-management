<?php
/*  USER‑LOGIN MODULE
 *  ————————————————
 *  • Authenticates registered customers
 *  • On success: starts session and sends user to dashboard.php
 *  • On failure: reloads with error message
 *  • References: Section “Source Code → page 56, lines 12‑83”
 *----------------------------------------------------------*/

session_start();
require_once 'dbconnect.php';   // (same dbconnect.php you already added)

/* ---------- Handle form POST ---------- */
if (isset($_POST['login'])) {
    // trim & sanitise
    $email    = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));

    // basic validation
    if ($email === '' || $password === '') {
        $err = 'Please enter both e‑mail and password.';
    } else {
        // hash the incoming password exactly as in new-user.php
        $hash = md5($password);

        // look up the user
        $sql  = "SELECT * FROM customer WHERE c_email = '$email' AND c_pass = '$hash'";
        $res  = mysqli_query($con, $sql);

        if (mysqli_num_rows($res) === 1) {
            // success → store minimal info in session
            $row = mysqli_fetch_assoc($res);
            $_SESSION['cid']   = $row['c_id'];
            $_SESSION['cname'] = $row['c_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $err = 'Invalid credentials — try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Login | Asset Management System</title>
  <link rel="stylesheet" href="assets/css/style.css"><!-- (same master CSS) -->
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="container">
  <h2 class="page-title">Customer Login</h2>

  <?php if (isset($err)): ?>
      <div class="alert alert-error"><?= $err; ?></div>
  <?php endif; ?>

  <form method="post" class="form-card">
      <label>E‑mail ID</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <button type="submit" name="login" class="btn-primary">Login</button>
      <p class="hint">New user? <a href="new-user.php">Register here</a>.</p>
  </form>
</div>

<?php include 'partials/footer.php'; ?>
</body>
</html>

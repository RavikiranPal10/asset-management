<?php
// nav.php — shared navigation with built-in styles and login-aware items
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['auth_user_id']);
?>
<style>
  /* Self-contained nav styles so you don't have to edit each page */
  .nav{background:#1165AE;padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;
       box-shadow:0 4px 10px rgba(0,0,0,.06);font:16px/1.6 "Bookman Old Style","Times New Roman",Times,serif;}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;text-decoration:none;
         border-radius:10px;font-weight:bold;transition:all .18s ease}
  .nav a:hover{background:#ffd93b;color:#000;transform:translateY(-2px)}
</style>

<nav class="nav" aria-label="Main">
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="aboutus.php">About Us</a></li>

    <?php if ($loggedIn): ?>
      <!-- Logged-in items -->
      <li><a href="product.php">Properties</a></li>
      <li><a href="viewed.php">Viewed Properties</a></li>
      <li><a href="visits.php">Schedule Visits</a></li>
      <li><a href="checkout.php">Checkout</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="support.php">Support</a></li>
      <li><a href="complain.php">Complain</a></li>
      <li><a href="feedback.php">Feedback</a></li>
      <li><a href="contact-us.php">Contact Us</a></li>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <!-- Public items (unchanged) -->
      <li><a href="new-user.php">New User</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="product.php">Properties</a></li>
      <li><a href="support.php">Support</a></li>
      <li><a href="complain.php">Complain</a></li>
      <li><a href="feedback.php">Feedback</a></li>
      <li><a href="contact-us.php">Contact Us</a></li>
    <?php endif; ?>
  </ul>
</nav>

</nav>

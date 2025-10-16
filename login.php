<?php
// login.php — full page with Bookman Old Style, redirect-aware login
session_start(); // must be at top before any HTML output
$next = isset($_GET['next']) ? $_GET['next'] : 'index.php';
$item = isset($_GET['item']) ? $_GET['item'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login | Property / Asset Management System</title>

<style>
  :root{
    --bg:#f6f8fb;--ink:#0f1b2d;--muted:#54657e;
    --brand:#1165AE;--brand-2:#0b4f87;--accent:#ffd93b;
    --surface:#ffffff;--shadow:0 2px 10px rgba(0,0,0,.08),0 8px 30px rgba(0,0,0,.06);
    --radius:14px;--maxw:1000px;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink)}
  body{font:16px/1.6 "Bookman Old Style","Times New Roman",Times,serif;}
  img{max-width:100%;height:auto;display:block}

  .wrap{max-width:var(--maxw);margin:0 auto;background:var(--surface);
        box-shadow:var(--shadow);border-radius:0 0 var(--radius) var(--radius);overflow:hidden;}

  /* HEADER */
  .hero{
    background:linear-gradient(135deg,#009dbe 0%, #44c3e6 60%, #8ed9ee 100%);
    color:#fff;text-align:center;padding:42px 18px 50px;
  }
  .hero h1{margin:0;font-size:32px;font-weight:bold;letter-spacing:.3px;}
  .hero p{margin:10px auto 0;font-size:16px;max-width:560px;color:#f9f9f9;}

  /* NAVIGATION */
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06);}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px;}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:all .18s ease;}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px);}

  /* LOGIN FORM */
  .login-section{display:grid;grid-template-columns:1fr 1fr;gap:24px;padding:24px 18px 40px;align-items:center;}
  .card{background:#fff;border:1px solid #e9edf3;border-radius:var(--radius);box-shadow:var(--shadow);padding:22px;}
  .card h2{text-align:center;font-size:22px;color:var(--brand-2);margin-bottom:20px;}
  label{display:block;font-weight:bold;margin-top:12px;}
  input{width:100%;padding:10px;margin-top:6px;border:1px solid #ccd5e0;border-radius:8px;font-family:"Bookman Old Style","Times New Roman",Times,serif;}
  .btns{text-align:center;margin-top:20px;}
  button{background:#1165AE;border:none;color:#fff;padding:10px 20px;border-radius:8px;font-weight:bold;cursor:pointer;font-family:"Bookman Old Style","Times New Roman",Times,serif;}
  button:hover{background:#0b4f87;}
  .photo{border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:#fff;padding:10px;}
  .photo img{border-radius:var(--radius);}
  .error{color:#b00020;font-weight:bold;text-align:center;margin-bottom:8px;}
  .success{color:#0a7b24;font-weight:bold;text-align:center;margin-bottom:8px;}

  /* FOOTER */
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius);}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius);}
  footer small{display:block;color:#6c7f99;}

  @media(max-width:780px){.login-section{grid-template-columns:1fr;}}
</style>
</head>

<body>
<div class="wrap">
  <!-- Header -->
  <header class="hero">
    <h1>User Login</h1>
    <p>Access your account to manage your properties and payments.</p>
  </header>

  <!-- Navigation -->
<?php
// nav.php — shared navigation (Bookman), shows different items when logged-in
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user']['userid']);
?>
<nav class="nav">
  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="aboutus.php">About Us</a></li>

    <?php if ($loggedIn): ?>
      <!-- Logged-in additions -->
      <li><a href="product.php">Product</a></li>
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
      <!-- Public menu -->
      <li><a href="new-user.php">New User</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="product.php">Product</a></li>
      <li><a href="support.php">Support</a></li>
      <li><a href="complain.php">Complain</a></li>
      <li><a href="feedback.php">Feedback</a></li>
      <li><a href="contact-us.php">Contact Us</a></li>
    <?php endif; ?>
  </ul>
</nav>


  <!-- Login Section -->
  <section class="login-section">
    <div class="photo">
      <img src="/asset-management/user-login-image.jpg" alt="Login Illustration" />
    </div>

    <div class="card">
      <h2>Login to Your Account</h2>

      <?php
        if(!empty($_SESSION['flash_error'])){
          echo "<div class='error'>".$_SESSION['flash_error']."</div>";
          unset($_SESSION['flash_error']);
        }
        if(!empty($_SESSION['flash_success'])){
          echo "<div class='success'>".$_SESSION['flash_success']."</div>";
          unset($_SESSION['flash_success']);
        }
      ?>

      <!-- ✅ Updated form to carry forward ?next= and ?item= parameters -->
      <form method="post" action="login-process.php?next=<?php echo urlencode($next); ?>&item=<?php echo urlencode($item); ?>" autocomplete="off">
        <label for="userid">User ID</label>
        <input type="text" name="userid" id="userid" required />

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required />

        <div class="btns">
          <button type="submit">Login</button>
          <button type="reset" style="background:#ccc;color:#000;margin-left:6px;">Reset</button>
        </div>
      </form>

      <p style="text-align:center;margin-top:12px;">
        Don’t have an account?
        <a href="new-user.php" style="color:#1165AE;text-decoration:none;font-weight:bold;">Register Here</a>
      </p>
    </div>
  </section>

  <!-- Footer -->
  <img class="footer-img" src="/asset-management/footer.jpg" alt="Footer graphic">
  <footer>
    <div><strong>All Rights Reserved | Copyright Protected</strong></div>
    <small>
      Developed by <strong>Ravikiran Pal</strong> | Enrollment No: <em>2003727907</em> |
      Under the guidance of <em>Mrs. Madhuri Jha</em> | IGNOU BCSP-064
    </small>
  </footer>
</div>
</body>
</html>

<?php /* new-user.php — New User Registration (Bookman Old Style, reduced image) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>New User Registration | Property / Asset Management System</title>

<style>
  :root{
    --bg:#f6f8fb;
    --ink:#0f1b2d;
    --muted:#54657e;
    --brand:#1165AE;
    --brand-2:#0b4f87;
    --accent:#ffd93b;
    --surface:#ffffff;
    --shadow:0 2px 10px rgba(0,0,0,.08),0 8px 30px rgba(0,0,0,.06);
    --radius:14px;
    --maxw:1000px;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);}
  body{font:16px/1.6 "Bookman Old Style","Times New Roman",serif;}

  img{max-width:100%;height:auto;display:block}
  .wrap{max-width:var(--maxw);margin:0 auto;background:var(--surface);
        box-shadow:var(--shadow);border-radius:0 0 var(--radius) var(--radius);overflow:hidden;}

  /* HEADER */
  .hero{
    background:linear-gradient(135deg,#00a8cc 0%,#48cae4 60%,#90e0ef 100%);
    color:#fff;text-align:center;padding:42px 18px 50px;
  }
  .hero h1{margin:0;font-size:32px;font-weight:bold;letter-spacing:.3px;}
  .hero p{margin:10px auto 0;font-size:18px;max-width:580px;}

  /* NAV */
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06);}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px;}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:all .18s ease;}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px);}

  /* PAGE TITLE */
  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center;}
  .page-title h2{margin:0;font-size:22px;font-weight:bold;}

  /* FORM SECTION */
  .register-grid{display:grid;grid-template-columns:0.7fr 1.3fr;gap:24px;padding:24px 18px;align-items:center;}
  .photo{border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);max-width:85%;margin:auto;}
  .photo img{width:100%;height:auto;object-fit:cover;border-radius:var(--radius);}
  .form-card{background:#fff;border:1px solid #e9edf3;border-radius:var(--radius);
             padding:18px;box-shadow:var(--shadow);}
  .form-card label{display:block;font-weight:bold;margin-bottom:4px;margin-top:10px;}
  .form-card input, .form-card select, .form-card textarea{
    width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;
    font-family:"Bookman Old Style","Times New Roman",serif;font-size:15px;
  }
  .form-card .actions{margin-top:18px;text-align:center;}
  .form-card button{
    background:var(--brand);color:#fff;border:none;padding:10px 24px;
    border-radius:8px;font-weight:bold;font-family:"Bookman Old Style","Times New Roman",serif;
    cursor:pointer;transition:all .2s ease;
  }
  .form-card button:hover{background:var(--brand-2);transform:translateY(-2px);}
  .form-card button[type="reset"]{background:#ccc;color:#000;margin-left:6px;}

  /* FOOTER */
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius);}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius);}
  footer small{display:block;color:#6c7f99;}

  @media(max-width:780px){
    .register-grid{grid-template-columns:1fr;}
    .photo{max-width:100%;}
  }
</style>
</head>

<body>
<div class="wrap">
  <!-- HEADER -->
  <header class="hero">
    <h1>New User Registration</h1>
    <p>Join us and shop, schedule visits and manage your properties efficiently.</p>
  </header>

  <!-- NAVIGATION -->
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


  <!-- TITLE -->
  <div class="page-title"><h2>Register a New User Account</h2></div>

  <!-- FORM -->
  <section class="register-grid">
    <div class="photo">
      <img src="/asset-management/new-user-image.jpg" alt="User registration banner">
    </div>

    <div class="form-card">
      <form action="save-user.php" method="post">
        <label for="userid">User ID</label>
        <input type="text" id="userid" name="userid" required>

        <label for="username">User Name</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="sex">Sex</label>
        <select id="sex" name="sex">
          <option value="">--Select--</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>

        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" required>

        <label for="address">Address</label>
        <textarea id="address" name="address" rows="2" required></textarea>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="contact">Contact No</label>
        <input type="text" id="contact" name="contact" maxlength="10" required>

        <div class="actions">
          <button type="submit">Save</button>
          <button type="reset">Reset</button>
        </div>
      </form>
    </div>
  </section>

  <!-- FOOTER -->
  <img class="footer-img" src="/asset-management/footer.jpg" alt="Footer graphic">
  <footer>
    <div><strong>All Rights Reserved | Copyright Protected</strong></div>
    <small>Developed by <strong>Ravikiran Pal</strong> | Enrollment No: <em>2003727907</em> | Under the guidance of <em>Mrs. Madhuri Jha</em> | IGNOU BCSP-064</small>
  </footer>
</div>
</body>
</html>

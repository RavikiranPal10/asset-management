<?php
/* index.php — Bookman Old Style + login-aware nav include */
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['auth_user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Property / Asset Management System</title>

<style>
  :root{
    --bg:#f6f8fb;
    --ink:#0f1b2d;
    --muted:#3a3a3a;
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
  body{font-family:"Bookman Old Style","Times New Roman",serif;font-size:16px;line-height:1.6;}

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

  /* (Keeping your nav styles here so appearance stays identical; nav.php also brings its own, which is fine) */
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06);}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px;}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:all .18s ease;}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px);}

  /* SECTION TITLE */
  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center;}
  .page-title h2{margin:0;font-size:22px;font-weight:bold;}

  /* INTRO */
  .intro{display:grid;grid-template-columns:1fr 1.2fr;gap:24px;padding:22px 18px;}
  .card-photo{border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:#fff;min-height:260px;}
  .card-photo img{width:100%;height:100%;object-fit:cover;}
  .intro .copy .blurb{border-radius:var(--radius);box-shadow:var(--shadow);padding:14px;background:#fff;}
  .intro p{margin:0 0 10px;color:var(--muted);}

  /* CTA */
  .cta-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin:8px 18px 22px;}
  .btn{text-align:center;text-decoration:none;font-weight:bold;padding:12px;border-radius:10px;
       box-shadow:var(--shadow);transition:all .18s ease;cursor:pointer;}
  .btn-primary{background:var(--brand);color:#fff;}
  .btn-primary:hover{background:var(--brand-2);}
  .btn-outline{background:#fff;border:2px solid var(--brand);color:var(--brand);}
  .btn-outline:hover{background:var(--brand);color:#fff;}

  /* FOOTER */
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius);}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius);}
  footer small{display:block;color:#6c7f99;}

  @media(max-width:780px){
    .intro{grid-template-columns:1fr;}
    .cta-bar{grid-template-columns:1fr 1fr;}
    .hero h1{font-size:26px;}
  }
</style>
</head>

<body>
<div class="wrap">
  <header class="hero">
    <h1>Your Trusted Real Estate Partner</h1>
    <p>Buy, sell &amp; manage properties with confidence.</p>
  </header>

  <!-- Shared, login-aware navigation -->
  <?php require __DIR__ . '/nav.php'; ?>

  <div class="page-title"><h2>Home</h2></div>

  <section class="intro">
    <div class="card-photo"><img src="/asset-management/banner-about.jpg?v=2" alt="Property promotional banner"></div>
    <div class="copy">
      <div class="card-photo" style="margin-bottom:12px;"><img src="/asset-management/img1.jpg?v=1" alt="Residential property exterior"></div>
      <div class="blurb">
        <p>The internet has transformed real estate. Our <strong>Property / Asset Management System</strong> helps you register assets, view listings, raise complaints, submit feedback, and manage billing — all in one place.</p>
        <p>Property companies can list flats and plots with clarity, while users browse and decide confidently. Administrators get streamlined tools for faster service and reliable records.</p>
      </div>
    </div>
  </section>

  <!-- CTA: changes depending on login state -->
  <nav class="cta-bar" role="navigation" aria-label="Quick actions">
    <?php if (!$loggedIn): ?>
      <a class="btn btn-primary" href="new-user.php">➕ New User Registration</a>
      <a class="btn btn-primary" href="login.php">🔐 Login</a>
      <a class="btn btn-outline" href="product.php">🏠 View Properties</a>
      <a class="btn btn-outline" href="contact-us.php">📞 Contact Us</a>
    <?php else: ?>
      <a class="btn btn-primary" href="product.php">🏠 View Properties</a>
      <a class="btn btn-primary" href="viewed.php">🕘 Viewed Properties</a>
      <a class="btn btn-outline" href="visits.php">📅 Schedule Visits</a>
      <a class="btn btn-outline" href="profile.php">👤 Profile</a>
    <?php endif; ?>
  </nav>

  
  <footer>
    <div><strong>All Rights Reserved | Copyright Protected</strong></div>
    <small>Developed by <strong>Ravikiran Pal</strong> | Enrollment No: <em>2003727907</em> | Under the guidance of <em>Mrs. Madhuri Jha</em> | IGNOU BCSP-064</small>
  </footer>
</div>
</body>
</html>

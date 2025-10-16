<?php
// contact-us.php — unified Bookman style + header/footer + modern layout
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Contact Us | Property / Asset Management System</title>

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

  /* NAV (no Pay & Shopping, no Bill) */
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06);}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px;}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:all .18s ease;}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px);}

  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center}
  .page-title h2{margin:0;font-size:22px;font-weight:bold;}

  /* CONTENT */
  .content{display:grid;grid-template-columns:1fr 1.3fr;gap:24px;padding:28px 18px 40px;align-items:center;}
  .photo{border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);}
  .photo img{border-radius:var(--radius);}
  .info h3{margin-top:0;font-size:22px;color:var(--brand-2);}
  .info p{font-size:17px;margin:6px 0;color:#0f1b2d;}
  .info strong{color:var(--brand-2);}
  .info ul{list-style:none;padding:0;margin:12px 0;}
  .info li{margin-bottom:6px;}
  .info a{text-decoration:none;color:var(--brand-2);font-weight:bold;}
  .info a:hover{color:#0b4f87;text-decoration:underline;}

  /* FOOTER */
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius);}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius);}
  footer small{display:block;color:#6c7f99;}

  @media(max-width:820px){
    .content{grid-template-columns:1fr;}
  }
</style>
</head>

<body>
<div class="wrap">

  <!-- Header -->
  <header class="hero">
    <h1>Contact us.</h1>
    <p>Reach out to us at the below details.</p>
  </header>

  <!-- Navigation -->
<?php
// nav.php — shared navigation (Bookman), shows different items when logged-in
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user']['userid']);
?>
<?php require __DIR__ . '/nav.php'; ?>


  <!-- Page Title -->
  <div class="page-title"><h2>Contact Us</h2></div>

  <!-- Contact Section -->
  <section class="content">
    <div class="photo">
      <img src="/asset-management/contact-support.jpg" alt="Contact support team">
    </div>
    <div class="info">
      <h3>We’d Love to Hear from You!</h3>
      <p>For any query, feedback, or property assistance, feel free to contact us.</p>
      <ul>
        <li><strong>Toll-Free:</strong> 1800 180 190</li>
        <li><strong>Email:</strong> <a href="mailto:support@propertyassist.com">support@propertyassist.com</a></li>
        <li><strong>Working Hours:</strong> Mon – Sat, 9:00 AM – 6:00 PM</li>
        <li><strong>Office Address:</strong> Lodha Splendora, Bhayandarpada, Thane (W), Maharashtra - 400615</li>
      </ul>
      <p>Our team is dedicated to resolving your issues promptly and ensuring a seamless property experience.</p>
      <p><strong>Asset Management System</strong></p>
    </div>
  </section>

  <!-- Footer -->

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

<?php
// aboutus.php — Public-facing About page (Bookman Old Style) using shared nav.php
session_start(); // start session BEFORE any output
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>About Us | Property / Asset Management System</title>

<style>
  :root{
    --bg:#f6f8fb;
    --ink:#0f1b2d;
    --muted:#54657e;
    --brand:#1165AE;       /* nav blue */
    --brand-2:#0b4f87;     /* nav hover blue */
    --accent:#ffd93b;      /* hover accent */
    --surface:#ffffff;
    --shadow:0 2px 10px rgba(0,0,0,.08),0 8px 30px rgba(0,0,0,.06);
    --radius:14px;
    --maxw:1000px;
  }

  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);}
  body{font:16px/1.6 "Bookman Old Style","Times New Roman",Times,serif;}
  img{max-width:100%;height:auto;display:block;}

  .wrap{max-width:var(--maxw);margin:0 auto;background:var(--surface);
        box-shadow:var(--shadow);border-radius:0 0 var(--radius) var(--radius);overflow:hidden;}

  /* ---------- HEADER ---------- */
  .hero{
    background:linear-gradient(135deg,#009dbe 0%, #44c3e6 60%, #8ed9ee 100%);
    color:#fff;text-align:center;padding:42px 18px 50px;
  }
  .hero h1{margin:0;font-size:32px;font-weight:bold;letter-spacing:.3px;}
  .hero p{margin:10px auto 0;font-size:16px;max-width:560px;color:#f9f9f9;opacity:.95;line-height:1.5;}

  /* ---------- NAV (styles only; markup comes from nav.php) ---------- */
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06);}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px;}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:all .18s ease;}
  .nav a:hover,.nav a:focus{background:var(--accent);color:#000;transform:translateY(-2px);box-shadow:0 3px 6px rgba(0,0,0,.15);}

  /* ---------- PAGE TITLE ---------- */
  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center;}
  .page-title h2{margin:0;font-size:22px;font-weight:bold;color:#112;}

  /* ---------- ABOUT CONTENT ---------- */
  .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;padding:24px 18px;}
  .card{background:#fff;border:1px solid #e9edf3;border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
  .photo{border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:#fff;min-height:260px}
  .photo img{width:100%;height:100%;object-fit:cover}
  h3{margin:0 0 8px;font-size:18px;color:var(--brand-2)}
  p{margin:10px 0;color:var(--muted)}
  ul{margin:8px 0 0 20px}
  li{margin:6px 0}

  /* ---------- HIGHLIGHTS ---------- */
  .highlights{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin:4px 18px 22px}
  .pill{background:#fff;border:1px solid #e9edf3;border-radius:12px;box-shadow:var(--shadow);padding:12px;text-align:center;font-weight:bold}

  /* ---------- FOOTER ---------- */
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius)}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius)}
  footer small{display:block;color:#6c7f99}

  /* ---------- Responsive ---------- */
  @media (max-width:780px){
    .about-grid{grid-template-columns:1fr}
    .highlights{grid-template-columns:1fr 1fr}
    .hero h1{font-size:26px}
  }
</style>
</head>

<body>
  <div class="wrap">

    <!-- Header -->
    <header class="hero">
      <h1>About Us</h1>
      <p>We help people discover, evaluate, and secure the homes and plots they love—fully online.</p>
    </header>

    <!-- Shared Navigation (auto hides/shows items based on login) -->
    <?php require __DIR__ . '/nav.php'; ?>

    <!-- Title -->
    <div class="page-title"><h2>Who We Are</h2></div>

    <!-- About content -->
    <section class="about-grid">
      <div class="photo">
        <img src="/asset-management/happy-business.jpg?v=1" alt="Our real estate service banner">
      </div>

      <div class="card">
        <h3>Our Story</h3>
        <p>
          We’re a real-estate technology team focused on making buying and selling property simple and transparent.
          From listing discovery to documentation support, we bring the entire experience online—with friendly
          human help whenever you need it.
        </p>

        <h3>What We Do</h3>
        <ul>
          <li><strong>Property Listings:</strong> Verified flats and plots with clear photos and key details.</li>
          <li><strong>Shortlist &amp; Enquiry:</strong> Save favourites, compare basics, and request guided visits.</li>
          <li><strong>Assisted Registration:</strong> Step-by-step help with paperwork and appointment scheduling.</li>
          <li><strong>Secure Billing:</strong> Simple billing records and receipts you can access anytime.</li>
          <li><strong>Support &amp; Feedback:</strong> Raise issues and share feedback—everything tracked in one place.</li>
        </ul>
      </div>
    </section>

    <!-- Highlights -->
    <section class="highlights" aria-label="Highlights">
      <div class="pill">Verified Listings</div>
      <div class="pill">Guided Visits</div>
      <div class="pill">Secure Documentation</div>
    </section>

    <!-- Values & Contact CTA -->
    <section class="about-grid" aria-label="Values and contact">
      <div class="card">
        <h3>Why Choose Us</h3>
        <ul>
          <li>Clear, modern interface that’s easy to use on any device.</li>
          <li>End-to-end assistance—from discovery to registration.</li>
          <li>Transparent updates and reliable customer support.</li>
        </ul>

        <h3 style="margin-top:14px;">Our Promise</h3>
        <p>
          We put clarity, speed, and trust at the center of your property journey.
          Whether you’re buying your first home or listing your property, we’re here to help.
        </p>
      </div>

      <div class="photo">
        <img src="/asset-management/img1.jpg?v=1" alt="Featured residential property exterior">
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

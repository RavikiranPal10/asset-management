<?php
// support.php — Customer Support (Bookman Old Style) with unified header/footer
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Customer Support | Property / Asset Management System</title>

<style>
  :root{
    --bg:#f6f8fb;--ink:#0f1b2d;--muted:#54657e;
    --brand:#1165AE;--brand-2:#0b4f87;--accent:#ffd93b;
    --surface:#ffffff;--ring:rgba(17,101,174,.15);
    --shadow:0 2px 10px rgba(0,0,0,.08),0 8px 30px rgba(0,0,0,.06);
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
  .hero p{margin:10px auto 0;font-size:16px;max-width:600px;color:#f9f9f9;}

  /* NAV (no Pay & Shopping / Bill) */
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06)}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:.18s}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px)}

  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center}
  .page-title h2{margin:0;font-size:22px;font-weight:bold}

  /* CONTENT */
  .grid{display:grid;grid-template-columns:1fr 1.4fr;gap:24px;padding:22px 18px 28px;align-items:start}
  .photo{border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:#fff}
  .photo img{width:100%;height:100%;object-fit:cover}

  .card{background:#fff;border:1px solid #e9edf3;border-radius:var(--radius);box-shadow:var(--shadow);padding:18px}
  .card h3{margin:0 0 12px;font-size:18px;color:var(--brand-2)}

  label{display:block;font-weight:bold;margin-top:10px}
  input[type="text"],input[type="email"],input[type="date"],select,textarea{
    width:100%;padding:10px;margin-top:6px;border:1px solid #ccd5e0;border-radius:10px;
    font-family:"Bookman Old Style","Times New Roman",Times,serif;
  }
  textarea{min-height:110px;resize:vertical}

  .row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .actions{margin-top:16px;display:flex;gap:10px}
  .btn{display:inline-block;padding:10px 16px;border-radius:10px;border:1px solid transparent;
       font-weight:bold;text-decoration:none;cursor:pointer;box-shadow:var(--shadow)}
  .btn-primary{background:var(--brand);color:#fff}
  .btn-primary:hover{background:var(--brand-2)}
  .btn-reset{background:#e9eef6;color:#0b4f87;border-color:#cfe0f5}
  .btn-reset:hover{background:#dce9fb}

  .note{margin-top:10px;color:#6a7a90;font-size:14px}

  /* FLASH MESSAGES */
  .flash-success{color:#0a7b24;font-weight:bold;margin-bottom:10px;text-align:left;}
  .flash-error{color:#b00020;font-weight:bold;margin-bottom:10px;text-align:left;}

  /* FOOTER */
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius)}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius)}
  footer small{display:block;color:#6c7f99}

  @media(max-width:820px){
    .grid{grid-template-columns:1fr}
  }
</style>
</head>
<body>
<div class="wrap">

  <!-- Header -->
  <header class="hero">
    <h1>Support</h1>
    <p>Reach out to us by raising a ticket</p>
  </header>

  <!-- Navigation -->
<?php
// nav.php — shared navigation (Bookman), shows different items when logged-in
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$loggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user']['userid']);
?>
<?php require __DIR__ . '/nav.php'; ?>


  <!-- Title -->
  <div class="page-title"><h2>Customer Support</h2></div>

  <!-- Content -->
  <section class="grid">
    <div class="photo">
      <img src="/asset-management/about-banner.jpg" alt="Customer Support">
    </div>

    <div class="card">
      <h3>Raise a Support Request</h3>

      <!-- FLASH MESSAGES -->
      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="flash-error"><?= $_SESSION['flash_error']; ?></div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="flash-success"><?= $_SESSION['flash_success']; ?></div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>

      <form method="post" action="support-submit.php" autocomplete="off">
        <div class="row">
          <div>
            <label for="support_type">Support Type</label>
            <select id="support_type" name="support_type" required>
              <option value="">-- Select --</option>
              <option>Account / Login</option>
              <option>Property Listing</option>
              <option>Billing / Receipt</option>
              <option>Site Feedback</option>
              <option>Other</option>
            </select>
          </div>
          <div>
            <label for="priority">Priority</label>
            <select id="priority" name="priority" required>
              <option>Normal</option>
              <option>High</option>
              <option>Urgent</option>
            </select>
          </div>
        </div>

        <div class="row">
          <div>
            <label for="date">Date</label>
            <input type="date" id="date" name="date" required>
          </div>
          <div>
            <label for="contact">Contact No</label>
            <input type="text" id="contact" name="contact" maxlength="15" placeholder="+91-XXXXXXXXXX" required>
          </div>
        </div>

        <div class="row">
          <div>
            <label for="email">Email ID</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" required>
          </div>
          <div>
            <label for="ticket_ref">Ticket Reference (optional)</label>
            <input type="text" id="ticket_ref" name="ticket_ref" placeholder="AUTO / manual">
          </div>
        </div>

        <label for="message">Remarks / Message</label>
        <textarea id="message" name="message" placeholder="Describe your issue… (min 20 characters)" minlength="20" required></textarea>

        <div class="actions">
          <button class="btn btn-primary" type="submit">Submit Ticket</button>
          <button class="btn btn-reset" type="reset">Reset</button>
        </div>

        <div class="note">You will receive an acknowledgement on your email once the ticket is logged successfully.</div>
      </form>
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

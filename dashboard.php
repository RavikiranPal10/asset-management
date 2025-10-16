<?php
session_start();
$userid = $_SESSION['user_id'] ?? $_SESSION['auth_user_id'] ?? '';
$name   = $_SESSION['user_name'] ?? $_SESSION['auth_username'] ?? 'User';
if (!$userid) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard | Property / Asset Management System</title>
<style>
  :root{--bg:#f6f8fb;--ink:#0f1b2d;--muted:#54657e;--brand:#1165AE;--brand-2:#0b4f87;
        --accent:#ffd93b;--surface:#ffffff;--shadow:0 2px 10px rgba(0,0,0,.08),0 8px 30px rgba(0,0,0,.06);
        --radius:14px;--maxw:1000px;}
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink)}
  body{font:16px/1.6 "Bookman Old Style","Times New Roman",Times,serif;}
  .wrap{max-width:var(--maxw);margin:0 auto;background:var(--surface);box-shadow:var(--shadow);
        border-radius:0 0 var(--radius) var(--radius);overflow:hidden;}
  .hero{background:linear-gradient(135deg,#009dbe 0%, #44c3e6 60%, #8ed9ee 100%);color:#fff;text-align:center;padding:42px 18px 50px;}
  .hero h1{margin:0;font-size:32px;font-weight:bold;letter-spacing:.3px;}
  .hero p{margin:10px auto 0;font-size:16px;max-width:640px;color:#f9f9f9}
  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center}
  .page-title h2{margin:0;font-size:22px;font-weight:bold}
  .welcome{margin:20px auto;max-width:600px;background:#fff;border:1px solid #e9edf3;border-radius:18px;
           box-shadow:var(--shadow);padding:20px;text-align:center}
  .btn{display:inline-block;padding:10px 14px;border-radius:10px;border:1px solid #cfe0f5;color:#0b4f87;
       text-decoration:none;font-weight:bold}
  .btn:hover{border-color:#1165AE}
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius)}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius)}
  footer small{display:block;color:#6c7f99}
</style>
</head>
<body>
<div class="wrap">

  <header class="hero">
    <h1>Welcome back</h1>
    <p>Quickly access your properties, visits, and account actions.</p>
  </header>

  <?php require __DIR__ . '/nav.php'; ?>  <!-- ✅ fixes the include warning -->

  <div class="page-title"><h2>Dashboard</h2></div>

  <div class="welcome">
    <h2 style="margin:0 0 6px">Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
    <div>Your User ID: <strong><?php echo htmlspecialchars($userid); ?></strong></div>
    <div style="margin-top:10px"><a class="btn" href="logout.php">Logout</a></div>
  </div>

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

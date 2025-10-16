<?php
/* profile.php — User Dashboard (Bookman Old Style) */
session_start();
require __DIR__ . '/db.php';

/* IMPORTANT: disable mysqli exception mode so we can handle fallbacks gracefully */
if (function_exists('mysqli_report')) {
  mysqli_report(MYSQLI_REPORT_OFF);
}

/* ---- Login Guard ---- */
$loggedIn = (
  !empty($_SESSION['user_id']) ||
  !empty($_SESSION['auth_user_id']) ||
  (!empty($_SESSION['user']) && !empty($_SESSION['user']['userid']))
);
if (!$loggedIn) {
  $_SESSION['flash_error'] = 'Please login to access your profile.';
  header('Location: login.php?next=' . urlencode('profile.php'));
  exit;
}

/* ---- Normalize current user id ---- */
$currentUserId =
  $_SESSION['auth_user_id'] ??
  $_SESSION['user_id'] ??
  ($_SESSION['user']['userid'] ?? null);

/* ---- Load current user row (for email/name) ---- */
$user = null; $user_email = ''; $user_name = '';
if ($stmt = $mysqli->prepare("SELECT id, user_id, username, email FROM users WHERE user_id=? LIMIT 1")) {
  $stmt->bind_param('s', $currentUserId);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();
  if ($user) { $user_email = $user['email'] ?? ''; $user_name = $user['username'] ?? $currentUserId; }
  $stmt->close();
}

/* ---- CSRF Token ---- */
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); }
$csrf = $_SESSION['csrf_token'];

/* ---- Helpers ---- */
function fetch_rows($mysqli, $sql, $bindTypes = '', $bindValues = []) {
  $rows = [];
  if ($stmt = $mysqli->prepare($sql)) {
    if ($bindTypes && $bindValues) $stmt->bind_param($bindTypes, ...$bindValues);
    if ($stmt->execute()) {
      $res = $stmt->get_result();
      while ($r = $res->fetch_assoc()) $rows[] = $r;
    }
    $stmt->close();
  }
  return $rows;
}
function table_has_col($mysqli, $table, $col) {
  $q = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '".$mysqli->real_escape_string($col)."'");
  return $q && $q->num_rows > 0;
}

/* ---- Visits ---- */
$visits = fetch_rows(
  $mysqli,
  "SELECT id, prop_id, prop_city, prop_name, visit_datetime, contact, email, created_at
   FROM visits
   WHERE user_id = ? OR email = ?
   ORDER BY created_at DESC",
  'ss', [$currentUserId, $user_email]
);

/* ---- Orders & Bills (adjust if your table names differ) ---- */
$purchases = fetch_rows(
  $mysqli,
  "SELECT id, order_code, item_id, item_name, amount, discount, status, created_at
   FROM orders
   WHERE user_id = ? OR email = ?
   ORDER BY created_at DESC",
  'ss', [$currentUserId, $user_email]
);

$bills = fetch_rows(
  $mysqli,
  "SELECT id, bill_no, item_id, item_name, amount, paid_on, method
   FROM bills
   WHERE user_id = ? OR email = ?
   ORDER BY paid_on DESC",
  'ss', [$currentUserId, $user_email]
);

/* ---- Support tickets (robust column detection) ---- */
$supportTable = 'support_tickets';     // change if your table name differs
// choose which timestamp column exists: prefer 'created_at', fallback to 'date', then 'submitted_at', 'opened_at'
$tsCols = ['created_at','date','submitted_at','opened_at'];
$chosen = null;
foreach ($tsCols as $c) {
  if (table_has_col($mysqli, $supportTable, $c)) { $chosen = $c; break; }
}
if ($chosen === null) { $chosen = 'NULL'; }   // no timestamp column found; won't crash

$supports = fetch_rows(
  $mysqli,
  "SELECT id, support_type, priority,
          $chosen AS date, contact, email, message, status,
          $chosen AS created_at
   FROM $supportTable
   WHERE user_id = ? OR email = ?
   ORDER BY $chosen DESC",
  'ss', [$currentUserId, $user_email]
);

/* ---- Complaints ---- */
$complaints = fetch_rows(
  $mysqli,
  "SELECT id, category, subject, description, email, status, created_at
   FROM complaints
   WHERE user_id = ? OR email = ?
   ORDER BY created_at DESC",
  'ss', [$currentUserId, $user_email]
);

/* ---- Feedback ---- */
$feedbacks = fetch_rows(
  $mysqli,
  "SELECT id, topic, rating, comments, email, created_at
   FROM feedback
   WHERE user_id = ? OR email = ?
   ORDER BY created_at DESC",
  'ss', [$currentUserId, $user_email]
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Profile | Property / Asset Management System</title>
<style>
  :root{--bg:#f6f8fb;--ink:#0f1b2d;--muted:#54657e;--brand:#1165AE;--brand-2:#0b4f87;--accent:#ffd93b;--surface:#ffffff;--ring:rgba(17,101,174,.15);--shadow:0 2px 10px rgba(0,0,0,.08),0 8px 30px rgba(0,0,0,.06);--radius:14px;--maxw:1000px}
  *{box-sizing:border-box} html,body{margin:0;padding:0;background:var(--bg);color:var(--ink)}
  body{font:16px/1.6 "Bookman Old Style","Times New Roman",Times,serif;} img{max-width:100%;height:auto;display:block}
  .wrap{max-width:var(--maxw);margin:0 auto;background:var(--surface);box-shadow:var(--shadow);border-radius:0 0 var(--radius) var(--radius);overflow:hidden}
  .hero{background:linear-gradient(135deg,#009dbe 0%, #44c3e6 60%, #8ed9ee 100%);color:#fff;text-align:center;padding:42px 18px 50px}
  .hero h1{margin:0;font-size:32px;font-weight:bold;letter-spacing:.3px}
  .hero p{margin:10px auto 0;font-size:16px;max-width:640px;color:#f9f9f9}
  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06)}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:.18s}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px)}
  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center}
  .page-title h2{margin:0;font-size:22px;font-weight:bold}
  .tabs{display:flex;flex-wrap:wrap;gap:10px;padding:14px 18px}
  .tablink{display:inline-block;padding:8px 14px;border-radius:999px;background:#fff;border:1px solid #e2eaf3;font-weight:bold;text-decoration:none;color:#0b4f87;box-shadow:var(--shadow)}
  .tablink.active{background:var(--brand);color:#fff;border-color:var(--brand)}
  .section{padding:12px 18px 24px}
  .card{background:#fff;border:1px solid #e9edf3;border-radius:var(--radius);box-shadow:var(--shadow);padding:16px;margin-top:10px}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  .grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
  .muted{color:var(--muted)} .label{font-weight:bold}
  table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #e9edf3;border-radius:12px;overflow:hidden;background:#fff;box-shadow:var(--shadow)}
  thead th{background:#e9f3ff;font-weight:bold;text-align:center;padding:10px;border-bottom:1px solid #e9edf3;color:#0b4f87}
  tbody td{padding:10px;border-top:1px solid #f1f4f8;text-align:center} tbody tr:first-child td{border-top:none}
  label{display:block;font-weight:bold;margin-top:10px}
  input[type="password"], input[type="email"], input[type="text"]{width:100%;padding:10px;margin-top:6px;border:1px solid #ccd5e0;border-radius:10px;font-family:"Bookman Old Style","Times New Roman",Times,serif}
  .actions{margin-top:14px;display:flex;gap:10px;flex-wrap:wrap}
  .btn{display:inline-block;padding:10px 16px;border-radius:10px;border:1px solid transparent;font-weight:bold;text-decoration:none;cursor:pointer;box-shadow:var(--shadow)}
  .btn-primary{background:var(--brand);color:#fff}.btn-primary:hover{background:var(--brand-2)}
  .btn-danger{background:#c62828;color:#fff}.btn-danger:hover{background:#aa1f1f}
  .note{font-size:14px;color:#6a7a90;margin-top:6px}
  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius)}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius)}
  footer small{display:block;color:#6c7f99}
  @media(max-width:860px){.grid2{grid-template-columns:1fr}.grid3{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="wrap">

  <header class="hero">
    <h1>My Profile</h1>
    <p>Welcome, <strong><?php echo htmlspecialchars($user_name ?: $currentUserId); ?></strong>. View your activity and manage your account.</p>
  </header>

  <?php require __DIR__ . '/nav.php'; ?>

  <div class="page-title"><h2>Dashboard</h2></div>

  <nav class="tabs" id="tabs">
    <a href="#overview"   class="tablink active">Overview</a>
    <a href="#visits"     class="tablink">My Visits</a>
    <a href="#purchases"  class="tablink">Purchases / Bills</a>
    <a href="#support"    class="tablink">Support Tickets</a>
    <a href="#complaints" class="tablink">Complaints</a>
    <a href="#feedback"   class="tablink">Feedback</a>
    <a href="#account"    class="tablink">Account</a>
  </nav>

  <!-- Overview -->
  <section class="section" id="overview">
    <div class="card">
      <div class="grid3">
        <div><div class="label">User ID</div><div><?php echo htmlspecialchars($currentUserId); ?></div></div>
        <div><div class="label">Name</div><div><?php echo htmlspecialchars($user_name ?: '-'); ?></div></div>
        <div><div class="label">Email</div><div><?php echo htmlspecialchars($user_email ?: '-'); ?></div></div>
      </div>
      <div class="grid3" style="margin-top:12px">
        <div><div class="label">Total Visits</div><div><?php echo count($visits); ?></div></div>
        <div><div class="label">Total Orders</div><div><?php echo count($purchases); ?></div></div>
        <div><div class="label">Open Tickets</div><div><?php $open=0; foreach($supports as $s){ if (strtolower($s['status']??'')!=='closed') $open++; } echo $open; ?></div></div>
      </div>
      <div class="note">Tip: Use the tabs above to navigate your data.</div>
    </div>
  </section>

  <!-- Visits -->
  <section class="section" id="visits" style="display:none">
    <div class="card">
      <h3>Scheduled Visits</h3>
      <?php if(!$visits): ?>
        <div class="note">No visits found. Schedule one from <a href="product.php">Properties</a>.</div>
      <?php else: ?>
      <table>
        <thead><tr><th>ID</th><th>Property</th><th>City</th><th>Date & Time</th><th>Email</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach($visits as $v): ?>
          <tr>
            <td><?php echo htmlspecialchars($v['id']); ?></td>
            <td><?php echo htmlspecialchars($v['prop_id'].' — '.$v['prop_name']); ?></td>
            <td><?php echo htmlspecialchars($v['prop_city']); ?></td>
            <td><?php echo htmlspecialchars($v['visit_datetime']); ?></td>
            <td><?php echo htmlspecialchars($v['email']); ?></td>
            <td><?php echo htmlspecialchars($v['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </section>

  <!-- Purchases / Bills -->
  <section class="section" id="purchases" style="display:none">
    <div class="card">
      <h3>Orders</h3>
      <?php if(!$purchases): ?>
        <div class="note">No orders yet.</div>
      <?php else: ?>
      <table>
        <thead><tr><th>ID</th><th>Order Code</th><th>Item</th><th>Amount</th><th>Discount</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach($purchases as $o): ?>
          <tr>
            <td><?php echo htmlspecialchars($o['id']); ?></td>
            <td><?php echo htmlspecialchars($o['order_code']); ?></td>
            <td><?php echo htmlspecialchars($o['item_id'].' — '.$o['item_name']); ?></td>
            <td><?php echo htmlspecialchars($o['amount']); ?></td>
            <td><?php echo htmlspecialchars($o['discount']); ?></td>
            <td><?php echo htmlspecialchars($o['status']); ?></td>
            <td><?php echo htmlspecialchars($o['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>

      <h3 style="margin-top:16px">Bills</h3>
      <?php if(!$bills): ?>
        <div class="note">No bills yet.</div>
      <?php else: ?>
      <table>
        <thead><tr><th>ID</th><th>Bill No</th><th>Item</th><th>Amount</th><th>Paid On</th><th>Method</th></tr></thead>
        <tbody>
          <?php foreach($bills as $b): ?>
          <tr>
            <td><?php echo htmlspecialchars($b['id']); ?></td>
            <td><?php echo htmlspecialchars($b['bill_no']); ?></td>
            <td><?php echo htmlspecialchars($b['item_id'].' — '.$b['item_name']); ?></td>
            <td><?php echo htmlspecialchars($b['amount']); ?></td>
            <td><?php echo htmlspecialchars($b['paid_on']); ?></td>
            <td><?php echo htmlspecialchars($b['method']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </section>

  <!-- Support Tickets -->
  <section class="section" id="support" style="display:none">
    <div class="card">
      <h3>Support Tickets</h3>
      <?php if(!$supports): ?>
        <div class="note">No tickets found. Create one on the <a href="support.php">Support</a> page.</div>
      <?php else: ?>
      <table>
        <thead><tr><th>ID</th><th>Type</th><th>Priority</th><th>Date</th><th>Email</th><th>Status</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach($supports as $s): ?>
          <tr>
            <td><?php echo htmlspecialchars($s['id']); ?></td>
            <td><?php echo htmlspecialchars($s['support_type']); ?></td>
            <td><?php echo htmlspecialchars($s['priority']); ?></td>
            <td><?php echo htmlspecialchars($s['date']); ?></td>
            <td><?php echo htmlspecialchars($s['email']); ?></td>
            <td><?php echo htmlspecialchars($s['status'] ?? 'Open'); ?></td>
            <td><?php echo htmlspecialchars($s['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </section>

  <!-- Complaints -->
  <section class="section" id="complaints" style="display:none">
    <div class="card">
      <h3>Complaints</h3>
      <?php if(!$complaints): ?>
        <div class="note">No complaints submitted. Try the <a href="complain.php">Complain</a> page.</div>
      <?php else: ?>
      <table>
        <thead><tr><th>ID</th><th>Category</th><th>Subject</th><th>Status</th><th>Email</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach($complaints as $c): ?>
          <tr>
            <td><?php echo htmlspecialchars($c['id']); ?></td>
            <td><?php echo htmlspecialchars($c['category']); ?></td>
            <td><?php echo htmlspecialchars($c['subject']); ?></td>
            <td><?php echo htmlspecialchars($c['status'] ?? 'Open'); ?></td>
            <td><?php echo htmlspecialchars($c['email']); ?></td>
            <td><?php echo htmlspecialchars($c['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </section>

  <!-- Feedback -->
  <section class="section" id="feedback" style="display:none">
    <div class="card">
      <h3>Feedback</h3>
      <?php if(!$feedbacks): ?>
        <div class="note">No feedback yet. Share some on the <a href="feedback.php">Feedback</a> page.</div>
      <?php else: ?>
      <table>
        <thead><tr><th>ID</th><th>Topic</th><th>Rating</th><th>Comments</th><th>Email</th><th>Created</th></tr></thead>
        <tbody>
          <?php foreach($feedbacks as $f): ?>
          <tr>
            <td><?php echo htmlspecialchars($f['id']); ?></td>
            <td><?php echo htmlspecialchars($f['topic']); ?></td>
            <td><?php echo htmlspecialchars($f['rating']); ?></td>
            <td><?php echo htmlspecialchars($f['comments']); ?></td>
            <td><?php echo htmlspecialchars($f['email']); ?></td>
            <td><?php echo htmlspecialchars($f['created_at']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </section>

  <!-- Account -->
  <section class="section" id="account" style="display:none">
    <div class="card">
      <h3>Account Settings</h3>
      <div class="grid2">
        <div><div class="label">User ID</div><div><?php echo htmlspecialchars($currentUserId); ?></div></div>
        <div><div class="label">Email</div><div><?php echo htmlspecialchars($user_email ?: '-'); ?></div></div>
      </div>

      <h3 style="margin-top:16px">Reset Password</h3>
      <form action="profile-password.php" method="post" autocomplete="off">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
        <label for="old_password">Current Password</label>
        <input type="password" id="old_password" name="old_password" required>
        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" minlength="6" required>
        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" minlength="6" required>
        <div class="actions"><button class="btn btn-primary" type="submit">Update Password</button></div>
        <div class="note">Use at least 6 characters. You’ll need to login again after a successful change.</div>
      </form>

      <h3 style="margin-top:20px;color:#c62828">Delete Account</h3>
      <form action="profile-delete.php" method="post" onsubmit="return confirm('This will permanently delete your account and related records (visits, feedback, tickets, etc.) where applicable. Continue?');">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
        <label><input type="checkbox" name="ack" value="1" required> I understand this action cannot be undone.</label>
        <div class="actions"><button class="btn btn-danger" type="submit">Delete My Account</button></div>
      </form>

      <div class="note">If you need a data export before deletion, contact support.</div>
    </div>
  </section>

  
  <footer>
    <div><strong>All Rights Reserved | Copyright Protected</strong></div>
    <small>Developed by <strong>Ravikiran Pal</strong> | Enrollment No: <em>2003727907</em> | Under the guidance of <em>Mrs. Madhuri Jha</em> | IGNOU BCSP-064</small>
  </footer>
</div>

<script>
  // tiny client-side tabs
  const tabs = document.querySelectorAll('.tablink');
  const sections = {
    overview: document.getElementById('overview'),
    visits: document.getElementById('visits'),
    purchases: document.getElementById('purchases'),
    support: document.getElementById('support'),
    complaints: document.getElementById('complaints'),
    feedback: document.getElementById('feedback'),
    account: document.getElementById('account'),
  };
  function showTab(id){
    Object.values(sections).forEach(s => s.style.display = 'none');
    tabs.forEach(t => t.classList.remove('active'));
    sections[id].style.display = 'block';
    document.querySelector('.tablink[href="#'+id+'"]').classList.add('active');
    history.replaceState(null,null,'#'+id);
  }
  tabs.forEach(t => t.addEventListener('click', e => {
    e.preventDefault();
    showTab(t.getAttribute('href').slice(1) || 'overview');
  }));
  const initial = (location.hash || '#overview').slice(1);
  if (sections[initial]) showTab(initial); else showTab('overview');
</script>
</body>
</html>

<?php
/* product.php — Product/Listing module with City Filters (Bookman Old Style) */
session_start();

/* --- Demo data (add more anytime) --- */
$products = [
  // ---------- DELHI ----------
  [
    'id' => 'P001',
    'city' => 'Delhi',
    'category' => 'Home',
    'type' => 'Type 1',
    'name' => '5 BHK in Lajpat Nagar',
    'desc' => 'Premium independent house in New Delhi with modern architecture and landscaped garden.',
    'amount' => '₹78 Lakh',
    'discount' => '₹2 Lakh',
    'image' => '/asset-management/img2.jpg',
  ],
  [
    'id' => 'P002',
    'city' => 'Delhi',
    'category' => 'Home',
    'type' => 'Type 2',
    'name' => '4 BHK in Okhla',
    'desc' => 'Spacious family home in a peaceful neighbourhood; easy access to schools and markets.',
    'amount' => '₹60 Lakh',
    'discount' => '₹4 Lakh',
    'image' => '/asset-management/img1.jpg',
  ],
  [
    'id' => 'P003',
    'city' => 'Delhi',
    'category' => 'Home',
    'type' => 'Type 1',
    'name' => '3 BHK in Murad Nagar',
    'desc' => 'Well-connected area, great for families and first-time buyers.',
    'amount' => '₹50 Lakh',
    'discount' => '₹5 Lakh',
    'image' => '/asset-management/img3.jpg',
  ],
  [
    'id' => 'P007',
    'city' => 'Delhi',
    'category' => 'Home',
    'type' => 'Type 3',
    'name' => '2 BHK in Rohini',
    'desc' => 'Affordable, well-ventilated unit in a secure community near Metro station.',
    'amount' => '₹35 Lakh',
    'discount' => '₹2 Lakh',
    'image' => '/asset-management/delhi-4.jpg',
  ],

  // ---------- MUMBAI ----------
  [
    'id' => 'P004',
    'city' => 'Mumbai',
    'category' => 'Home',
    'type' => 'Type 2',
    'name' => '2 BHK in Andheri West',
    'desc' => 'Modern apartment with clubhouse access and covered parking.',
    'amount' => '₹1.2 Cr',
    'discount' => '₹5 Lakh',
    'image' => '/asset-management/Mumbai-1.jpg',
  ],
  [
    'id' => 'P008',
    'city' => 'Mumbai',
    'category' => 'Home',
    'type' => 'Type 3',
    'name' => '3 BHK in Bandra East',
    'desc' => 'Luxury sea-view residence with modern interiors and terrace garden.',
    'amount' => '₹2.5 Cr',
    'discount' => '₹10 Lakh',
    'image' => '/asset-management/Mumbai-2.jpg',
  ],

  // ---------- CHENNAI ----------
  [
    'id' => 'P005',
    'city' => 'Chennai',
    'category' => 'Home',
    'type' => 'Type 1',
    'name' => '3 BHK in Velachery',
    'desc' => 'Airy corner unit, near schools and tech parks.',
    'amount' => '₹85 Lakh',
    'discount' => '₹3 Lakh',
    'image' => '/asset-management/chennai-1.jpg',
  ],
  [
    'id' => 'P009',
    'city' => 'Chennai',
    'category' => 'Home',
    'type' => 'Type 2',
    'name' => '2 BHK in Anna Nagar',
    'desc' => 'Comfortable mid-floor apartment with good cross-ventilation and 24-hour security.',
    'amount' => '₹65 Lakh',
    'discount' => '₹2 Lakh',
    'image' => '/asset-management/chennai-2.jpg',
  ],
  [
    'id' => 'P010',
    'city' => 'Chennai',
    'category' => 'Home',
    'type' => 'Type 3',
    'name' => '4 BHK in Thoraipakkam',
    'desc' => 'Large family villa with rooftop garden and two car parks.',
    'amount' => '₹1.3 Cr',
    'discount' => '₹6 Lakh',
    'image' => '/asset-management/chennai-3.jpg',
  ],

  // ---------- KOLKATA ----------
  [
    'id' => 'P006',
    'city' => 'Kolkata',
    'category' => 'Home',
    'type' => 'Type 2',
    'name' => '2 BHK in New Town',
    'desc' => 'Gated complex with gym and community hall.',
    'amount' => '₹55 Lakh',
    'discount' => '₹2 Lakh',
    'image' => '/asset-management/kolkata-1.jpg',
  ],
  [
    'id' => 'P011',
    'city' => 'Kolkata',
    'category' => 'Home',
    'type' => 'Type 1',
    'name' => '3 BHK in Salt Lake',
    'desc' => 'Spacious apartment near City Centre with park-facing balcony.',
    'amount' => '₹70 Lakh',
    'discount' => '₹3 Lakh',
    'image' => '/asset-management/kolkata-2.jpg',
  ],
  [
    'id' => 'P012',
    'city' => 'Kolkata',
    'category' => 'Home',
    'type' => 'Type 3',
    'name' => '4 BHK in Ballygunge',
    'desc' => 'Heritage-style villa in prime location with marble flooring and garden.',
    'amount' => '₹1.1 Cr',
    'discount' => '₹5 Lakh',
    'image' => '/asset-management/kolkata-3.jpg',
  ],
];

/* derive city list from data (plus All) */
$cities = array_values(array_unique(array_map(fn($p)=>$p['city'], $products)));
sort($cities);
array_unshift($cities, 'All');

/* read current filter */
$current = isset($_GET['city']) ? trim($_GET['city']) : 'All';
$filtered = array_values(array_filter($products, function($p) use($current){
  return $current === 'All' ? true : (strcasecmp($p['city'], $current) === 0);
}));
$count = count($filtered);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Product Listings | Property / Asset Management System</title>

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

  .hero{background:linear-gradient(135deg,#009dbe 0%, #44c3e6 60%, #8ed9ee 100%);
        color:#fff;text-align:center;padding:42px 18px 50px;}
  .hero h1{margin:0;font-size:32px;font-weight:bold;letter-spacing:.3px}
  .hero p{margin:10px auto 0;font-size:16px;max-width:640px;color:#f9f9f9}

  .nav{background:var(--brand);padding:12px 0 16px;border-top:4px solid #e53935;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.06)}
  .nav ul{display:flex;flex-wrap:wrap;justify-content:center;list-style:none;margin:0;padding:0;gap:10px 12px}
  .nav a{display:inline-block;padding:9px 16px;background:#0e5595;color:#fff;font-weight:bold;text-decoration:none;border-radius:10px;transition:.18s}
  .nav a:hover{background:var(--accent);color:#000;transform:translateY(-2px)}

  .page-title{padding:16px 18px;background:#f2f7fc;border-bottom:1px solid #e9edf3;text-align:center}
  .page-title h2{margin:0;font-size:22px;font-weight:bold}

  /* City filter bar */
  .filters{padding:14px 18px;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
  .filters .pill{
    padding:8px 14px;border-radius:999px;background:#fff;border:1px solid #e2eaf3;text-decoration:none;
    color:#0b4f87;font-weight:bold;box-shadow:var(--shadow)
  }
  .filters .pill:hover{border-color:var(--brand);box-shadow:0 0 0 4px var(--ring)}
  .filters .selected{background:var(--brand);color:#fff;border-color:var(--brand)}

  .count{margin-left:auto;color:#54657e;font-weight:bold}

  .table-wrap{padding:10px 18px 28px}
  table{width:100%;border-collapse:separate;border-spacing:0;border:1px solid #e9edf3;border-radius:14px;overflow:hidden;background:#fff;box-shadow:var(--shadow)}
  thead th{background:#e9f3ff;font-weight:bold;text-align:center;padding:10px;border-bottom:1px solid #e9edf3;color:#0b4f87}
  tbody td{padding:12px;vertical-align:middle;border-top:1px solid #f1f4f8;text-align:center}
  tbody tr:first-child td{border-top:none}
  .imgcell{width:260px}
  .imgbox{border-radius:12px;overflow:hidden;box-shadow:var(--shadow)}
  .name{font-weight:bold;margin-bottom:4px}
  .muted{color:var(--muted)}
  .price{font-weight:bold}
  .discount{display:inline-block;margin-left:6px;padding:2px 8px;border-radius:999px;background:#e8f3ff;border:1px solid #cfe4ff;color:#0b4f87;font-size:14px}

  /* ACTION BUTTONS */
  .actions{
    display:flex;
    flex-direction:column;
    gap:8px;
    align-items:center;
    justify-content:center;
  }
  .actions a{
    display:inline-block;
    text-decoration:none;
    font-weight:bold;
    padding:10px 14px;
    border-radius:10px;
    color:#fff;
    transition:all .18s ease;
    width:140px;
    text-align:center;
    box-shadow:var(--shadow);
  }
  .btn-shop{background:var(--brand);}
  .btn-shop:hover{background:var(--accent);color:#000}
  .btn-visit{background:#0b4f87;}
  .btn-visit:hover{background:#ffd93b;color:#000}

  @media (max-width: 860px){
    thead{display:none}
    table, tbody, tr, td{display:block;width:100%}
    tbody tr{border:1px solid #e9edf3;border-radius:12px;margin-bottom:14px;box-shadow:var(--shadow)}
    tbody td{border:none;padding:10px 12px;text-align:left}
    .imgcell{order:-1}
    .actions{align-items:flex-start}
  }

  .footer-img{width:100%;border-radius:0 0 var(--radius) var(--radius)}
  footer{text-align:center;color:#3e516b;padding:14px;background:#fff;border-top:1px solid #e9edf3;border-radius:0 0 var(--radius) var(--radius)}
  footer small{display:block;color:#6c7f99}
</style>
</head>

<body>
<div class="wrap">

  <header class="hero">
    <h1>Product</h1>
    <p>Filter by city to see properties in your preferred location. Click <em>Shop Now</em> to proceed to booking.</p>
  </header>

  <nav class="nav">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="aboutus.php">About Us</a></li>
      <li><a href="new-user.php">New User</a></li>
      <li><a href="login.php">Login</a></li>
      <li><a href="product.php">Properties</a></li>
      <li><a href="support.php">Support</a></li>
      <li><a href="complain.php">Complain</a></li>
      <li><a href="feedback.php">Feedback</a></li>
      <li><a href="contact-us.php">Contact Us</a></li>
    </ul>
  </nav>

  <div class="page-title"><h2>Available Properties</h2></div>

  <!-- City filter controls -->
  <div class="filters" aria-label="City filters">
    <?php foreach ($cities as $city):
      $isSelected = (strcasecmp($city, $current) === 0);
      $href = 'product.php' . ($city === 'All' ? '' : ('?city=' . urlencode($city)));
    ?>
      <a class="pill <?php echo $isSelected ? 'selected' : ''; ?>" href="<?php echo $href; ?>">
        <?php echo htmlspecialchars($city); ?>
      </a>
    <?php endforeach; ?>
    <div class="count"><?php echo $count; ?> item<?php echo $count===1?'':'s'; ?> found</div>
  </div>

  <div class="table-wrap">
    <?php if ($count === 0): ?>
      <div style="padding:14px 0;text-align:center;color:#54657e;font-weight:bold;background:#fff;border:1px solid #e9edf3;border-radius:12px;">
        No properties found for <em><?php echo htmlspecialchars($current); ?></em>.
      </div>
    <?php else: ?>
    <table aria-label="Product listings">
      <thead>
        <tr>
          <th>Item ID</th>
          <th>City</th>
          <th>Category</th>
          <th>Type</th>
          <th>Name &amp; Description</th>
          <th>Amount</th>
          <th>Discounted Amount</th>
          <th class="imgcell">Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($filtered as $p): ?>
          <tr>
            <td><?php echo htmlspecialchars($p['id']); ?></td>
            <td><?php echo htmlspecialchars($p['city']); ?></td>
            <td><?php echo htmlspecialchars($p['category']); ?></td>
            <td><?php echo htmlspecialchars($p['type']); ?></td>
            <td>
              <div class="name"><?php echo htmlspecialchars($p['name']); ?></div>
              <div class="muted"><?php echo htmlspecialchars($p['desc']); ?></div>
            </td>
            <td class="price"><?php echo htmlspecialchars($p['amount']); ?></td>
            <td><span class="discount"><?php echo htmlspecialchars($p['discount']); ?> off</span></td>
            <td class="imgcell">
              <div class="imgbox">
                <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
              </div>
            </td>
            <td class="actions">
  <a href="login.php?next=shopping.php&item=<?php echo urlencode($p['id']); ?>" class="btn-shop">Shop Now</a>
  <a href="login.php?next=visit.php&item=<?php echo urlencode($p['id']); ?>" class="btn-visit">Schedule Visit</a>
</td>

          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
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

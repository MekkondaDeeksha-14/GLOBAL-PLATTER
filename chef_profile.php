<?php
session_start();

/* ---------- PROTECT CHEF PAGE ---------- */
if (!isset($_SESSION['chef_id'])) {
    header("Location: login.php");
    exit;
}

$chef_id   = $_SESSION['chef_id'];
$chef_name = $_SESSION['chef_name'] ?? "Chef";

/* ---------- TEMP DISPLAY STATS (NO DB) ---------- */
$total_bookings   = 24;
$pending_requests = 6;
$avg_rating       = 4.5;
$experience_years = 5;

$avatar = "assets/images/chef_avatar.png";

/* ---------- DEMO RATINGS ---------- */
$reviews = [
    ["name"=>"Aarav","rating"=>5,"review"=>"Absolutely amazing dishes!"],
    ["name"=>"Riya","rating"=>4,"review"=>"Great taste and presentation."],
    ["name"=>"Nathan","rating"=>5,"review"=>"Very professional chef."],
    ["name"=>"Meera","rating"=>4,"review"=>"Loved the flavors!"],
    ["name"=>"Carlos","rating"=>3,"review"=>"Good but could improve."],
];
shuffle($reviews);
$recent_reviews = array_slice($reviews, 0, 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chef Dashboard | Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background: radial-gradient(circle at top,#3b0000,#0a0000);
    color:#fff;
}
.container{max-width:1200px;margin:90px auto;padding:30px;}
.section{margin-top:50px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;}

.navbar{
    position:fixed;top:0;width:100%;
    display:flex;justify-content:space-between;align-items:center;
    padding:15px px;
    background: rgba(184,115,51,0.9);
    backdrop-filter: blur(10px);
    border-bottom:2px solid gold;
    z-index:100;
}
.logo{font-size:22px;font-weight:bold;}
.nav-links{display:flex;gap:22px;list-style:none;margin:6;padding:8;}
.nav-links a{
    color:#fff;text-decoration:none;font-weight:600;
}
.nav-links a.active{color:#ffd700;}

.hero{
    display:flex;flex-wrap:wrap;align-items:center;gap:25px;
    background: rgba(255,255,255,0.08);
    padding:30px;border-radius:20px;
}
.avatar{
    width:110px;height:110px;border-radius:50%;
    background:url('<?php echo $avatar; ?>') center/cover no-repeat;
}
.hero h1{
    font-size:34px;
    background:linear-gradient(90deg,#ffd700,#ffda75,#ffd700);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

.card{
    background:rgba(0,0,0,0.35);
    padding:25px;border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.6);
}
.label{font-size:14px;color:#ffd700;}
.value{font-size:28px;font-weight:700;margin-top:8px;}

.review{
    padding:12px 0;
    border-bottom:1px solid rgba(255,255,255,0.15);
}
.review:last-child{border:none;}
.stars{color:gold;}

.action-btn{
    display:block;
    text-align:center;
    margin-top:20px;
    padding:14px;
    background:linear-gradient(90deg,#ffb347,#ffd700);
    color:#000;
    border-radius:14px;
    text-decoration:none;
    font-weight:700;
    transition:0.3s;
}
.action-btn:hover{transform:scale(1.05);}

.logout{
    display:inline-block;
    margin:60px auto 0;
    background:#ffd700;color:#000;
    padding:14px 35px;border-radius:10px;
    text-decoration:none;font-weight:bold;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="logo">
        <a href="LandingPage.php" style="color:inherit;text-decoration:none;">GLOBAL PLATTER</a>
    </div>
    <ul class="nav-links">
        <li><a href="chef_profile.php" class="active">Dashboard</a></li>
    </ul>
</nav>

<div class="container">

<!-- HERO -->
<div class="hero">
    <div class="avatar"></div>
    <div>
        <h1>Welcome Chef <?php echo htmlspecialchars($chef_name); ?> 👨‍🍳</h1>
        <p><?php echo $experience_years; ?>+ years of culinary experience</p>
    </div>
</div>

<!-- STATS -->
<div class="section grid">

<div class="card">
    <div class="label">Total Bookings</div>
    <div class="value"><?php echo $total_bookings; ?></div>
</div>

<div class="card">
    <div class="label">Pending Booking Requests</div>
    <div class="value"><?php echo $pending_requests; ?></div>
</div>

<div class="card">
    <div class="label">Average Rating</div>
    <div class="value"><?php echo $avg_rating; ?> ⭐</div>
</div>

</div>

<!-- FEATURES -->
<div class="section grid">

<div class="card">
    <div class="label">Your Menu</div>
    <p>Add, edit or remove cuisines & dishes shown on your profile.</p>
    <a href="chef_dishes.php" class="action-btn">Manage Dishes</a>
</div>

<div class="card">
    <div class="label">Manage Bookings</div>
    <p>View and manage booking requests in one place.</p>
    <a href="chef_bookings.php" class="action-btn">Manage Bookings</a>
</div>

</div>

<!-- RECENT RATINGS -->
<div class="section">
<div class="card">
    <div class="label">Recent Ratings</div>

    <?php foreach($recent_reviews as $r): ?>
        <div class="review">
            <div class="stars"><?php echo str_repeat("⭐", $r['rating']); ?></div>
            <small>"<?php echo $r['review']; ?>" — <?php echo $r['name']; ?></small>
        </div>
    <?php endforeach; ?>

</div>
</div>

<a href="logout.php" class="logout">Logout</a>

</div>
</body>
</html>

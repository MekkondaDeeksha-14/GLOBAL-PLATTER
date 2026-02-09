<?php
session_start();

/* ---------- PROTECT PAGE (USER ONLY) ---------- */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

/* ---------- DB CONNECTION ---------- */
include 'db.php';

/* ---------- USER DATA ---------- */
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? "User";

/* ---------- STATS ---------- */
$sessions_attended = rand(8, 25);
$progress = min(100, ($sessions_attended / 30) * 100);

/* ---------- UPCOMING SESSIONS ---------- */
$upcoming_sessions = [
    ["chef"=>"Chef Luca Romano","dish"=>"Truffle Pasta","cuisine"=>"Italian","days"=>2],
    ["chef"=>"Chef Kenji Sato","dish"=>"Sushi Rolling","cuisine"=>"Japanese","days"=>5],
    ["chef"=>"Chef Marie Dupont","dish"=>"French Desserts","cuisine"=>"French","days"=>7]
];

/* ---------- AVATAR ---------- */
$avatar = "assets/images/pro_avatar.jpg";

/* ---------- HANDLE MEME DELETE ---------- */
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_meme'])){
    $meme_path = $_POST['delete_meme'];
    if(file_exists($meme_path)){
        unlink($meme_path);
    }
    $stmt_del = $conn->prepare("DELETE FROM memes WHERE user_id=? AND meme_path=?");
    $stmt_del->bind_param("is", $user_id, $meme_path);
    $stmt_del->execute();
    header("Location: profile.php");
    exit;
}

/* ---------- FETCH USER MEMES ---------- */
$stmt = $conn->prepare("SELECT meme_path, created_at FROM memes WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$memes_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile | Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{
    margin:0;
    font-family:'Poppins',sans-serif;
    background: radial-gradient(circle at top,#6b0000,#0a0000);
    color:#fff;
}
.container{max-width:1200px;margin:80px auto;padding:30px;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:25px;}
.navbar{
    position:fixed;
    top:0;
    width:95%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 40px;
    background: rgba(184,115,51,0.9);
    border-bottom:2px solid gold;
    z-index:100;
}
.nav-links{display:flex;list-style:none;gap:20px;}
.nav-links a{color:#fff;text-decoration:none;font-weight:600;}
.nav-links a.active{color:#ffd700;}
.hero{
    display:flex;
    gap:25px;
    background: rgba(255,255,255,0.08);
    padding:30px;
    border-radius:20px;
}
.avatar{
    width:110px;
    height:110px;
    border-radius:50%;
    background:url('<?php echo htmlspecialchars($avatar); ?>') center/cover no-repeat;
}
.card{
    background:rgba(0,0,0,0.35);
    padding:25px;
    border-radius:18px;
    text-align:center;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}
.label{font-size:14px;color:#ffd700;margin-bottom:10px;}
.value{font-size:28px;font-weight:700;}
.progress-circle{
    position:relative;
    width:140px;
    height:140px;
    margin:auto;
}
.progress-circle svg{transform:rotate(-90deg);}
.progress-circle circle{fill:none;stroke-width:12;}
.bg{stroke:#2b0000;}
.fg{stroke:url(#grad);stroke-dasharray:314;stroke-dashoffset:314;transition:1.5s;}
.center{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    font-size:22px;
    font-weight:bold;
    color:#ffd700;
}
.my-memes{
    background:rgba(0,0,0,0.35);
    padding:20px;
    border-radius:18px;
    margin-top:50px;
}
.meme-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}
.meme-card{
    background:#fff8e7;
    color:#2d2d2d;
    padding:10px;
    border-radius:12px;
    text-align:center;
}
.meme-card img{
    width:100%;
    max-height:220px;
    object-fit:contain;
    border-radius:10px;
}
button.delete-btn{
    background:#ff4d4d;
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    cursor:pointer;
    margin-top:10px;
}
.logout {
    display: inline-block;       /* change from block to inline-block */
    margin: 20px auto 0;         /* center it horizontally */
    background: #ffd700;
    color: #000;
    padding: 16px 25px;           /* smaller padding */
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;             /* smaller font */
    text-align: center;
}

.upcoming .label{color:#ffd700;}
.stats-section{
    margin-top:20px; /* small gap between hero and stats */
}
</style>
</head>
<body>

<nav class="navbar">
<b>GLOBAL PLATTER</b>
<ul class="nav-links">
<li><a href="profile.php" class="active">My Profile</a></li>
<li><a href="meme_generator.php">Make a Meme</a></li>
<li><a href="Chefs.php">Chefs</a></li>
<li><a href="about.php">About Us</a></li>
</ul>
</nav>

<div class="container">

<!-- HERO -->
<div class="hero">
<div class="avatar"></div>
<div>
<h1>Welcome back, <?php echo htmlspecialchars($username); ?> 👋</h1>
<p>Keep Cooking 🔥</p>
</div>
</div>

<!-- STATS -->
<div class="section stats-section grid">
<div class="card">
<div class="label">Sessions Attended</div>
<div class="value" id="count"><?php echo $sessions_attended; ?></div>
</div>

<div class="card">
<div class="label">Learning Progress</div>
<div class="progress-circle">
<svg width="140" height="140">
<defs>
<linearGradient id="grad">
<stop offset="0%" stop-color="#ffd700"/>
<stop offset="100%" stop-color="#ffda75"/>
</linearGradient>
</defs>
<circle class="bg" cx="70" cy="70" r="50"></circle>
<circle class="fg" cx="70" cy="70" r="50"></circle>
</svg>
<div class="center"><?php echo intval($progress); ?>%</div>
</div>
</div>
</div>

<!-- UPCOMING SESSIONS -->
<div class="section upcoming">
<h2>📅 Upcoming Sessions</h2>
<div class="grid">
<?php foreach($upcoming_sessions as $session): ?>
<div class="card">
<div class="label" style="color:#ffd700;"><?php echo $session['cuisine']; ?></div>
<p><?php echo $session['dish']; ?></p>
<p>👨‍🍳 <?php echo $session['chef']; ?></p>
<p>⏳ In <?php echo $session['days']; ?> days</p>
</div>
<?php endforeach; ?>
</div>
</div>

<!-- MY MEMES -->
<div class="section my-memes">
<h2>😂 My Memes</h2>
<?php if($memes_result->num_rows > 0): ?>
<div class="meme-grid">
<?php while($row = $memes_result->fetch_assoc()): ?>
<div class="meme-card">
<img src="<?php echo htmlspecialchars($row['meme_path']); ?>">
<p><?php echo date("d M Y", strtotime($row['created_at'])); ?></p>
<form method="POST">
    <input type="hidden" name="delete_meme" value="<?php echo htmlspecialchars($row['meme_path']); ?>">
    <button type="submit" class="delete-btn">Delete</button>
</form>
</div>
<?php endwhile; ?>
</div>
<?php else: ?>
<p>You haven’t created any memes yet 😅</p>
<?php endif; ?>
</div>

<!-- LOGOUT BUTTON -->
<a href="logout.php" class="logout">Logout</a>

</div>

<script>
let c=0,target=<?php echo $sessions_attended; ?>;
const el=document.getElementById('count');
const i=setInterval(()=>{
    c++;
    el.textContent=c;
    if(c>=target) clearInterval(i);
},40);

const circle=document.querySelector('.fg');
const r=50,circ=2*Math.PI*r;
circle.style.strokeDasharray=circ;
circle.style.strokeDashoffset=circ;
setTimeout(()=>{
circle.style.strokeDashoffset=circ-(<?php echo intval($progress); ?>/100)*circ;
},500);
</script>

</body>
</html>

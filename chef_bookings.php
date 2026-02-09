<?php
session_start();
if (!isset($_SESSION['chef_id'])) die("Unauthorized");

$chef_id   = $_SESSION['chef_id'];
$chef_name = $_SESSION['chef_name'] ?? "Chef";

/* ---------- RANDOM USERS & DISHES ---------- */
$users = ["Amit","Sam","Rebeka","Nathan","Kate","Riya","Victor","Meera","Carlos","Aarav"];
$dishes = ["Truffle Pasta","Sushi Platter","Mexican Tacos","Butter Chicken","Ramen","Pizza Margherita","Pasta Alfredo"];

/* ---------- INIT BOOKINGS IN SESSION ---------- */
if (!isset($_SESSION['demo_bookings'])) {
    $_SESSION['demo_bookings'] = [];
    for ($i=1; $i<=5; $i++) {
        $randUser = $users[array_rand($users)];
        $randDish = $dishes[array_rand($dishes)];
        $randDate = date("Y-m-d", strtotime("+".rand(0,7)." days"));
        $randTime = rand(10,20).":00";
        $_SESSION['demo_bookings'][] = [
            "id"=>$i,
            "chef_id"=>$chef_id,
            "user"=>$randUser,
            "dish"=>$randDish,
            "date"=>$randDate,
            "time"=>$randTime,
            "status"=>"pending"
        ];
    }
}

/* ---------- HANDLE ACCEPT / REJECT ---------- */
if (isset($_GET['accept']) || isset($_GET['reject'])) {
    foreach ($_SESSION['demo_bookings'] as &$b) {
        if ($b['id'] == ($_GET['accept'] ?? $_GET['reject'])) {
            $b['status'] = isset($_GET['accept']) ? 'accepted' : 'rejected';
        }
    }
    header("Location: chef_bookings.php");
    exit;
}

/* ---------- GET BOOKINGS FOR THIS CHEF ---------- */
$chefBookings = array_filter($_SESSION['demo_bookings'], fn($b) => $b['chef_id'] == $chef_id);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Bookings | Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    margin:0;
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg, #1b0000, #400000);
    color:#fff;
    padding:20px;
}
h2{
    color:#ffd700;
    text-align:center;
    font-size:28px;
    margin-bottom:30px;
}
.card{
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:20px;
    padding:25px;
    margin-bottom:20px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover{
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(255,255,255,0.1);
}
.card b{
    font-weight:600;
}
.status{
    font-weight:bold;
    padding:5px 12px;
    border-radius:12px;
    font-size:14px;
}
.pending{background:#ffa500;color:#000;}
.accepted{background:#90ee90;color:#000;}
.rejected{background:#ff4d4d;color:#fff;}

.actions{
    margin-top:15px;
}
.actions a{
    display:inline-block;
    padding:10px 18px;
    margin-right:10px;
    border-radius:12px;
    font-weight:600;
    text-decoration:none;
    transition: transform 0.2s, box-shadow 0.2s;
}
.actions a.accept{
    background: linear-gradient(90deg, #00ff6a, #00b33c);
    color:#000;
}
.actions a.reject{
    background: linear-gradient(90deg, #ff4d4d, #ff0000);
    color:#fff;
}
.actions a:hover{
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(255,255,255,0.2);
}

.slot{
    display:inline-block;
    background: rgba(255,255,255,0.1);
    padding:5px 12px;
    border-radius:12px;
    font-size:14px;
    margin-top:5px;
}

/* Back to profile button */
.back-profile{
    display:block;
    text-align:center;
    margin:40px auto 0;
    padding:14px 25px;
    border-radius:14px;
    background: linear-gradient(90deg,#ffd700,#ffb347);
    color:#000;
    font-weight:700;
    text-decoration:none;
    width:200px;
    transition: transform 0.2s, box-shadow 0.2s;
}
.back-profile:hover{
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(255,255,255,0.2);
}
</style>
</head>
<body>

<h2>Booking Requests - Chef <?php echo htmlspecialchars($chef_name); ?></h2>

<?php if(empty($chefBookings)): ?>
    <p style="text-align:center;font-size:18px;">No bookings in your queue yet.</p>
<?php endif; ?>

<?php foreach($chefBookings as $b): ?>
<div class="card">
    <b>User:</b> <?php echo htmlspecialchars($b['user']); ?><br>
    <b>Dish:</b> <?php echo htmlspecialchars($b['dish']); ?><br>
    <span class="slot"><?php echo htmlspecialchars($b['date']." | ".$b['time']); ?></span><br>
    <b>Status:</b> <span class="status <?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span>

    <?php if($b['status']=="pending"): ?>
    <div class="actions">
        <a class="accept" href="?accept=<?php echo $b['id']; ?>">Accept</a>
        <a class="reject" href="?reject=<?php echo $b['id']; ?>">Reject</a>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<!-- Back to Profile Button -->
<a href="chef_profile.php" class="back-profile">Back to Profile</a>

</body>
</html>

<?php
session_start();

/* ---------- PROTECT CHEF ---------- */
if (!isset($_SESSION['chef_id'])) {
    header("Location: login.php");
    exit;
}

$chef_id   = $_SESSION['chef_id'];
$chef_name = $_SESSION['chef_name'] ?? "Chef";

/* ---------- ENSURE DATA FOLDER ---------- */
if(!is_dir("data")) mkdir("data", 0777, true);

/* ---------- FILE FOR CHEF DISHES ---------- */
$file = "data/chef_dishes_$chef_id.json";
if(!file_exists($file)) file_put_contents($file, json_encode([]));

/* ---------- LOAD DISHES ---------- */
$dishes = json_decode(file_get_contents($file), true);
if(!is_array($dishes)) $dishes = [];

/* ---------- GENERATE RANDOM DEMO DISHES IF EMPTY ---------- */
if(empty($dishes)) {
    $cuisines = ["Italian","Indian","Mexican","Japanese","French","Chinese"];
    $dishNames = ["Truffle Pasta","Sushi Platter","Butter Chicken","Ramen","Pizza Margherita","Tacos","Paneer Tikka","Pasta Alfredo"];
    for($i=1;$i<=5;$i++){
        $randCuisine = $cuisines[array_rand($cuisines)];
        $randDish = $dishNames[array_rand($dishNames)];
        $randPrice = rand(200,1000);
        $dishes[] = [
            "id"=>time()+$i,
            "cuisine"=>$randCuisine,
            "name"=>$randDish,
            "price"=>$randPrice,
            "image"=>""
        ];
    }
    file_put_contents($file,json_encode($dishes, JSON_PRETTY_PRINT));
}

$message = "";

/* ---------- ADD DISH ---------- */
if(isset($_POST['add_dish'])){
    $dish = [
        "id" => time(),
        "cuisine" => trim($_POST['cuisine']),
        "name" => trim($_POST['dish']),
        "price" => (int)$_POST['price'],
        "image" => ""
    ];

    if(!empty($_FILES['image']['name'])){
        $dish['image'] = time() . "_" . $_FILES['image']['name'];
        if(!is_dir("uploads/dishes")) mkdir("uploads/dishes",0777,true);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/dishes/".$dish['image']);
    }

    $dishes[] = $dish;
    file_put_contents($file,json_encode($dishes, JSON_PRETTY_PRINT));
    $message = "Dish added successfully ✅";
}

/* ---------- DELETE DISH ---------- */
if(isset($_GET['delete'])){
    $dishes = array_filter($dishes, fn($d)=>$d['id'] != $_GET['delete']);
    file_put_contents($file,json_encode(array_values($dishes), JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Dishes | Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
    margin:0;
    font-family:'Poppins',sans-serif;
    background: linear-gradient(135deg,#1b0000,#400000);
    color:#fff;
    padding:20px;
}
h2{
    text-align:center;
    font-size:28px;
    margin-bottom:20px;
    color:#ffd700;
}
.container{
    max-width:1100px;
    margin:0 auto;
}
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}
.card{
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(10px);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:20px;
    padding:25px;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover{
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(255,255,255,0.1);
}
input,button{
    width:100%;
    padding:12px;
    margin-top:10px;
    border-radius:12px;
    border:none;
    font-size:14px;
}
button{
    background: linear-gradient(90deg,#ffb347,#ffd700);
    font-weight:bold;
    cursor:pointer;
}
img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:12px;
}
.delete{
    display:inline-block;
    margin-top:10px;
    padding:10px 18px;
    border-radius:12px;
    background: linear-gradient(90deg,#ff4d4d,#ff0000);
    color:#fff;
    text-decoration:none;
    font-weight:600;
    transition:0.2s;
}
.delete:hover{
    transform: scale(1.05);
}
.msg{
    color:#90ee90;
    font-weight:bold;
    margin-bottom:10px;
}
.back-profile{
    display:block;
    margin:30px auto;
    text-align:center;
    padding:14px 35px;
    border-radius:12px;
    background: linear-gradient(90deg,#ffd700,#ffb347);
    color:#000;
    font-weight:bold;
    text-decoration:none;
    width:200px;
}
</style>
</head>
<body>
<div class="container">

<h2>Manage Dishes - Chef <?php echo htmlspecialchars($chef_name); ?></h2>

<div class="grid">

<!-- ADD DISH FORM -->
<div class="card">
<h3>Add New Dish</h3>
<?php if($message): ?><div class="msg"><?php echo $message; ?></div><?php endif; ?>
<form method="POST" enctype="multipart/form-data">
    <input name="cuisine" placeholder="Cuisine" required>
    <input name="dish" placeholder="Dish Name" required>
    <input name="price" type="number" placeholder="Price" required>
    <input type="file" name="image">
    <button name="add_dish">Add Dish</button>
</form>
</div>

<!-- EXISTING DISHES -->
<?php foreach($dishes as $d): ?>
<div class="card">
<?php if($d['image']): ?>
<img src="uploads/dishes/<?php echo $d['image']; ?>">
<?php endif; ?>
<h3><?php echo htmlspecialchars($d['name']); ?></h3>
<p><?php echo htmlspecialchars($d['cuisine']); ?></p>
<p>₹<?php echo $d['price']; ?></p>
<a class="delete" href="?delete=<?php echo $d['id']; ?>">Delete</a>
</div>
<?php endforeach; ?>

</div>

<a href="chef_profile.php" class="back-profile">Back to Profile</a>

</div>
</body>
</html>

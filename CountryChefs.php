<?php
$country = $_GET['country'] ?? 'Unknown';

/* ---------- RANDOM DATA ---------- */
$first_names = ["Alex","Liam","Emma","Olivia","Noah","Ava","Sophia","Ethan","Mia","Arjun","Priya","Li","Chen","Ivan","Olga"];
$last_names = ["Smith","Johnson","Brown","Taylor","Patel","Sharma","Li","Chen","Petrov","Smirnova","Mehra","Kapoor"];
$specialties = ["Italian","Indian","French","Japanese","Mexican","Chinese","Mediterranean","Continental"];

/* ---------- DISHES BY COUNTRY ---------- */
$country_dishes = [
    "India" => ["Butter Chicken","Paneer Tikka","Biryani","Masala Dosa","Chole Bhature"],
    "Italy" => ["Pizza Margherita","Pasta Alfredo","Risotto","Lasagna","Bruschetta"],
    "France" => ["Croissant","Ratatouille","Coq au Vin","Crème Brûlée","Bouillabaisse"],
    "Japan" => ["Sushi","Ramen","Tempura","Udon","Okonomiyaki"],
    "China" => ["Kung Pao Chicken","Dim Sum","Chow Mein","Sweet & Sour Pork","Spring Rolls"],
    "Mexico" => ["Tacos","Burrito","Quesadilla","Enchiladas","Guacamole"],
    "Default" => ["Signature Dish","Chef’s Special","House Special"]
];

/* ---------- GENERATE CHEFS ---------- */
$chefs = [];
for ($i = 0; $i < 3; $i++) {

    $chef_name = $first_names[array_rand($first_names)] . " " . $last_names[array_rand($last_names)];
    $chef_image = "https://loremflickr.com/400/400/chef?lock=" . rand(1, 1000);
    $specialty = $specialties[array_rand($specialties)];
    $rating = rand(3, 5) + (rand(0, 1) * 0.5);

    $dish_pool = $country_dishes[$country] ?? $country_dishes["Default"];
    shuffle($dish_pool);

    $chefs[] = [
        "chef_name" => $chef_name,
        "experience" => rand(5, 15),
        "cost" => rand(1500, 3000),
        "image" => $chef_image,
        "top" => $i === 0,
        "specialty" => $specialty,
        "rating" => $rating,
        "dishes" => array_slice($dish_pool, 0, rand(2, 3))
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($country); ?> Chefs</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    margin:0;
    background:linear-gradient(-45deg,#ffe6d6,#fff0e6,#fff0f0,#ffe6d6);
    background-size:400% 400%;
    animation:gradientBG 15s ease infinite;
}
@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}
.navbar{
    background:rgba(128,0,0,0.85);
    color:#fff;
    padding:15px 30px;
    display:flex;
    justify-content:space-between;
    position:sticky;
    top:0;
}
.navbar a{color:#fff;text-decoration:none;font-weight:600;}
.container{max-width:1200px;margin:30px auto;padding:20px;}
h1{text-align:center;color:#800000;font-size:36px;margin-bottom:40px;}

.chef-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}
.chef-card{
    background:rgba(255,255,255,0.2);
    backdrop-filter:blur(10px);
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
    transition:0.4s;
    position:relative;
}
.chef-card:hover{transform:translateY(-10px);}
.chef-card img{width:100%;height:200px;object-fit:cover;}

.chef-details{padding:20px;}
.chef-details h2{
    margin:0 0 8px;
    font-size:24px;
    background:linear-gradient(90deg,#ff8c00,#ffd700);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}

.badge{
    display:inline-block;
    padding:6px 12px;
    border-radius:14px;
    margin:4px 4px 4px 0;
    font-weight:bold;
    font-size:13px;
}
.exp-badge{background:#ffd700;}
.cost-badge{background:#ff8c00;color:#fff;}
.specialty-badge{background:#ff6347;color:#fff;}
.dish-badge{background:#fff;border:1px solid #ff8c00;color:#800000;}
.rating{color:#ffbf00;font-weight:bold;}

.top-badge{
    position:absolute;
    top:12px;
    left:12px;
    background:#ff4500;
    color:#fff;
    padding:6px 14px;
    border-radius:12px;
    font-size:13px;
}

/* BOOK SLOT BUTTON */
.book-btn{
    display:block;
    margin-top:18px;
    padding:14px;
    background:linear-gradient(90deg,#ff8c00,#ffd700);
    color:#000;
    text-align:center;
    font-weight:700;
    border-radius:16px;
    text-decoration:none;
    transition:0.3s;
}
.book-btn:hover{
    transform:scale(1.05);
}
</style>
</head>

<body>

<div class="navbar">
    <b>GLOBAL PLATTER</b>
    <a href="Chefs.php">Back to Map</a>
</div>

<div class="container">
<h1><?php echo htmlspecialchars($country); ?> Chefs</h1>

<div class="chef-grid">
<?php foreach($chefs as $chef): ?>
<div class="chef-card">

    <?php if($chef['top']): ?>
        <div class="top-badge">Top Chef</div>
    <?php endif; ?>

    <img src="<?php echo $chef['image']; ?>" alt="Chef">

    <div class="chef-details">
        <h2><?php echo $chef['chef_name']; ?></h2>

        <span class="badge exp-badge"><?php echo $chef['experience']; ?> yrs</span>
        <span class="badge cost-badge">₹<?php echo $chef['cost']; ?></span>
        <span class="badge specialty-badge"><?php echo $chef['specialty']; ?></span>
        <span class="rating">⭐ <?php echo $chef['rating']; ?></span>

        <p><b>Popular Dishes:</b></p>
        <?php foreach($chef['dishes'] as $dish): ?>
            <span class="badge dish-badge"><?php echo $dish; ?></span>
        <?php endforeach; ?>

        <!-- BOOK SLOT FIRST -->
        <a class="book-btn"
           href="book_slot.php?chef=<?php echo urlencode($chef['chef_name']); ?>&cost=<?php echo $chef['cost']; ?>&country=<?php echo urlencode($country); ?>">
            Book Slot
        </a>

    </div>
</div>
<?php endforeach; ?>
</div>
</div>

</body>
</html>

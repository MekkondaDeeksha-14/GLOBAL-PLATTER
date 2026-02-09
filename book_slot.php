<?php
$chef    = $_GET['chef'] ?? '';
$price   = $_GET['cost'] ?? 0;
$country = $_GET['country'] ?? '';

$dishes = [];
if (isset($_GET['dishes'])) {
    $dishes = json_decode($_GET['dishes'], true) ?? [];
}

if (empty($chef) || $price <= 0) {
    header("Location: Chefs.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Slot | Global Platter</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
body{
    font-family:'Poppins',sans-serif;
    margin:0;
    min-height:100vh;
    background:linear-gradient(-45deg,#ffe6d6,#fff0e6,#fff0f0,#ffe6d6);
    background-size:400% 400%;
    animation:gradientBG 15s ease infinite;
}
@keyframes gradientBG{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}
.container{
    max-width:600px;
    margin:50px auto;
    background:rgba(255,255,255,0.85);
    padding:35px;
    border-radius:20px;
}
label{font-weight:600;margin-top:20px;display:block;}
input,select{
    width:100%;
    padding:12px;
    margin-top:8px;
    border-radius:12px;
    border:1px solid #ccc;
}
.price-box{
    margin-top:25px;
    padding:15px;
    background:#fff3e6;
    border-radius:14px;
    text-align:center;
    font-weight:700;
}
button{
    width:100%;
    margin-top:30px;
    padding:15px;
    background:linear-gradient(90deg,#ff8c00,#ffd700);
    border:none;
    border-radius:18px;
    font-weight:700;
}
</style>

<script>
function updatePrice(){
    let base = <?php echo (int)$price; ?>;
    let duration = document.getElementById("duration").value;
    let multiplier = duration === "60" ? 1.5 : duration === "90" ? 2 : 1;
    let finalPrice = Math.round(base * multiplier);
    document.getElementById("finalPrice").innerText = "₹" + finalPrice;
    document.getElementById("priceInput").value = finalPrice;
}
</script>
</head>

<body>

<div class="container">
<h1>Book Your Slot</h1>
<p>Chef <b><?php echo htmlspecialchars($chef); ?></b> (<?php echo htmlspecialchars($country); ?>)</p>

<form action="payments.php" method="GET">

<input type="hidden" name="chef" value="<?php echo htmlspecialchars($chef); ?>">
<input type="hidden" name="country" value="<?php echo htmlspecialchars($country); ?>">
<input type="hidden" name="price" id="priceInput" value="<?php echo $price; ?>">

<label>Select Date</label>
<input type="date" name="date" required>

<label>Select Time Slot</label>
<select name="time" required>
    <option value="">-- Select Time --</option>
    <option>10:00 AM – 11:00 AM</option>
    <option>12:00 PM – 1:00 PM</option>
    <option>3:00 PM – 4:00 PM</option>
    <option>6:00 PM – 7:00 PM</option>
    <option>8:00 PM – 9:00 PM</option>
</select>

<label>Session Duration</label>
<select name="duration" id="duration" onchange="updatePrice()" required>
    <option value="30">30 Minutes</option>
    <option value="60">60 Minutes</option>
    <option value="90">90 Minutes</option>
</select>

<!-- ✅ DISHES AS DROPDOWN -->
<label>Select Dish</label>
<select name="dish" required>
    <option value="">-- Select Dish --</option>
    <?php foreach($dishes as $dish): ?>
        <option value="<?php echo htmlspecialchars($dish); ?>">
            <?php echo htmlspecialchars($dish); ?>
        </option>
    <?php endforeach; ?>
</select>

<div class="price-box">
Total Price: <span id="finalPrice">₹<?php echo $price; ?></span>
</div>

<button type="submit">Proceed to Payment</button>

</form>
</div>

</body>
</html>

<?php 
include 'db.php';

$message = "";
$user_id = 1;
$showPopup = false; // ✅ NEW

/* ========= GET BOOKING DETAILS ========= */
$chef_name = $_GET['chef'] ?? '';
$chef_cost = $_GET['price'] ?? '';

if ($chef_name === "" || $chef_cost === "" || !is_numeric($chef_cost) || $chef_cost <= 0) {
    die("❌ Booking amount not received. Please go back and try again.");
}

/* ================== UPI PAYMENT ================== */
if (isset($_POST['pay_upi'])) {
    $upi_app = $_POST['upi_app'] ?? '';
    $upi_id  = trim($_POST['upi_id'] ?? '');

    if ($upi_app == "" || $upi_id == "") {
        $message = "❌ Please select UPI app and enter UPI ID.";
    } elseif (!preg_match("/^[\w.\-]{2,}@[a-zA-Z]{2,}$/", $upi_id)) {
        $message = "❌ Invalid UPI ID format.";
    } else {
        $message = "✅ ₹".number_format($chef_cost,2)." paid successfully via $upi_app";
    }
}

/* ================== CARD PAYMENT ================== */
if (isset($_POST['pay_card'])) {
    $card_number = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
    $card_type   = $_POST['card_type'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';

    if (strlen($card_number) < 12 || !is_numeric($card_number)) {
        $message = "❌ Invalid card number.";
    } elseif (!preg_match("/^(0[1-9]|1[0-2])\/\d{2}$/", $expiry_date)) {
        $message = "❌ Invalid expiry format (MM/YY).";
    } else {
        $masked = "**** **** **** " . substr($card_number, -4);
        $message = "✅ ₹".number_format($chef_cost,2)." paid successfully using $card_type ($masked)";
    }
}

/* ================== PAY LATER ================== */
if (isset($_POST['pay_later'])) {
    if (empty($_POST['later_option'])) {
        $message = "❌ Please select a Pay Later option.";
    } else {
        $message = "✅ ₹".number_format($chef_cost,2)." booked using ".$_POST['later_option'];
        $showPopup = true; // ✅ TRIGGER POPUP
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment - Global Platter</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{font-family:Poppins,sans-serif;background:#fafafa;margin:20px}
.container{max-width:720px;margin:auto;background:#fff;padding:25px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,.1)}
h1,h2{text-align:center}
h2{margin-top:30px;border-bottom:2px solid #ffa500;padding-bottom:6px}
.total-box{text-align:center;background:#fff3e6;padding:18px;border-radius:10px;font-size:20px}
input,select,button{width:100%;padding:10px;margin:6px 0;border-radius:6px;border:1px solid #ccc}
button{background:linear-gradient(90deg,#ff8c00,#ffd700);border:none;font-weight:bold;cursor:pointer}
.msg{text-align:center;font-weight:bold;margin:10px 0}
.paylater{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.later{border:1px solid #ddd;padding:14px;border-radius:8px}
.back-btn{display:inline-block;margin-top:30px;padding:12px 25px;background:#800000;color:#fff;text-decoration:none;border-radius:8px}
</style>
</head>

<body>
<div class="container">

<h1>Payment Page</h1>

<div class="total-box">
👨‍🍳 <b><?= htmlspecialchars($chef_name) ?></b><br><br>
💰 Total Amount: <b>₹<?= number_format($chef_cost,2) ?></b>
</div>

<?php if($message): ?>
<p class="msg"><?= $message ?></p>
<?php endif; ?>

<!-- ================= UPI ================= -->
<h2>UPI Payment</h2>
<form method="POST">
<select name="upi_app" required>
    <option value="">Select UPI App</option>
    <option value="GPay">GPay</option>
    <option value="PhonePe">PhonePe</option>
    <option value="Paytm">Paytm</option>
    <option value="BHIM">BHIM</option>
</select>
<input type="text" name="upi_id" placeholder="example@upi" required>
<button name="pay_upi">Pay ₹<?= number_format($chef_cost,2) ?></button>
</form>

<!-- ================= CARD ================= -->
<h2>Card Payment</h2>
<form method="POST">
<input type="text" name="card_number" placeholder="Card Number" required>
<select name="card_type" required>
<option value="">Select Card Type</option>
<option>Visa</option>
<option>MasterCard</option>
<option>Rupay</option>
</select>
<input type="text" name="expiry_date" placeholder="MM/YY" required>
<button name="pay_card">Pay ₹<?= number_format($chef_cost,2) ?></button>
</form>

<!-- ================= PAY LATER ================= -->
<h2>Pay Later</h2>
<form method="POST">
<div class="paylater">
<label class="later"><input type="radio" name="later_option" value="Simpl"> Simpl</label>
<label class="later"><input type="radio" name="later_option" value="LazyPay"> LazyPay</label>
<label class="later"><input type="radio" name="later_option" value="ICICI PayLater"> ICICI PayLater</label>
<label class="later"><input type="radio" name="later_option" value="HDFC FlexiPay"> HDFC FlexiPay</label>
</div>
<button name="pay_later">Confirm Booking</button>
</form>

<div style="text-align:center">
<a href="Chefs.php" class="back-btn">⬅ Back to Chefs</a>
</div>

</div>

<!-- ✅ POPUP MESSAGE -->
<?php if ($showPopup): ?>
<script>
alert("📧 The booking details will be shared in your registered email.");
</script>
<?php endif; ?>

</body>
</html>

<?php
// about.php
include 'db.php'; // database connection file

$successMsg = $errorMsg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $feedback = trim($_POST['feedback']);

    if (!empty($name) && !empty($feedback)) {
        $stmt = $conn->prepare("INSERT INTO feedback (name, feedback) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $feedback);
        if ($stmt->execute()) {
            $successMsg = "✅ Thank you! Your feedback has been submitted.";
        } else {
            $errorMsg = "❌ Error: Could not save your feedback. Please try again.";
        }
        $stmt->close();
    } else {
        $errorMsg = "⚠️ Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Global Platter</title>
  <link rel="stylesheet" href="about.css">
  <style>
.about-container p {
  font-size: 18px;        /* increase font size */
  font-weight: 500;       /* slightly bold */
  line-height: 1.8;       /* better readability */
  color: #333;
  text-align: justify;
}

    .feedback-container {
      margin: 40px auto;
      max-width: 600px;
      background: #fff8f0;
      border: 2px solid #6b0000;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .feedback-container h2 {
      color: #6b0000;
      margin-bottom: 16px;
      text-align: center;
    }
    .feedback-container form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .feedback-container input,
    .feedback-container textarea {
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 18px;
    }
    .feedback-container button {
      background: #6b0000;
      color: #fff;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: 0.2s;
    }
    .feedback-container button:hover {
      background: #a30000;
    }
    .msg {
      text-align: center;
      margin-bottom: 12px;
      font-weight: bold;
    }
    .msg.success { color: green; }
    .msg.error { color: red; }
  </style>
</head>
<body>
  
  <!-- Top Navigation -->
  <header>
    <nav class="navbar">
      <!-- CLICKABLE LOGO -->
      <div class="logo">
        <a href="LandingPage.php" style="text-decoration:none; color:inherit;">
          <b>GLOBAL PLATTER</b>
        </a>
      </div>

      <ul class="nav-links">
        
        <li><a href="login.php">Login/Signup</a></li>
        <li><a href="profile.php">My Profile</a></li>
        <li><a href="meme_generator.php">Make a Meme</a></li>
        <li><a href="Chefs.php">Chefs</a></li>
        <li><a href="about.php" class="active">About Us</a></li>
        
      </ul>
    </nav>
  </header>

  <!-- About Section -->
  <div class="about-container">
    <h1>About Us</h1>
    <p>

At Global Platter, we believe cooking is more than just a skill — it is a story, a culture
and a legacy passed from one generation to the next. Our platform connects passionate food lovers 
with experienced culinary mentors and professional chefs from around the world, offering hands-on learning 
that goes beyond recipes. From traditional regional cuisines to modern fine-dining techniques, each chef 
shares their expertise, experience, and cultural insights. Whether you are a beginner exploring home cooking
or an aspiring professional refining advanced techniques, Global Platter ensures your culinary journey is personal, interactive
and immersive. Here, culinary traditions are celebrated, learning is practical, and food becomes a bridge between cultures.
At Global Platter, you don’t just learn how to cook — you learn the art, history, and soul of cuisine.
    </p>

  </div>

  <!-- Feedback Form -->
  <div class="feedback-container">
    <h2>💬 Share Your Feedback</h2>
    <?php if ($successMsg): ?>
      <div class="msg success"><?php echo $successMsg; ?></div>
    <?php elseif ($errorMsg): ?>
      <div class="msg error"><?php echo $errorMsg; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="text" name="name" placeholder="Your Name" required>
      <textarea name="feedback" rows="4" placeholder="Your Feedback..." required></textarea>
      <button type="submit">Submit Feedback</button>
    </form>
  </div>

</body>
</html>

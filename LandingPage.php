<?php 
// LandingPage.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Global Platter</title>

  <!-- Main CSS -->
  <link rel="stylesheet" href="LandingPage.css">

  <!-- Pristina font -->
  <link href="https://fonts.googleapis.com/css2?family=Pristina&display=swap" rel="stylesheet">

  <style>
    /* Title font override */
    .main-title {
      font-family: 'Pristina', cursive;
      font-style: italic;
      font-size: 6rem;
    }
  </style>
</head>

<body>

  <!-- Navigation -->
  <header>
    <nav class="navbar">
      <div class="logo">GLOBAL PLATTER</div>
      <ul class="nav-links">
        <li><a href="login.php">Login/Signup</a></li>
        <li><a href="meme_generator.php">Make a Meme</a></li>
        <li><a href="Chefs.php">Chefs</a></li>
        <li><a href="About.php">About Us</a></li>
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
  <div class="landing-container">
    <h1 class="main-title">{Beyond Borders, Beyond Flavours}</h1>

    <!-- Floating Icons -->
    <img class="food-image img1" src="assets/cuisines/sushi.png" alt="Sushi">
    <img class="food-image img2" src="assets/cuisines/curry.png" alt="Curry">
    <img class="food-image img3" src="assets/cuisines/taco.png" alt="Taco">
    <img class="food-image img4" src="assets/cuisines/dumpling.png" alt="Dumpling">
    <img class="food-image img5" src="assets/cuisines/salad.png" alt="Salad">
    <img class="food-image img6" src="assets/cuisines/baguette.png" alt="Baguette">
    <img class="food-image img7" src="assets/cuisines/steak.png" alt="Steak">
    <img class="food-image img8" src="assets/cuisines/pizza.png" alt="Pizza">
  </div>

</body>
</html>

<?php
session_start();
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================= LOGIN ================= */
    if (isset($_POST['login'])) {

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($role === "user") {
            $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email=?");
        } else {
            $stmt = $conn->prepare("SELECT id, chef_name AS username, email, password FROM chefs_login WHERE email=?");
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);

                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $role;

                if ($role === "user") {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    header("Location: profile.php");
                } else {
                    $_SESSION['chef_id'] = $row['id'];
                    $_SESSION['chef_name'] = $row['username'];
                    header("Location: chef_profile.php");
                }
                exit;
            } else {
                $message = "Incorrect password!";
            }
        } else {
            $message = ucfirst($role) . " not registered!";
        }
    }

    /* ================= SIGNUP ================= */
    if (isset($_POST['signup'])) {

        $role = $_POST['signup_role'];
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        if ($role === "user") {

            $username = trim($_POST['username']);

            $check = $conn->prepare("SELECT id FROM users WHERE email=?");
            $check->bind_param("s", $email);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows > 0) {
                $message = "User email already registered!";
            } else {

                $stmt = $conn->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
                $stmt->bind_param("sss", $username, $email, $password);

                $message = $stmt->execute()
                    ? "User signup successful! Please login."
                    : "Signup failed!";
            }

        } else {

            $chef_name = trim($_POST['chef_name']);

            $check = $conn->prepare("SELECT id FROM chefs_login WHERE email=?");
            $check->bind_param("s", $email);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows > 0) {
                $message = "Chef email already registered!";
            } else {

                $stmt = $conn->prepare("INSERT INTO chefs_login (chef_name,email,password) VALUES (?,?,?)");
                $stmt->bind_param("sss", $chef_name, $email, $password);

                $message = $stmt->execute()
                    ? "Chef signup successful! Please login."
                    : "Signup failed!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login | Global Platter</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="login.css">
</head>
<body>

<video autoplay loop muted id="background-video">
    <source src="assets/videos/waiter-serving.mp4" type="video/mp4">
</video>

<header class="navbar">
<div class="logo">
<a href="LandingPage.php" style="text-decoration:none;color:inherit;">
<b>GLOBAL PLATTER</b>
</a>
</div>
</header>

<div class="home-container">

<div class="hero">
<h1>Welcome!</h1>
<p>Login or Sign Up to continue enjoying Global Platter</p>

<div class="hero-buttons">
<button id="login-tab" class="btn">Login</button>
<button id="signup-tab" class="btn ghost">Sign Up</button>
</div>
</div>

<?php if ($message): ?>
<p class="message"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<!-- ================= LOGIN ================= -->
<form id="login-form" class="auth-form" method="POST">

<h3>Login As</h3>

<select name="role" required>
<option value="user">User</option>
<option value="chef">Chef</option>
</select>

<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="login" class="btn">Login</button>

</form>

<!-- ================= SIGNUP ================= -->
<form id="signup-form" class="auth-form" method="POST" style="display:none;">

<h3>Sign Up As</h3>

<select name="signup_role" id="signup-role" onchange="toggleSignupRole()" required>
<option value="user">User</option>
<option value="chef">Chef</option>
</select>

<!-- USER FIELDS -->
<div id="user-fields">
<input type="text" name="username" placeholder="Username">
</div>

<!-- CHEF FIELDS -->
<div id="chef-fields" style="display:none;">
<input type="text" name="chef_name" placeholder="Chef Name">
</div>

<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="signup" class="btn">Sign Up</button>

</form>

</div>

<script>
const loginTab = document.getElementById('login-tab');
const signupTab = document.getElementById('signup-tab');
const loginForm = document.getElementById('login-form');
const signupForm = document.getElementById('signup-form');

loginTab.onclick = () => {
loginForm.style.display = 'block';
signupForm.style.display = 'none';
};

signupTab.onclick = () => {
signupForm.style.display = 'block';
loginForm.style.display = 'none';
};

function toggleSignupRole() {
let role = document.getElementById('signup-role').value;

document.getElementById('user-fields').style.display =
role === 'user' ? 'block' : 'none';

document.getElementById('chef-fields').style.display =
role === 'chef' ? 'block' : 'none';
}
</script>

</body>
</html>
s
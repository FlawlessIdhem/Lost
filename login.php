<?php
session_start();

$error = "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=lost_db", "root", "spidermanlk7al");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = "Please enter both username and password.";
        } else {
            // Get user by username
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: LOST.php"); // Redirect after successful login
                exit();
            } else {
                $error = "Username or password is incorrect.";
            }
        }
    }

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOST</title>
    <link rel="stylesheet" href="LOST.css">
</head>
<body>
    <header>
        <div class="logo"><h2><a href="LOST.php">LOST</a></h2></div>
        <nav>
            <input type="checkbox" id="menu-toggle" class="menu-toggle">
            <label for="menu-toggle" class="hamburger">&#9776;</label>
        
            <ul class="nav-left">
                <li><a href="LOST.php#about" id="header" class="btn">Overview</a></li>
                <li><a href="LOST.php#cast" id="header1" class="btn">Cast</a></li>
                <li><a href="LOST.php#news" id="header2" class="btn">News</a></li>
                <li><a href="LOST.php#contact" id="header3" class="btn">Contact</a></li>
            </ul>
            <ul class="nav-right">
            
            <?php if (isset($_SESSION['username'])): ?>
  <div class="profile-container">
    <a href="#" class="profile-icon">
      <img src="https://img.icons8.com/?size=100&id=85050&format=png&color=FFFFFF" alt="Profile">
    </a>
    <div class="profile-menu">
      <div class="profile-header">
        <img src="https://img.icons8.com/?size=100&id=85050&format=png&color=FFFFFF" alt="Profile">
        <p class="profile-username"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
      </div>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <button class="profile-btn" onclick="window.location.href='characters.php'">Management (Admin Only)</button>
        <?php endif; ?>
      <button class="profile-btn" onclick="window.location.href='search.php'">Characters search</button>
      <button class="profile-btn" id="logout" onclick="confirmLogout(event)">Logout</button>
      <div class="language-selector">
            <select id="lang">
                <option value="en">English</option>
                <option value="ar">Arabic</option>
                <option value="de">German</option>
                <option value="fr">Francais</option>
            </select>
        </div>
    </div>
  </div>
<?php endif; ?>
        <script>
            function confirmLogout(event) {
                event.preventDefault();
                window.location.href = "logout.php";
            }
        </script>
                <li><a href="LOST.php#free-trial" id="header4" class="free2" class="btn">Free Trial</a></li>
                <li><a href="LOST.php#free-trial" id="header5" class="buy2" class="btn">Buy Now</a></li>
            </ul>
        </nav>
    </header>

    <section id="home" class="logpage">
    <div class="login-form">
        <h2 id="login">Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="input-field">
                <input type="text" name="username" required>
                <label id="enter_username">Enter your username</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required>
                <label id="enter_password">Enter your password</label>  
            </div>
            <div class="forget">
                <label for="remember">
                    <input type="checkbox" id="remember">
                    <p id="remember_me">Remember me</p>
                </label>
                <a href="#" id="forgot_password">Forgot password?</a>
            </div>
            <button type="submit" id="log_in">Log in</button>

            <div class="divider"> <hr> or <hr></div>
            <button class="google-btn"><i class="fa-brands fa-google"></i> Log in with Google</button>
            <button class="facebook-btn"><i class="fa-brands fa-facebook-f"></i> Log in with Facebook</button>
            <div class="register">
                <p id="register">
                    Don't have an account?
                    <a onclick="window.location.href='register.php'">Register</a>
                </p>
            </div>
        </form>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const icon = document.querySelector('.profile-icon');
  const menu = document.querySelector('.profile-menu');

  if (icon && menu) {
    icon.addEventListener('click', (e) => {
      e.preventDefault();
      menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
      if (!icon.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
      }
    });
  }
});
</script>
    <style>
        .nav-left li a::before {
            background-color: rgb(0, 87, 116);
        }
    </style>
</body>
</html>
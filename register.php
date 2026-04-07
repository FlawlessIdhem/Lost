<?php
session_start();

$errors = [];
$success = "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=lost_db", "root", "spidermanlk7al");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Get and sanitize inputs
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $errors[] = "Please fill in all fields.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email address.";
        }

        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email already exists.";
        }

        // If no errors, insert user
        if (empty($errors)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $hashedPassword]);

            $success = "Account created! <a href='login.php'>Login here</a>";
        }
    }
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
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
    <section id="home" class="regpage">
        <!-- Show Errors -->
         
   <?php if (!empty($errors)): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success"><?= $success ?></div>
<?php endif; ?>


            <div class="login-form">
        <h2 id="login">Register</h2>
        <form action="login.php" method="POST">
            <div class="input-field">
                <input type="text" name="username" required>
                <label id="enter_username">Username</label>
            </div>
            <div class="input-field">
                <input type="email" name="email" required>
                <label id="enter_email">Adresse e-mail</label>
            </div>
            <div class="input-field">
                <input type="password" name="password" required>
                <label id="enter_password">Password</label>
            </div>
            <div class="input-field">
                <input type="password" name="confirm_password" required>
                <label id="confirm_password">Confirm your password</label>
            </div>
            <div class="forget">
                <label for="remember">
                    <input type="checkbox" id="remember">
                    <p id="remember_me">I would like to receive news and promotional messages from Lost.</p>
                </label>
            </div>
            <button type="submit" id="log_in">Create an account</button>

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
        .login-form {
            width: 600px;
        }
    </style>
</body>
</html>

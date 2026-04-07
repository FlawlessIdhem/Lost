<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=lost_db;charset=utf8", "root", "spidermanlk7al");
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

$selectedFactionId = null;
$characters = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $selectedFactionId = $_POST['faction_id'];

    $stmt = $pdo->prepare("
        SELECT characters.*, factions.name AS faction_name
        FROM characters
        JOIN factions ON characters.faction_id = factions.id
        WHERE faction_id = ?
    ");
    $stmt->execute([$selectedFactionId]);
    $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$factions = $pdo->query("SELECT id, name FROM factions")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Characters - LOST</title>
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
      <button class="profile-btn" onclick="window.location.href='search.php'">characters search</button>
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
                <li><a href="LOST.php#free-trial" id="header4" class="free" class="btn">Free Trial</a></li>
                <li><a href="LOST.php#free-trial" id="header5" class="buy" class="btn">Buy Now</a></li>
            </ul>
        </nav>
    </header>

<main class="search-container">
    <h2 class="h22" id="h2c">Search For Characters</h2>

    <form method="POST" action="search.php" id="searchForm" class="search-form">
        <select name="faction_id" id="faction_id" onchange="document.getElementById('searchForm').submit();">
            <option value="">-- Select Faction --</option>
            <?php foreach ($factions as $faction): ?>
                <option value="<?= $faction['id'] ?>" <?= ($selectedFactionId == $faction['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($faction['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if (!empty($characters)): ?>
        <h3 class="h22">Characters</h3>
        <table class="character-table">
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Faction</th>
                <th>Image</th>
            </tr>
            <?php foreach ($characters as $char): ?>
                <tr>
                    <td><?= htmlspecialchars($char['name']) ?></td>
                    <td><?= htmlspecialchars($char['role']) ?></td>
                    <td><?= htmlspecialchars($char['faction_name']) ?></td>
                    <td>
                        <?php if (!empty($char['image_path'])): ?>
                            <img src="uploads/<?= htmlspecialchars($char['image_path']) ?>" width="60">
                        <?php else: ?>
                            No image
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($selectedFactionId !== null): ?>
        <p class="no-results">No characters found in this faction.</p>
    <?php endif; ?>
</main>

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
</body>
</html>

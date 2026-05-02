<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // redirect non-admins
    exit();
}
?>
<?php

try {
    require_once 'db.php';
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
// Add 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_character'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $faction_id = $_POST['faction_id'];

    $image_path = null;

    if (!empty($_FILES['image']['name'])) {



        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Create folder
        }

        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = $image_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO characters (name, role, faction_id, image_path) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $role, $faction_id, $image_path]);
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM characters WHERE id = ?");
    $stmt->execute([$id]);
}

// Update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_character'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $faction_id = $_POST['faction_id'];
    $stmt = $pdo->prepare("UPDATE characters SET name=?, role=?, faction_id=? WHERE id=?");
    $stmt->execute([$name, $role, $faction_id, $id]);

}
?>
<?php
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : null; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Character Management - LOST</title>
    <link rel="stylesheet" href="LOST.css">
</head>
<body>

<header>
        <div class="logo"><h2><a href="index.php">LOST</a></h2></div>
        <nav>
            <input type="checkbox" id="menu-toggle" class="menu-toggle">
            <label for="menu-toggle" class="hamburger">&#9776;</label>
        
            <ul class="nav-left">
                <li><a href="index.php#about" id="header" class="btn">Overview</a></li>
                <li><a href="index.php#cast" id="header1" class="btn">Cast</a></li>
                <li><a href="index.php#news" id="header2" class="btn">News</a></li>
                <li><a href="index.php#contact" id="header3" class="btn">Contact</a></li>
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
      <button class="profile-btn" onclick="window.location.href='characters.php'">Management (Admin Only)</button>
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
                <li><a href="index.php#free-trial" id="header4" class="free" class="btn">Free Trial</a></li>
                <li><a href="index.php#free-trial" id="header5" class="buy" class="btn">Buy Now</a></li>
            </ul>
        </nav>
    </header>

    <section id="see">
        <style>
            
        #see {
            position: relative;
            top:100px;
            text-align:center;
        }
        a {
        color: #00aaff;
        text-decoration: none;
        }
        .h2{
            padding:3px;
            border-top:2px solid;
            border-bottom:2px solid;
            display: inline-block;
            margin-bottom:80px; 
            color:white;
            text-shadow: 0px 0px 10px black,
                        0px 0px 20px black,
                        0px 0px 40px black,
                        0px 0px 80px black;
        }
        body {
            background: linear-gradient(135deg, #000000,rgb(49, 0, 0),rgb(0, 0, 0));
            background-attachment: fixed;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 125vh;
        }

        form input, select, button {
            padding: 10px;
            margin: 5px ;
            border-radius: 6px;
            border:none;
            font-size: 1em;
            width: 30%;
            margin:auto;
        }
        form input ,select{
            background-color: #111;
            border: 1px solid white;
            color:white;
            font-family: OCR A Std, monospace;
        }

        form button {
            background-color: darkred;
            color: black;
            cursor: pointer;
            margin-bottom:70px;
            transition: background-color 0.3s ease;
            font-family: Impact, fantasy;
            font-size:20px;
        }

        form button:hover {
            background-color: rgb(89, 9, 17);
        }
            
        table {
            width: 80%;
            margin:auto;
            border-collapse: collapse;
            margin-top: 20px;
            
        }

        table th, table td {
            border-top: 1px solid #444;
            padding: 10px;  
            text-align: center;
            
        }

        table th {
            border-top:none;
            padding:20px;
            color:darkred;
            font-family: Impact, fantasy;
            font-size:20px;
            font-weight:lighter;
        }

        img {
            border-radius: 6px;
        }
        table input,
        table select {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #555;
            background-color: #1e1e1e;
            color: #fff;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        table button[type="submit"] {
            background-color:rgb(0, 177, 12);
            color: black;
            padding: 10px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 18px;
            font-family: Impact, fantasy;
        }

        table button[type="submit"]:hover {
            background-color: #218838;
        }
        .a1, #a2 {
            background-color: darkred;
            color: black;
            padding: 10px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 18px;
            font-family: Impact, fantasy;
        }
        #a2{
        
        }
        #edit{
            background-color:orange;
        }
        #edit:hover{
            background-color:rgb(255, 153, 0);
        }
        .a1:hover {
            background-color:rgb(108, 0, 11);
        }
        #a2:hover {
            background-color: rgb(108, 0, 11);
        }
        input[type="file"] {
            display: none;
        }
        .custom-file-upload {
            padding: 8px 15px;
            width: 30%;
            cursor: pointer;
            background-color:rgb(218, 149, 0);
            color: black;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin:auto;
            margin-bottom:15px;
            font-family: Impact, fantasy;
            font-size:19px;
        }

        .custom-file-upload:hover {
            background-color: rgb(255, 136, 0);
        }

        </style>
        
        <h2 class="h2">Add New Character</h2>
        <form method="POST" action="characters.php" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Character Name" required><br>
            <input type="text" name="role" placeholder="Role"><br>

            
            <select name="faction_id" required>
                <option value="">Select Faction</option>
                <?php
                $stmt = $pdo->query("SELECT id, name FROM factions");
                while ($faction = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$faction['id']}'>{$faction['name']}</option>";
                }
                ?>
            </select><br>

            <label for="image" class="custom-file-upload">Choose Image</label>
            <input id="image" type="file" name="image">

            <button type="submit" name="add_character">Add Character</button>
        </form>

        <h2 class="h2">All Characters</h2>
        <table>
    <tr>
        <th>Name</th>
        <th>Role</th>
        <th>Faction</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php
    $stmt = $pdo->query("
        SELECT characters.*, factions.name AS faction_name
        FROM characters
        LEFT JOIN factions ON characters.faction_id = factions.id
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $isEditing = $edit_id == $row['id'];

        echo "<tr>";

        if ($isEditing) {
            echo "<form method='POST' action='characters.php'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <td><input type='text' name='name' value='{$row['name']}'></td>
                <td><input type='text' name='role' value='{$row['role']}'></td>
                <td><select name='faction_id'>";
                $factions = $pdo->query("SELECT id, name FROM factions");
                while ($faction = $factions->fetch(PDO::FETCH_ASSOC)) {
                    $selected = $faction['id'] == $row['faction_id'] ? "selected" : "";
                    echo "<option value='{$faction['id']}' $selected>{$faction['name']}</option>";
                }
                echo "</select></td>
                <td>";
            if ($row['image_path']) {
                echo "<img src='uploads/{$row['image_path']}' width='60'>";
            } else {
                echo "No image";
            }
            echo "</td>
                <td>
                    <button type='submit' name='update_character'>Save</button>
                    <a id='a2' href='characters.php'>Cancel</a>
                </td>
                </form>";
        } else {
            echo "<td>{$row['name']}</td>
                  <td>{$row['role']}</td>
                  <td>{$row['faction_name']}</td>
                  <td>";
            if ($row['image_path']) {
                echo "<img src='uploads/{$row['image_path']}' width='60'>";
            } else {
                echo "No image";
            }
            echo "</td>
                  <td>
                      <a class='a1' id='edit' href='characters.php?edit={$row['id']}'>Edit</a>
                      <a class='a1' href='characters.php?delete={$row['id']}' >Delete</a>
                  </td>";
        }

        echo "</tr>";
    }
    ?>
</table>
        
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
</body>
</html>

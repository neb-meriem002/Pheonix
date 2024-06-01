<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données (à adapter selon votre configuration)
$conn = new mysqli('localhost', 'root', '', 'todo_list');

// Vérifie la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Obtient l'ID de l'utilisateur
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$tasks = [];

if (isset($_POST['search'])) {
    $search = '%' . strtolower($_POST['search']) . '%';

    // Prépare et exécute la requête SQL pour la recherche
    $stmt = $conn->prepare("SELECT id, task, category_id FROM tasks WHERE user_id = ? AND LOWER(task) LIKE ?");
    $stmt->bind_param("is", $user_id, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style-table.css">
</head>
<body>


<header>
            <div class="logo">
                <img src="icon.png"/>
                <p>To-Do list</p>
            </div>
            <div class="logo2">
                <a href="index.php">Déconnecter</a>
            </div>

        </header>
        
        <div class="main">
    <div class="barre-cote" id="mySidenav">
    <button id="open" class="openbtn" onclick="openNav()"><img class="nav" src="menu.png"></button>
            <button id="close" class="closebtn" onclick="closeNav()"><img class="nav" src="close.png"></button>
        <?php
        $msg = "Bonjour ". $username . " !";
        ?>
        <div class="info">

            <h2  class="option" id="myHeader"><?php echo htmlspecialchars($msg); ?></h2>
            <h2 >Menu</h2>
        </div>
        <button class="button-add" id="openDialogBtn2">
            <a>
            <div id="hoverElement" class="org-bouton" >
                <img src="add.png">
                <p style="font-size:16px">Ajouter une note</p>
            </a>
        </button>
        <div id="hoverElement" class="org-bouton">
            <a href="search_task_.php">
                <img src="search.png">
                <p> Rechercher</p>
            </a>
        </div>
        <div id="hoverElement" class="org-bouton">
            <a href="add_task.php">
                <img src="icon.png">
                <p id="option">Tâches</p>
            </a>

        </div>

        <div id="hoverElement" class="org-bouton">
            <a href="notes.php">
                <img src="note.png">
                <p id="option">Notes</p>
            </a>
        
        </div>
        

        <div id="hoverElement" class="org-bouton">
            <a class="project" href="category.php">
                <img src="project.png">
                <p id="option"> Catégorie(s)</p>
                <div>
                    <button id="ajout_cat" type="button" class="prj"><img src="add-prj.png"></button>
                </div>

                <div>
                    <button type="button" class="prj"><img src="show-prj.png"></button>
                </div>
            </a>
        </div>

    </div>


    <script>

        function openNav() {
            var sidenav = document.getElementById("mySidenav");
            var element = document.getElementById("option");

            
            sidenav.style.width = "250px";
            sidenav.classList.add("open");

            var header = document.getElementById("myHeader");
            if (header) {
                header.style.display = "block";
            }
            var header = document.getElementById("open");
            if (header) {
                header.style.display = "none";
            }
            var header = document.getElementById("close");
            if (header) {
                header.style.display = "block";
            }
            var targetElements = document.querySelectorAll('.org-bouton');

            targetElements.forEach(hover => {
                hover.style.width = "210px";
            });
        }

        function closeNav() {
            var sidenav = document.getElementById("mySidenav");
            var element = document.getElementById("option");

            sidenav.style.width = "100px";
            sidenav.classList.remove("open");

            document.querySelector(".main").style.marginLeft = "0";
            var header = document.getElementById("myHeader");
            if (header) {
                header.style.display = "none";
            }
            var header = document.getElementById("close");
            if (header) {
                header.style.display = "none";
            }
            var header = document.getElementById("open");
            if (header) {
                header.style.display = "block";
            }
            var targetElements = document.querySelectorAll('.org-bouton');

            targetElements.forEach(hover => {
                hover.style.width = "50px";
            });
        }
    </script>



    <div class="contenu" id="main">
    <center><h2 class="titre"> Quelque chose à rechercher ?</h2></center>

        <!-- Search form again for convenience -->
        <form action="search_task_.php" method="POST">
            <center><input type="text" name="search" class="task_input" placeholder="Rechercher"></center>
            <center><button type="submit" class="search-btn">Chercher</button></center>
        </form>

        <center><h2 class="titre">Résultat(s):</h2></center>
        <ul>
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                    <li><?php echo htmlspecialchars($task['task']) . ' (' . htmlspecialchars($task['category_id']) . ')'; ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                
                <center><li class="titre">Pas de tâche trouvée.</li></center>
            <?php endif; ?>
        </ul>
        <p><center><a class="titre back-link" href="add_task.php">Retour aux menu tâches</a></center></p>
    </div>
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    color: #333;
    margin: 0;
    padding: 0;
}


/* Centered Content */
.contenu {
    display:flex;
    justify-content:center;
    width: 80%;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

/* Search Form */
form {
    display: flex;
    justify-content: center;
    flex-direction:column;
    margin-bottom: 20px;
}

.task_input {
    width: 60%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

.search-btn {
    padding: 10px 15px;
    border: none;
    background: rgb(237, 151, 151);
    color: #fff;
    width: 20%;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
}

.search-btn:hover {
    background: rgb(197, 69, 69);
}

/* Task List */
ul {
    
    list-style-type: none;
    padding: 0;
}

/* Links */
.back-link {
    width: 30%;
    display: block;
    margin-top: 20px;
    padding-top:10px;
    padding-bottom:10px;
    text-align: center;
    text-decoration: none;
    font-size: 20px;
}

.back-link:hover {
    text-decoration: underline;
}

/* No Tasks Found */
ul li.titre {
    color: #ff0000;
    font-weight: bold;
}
</style>
</body>
</html>

<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données (à adapter selon votre configuration)
$conn = new mysqli('localhost', 'root', '', 'todo_list');

$search = $_POST['search'] ?? '';

// Vérifie la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Requête avec jointure entre tasks et categories
$sql = "SELECT tasks.*, categories.category_name 
        FROM tasks 
        LEFT JOIN categories ON tasks.category_id = categories.id
        WHERE tasks.task LIKE ?";

$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

$stmt->close();
$conn->close();
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
                <!-- <div>
                    <button id="ajout_cat" type="button" class="prj"><img src="add-prj.png"></button>
                </div>

                <div>
                    <button type="button" class="prj"><img src="show-prj.png"></button>
                </div> -->
            </a>
        </div>

    </div>


    <script>

        function openNav() {
            var sidenav = document.getElementById("mySidenav");
            var element = document.getElementById("option");

            
            sidenav.style.width = "320px";
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



<div class="liste-tasks" id="main">
<div style="width:95%;">
    <center><h2 class="titre">Quelque chose à rechercher ?</h2></center>

    <!-- Formulaire de recherche -->
    <form action="search_task_.php" method="POST">
        <center><input type="text" name="search" class="task_input" placeholder="Rechercher"></center>
        <center><button type="submit" class="search-btn">Chercher</button></center>
    </form>




        <?php 
        // Vérifier si une recherche a été effectuée
        if (isset($_POST['search']) && !empty($_POST['search'])): 
        ?>

            <?php if (!empty($tasks)): ?>
                <center><h3 class="titre">Résultat(s)</h3></center>
                <div class="liste" id="wid-tab">
                <table>
                    <thead class="thead2" id="thd">
                        <tr>
                            <th>N°</th>
                            <th>Tâche</th>
                            <th style="width: 150px;" >Catégorie</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($tasks as $row):
                            $etat = $row['etat'] ?? 'Not_Done'; // Valeur par défaut
                            $category_name = $row['category_name'] ?? 'Sans catégorie'; // Valeur par défaut
                            $task_style = ($etat == 'Done') ? 'text-decoration: line-through;' : '';
                            $image_src = ($etat == 'Done') ? 'done.png' : 'undone.png';
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                        
                            <form method="POST" action="search_task_.php" class="task-form">
                                <input type="hidden" name="task_id_edit" value="<?php echo $row['id']; ?>">
                        
                                <!-- Nom de la tâche -->
                                <td>
                                    <span class="task-text" style="<?php echo $task_style; ?>">
                                        <?php echo htmlspecialchars($row['task']); ?>
                                    </span>
                                    <input type="text" name="task_edit" class="edit-task-input" 
                                        style="display: none;" value="<?php echo htmlspecialchars($row['task']); ?>">
                                </td>
                        
                                <td><?php echo htmlspecialchars($category_name); ?></td>
                        
                                
                            </form>
                        </tr>
                        <?php
                            $i++;
                        endforeach;
                        
                        ?>
                    </tbody>
                </table>
                    </div>   
            <?php else: ?>
                <center><h3 class="titre">Résultat(s) :  Pas de tâche trouvée.</h3></center>
            <?php endif; ?>
        <?php endif; ?>

        <!-- <p><center><a class="titre back-link" href="add_task.php">Retour au menu tâches</a></center></p> -->
    </div>
    </div>
    <script>
        document.querySelectorAll('.edit-task-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr'); // Find the closest row
                const taskText = row.querySelector('.task-text');
                const editInput = row.querySelector('.edit-task-input');
                const saveButton = row.querySelector('.submit-edit-task');
                const editTaskBtn = row.querySelector('.edit-task-btn');

                // Show edit input field and hide task text
                editInput.style.display = 'inline-block';
                editInput.focus(); // Auto-focus on input
                taskText.style.display = 'none';
                saveButton.style.display = 'inline-block';
                editTaskBtn.style.display = 'none';
            });
        });
    </script>

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

</style>
</body>
</html>

<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Connexion à la base de données (à adapter selon votre configuration)
$db = new mysqli('localhost', 'root', '', 'todo_list');

// Vérifie la connexion
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$username = $_SESSION['username'];

// Obtient l'ID de l'utilisateur
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$stmt->close();

// initialize errors variable
$errors = "";

// insert a note if submit button is clicked
if (isset($_POST['submit_note'])) {
    if (empty($_POST['note_text'])) {
        $errors = "You must fill in the note";
    } else {
        $note_text = $_POST['note_text'];
        $category_id = $_POST['category_id']; // Assuming you have a category_id in your form
        // Prepare and bind
        $stmt = $db->prepare("INSERT INTO notes (note_text, category_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $note_text, $category_id, $user_id);
        // Execute the statement
        $stmt->execute();
        $stmt->close();
        header('location: notes.php');
    }
}

// insert a category if submit button is clicked
if (isset($_POST['submit_category'])) {
    if (empty($_POST['category_name'])) {
        $errors = "You must fill in the category name";
    } else {
        $category_name = $_POST['category_name'];
        // Prepare and bind
        $stmt = $db->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $category_name, $user_id);
        // Execute the statement
        $stmt->execute();
        $stmt->close();
        header('location: notes.php');
    }
}

// delete note
if (isset($_POST['delete_note'])) {
    $note_id = $_POST['note_id_delete'];
    // Prepare and bind
    $stmt = $db->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $note_id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: notes.php');
}

// edit note
if (isset($_POST['edit_note'])) {
    $note_id = $_POST['note_id_edit'];
    $edited_note_text = $_POST['note_edit'];
    // Prepare and bind
    $stmt = $db->prepare("UPDATE notes SET note_text = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $edited_note_text, $note_id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: notes.php');
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>To Do list</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style-table.css">
    <link rel="stylesheet" href="notes-php.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: 'Arial', sans-serif;;
        }
    </style>
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
            </div>
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
<div class="liste-tasks">
<div style="width:98%;">
    <center><h2 class="titre">Sticky Notes</h2></center>
    <div id="wid-tab" class="note" style="width:100%">
        <?php
        // Replace 'your_database_hostname', 'your_database_username', 'your_database_password', and 'your_database_name' with your actual database credentials
        $mysqli = new mysqli('localhost', 'root', '', 'todo_list');

        // Check connection
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        // Fetch user's notes with dates and categories
        $sql = "SELECT notes.*, categories.category_name AS category_name FROM notes LEFT JOIN categories ON notes.category_id = categories.id WHERE notes.user_id = $user_id";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            echo '<div class="sticky-note-container">'; // Open container div
            while($row = $result->fetch_assoc()) {
                // Styling for the sticky note
                echo '<div class="sticky-note">';
                // Note content
                echo '<p style="margin-bottom:20px;">' . $row["note_text"] . '</p>';
                // Date
                echo '<p>Date: ' . $row["date_creation"] . '</p>';
                // Category
                // echo '<p>Category: ' . $row["category_name"] . '</p>';
                // Delete note form
                ?>
                                        <form action="notes.php" method="post">
                                        <button type="submit" name="delete_note" title="Delete Note">
                                        <input type="hidden" name="note_id_delete" value="<?php echo $row['id']; ?>">
                                            <img src="delete.png" width="20">
                                        </button>
            </form>
                                        <?php
                echo "</div>"; // Close sticky-note div
            }
            echo '</div>'; // Close container div
        } else {
            echo '<center><h2 style="font-size:20px" class="titre">Pas encore de Notes enrengistrées.</h2></center> ';
        }
        ?>
        <!-- Add Note Form -->
        <!-- Add Category Form -->
       
    </div>
</div>
        </div>
    <!-- <div class="button-container2">
        <button class="button-add" id="openDialogBtn">
            <img src="add.png" width="70">
        </button>
    </div> -->
</div>
   

        <script>
                document.addEventListener("DOMContentLoaded", function() {
                const openDialogBtn2 = document.getElementById("openDialogBtn2");
                const openDialogBtn = document.getElementById("openDialogBtn");
                const closeDialogBtn = document.getElementById("closeDialogBtn");
                const dialog = document.getElementById("dialog");

                // Function to open the dialog
                openDialogBtn2.addEventListener("click", function() {
                    dialog.classList.add("show");
                });

                openDialogBtn.addEventListener("click", function() {
                    dialog.classList.add("show");
                });

                // Function to close the dialog
                closeDialogBtn.addEventListener("click", function() {
                    dialog.classList.remove("show");
                });

                // Close the dialog when clicking outside of the dialog box
                window.addEventListener("click", function(event) {
                    if (event.target === dialog) {
                        dialog.classList.remove("show");
                    }
                });
            });
        </script>

                <div id="dialog" class="dialog-overlay">
                    <div class="dialog-box">
                        <span class="close-btn" id="closeDialogBtn">&times;</span>
                        <h2>
                            Entrez votre note
                        </h2>
                        <p><br></p>
                        <form action="notes.php" method="post">
                                    <input type="text" name="note_text" placeholder="Enter note" class="task_input">
                                    <!-- Assuming you have a category dropdown or input field -->
                                    <input type="submit" name="submit_note" value="Add Note" class="add_btn">
                        </form>
                        
                    </div>
                </div>

    </body>
</html>


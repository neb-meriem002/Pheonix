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

// insert a quote if submit button is clicked
if (isset($_POST['submit'])) {
    if (empty($_POST['task'])) {
        $errors = "You must fill in the task";
    } else {
        $task = $_POST['task'];
        $category_id = $_POST['category_id']; // Assuming you have a category_id in your form
        // Prepare and bind
        $stmt = $db->prepare("INSERT INTO tasks (task, category_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $task, $category_id, $user_id);
        // Execute the statement
        $stmt->execute();
        $stmt->close();
        header('location: add_task.php');
    }
}

// insert a category if submit button is clicked
if (isset($_POST['category_btn'])) {
    if (empty($_POST['category_input'])) {
        $errors = "You must fill in the task";
    } else {
        $category = $_POST['category_input'];
        // Prepare and bind
        $stmt = $db->prepare("INSERT INTO categories ( name, user_id) VALUES ( ?, ?)");
        $stmt->bind_param("si", $category, $user_id);
        // Execute the statement
        $stmt->execute();
        $stmt->close();
        header('location: add_task.php');
    }
}

// delete task
if (isset($_POST['del_task'])) {
    $id = $_POST['task_id_delete'];
    // Prepare and bind
    $stmt = $db->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: add_task.php');
}

// done task
if (isset($_POST['done_task'])) {
    $id = $_POST['task_id_done'];
    // Prepare and bind
    $stmt = $db->prepare("UPDATE tasks SET etat = IF(etat='Done', 'Not_Done', 'Done') WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: add_task.php');
}

// edit task
if (isset($_POST['edit_task'])) {
    $id = $_POST['task_id_edit'];
    $edited_task_name = $_POST['task_edit'];
    // Prepare and bind
    $stmt = $db->prepare("UPDATE tasks SET task = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $edited_task_name, $id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: add_task.php');
}


?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>To Do list</title>
        <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="style-table.css">
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
                <p style="font-size:16px">Ajouter une tâche</p>
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
            
        
        <?php
            // select all tasks if page is visited or refreshed
            $tasks = mysqli_query($db, "SELECT tasks.*, categories.category_name 
            FROM tasks 
            LEFT JOIN categories ON tasks.category_id = categories.id 
            WHERE tasks.user_id = '$user_id'");

            if ($tasks) {
                if (mysqli_num_rows($tasks) == 0) {
                    echo '<center><h2 style="font-size:20px" class="titre">Pas encore de tâches enrengistrées.</h2></center>.';
                    ?>
                    <div class="button-container">
                    <button class="button-add" id="openDialogBtn"><img src="add.png" width="70"> <p>Ajouter une nouvelle tâche</p></button>
                    </div>
            <?php
                    add_button();
            }   else {
                add_button();
        ?>
        
            <div class="liste-tasks" id="main">
                
                <div style="width:95%;">
                            <center><h2 class="titre">ToDo List :  Qu'a-t-on à faire ?</h2></center>
                                <div class="liste" id="wid-tab">
                                
                                    <table>
                                        <thead class="thead2" id="thd">
                                            <tr>
                                                <th>N</th>
                                                <th>Tâche</th>
                                                <th>Categorie</th>
                                                <th style="width: 90px;">Action</th>
                                            </tr>
                                        </thead>
                
                                        




                                        <tbody>
                                    <?php
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($tasks)) {
                                        $etat = $row['etat'];
                                        $task_style = ($etat == 'Done') ? 'text-decoration: line-through;' : '';
                                        $image_src = ($etat == 'Done') ? 'done.png' : 'undone.png';
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>

                                        <form method="POST" action="add_task.php" class="task-form">
                                            <input type="hidden" name="task_id_edit" value="<?php echo $row['id']; ?>">

                                            <!-- Task Name Cell -->
                                            <td>
                                                <!-- Task Display Text -->
                                                <span class="task-text" style="<?php echo $task_style; ?>"><?php echo htmlspecialchars($row['task']); ?></span>

                                                <!-- Task Edit Input (Hidden Initially) -->
                                                <input type="text" name="task_edit" class="edit-task-input" 
                                                    style="display: none;" value="<?php echo htmlspecialchars($row['task']); ?>">
                                            </td>

                                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>

                                            <!-- Other Actions -->
                                            <td>
                                                <!-- Delete Button -->
                                                <input type="hidden" name="task_id_delete" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="del_task" title="Delete Task">
                                                    <img src="delete.png" width="20">
                                                </button>

                                                <!-- Mark as Done Button -->
                                                <input type="hidden" name="task_id_done" value="<?php echo $row['id']; ?>">
                                                <button type="submit" name="done_task" title="Mark as Done">
                                                    <img src="<?php echo $image_src; ?>" width="20">
                                                </button>

                                                <!-- Edit Button -->
                                                <button type="button" class="edit-task-btn" title="Edit Task">
                                                    <img src="edit.png" width="20">
                                                </button>

                                                <!-- Submit Edit Button (Hidden Initially) -->
                                                <button type="submit" name="edit_task" class="submit-edit-task" 
                                                        style="display: none;" title="Save Changes">
                                                    <img src="edit-done.png" width="20">
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
            </div>
        </div> 
        <?php
    }
} else {
    echo "Error executing query: " . mysqli_error($db);
}
?>

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

                    

    </div>
    </div>

    </body>
</html>

<?php
    function add_button()
    {
        ?>
       
          
    
       <div id="dialog" class="dialog-overlay">
    <div class="dialog-box">
        <span class="close-btn" id="closeDialogBtn">&times;</span>
        <h2>Entrez votre tâche</h2>
        <p><br></p>
        <form method="post" action="add_task.php" class="input_form">
            <?php if (isset($errors)) { ?>
                <p><?php echo $errors; ?></p>
            <?php } ?>
            <div class="input-class">
                <input type="text" name="task" class="task_input" required>
                <select name="category_id" class="category_input task_input" required>
                    <option value="">Choisir une categorie</option>
                    <?php
                    $conn = new mysqli('localhost', 'root', '', 'todo_list');
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $result = $conn->query("SELECT id, category_name FROM categories");
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['category_name'] . "</option>";
                    }
                    $conn->close();
                    ?>
                </select>
                <button type="submit" name="submit" id="add_btn" class="add_btn">Ajouter</button>
            </div>
        </form>
    </div>
</div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
        const openDialogBtn2 = document.getElementById("openDialogBtn2");
        const openDialogBtn = document.getElementById("openDialogBtn");
        const closeDialogBtn = document.getElementById("closeDialogBtn");
        const dialog = document.getElementById("dialog");
        const thd = document.getElementById("thd");

        // Function to open the dialog safely
        function openDialog() {
            if (dialog) {
                dialog.classList.add("show");
                if (thd) thd.classList.remove("thead2");
            }
        }

        // Attach event listeners only if the elements exist
        if (openDialogBtn) {
            openDialogBtn.addEventListener("click", openDialog);
        }

        if (openDialogBtn2) {
            openDialogBtn2.addEventListener("click", openDialog);
        }

        // Function to close the dialog
        if (closeDialogBtn) {
            closeDialogBtn.addEventListener("click", function() {
                if (dialog) {
                    dialog.classList.remove("show");
                    if (thd) thd.classList.add("thead2");
                }
            });
        }

        // Close the dialog when clicking outside the box
        window.addEventListener("click", function(event) {
            if (event.target === dialog) {
                dialog.classList.remove("show");
                if (thd) thd.classList.add("thead2");
            }
        });
    });

    </script>

    <?php
    }
    ?>
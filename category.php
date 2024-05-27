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
    if (empty($_POST['name'])) {
        $errors = "You must fill in the task";
    } else {
        $task = $_POST['name'];
        $stmt = $db->prepare("INSERT INTO categories (name, user_id) VALUES ( ?, ?)");
        $stmt->bind_param("si", $task, $user_id);
        // Execute the statement
        $stmt->execute();
        $stmt->close();
        header('location: category.php');
    }
}

// delete task
if (isset($_POST['del_task'])) {
    $id = $_POST['task_id_delete'];
    // Prepare and bind
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: category.php');
}

// edit task
if (isset($_POST['edit_task'])) {
    $id = $_POST['task_id_edit'];
    $edited_task_name = $_POST['task_edit'];
    // Prepare and bind
    $stmt = $db->prepare("UPDATE categories SET name = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $edited_task_name, $id, $user_id);
    // Execute the statement
    $stmt->execute();
    $stmt->close();
    header('location: category.php');
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

                <a href="#">Editer profil</a>
            </div>

        </header>
        <div class="main">
            
            <div class="barre-cote" id="mySidenav">
            <button id="open" class="openbtn" onclick="openNav()">Open Sidebar</button>
            <button id="close" class="closebtn" onclick="closeNav()">Closing</button>
                <?php
                    $msg = "Bonjour ". $username . " !";
                ?>
                <div class="info">
                
                    <h2  class="option" id="myHeader"><?php echo htmlspecialchars($msg); ?></h2>
                    <h2 >Menu</h2>
                </div>
                <button class="button-add" id="openDialogBtn2">
                    <div id="hoverElement" class="org-bouton">
                            <img src="add.png">
                            <p>Ajouter une categorie</p>
                    </div>
                 </button>
                <div id="hoverElement" class="org-bouton"> 
                    <a href="#">
                    <img src="search.png">  
                    <p> Rechercher</p>
                </a>
                </div>
                <div id="hoverElement" class="org-bouton"> 
                    <a href="notes.php">
                        <img src="note.png"> 
                        <p id="option">Notes</p>
                    </a>
                
                </div>
                <div id="hoverElement" class="org-bouton"> 
                    <a class="project" href="#">
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
                    var tab = document.getElementById("wid-tab");
                    var thd = document.getElementById("thd");
                    
                    thd.classList.remove("thead2");
                    sidenav.style.width = "310px";
                    sidenav.classList.add("open");
                    tab.style.width = "850px";

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
                    var tab = document.getElementById("wid-tab");
                    var thd = document.getElementById("thd");
                    
                    thd.classList.add("thead2");
                    sidenav.style.width = "100px";
                    sidenav.classList.remove("open");
                    tab.style.width = "1000px";

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
            


        
        <?php
            // select all tasks if page is visited or refreshed
            $tasks = mysqli_query($db, "SELECT * FROM categories WHERE user_id = $user_id");

            if ($tasks) {
                if (mysqli_num_rows($tasks) == 0) {
                    echo "The table is empty.";
                    ?>
                    <div class="button-container">
                    <button class="button-add" id="openDialogBtn"><img src="add.png" width="70"> <p>Ajouter une nouvelle categorie</p></button>
                    </div>
            <?php
                    category_button();
            }   else {
                category_button();
        ?>
        
            <div class="liste-tasks">
                
                <div style="width:90%;">
                <center><h2 class="titre">ToDo List :  Qu'a-t-on à faire ?</h2></center>
                    <div class="liste" id="wid-tab">
                    
                        <table>
                            <thead class="thead2" id="thd">
                                <tr>
                                    <th>N</th>
                                    <th>Tasks</th>
                                    <th style="width: 90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($tasks)) {
                                ?>
                                            
                                <tr>
                                <td><?php echo $i; ?></td>
                                <td>
                                    <span class="task-text"><?php echo htmlspecialchars($row['note_text']); ?></span>
                                    <input type="text" name="task_edit" class="edit-task-input" style="display: none;" value="<?php echo htmlspecialchars($row['note_text']); ?>">
                                </td>
                                <td>
                                    <form method="POST" action="add_task.php" class="task-form">
                                        <input type="hidden" name="task_id_delete" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="del_task" title="Delete Task">
                                            <img src="delete.png" width="20">
                                        </button>

                                        <input type="hidden" name="task_id_edit" value="<?php echo $row['id']; ?>">
                                        <button type="button" class="edit-task-btn" title="Edit Task">
                                            <img src="edit.png" width="20">
                                        </button>
                                        
                                        <button type="submit" name="edit_task" class="submit-edit-task" style="display: none;" title="Save Changes">
                                            <img src="edit-done.png" width="20">
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                        </div>
        </div>

        <div class="button-container2">
            <button class="button-add" id="openDialogBtn">
                <img src="add.png" width="70">
            </button>
        </div>
        <?php
    }
} else {
    echo "Error executing query: " . mysqli_error($db);
}
?>

<script>
    // JavaScript to toggle edit input field
    document.querySelectorAll('.edit-task-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskRow = this.closest('tr');
            const taskText = taskRow.querySelector('.task-text');
            const editInput = taskRow.querySelector('.edit-task-input');
            const saveButton = taskRow.querySelector('.submit-edit-task');
            const editTaskBtn = taskRow.querySelector('.edit-task-btn');

            // Show edit input field and hide task text
            editInput.value = taskText.textContent.trim();
            taskText.style.display = 'none';
            editInput.style.display = 'block';
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
            <h2>
                Entrez votre tâche
            </h2>
            <p><br></p>
            <form method="post" action="category.php" class="input_form">
                <?php if (isset($errors)) { ?>
                    <p><?php echo $errors; ?></p>
                <?php } ?>
                <div class="input-class">
                    <input type="text" name="task" class="task_input">
                    <button type="submit" name="submit" id="add_btn" class="add_btn">
                        Ajouter
                    </button>
                </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const openDialogBtn2 = document.getElementById("openDialogBtn2");
    const openDialogBtn = document.getElementById("openDialogBtn");
    const closeDialogBtn = document.getElementById("closeDialogBtn");
    const dialog = document.getElementById("dialog");
    const thd = document.getElementById("thd");

    // Function to open the dialog
    openDialogBtn.addEventListener("click", function() {
        dialog.classList.add("show");
        thd.classList.remove("thead2");
    });

    openDialogBtn2.addEventListener("click", function() {
        dialog.classList.add("show");
        thd.classList.remove("thead2");
    });

    // Function to close the dialog
    closeDialogBtn.addEventListener("click", function() {
        dialog.classList.remove("show");
        thd.classList.add("thead2");
    });

    // Close the dialog when clicking outside of the dialog box
    window.addEventListener("click", function(event) {
        if (event.target === dialog) {
            dialog.classList.remove("show");
            thd.classList.add("thead2");
        }
    });
});
</script>
<div id="categ" class="dialog-overlay">
                    <div class="dialog-box">
                        <span class="close-btn" id="closeDialogBtn2">&times;</span>
                        <h2>
                            hello
                        </h2>
                        <p><br></p>
                        <!-- <form method="post" action="add_task.php" class="input_form">
                            <?php if (isset($errors)) { ?>
                                <p><?php echo $errors; ?></p>
                            <?php } ?>
                            <div class="input-class">
                                <input type="text" name="task" class="task_input">
                                <button type="submit" name="submit" id="add_btn" class="add_btn">
                                    Ajouter
                                </button>
                            </div> -->
                    </div>
                </div>
                    <script>
                         document.addEventListener("DOMContentLoaded", function() {
                        const ajout_cat = document.getElementById("ajout_cat");
                        const dialog2 = document.getElementById("categ");
                        const closeDialogBtn2 = document.getElementById("closeDialogBtn2");
                        ajout_cat.addEventListener("click", function() {
                            dialog2.classList.add("show");
                                });

                            closeDialogBtn2.addEventListener("click", function() {
                            dialog2.classList.remove("show");
                                });

                         });
                        
                    </script>

    <?php
    }
    ?>
<?php
function category_button()
{
    ?>
    <button id="showCategoryForm">Add category</button>
    <div class = category-block>
        <form method="post" action="add_task.php" class="input_form">
        <?php if (isset($errors)) { ?>
            <p><?php echo $errors; ?></p>
        <?php } ?>
        <input type="text" name="category_input" class="category_input">
        <button type="submit" name="category_btn" id="category_btn" class="add_category">Add category</button>
        </form>
    </div> <!-- close the task-block div -->

    <!-- JavaScript to show the category form -->
    <script>
        document.getElementById('showCategoryForm').addEventListener('click', function() {
            document.querySelector('.category-block').style.display = 'block'; // Corrected class name
        });
    </script>
<?php
}
?>

<?php
function category_button2()
{
    ?>
    <div class = category-block>
        <form method="post" action="add_task.php" class="input_form">
        <?php if (isset($errors)) { ?>
            <p><?php echo $errors; ?></p>
        <?php } ?>
        <input type="text" name="category_input" class="category_input">
        <button type="submit" name="category_btn" id="category_btn" class="add_category">Add category</button>
        </form>
    </div> <!-- close the task-block div -->

    <!-- JavaScript to show the category form -->
    <script>
        document.getElementById('showCategoryForm').addEventListener('click', function() {
            document.querySelector('.category-block').style.display = 'block'; // Corrected class name
        });
    </script>
<?php
}
?>

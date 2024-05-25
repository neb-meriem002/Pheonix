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
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>To Do list</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300&display=swap" rel="stylesheet">
        <style>
            body{
                font-family: 'Work Sans';

            }
        </style>
    </head>
    <body>
        <header>
            <img src="icon.png"/>
            <p>To-Do list</p>

        </header>
        <div class="main">
            <div class="barre-cote">
                <h2>Menu</h2>
                <div class="org-bouton">
                    <a href="add_task.html">
                        <img src="add.png">
                        <p>Ajouter une tache</p>
                    </a>
                </div>
                <div class="org-bouton"> 
                    <a href="#">
                    <img src="search.png">  
                    <p> Rechercher</p>
                </a>
                </div>
                <div class="org-bouton"> 
                    <a href="notes.html">
                        <img src="note.png"> 
                        <p>Notes</p>
                    </a>
                
                </div>
                <div class="org-bouton "> 
                    <a class="project" href="#">
                    <img src="project.png">  
                    <p> Projet(s)</p>
                    <div><button type="button" class="prj"><img src="add-prj.png"></button></div>
                    <div><button type="button" class="prj"><img src="show-prj.png"></button></div>
                    </a>
                </div>



            </div>
            
        <div class="contenu">
            <div>
                <h2>ToDo List Application PHP and MySQL database</h2>
            </div>


        
        <?php
            // select all tasks if page is visited or refreshed
            $tasks = mysqli_query($db, "SELECT * FROM tasks");

            if ($tasks) {
                if (mysqli_num_rows($tasks) == 0) {
                    echo "The table is empty.";
                    add_button();
            }   else {
        ?>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>N</th>
                            <th>Tasks</th>
                            <th style="width: 60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($tasks)) {
                            // Check if the task is 'Done' or 'Not_Done'
                            $etat = $row['etat'];
                            $task_style = ($etat == 'Done') ? 'text-decoration: line-through;' : ''; // Apply line-through style to 'Done' tasks
                        ?>
                                    
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td style="<?php echo $task_style; ?>"><?php echo $row['task']; ?></td>
                            <td>
                                <form method="POST" action="add_task.php">
                                    <input type="hidden" name="task_id_delete" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="del_task">x</button>
                                    <input type="hidden" name="task_id_done" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="done_task">D</button>
                                </form>
                            </td>
                            </tr>
                            <?php
                                $i++;
                                }
                                add_button();
                            }
                            ?>
                    </tbody>
                </table>
            </div>
        <?php
            } else {
                echo "Error executing query: " . mysqli_error($db);
            }
        ?>
                    

    </div>
    </div>

    </body>
</html>

<?php
    function add_button()
    {
        ?>
        <button id="showTaskForm">Add Task</button>
        <div class="task-block">
            <form method="post" action="add_task.php" class="input_form">
            <?php if (isset($errors)) { ?>
                <p><?php echo $errors; ?></p>
            <?php } ?>
            <input type="text" name="task" class="task_input">
            <button type="submit" name="submit" id="add_btn" class="add_btn">Add Task</button>
            </form>
            </div>
                    

            <script>
            document.getElementById('showTaskForm').addEventListener('click', function() {
            document.querySelector('.task-block').style.display = 'block';
            });
            </script>
    <?php
    }
    ?>
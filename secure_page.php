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

// Prépare et exécute la requête SQL
$stmt = $conn->prepare("SELECT id, task, category_id FROM tasks WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

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
    <title>To Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>To Do List</h1>

        <form action="add_task.php" method="POST">
            <input type="text" name="task" placeholder="Enter task" required>
            <select name="category">
                <option value="">Select category</option>
                <option value="Personal">Personal</option>
                <option value="Work">Work</option>
                <option value="Study">Study</option>
                <!-- Add more categories as needed -->
            </select>
            <button type="submit">Add Task</button>
        </form>

        <h2>Your Tasks</h2>
        <ul>
            <?php foreach ($tasks as $task): ?>
                <li><?php echo htmlspecialchars($task['task']) . ' (' . htmlspecialchars($task['category_id']) . ')'; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>

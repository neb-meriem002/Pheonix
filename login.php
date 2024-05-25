<?php
// Vérifie si les champs sont renseignés
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connexion à la base de données (à adapter selon votre configuration)
    $conn = new mysqli('localhost', 'root', '', 'todo_list');

    // Vérifie la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Vérifie les informations de connexion
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // Compare directement les mots de passe en clair
            // Démarre une session et redirige l'utilisateur vers une page sécurisée
            session_start();
            $_SESSION['username'] = $username;
            header('Location: add_task.php');
            exit;
        } else {
            // Mot de passe incorrect
            header('Location: index.php?error=login');
            exit;
        }
    } else {
        // Nom d'utilisateur incorrect
        header('Location: index.php?error=login');
        exit;
    }

    // Ferme la connexion à la base de données
    $stmt->close();
    $conn->close();
} else {
    // Redirige l'utilisateur vers la page de connexion si les champs ne sont pas renseignés
    header('Location: index.php');
    exit;
}
?>

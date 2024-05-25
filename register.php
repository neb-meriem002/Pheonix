<?php
// Vérifie si les champs sont renseignés
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password']; // Ne pas hacher le mot de passe

    // Connexion à la base de données (à adapter selon votre configuration)
    $conn = new mysqli('localhost', 'root', '', 'todo_list');

    // Vérifie la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Vérifie si l'utilisateur existe déjà
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirige l'utilisateur vers la page d'inscription avec un message d'erreur
        header('Location: index.php?action=signup&error=exists');
        exit;
    } else {
        // Insère le nouvel utilisateur dans la base de données
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            // Redirige l'utilisateur vers la page de connexion avec un message de succès
            header('Location: index.php?success=register');
            exit;
        } else {
            // Redirige l'utilisateur vers la page d'inscription avec un message d'erreur
            header('Location: index.php?action=signup&error=register');
            exit;
        }
    }

    // Ferme la connexion à la base de données
    $stmt->close();
    $conn->close();
} else {
    // Redirige l'utilisateur vers la page d'inscription si les champs ne sont pas renseignés
    header('Location: index.php?action=signup');
    exit;
}
?>

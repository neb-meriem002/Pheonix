<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion et Inscription</title>
    <link rel="stylesheet" href="style-php.css">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300&display=swap" rel="stylesheet">
    <style>/* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

</style>
</head>
<body>
    <div class="container">
        <?php
        // Afficher les messages d'erreur ou de succès
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'login') {
                echo '<p class="error">Nom d\'utilisateur ou mot de passe incorrect.</p>';
            } elseif ($_GET['error'] == 'exists') {
                echo '<p class="error">Nom d\'utilisateur déjà pris. Veuillez en choisir un autre.</p>';
            } elseif ($_GET['error'] == 'register') {
                echo '<p class="error">Erreur lors de l\'inscription. Veuillez réessayer.</p>';
            }
        }

        if (isset($_GET['success'])) {
            if ($_GET['success'] == 'register') {
                echo '<p class="success">Inscription réussie. Vous pouvez maintenant vous connecter.</p>';
            }
        }
        ?>

        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'signup') {
            // Afficher le formulaire d'inscription
            ?>
            <form class="signup-form" action="register.php" method="POST">
                <h2>Inscription</h2>
                <div class="form-group">
                    <label for="new-username">Nom d'utilisateur :</label>
                    <input type="text" id="new-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="new-password">Mot de passe :</label>
                    <input type="password" id="new-password" name="password" required>
                </div>
                <button type="submit">S'inscrire</button>
                <p>Déjà un compte ? <a href="index.php">Se connecter</a></p>
            </form>
            <?php
        } else {
            // Afficher le formulaire de connexion
            ?>
            <form class="login-form" action="login.php" method="POST">
                <h2>Connexion</h2>
                <div class="form-group">
                    <label for="username">Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Se Connecter</button>
                <p>Pas encore de compte ? <a href="index.php?action=signup">S'inscrire</a></p>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>

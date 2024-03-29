<?php
 // Démarrer la session (si elle n'est pas déjà active)
     if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion d'utilisateur</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
         integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
         crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
         integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
         crossorigin=""></script>

    <script src="https://unpkg.com/leaflet@latest/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-providers@latest/leaflet-providers.js"></script>

    <link rel="stylesheet" href="./templates/assets/css/style.css">
    <script src="./templates/assets/js/main.js" defer></script>
    <script src="functions/api.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <?php
                    // Si l'utilisateur n'est pas connecté, afficher les liens d'inscription et de connexion
                    if (!isset($_SESSION['user_id'])) {
                        echo '<li><a href="index.php?action=register">Inscription</a></li>';
                        echo '<li><a href="index.php?action=login">Connexion</a></li>';
                    } else {
                        // Si l'utilisateur est connecté, afficher les liens du tableau de bord et de déconnexion
                        echo '<li><a href="index.php?action=dashboard">Tableau de bord</a></li>';
                        echo '<li><a href="index.php?action=update">Modifier mes informations</a></li>';
                        echo '<li><a href="index.php?action=logout">Déconnexion</a></li>';
                    }
                ?>
            </ul>
        </nav>
    </header>
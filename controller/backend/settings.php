<?php 

function settings() {
    
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();
    $settingsManager = new SettingsManager();

    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: index.php?action=connection");
        exit();
    } 

    // Récupère le rôle de l'utilisateur
    $userRole = $usersManager->getRole($_SESSION["userID"]);

    if ($userRole != 1) {
        header("Location: index.php");
        exit();
    }

    if (!empty($_POST)) {
        $validation = true;
        $settings = new settings([
            "blog_name" => $_POST["blog_name"],
            "admin_email" => $_POST["admin_email"],
            "default_role" => $_POST["default_role"],
            "moderation" =>  isset($_POST["moderation"]) ? true : false,
            "posts_by_row" => $_POST["posts_by_row"],
        ]);
        // Vérifie si le nom du blog ne fait pas plus de 50 caractères
        if (iconv_strlen($settings->blog_name()) > 50) {
            $session->setFlash("Le nom du blog est trop long (maximum 50 caractères)", "danger");
            $validation = false;
        }
        // Vérifie si l'adresse email est correcte
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $settings->admin_email())) {
            $session->setFlash("L'adresse \"" .$settings->admin_email() . "\" est incorrecte.", "danger");
            $validation = false;
        }
        // Met à jour les données si validation est vrai
        if ($validation == true) {
            $settingsManager->update($settings);
            $session->setFlash("Les paramètres ont été mis à jour.", "success");
        }  
    } else  {
    // Récupère les paramètres
    $settings = $settingsManager->get();
    }
  
    require "view/backend/settingsView.php";
}
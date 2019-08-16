<?php 


function user() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();

    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: blog");
        exit();
    } else {
        // Récupère le rôle de l'utilisateur
        $userRole = $usersManager->getRole($_SESSION["userID"]);
        if ($userRole != 1) {
            header("Location: blog");
            exit();
        }
        // Récupère le rôle de l'utilisateur
        if ($_GET["id"] == $_SESSION["userID"]) {
            header("Location: profil");
            exit();
        }
    }
    // Mettre à jour les informations du profil
    if (!empty($_POST) && !empty($_POST["role"])) {
        $validation = true;  

        // Met à jour les informations du profil si validation est vraie
        if ($validation) {
            $user = new Users([
                "id" => $_GET["id"],
                "role" => $_POST["role"]
            ]);
            $usersManager->updateRole($user);
            $session->setFlash("Le profil a été mis à jour.", "success");
        }
    }

    // Récupère l'utilisateur
    $user = $usersManager->get($_GET["id"]); 

    require "view/backend/userView.php";
}

<?php 
function user() {
    $session = new model\Session();
    $usersManager = new model\UsersManager();

    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["user"]["id"])) {
        header("Location: connection");
        exit();
    } else {
        // Récupère le rôle de l'utilisateur
        $userRole = $usersManager->getRole($_SESSION["user"]["id"]);
        if ($userRole != 1) {
            header("Location: blog");
            exit();
        }
        // Récupère le rôle de l'utilisateur
        if ($_GET["id"] == $_SESSION["user"]["id"]) {
            header("Location: profil");
            exit();
        }
    }

    // Vérifie si l'utilisateur existe
    $isUserExists = $usersManager->exists($_GET["id"]);
    if (!$isUserExists) {
        $session->setFlash("Cet utilisateur n'existe pas.", "warning");
        header("Location: blog"); 
        exit();
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

    $user = $usersManager->get($_GET["id"]);

    require "view/backend/userView.php";
}

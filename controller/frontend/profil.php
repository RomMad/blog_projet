<?php 
function profil() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();

    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION["user"]["id"])) {
        header("Location: connection");
        exit();
    } else {
        // Récupère les informations de l'utilisateur
        $user = $usersManager->get($_SESSION["user"]["id"]);
    }
    if (isset($_GET["delete_cookies"])) {
        $cookies = [
            "orderBy[adminComments]",
            "orderBy[adminPosts]",
            "orderBy[adminPosts]", 
            "order[adminComments]",
            "order[adminPosts]", 
            "order[adminUsers]", 
            "pagination[nbDisplayed_adminComments]", 
            "pagination[nbDisplayed_adminPosts]",
            "pagination[nbDisplayed_adminUsers]", 
            "pagination[nbDisplayed_comments]", 
            "pagination[nbDisplayed_posts]"
        ];
        foreach($cookies as $cookie) {
            setcookie($cookie, "", time() - 3600, null, null, false, false);
        }
        $session->setFlash("Tous les cookies ont été supprimés.", "success");
    }
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $validation = true;
        
        // Mettre à jour les informations du profil
        if (isset($_POST["login"])) {
            $user = new Users([
                "id" => $_SESSION["user"]["id"],
                "login" => $_POST["login"],
                "email" => $_POST["email"],
                "name" => $_POST["name"],
                "surname" => $_POST["surname"],
                "birthdate" => $_POST["birthdate"],
                "role_user" => $_POST["role"]
            ]);
            // Compare le pass envoyé via le formulaire avec la base
            $isPasswordCorrect = password_verify($_POST["pass"], $usersManager->getPass($_SESSION["user"]["id"])); 
            // Vérifie si le login est déjà pris par un autre utilisateur
            $loginUsed = $usersManager->count("login = '" . $user->login() . "' AND u.id != " . $_SESSION["user"]["id"]);
            // Vérifie si l'email est déjà pris par un autre utilisateur
            $emailUsed = $usersManager->count("email = '" . $user->email() . "' AND u.id != " . $_SESSION["user"]["id"]);
            // Vérifie si le champ login est vide
            if (empty($user->login())) {
                $session->setFlash("Veuillez saisir un login.", "danger");
                $validation = false;
            }
            // Vérifie si le login est déjà pris par un autre utilisateur
            elseif ($loginUsed) {
                $session->setFlash("Ce login est déjà utilisé. Veuillez en choisir un autre.", "danger");
                $validation = false;
            }
            // Vérifie si le champ login est vide
            if (empty($user->email())) {
                $session->setFlash("L'adresse email est obligatoire.", "danger");
                $validation = false;
            }
            // Vérifie si l'email est correct
            elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user->email())) {
                $session->setFlash("L'adresse email " . $user->email() . " est incorrecte.", "danger");
                $validation = false;
            }
            // Vérifie si l'email est déjà pris par un autre utilisateur
            elseif ($emailUsed) {
                $session->setFlash("Cette adresse email est déjà utilisée.", "danger");
                $validation = false;
            }
            // Vérifie si le champ mot de passe est vide
            if (empty($_POST["pass"])) {
                $session->setFlash("Veuillez saisir votre mot de passe.", "danger");
                $validation = false;
            }
            // Vérifie si le mot de passe est correct
            elseif (!$isPasswordCorrect) {
                $session->setFlash("Le mot de passe est incorrect.", "danger");
                $validation = false;
            }
            // Vérifie si le champ de confirmation du mot de passe est vide
            if (empty($_POST["pass_confirm"])) {
                $session->setFlash("Veuillez saisir la confirmation de votre mot de passe.", "danger");
                $validation = false;
            }
            // Vérifie si la confirmation du mot de passe est identique
            elseif ($_POST["pass"] != $_POST["pass_confirm"]) {
                $session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
                $validation = false;
            }
            // Met à jour les informations du profil si validation est vraie
            if ($validation) {
                $usersManager->updateProfil($user);
                $_SESSION["user"]["login"] = $user->login();
                $session->setFlash("Le profil a été mis à jour.", "success");
            }
        }

        // Mettre à jour le mot de passe
        if (isset($_POST["old_pass"])) {
            // Compare le mot de passe envoyé via le formulaire avec la base
            $isPasswordCorrect = password_verify($_POST["old_pass"], $usersManager->getPass($_SESSION["user"]["id"])); 
            // Vérifie si le champ ancien mot de passe est vide
            if (empty(($_POST["old_pass"]))) {
                $session->setFlash("Veuillez saisir votre ancien mot de passe.", "danger");
                $validation = false;
            }
            // Vérifie si l'ancien mot de passe est correct   
            elseif (!$isPasswordCorrect) {
                $session->setFlash("L'ancien mot de passe est incorrect.", "danger");
                $validation = false;
            }
            // Vérifie si le champ nouveau mot de passe est vide
            if (empty($_POST["new_pass"])) {
                $session->setFlash("Veuillez saisir votre nouveau mot de passe.", "danger");
                $validation = false;
            }
            // Vérifie si le nouveau mot de passe est valide (entre 6 et 20 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre, 1 caractère spécial)
            elseif (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,20}$#", $_POST["new_pass"])) {
                $session->setFlash("Le nouveau mot de passe n'est pas valide.", "danger");
                $validation = false;
            }
            // Vérifie si le champ confirmation nouveau mot de passe est vide
            if (empty($_POST["new_pass_confirm"])) {
                $session->setFlash("Veuillez saisir la confirmation de votre nouveau mot de passe.", "danger");
                $validation = false;
            }       
            // Vérifie si la confirmation du mot de passe est identique
            elseif ($_POST["new_pass"] != $_POST["new_pass_confirm"]) {
                $session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
                $validation = false;
            }
            // Met à jour le mot de passe si validation est vraie
            if ($validation) {
                $newPassHash = password_hash($_POST["new_pass"], PASSWORD_DEFAULT); // Hachage du mot de passe
                $user = new Users([
                    "id" => $_SESSION["user"]["id"],
                    "pass" => $newPassHash
                ]);
                $usersManager->updatePass($user);
                $session->setFlash("Le mot de passe a été mis à jour.", "success");      
                // Récupère les informations de l'utilisateur
                $user = $usersManager->get($_SESSION["user"]["id"]);
            }
        }
    }
    require "view/frontend/profilView.php";
}
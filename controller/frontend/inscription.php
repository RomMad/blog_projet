<?php 
function inscription() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();

    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $user = new Users([
            "login" => htmlspecialchars($_POST["login"]),
            "name" => htmlspecialchars($_POST["name"]),
            "surname" => htmlspecialchars($_POST["surname"]),
            "email" => htmlspecialchars($_POST["email"]),
            "birthdate" => !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL,
            "pass" => htmlspecialchars($_POST["pass"])
        ]);

        $validation = true;

        // Vérifie si le login est déjà utilisé
        $loginUsed = $usersManager->count(" u.login = '" . $user->login() . "'");
        // Vérifie si l'adresse email est déjà utilisée
        $emailUsed = $usersManager->count(" u.email = '" . $user->email() . "'");

        // Vérifie si le champ login est vide
        if (empty($user->login())) {
            $session->setFlash("Veuillez saisir un Login.", "danger");
            $validation = false;
        }
        // Vérifie si le login est déjà utilisé
        elseif ($loginUsed) {
            $session->setFlash("Ce login est déjà utilisé. Veuillez en utiliser un autre.", "danger");
            $validation = false;
        }
        // Vérifie si l'adresse email est vide
        if (empty($user->email())) {
            $session->setFlash("L'adresse email est vide.", "danger");
            $validation = false;
        } 
        // Vérifie si l'adresse email est correcte
        elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user->email())) {
            $session->setFlash("L'adresse \"" .$user->email() . "\" est incorrecte.", "danger");
            $validation = false;
        }
        // Vérifie si l'adresse email est déjà utilisée
        elseif ($emailUsed) {
            $session->setFlash("L'adresse email est déjà utilisée.", "danger");
            $validation = false;
        }
        // Vérifie si le mot de passe est correct
        // (?=.*[a-z])  : teste la présence d'une lettre minuscule
        // (?=.*[A-Z])  : teste la présence d'une lettre majuscule
        // (?=.*[0-9])  : teste la présence d'un chiffre de 0 à 9
        // .{6,}$       : teste si au moins 6 caractères
        if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $user->pass())) {
            $session->setFlash("Le mot de passe n'est pas valide.", "danger");
            $validation = false;
        }
        // Vérifie si la confirmation du mot de passe est identique
        elseif (empty($_POST["pass_confirm"])) {
            $session->setFlash("La confirmation du mot de passe est vide.", "danger");
            $validation = false;
        }
        // Vérifie si la confirmation du mot de passe est identique
        elseif ($user->pass()!=$_POST["pass_confirm"]) {
            $session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
            $validation = false;
        }
        // Si validation est vrai, valide l'inscription de l'utilisateur
        if ($validation) {
            // Hachage du mot de passe
            $passHash = password_hash($user->pass(), PASSWORD_DEFAULT); 
            $user->SetPass($passHash);
            // Insert les données dans la table users
            $usersManager->add($user);
            // Récupère l'ID de l'utilisateur
            $user = $usersManager->verify($user->login());

            // Ajoute les infos de l"utilisateurs dans la Session
            $_SESSION["userID"] =  $user->id();
            $_SESSION["userLogin"] = $user->login();
            $_SESSION["userRole"] = $user->role();

            $session->setFlash("L'inscription est réussie.", "success");
            header("Location: index.php");
            exit();
        }
    }
    require "view/frontend/inscriptionView.php";
}
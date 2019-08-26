<?php 
namespace controller\frontend;

class ProfilController {

    protected   $_session,
                $_usersManager,
                $_user;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->init();
    }

    protected function init() {
        // Redirige vers la page de connexion si non connecté
        if (empty($_SESSION["user"]["id"])) {
            header("Location: connection");
            exit();
        } else {
            // Récupère les informations de l'utilisateur
            $this->_user = $this->_usersManager->get($_SESSION["user"]["id"]);
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
            $this->_session->setFlash("Tous les cookies ont été supprimés.", "success");
        }
        // Vérifie si informations dans variable POST
        if (!empty($_POST)) {
            $validation = true;
            
            // Mettre à jour les informations du profil
            if (isset($_POST["login"])) {
                $this->_user = new \model\Users([
                    "id" => $_SESSION["user"]["id"],
                    "login" => $_POST["login"],
                    "email" => $_POST["email"],
                    "name" => $_POST["name"],
                    "surname" => $_POST["surname"],
                    "birthdate" => $_POST["birthdate"],
                    "role_user" => $_POST["role"]
                ]);
                // Compare le pass envoyé via le formulaire avec la base
                $isPasswordCorrect = password_verify($_POST["pass"], $this->_usersManager->getPass($_SESSION["user"]["id"])); 
                // Vérifie si le login est déjà pris par un autre utilisateur
                $loginUsed = $this->_usersManager->count("login = '" . $this->_user->login() . "' AND u.id != " . $_SESSION["user"]["id"]);
                // Vérifie si l'email est déjà pris par un autre utilisateur
                $emailUsed = $this->_usersManager->count("email = '" . $this->_user->email() . "' AND u.id != " . $_SESSION["user"]["id"]);
                // Vérifie si le champ login est vide
                if (empty($this->_user->login())) {
                    $this->_session->setFlash("Veuillez saisir un login.", "danger");
                    $validation = false;
                }
                // Vérifie si le login est déjà pris par un autre utilisateur
                elseif ($loginUsed) {
                    $this->_session->setFlash("Ce login est déjà utilisé. Veuillez en choisir un autre.", "danger");
                    $validation = false;
                }
                // Vérifie si le champ login est vide
                if (empty($this->_user->email())) {
                    $this->_session->setFlash("L'adresse email est obligatoire.", "danger");
                    $validation = false;
                }
                // Vérifie si l'email est correct
                elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->_user->email())) {
                    $this->_session->setFlash("L'adresse email " . $this->_user->email() . " est incorrecte.", "danger");
                    $validation = false;
                }
                // Vérifie si l'email est déjà pris par un autre utilisateur
                elseif ($emailUsed) {
                    $this->_session->setFlash("Cette adresse email est déjà utilisée.", "danger");
                    $validation = false;
                }
                // Vérifie si le champ mot de passe est vide
                if (empty($_POST["pass"])) {
                    $this->_session->setFlash("Veuillez saisir votre mot de passe.", "danger");
                    $validation = false;
                }
                // Vérifie si le mot de passe est correct
                elseif (!$isPasswordCorrect) {
                    $this->_session->setFlash("Le mot de passe est incorrect.", "danger");
                    $validation = false;
                }
                // Vérifie si le champ de confirmation du mot de passe est vide
                if (empty($_POST["pass_confirm"])) {
                    $this->_session->setFlash("Veuillez saisir la confirmation de votre mot de passe.", "danger");
                    $validation = false;
                }
                // Vérifie si la confirmation du mot de passe est identique
                elseif ($_POST["pass"] != $_POST["pass_confirm"]) {
                    $this->_session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
                    $validation = false;
                }
                // Met à jour les informations du profil si validation est vraie
                if ($validation) {
                    $this->_usersManager->updateProfil($this->_user);
                    $_SESSION["user"]["login"] = $this->_user->login();
                    $this->_session->setFlash("Le profil a été mis à jour.", "success");
                }
            }

            // Mettre à jour le mot de passe
            if (isset($_POST["old_pass"])) {
                // Compare le mot de passe envoyé via le formulaire avec la base
                $isPasswordCorrect = password_verify($_POST["old_pass"], $this->_usersManager->getPass($_SESSION["user"]["id"])); 
                // Vérifie si le champ ancien mot de passe est vide
                if (empty(($_POST["old_pass"]))) {
                    $this->_session->setFlash("Veuillez saisir votre ancien mot de passe.", "danger");
                    $validation = false;
                }
                // Vérifie si l'ancien mot de passe est correct   
                elseif (!$isPasswordCorrect) {
                    $this->_session->setFlash("L'ancien mot de passe est incorrect.", "danger");
                    $validation = false;
                }
                // Vérifie si le champ nouveau mot de passe est vide
                if (empty($_POST["new_pass"])) {
                    $this->_session->setFlash("Veuillez saisir votre nouveau mot de passe.", "danger");
                    $validation = false;
                }
                // Vérifie si le nouveau mot de passe est valide (entre 6 et 20 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre, 1 caractère spécial)
                elseif (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,20}$#", $_POST["new_pass"])) {
                    $this->_session->setFlash("Le nouveau mot de passe n'est pas valide.", "danger");
                    $validation = false;
                }
                // Vérifie si le champ confirmation nouveau mot de passe est vide
                if (empty($_POST["new_pass_confirm"])) {
                    $this->_session->setFlash("Veuillez saisir la confirmation de votre nouveau mot de passe.", "danger");
                    $validation = false;
                }       
                // Vérifie si la confirmation du mot de passe est identique
                elseif ($_POST["new_pass"] != $_POST["new_pass_confirm"]) {
                    $this->_session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
                    $validation = false;
                }
                // Met à jour le mot de passe si validation est vraie
                if ($validation) {
                    $newPassHash = password_hash($_POST["new_pass"], PASSWORD_DEFAULT); // Hachage du mot de passe
                    $this->_user = new \model\Users([
                        "id" => $_SESSION["user"]["id"],
                        "pass" => $newPassHash
                    ]);
                    $this->_usersManager->updatePass($this->_user);
                    $this->_session->setFlash("Le mot de passe a été mis à jour.", "success");      
                    // Récupère les informations de l'utilisateur
                    $this->_user = $this->_usersManager->get($_SESSION["user"]["id"]);
                }
            }
        }
        require "view/frontend/profilView.php";
    }
}
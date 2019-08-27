<?php 
namespace controller\frontend;

class InscriptionController {

    protected   $_session,
                $_usersManager,
                $_user,
                $_validation;

    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->_validation = TRUE;
        $this->init();
    }

    protected function init() {
        // Vérifie si des informations ont été envoyées dans le formulaire
        if (!empty($_POST)) {
            $this->_user = new \model\Users([
                "login" => htmlspecialchars($_POST["login"]),
                "email" => htmlspecialchars($_POST["email"]),
                "pass" => htmlspecialchars($_POST["pass"]),
                "role" => $_SESSION["settings"]->default_role(),
                "name" => htmlspecialchars($_POST["name"]),
                "surname" => htmlspecialchars($_POST["surname"]),
                "birthdate" => !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL
            ]);
            // Vérifie les informations
            $this->loginCheck(); // Vérifie le login
            $this->emailCheck(); // Vérifie l'email
            $this->passCheck(); // Vérifie le mot de passe
            $this->confirmPassCheck(); // Vérifie la confirmation du mot de passe
            // Si validation est vrai, valide l'inscription de l'utilisateur
            if ($this->_validation) {
            $this->addUser(); // Ajoute l'utilisateur
            $this->connectionUser(); // Connecte l'utilisateur
            $this->_session->setFlash("Bienvenue sur le site !", "success");
            header("Location: blog");
            exit();
        }
        }
        require "view/frontend/inscriptionView.php";
    }

    // 
    public function loginCheck() {
        $loginUsed = $this->_usersManager->count(" u.login = '" . $this->_user->login() . "'");
        // Vérifie si le champ login est vide
        if (empty($this->_user->login())) {
            $this->_session->setFlash("Le login est non renseigné.", "danger");
            $this->_validation = FALSE;
        }
        // Vérifie si le login est déjà utilisé
        elseif ($loginUsed) {
            $this->_session->setFlash("Ce login est déjà utilisé. Veuillez en utiliser un autre.", "danger");
            $this->_validation = FALSE;
        }
    }

    //
    public function emailCheck() {
        $emailUsed = $this->_usersManager->count(" u.email = '" . $this->_user->email() . "'");
        // Vérifie si l'adresse email est vide
        if (empty($this->_user->email())) {
            $this->_session->setFlash("L'adresse email est vide.", "danger");
            $this->_validation = FALSE;
        } 
        // Vérifie si l'adresse email est correcte
        elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->_user->email())) {
            $this->_session->setFlash("L'adresse \"" .$this->_user->email() . "\" est incorrecte.", "danger");
            $this->_validation = FALSE;
        }
        // Vérifie si l'adresse email est déjà utilisée
        elseif ($emailUsed) {
            $this->_session->setFlash("L'adresse email est déjà utilisée.", "danger");
            $this->_validation = FALSE;
        }
    }

    //
    public function passCheck() {
        // Vérifie si le champ nouveau mot de passe est vide
        if (empty($this->_user->pass())) {
            $this->_session->setFlash("Le mot de passe est vide.", "danger");
            $this->_validation = FALSE;
        }
        // Vérifie si le mot de passe est correct
        // (?=.*[a-z])  : teste la présence d'une lettre minuscule
        // (?=.*[A-Z])  : teste la présence d'une lettre majuscule
        // (?=.*[0-9])  : teste la présence d'un chiffre de 0 à 9
        // (?=.*\W)     : teste la présence d'un caratère spécial ('\W' ce qui ne correspond pas à un mot)
        // .{6,20}$     : teste si entre 6 et 20 caractères
        elseif (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,20}$#", $this->_user->pass())) {
            $this->_session->setFlash("Le mot de passe n'est pas valide.", "danger");
            $this->_validation = FALSE;
        }
    }

    // Vérifie la confirmation du mot de passe
    public function confirmPassCheck() {
        // Vérifie si la confirmation du mot de passe est identique
        if (empty($_POST["pass_confirm"])) {
            $this->_session->setFlash("La confirmation du mot de passe est vide.", "danger");
            $this->_validation = FALSE;
        }
        // Vérifie si la confirmation du mot de passe est identique
        elseif ($this->_user->pass()!=$_POST["pass_confirm"]) {
            $this->_session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
            $this->_validation = FALSE;
        }
    }

    // Ajoute l'utilisateur
    protected function addUser() {
        // Hachage du mot de passe
        $passHash = password_hash($this->_user->pass(), PASSWORD_DEFAULT); 
        $this->_user->SetPass($passHash);
        // Insert les données dans la table users
        $this->_usersManager->add($this->_user);
        // Récupère l'ID de l'utilisateur et son password haché
        $this->_user = $this->_usersManager->verify($this->_user->login()); 
    }

    // Connecte l'utilisateur
    protected function connectionUser() {
        // Ajoute les infos de l"utilisateurs dans la Session
        $_SESSION["user"]["id"] = $this->_user->id();
        $_SESSION["user"]["login"] = $this->_user->login();
        $_SESSION["user"]["role"] = $this->_user->role();
        $_SESSION["user"]["profil"] = $this->_user->role_user();
        $_SESSION["user"]["name"] = $this->_user->name();
        $_SESSION["user"]["surname"] = $this->_user->surname();
        // Ajoute la date de connexion de l'utilisateur
        $this->_usersManager->addConnectionDate($this->_user);
        // Récupère la date de dernière connexion de l'utilisateur
        $_SESSION["lastConnection"] = $this->_usersManager->getLastConnection($this->_user);
    }
}
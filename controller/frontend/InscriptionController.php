<?php 
namespace controller\frontend;

class InscriptionController {

    protected   $_session,
                $_usersManager,
                $_user;

    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->init();
    }

    protected function init() {
        // Vérifie si informations dans variable POST
        if (!empty($_POST)) {
            $this->_user = new \model\Users([
                "login" => htmlspecialchars($_POST["login"]),
                "name" => htmlspecialchars($_POST["name"]),
                "surname" => htmlspecialchars($_POST["surname"]),
                "email" => htmlspecialchars($_POST["email"]),
                "birthdate" => !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL,
                "pass" => htmlspecialchars($_POST["pass"])
            ]);

            $validation = true;

            // Vérifie si le login est déjà utilisé
            $loginUsed = $this->_usersManager->count(" u.login = '" . $this->_user->login() . "'");
            // Vérifie si l'adresse email est déjà utilisée
            $emailUsed = $this->_usersManager->count(" u.email = '" . $this->_user->email() . "'");

            // Vérifie si le champ login est vide
            if (empty($this->_user->login())) {
                $this->_session->setFlash("Veuillez saisir un Login.", "danger");
                $validation = false;
            }
            // Vérifie si le login est déjà utilisé
            elseif ($loginUsed) {
                $this->_session->setFlash("Ce login est déjà utilisé. Veuillez en utiliser un autre.", "danger");
                $validation = false;
            }
            // Vérifie si l'adresse email est vide
            if (empty($this->_user->email())) {
                $this->_session->setFlash("L'adresse email est vide.", "danger");
                $validation = false;
            } 
            // Vérifie si l'adresse email est correcte
            elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->_user->email())) {
                $this->_session->setFlash("L'adresse \"" .$this->_user->email() . "\" est incorrecte.", "danger");
                $validation = false;
            }
            // Vérifie si l'adresse email est déjà utilisée
            elseif ($emailUsed) {
                $this->_session->setFlash("L'adresse email est déjà utilisée.", "danger");
                $validation = false;
            }
            // Vérifie si le champ nouveau mot de passe est vide
            if (empty($this->_user->pass())) {
                $this->_session->setFlash("Le mot de passe est vide.", "danger");
                $validation = false;
            }
            // Vérifie si le mot de passe est correct
            // (?=.*[a-z])  : teste la présence d'une lettre minuscule
            // (?=.*[A-Z])  : teste la présence d'une lettre majuscule
            // (?=.*[0-9])  : teste la présence d'un chiffre de 0 à 9
            // (?=.*\W)     : teste la présence d'un caratère spécial ('\W' ce qui ne correspond pas à un mot)
            // .{6,20}$     : teste si entre 6 et 20 caractères
            elseif (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,20}$#", $this->_user->pass())) {
                $this->_session->setFlash("Le mot de passe n'est pas valide.", "danger");
                $validation = false;
            }
            // Vérifie si la confirmation du mot de passe est identique
            elseif (empty($_POST["pass_confirm"])) {
                $this->_session->setFlash("La confirmation du mot de passe est vide.", "danger");
                $validation = false;
            }
            // Vérifie si la confirmation du mot de passe est identique
            elseif ($this->_user->pass()!=$_POST["pass_confirm"]) {
                $this->_session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
                $validation = false;
            }
            // Si validation est vrai, valide l'inscription de l'utilisateur
            if ($validation) {
                // Hachage du mot de passe
                $passHash = password_hash($this->_user->pass(), PASSWORD_DEFAULT); 
                $this->_user->SetPass($passHash);
                // Insert les données dans la table users
                $this->_usersManager->add($this->_user);
                // Récupère l'ID de l'utilisateur
                $this->_user = $this->_usersManager->verify($this->_user->login());
                
                // Ajoute les infos de l"utilisateurs dans la Session
                $_SESSION["user"]["id"] = $this->_user->id();
                $_SESSION["user"]["login"] = $this->_user->login();
                $_SESSION["user"]["role"] = $_SESSION["settings"]->default_role();
                $_SESSION["user"]["profil"] = $this->_user->role_user();
                $_SESSION["user"]["name"] = $this->_user->name();
                $_SESSION["user"]["surname"] = $this->_user->surname();

                // Ajoute la date de connexion de l'utilisateur
                $this->_usersManager->addConnectionDate($this->_user);

                // Récupère la date de dernière connexion de l'utilisateur
                $_SESSION["lastConnection"] = $this->_usersManager->getLastConnection($this->_user);

                $this->_session->setFlash("Bienvenue sur le site !", "success");
                header("Location: blog");
                exit();
            }
        }
        require "view/frontend/inscriptionView.php";
    }
}
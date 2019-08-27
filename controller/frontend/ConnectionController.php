<?php 
namespace controller\frontend;

class ConnectionController {

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
        // Redirige vers la page d'accueil si l'utilisateur est déjà connecté
        if (!empty($_SESSION["user"])) {
            header("Location: blog");
            exit;
        }
        // Vérifie si informations dans variable POST
        if (!empty($_POST)) {
            $this->validation();
            if ($this->_validation == TRUE) {
                $this->connection();
            }
        }
        require "view/frontend/connectionView.php";
    }

    // Vérifie si les informations sont valides
    protected function validation() {
        // Vérifie si le login est vide
        if (empty($_POST["login"]) || empty($_POST["pass"])) {
            $this->_validation = FALSE;
            $this->_session->setFlash("Veuillez saisir votre login <br />et votre mot de passe.", "danger");
        } else {
            // Récupère l'ID de l'utilisateur et son password haché
            $this->_user = $this->_usersManager->verify(htmlspecialchars($_POST["login"]));
            // Si l'utilisateur existe, vérifie le mot de passe   
            if ($this->_user) {
                $isPasswordCorrect = password_verify($_POST["pass"], $this->_user->pass());// Compare le password envoyé via le formulaire avec la base  
            }
            if (!$this->_user || !$isPasswordCorrect) {
                $this->_validation = FALSE;
                $this->_session->setFlash("Login ou mot de passe incorrect.", "danger");
            }
        }
    }

    // Connecte l'utilisateur
    protected function connection() {
        // Enregistre les informations de l'utilisateurs en session
        $_SESSION["user"]["id"] = $this->_user->id();
        $_SESSION["user"]["login"] = $this->_user->login();
        $_SESSION["user"]["role"] = $this->_user->role();
        $_SESSION["user"]["profil"] = $this->_user->role_user();
        $_SESSION["user"]["name"] = $this->_user->name();
        $_SESSION["user"]["surname"] = $this->_user->surname();

        // Enregistre le login et le mot de passe en cookie si la case "Se souvenir de moi" est cochée
        if (isset($_POST["remember"])) {
            setcookie("user[login]", $this->_user->login(), time() + 365*24*3600, NULL,NULL, FALSE, TRUE);
            // setcookie("user[pass]", htmlspecialchars($_POST["pass"]), time() + 365*24*3600, NULL,NULL, FALSE, TRUE);
        }

        // Récupère la date de dernière connexion de l'utilisateur
        $_SESSION["lastConnection"] = $this->_usersManager->getLastConnection($this->_user);

        // Ajoute la date de connexion de l'utilisateur
        $this->_usersManager->addConnectionDate($this->_user);

        $this->_session->setFlash("Vous êtes connecté.", "success");
        header("Location: blog");
        exit;
    }

}
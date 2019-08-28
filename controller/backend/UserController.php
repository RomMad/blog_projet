<?php 
namespace controller\backend;

class UserController {

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
        // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
        if (empty($_SESSION["user"]["id"])) {
            header("Location: connection");
            exit;
        } else {
            // Récupère le rôle de l'utilisateur
            $this->_userRole = $this->_usersManager->getRole($_SESSION["user"]["id"]);
            if ($this->_userRole != 1) {
                header("Location: blog");
                exit;
            }
            // Récupère le rôle de l'utilisateur
            if ($_GET["id"] == $_SESSION["user"]["id"]) {
                header("Location: profil");
                exit;
            }
        }
        // Vérifie si l'utilisateur existe
        $isUserExists = $this->_usersManager->exists($_GET["id"]);
        if (!$isUserExists) {
            $this->_session->setFlash("Cet utilisateur n'existe pas.", "warning");
            header("Location: blog"); 
            exit;
        }
        // Mettre à jour les informations du profil
        if (!empty($_POST)) {
            $this->postRole();
        }
        $this->_user = $this->_usersManager->get($_GET["id"]);
        require "view/backend/userView.php";
    }

    // Récupère les données en post, les vérifie et met à jour si valide
    protected function postRole() {
        if (empty($_POST["role"])) {
            $this->_session->setFlash("Le rôle est vide.", "danger");
            $this->_validation = FALSE; 
        }
        if (!($_POST["role"] >= 1 && $_POST["role"] <= 5))  {
            $this->_session->setFlash("Le rôle est incorrect.", "danger");
            $this->_validation = FALSE; 
        }
        if ($this->_validation) {
            $this->updateUser();
        }
    }

    // Met à jour les informations du profil
    protected function updateUser() {
        $this->_user = new \model\Users([
            "id" => $_GET["id"],
            "role" => $_POST["role"]
        ]);
        $this->_usersManager->updateRole($this->_user);
        $this->_session->setFlash("Le profil a été mis à jour.", "success");
    }
}
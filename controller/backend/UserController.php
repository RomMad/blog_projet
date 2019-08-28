<?php 
namespace controller\backend;

class UserController {

    protected   $_session,
                $_usersManager,
                $_user;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->init();
    }

    protected function init() {
        // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
        if (empty($_SESSION["user"]["id"])) {
            header("Location: connection");
            exit();
        } else {
            // Récupère le rôle de l'utilisateur
            $this->_userRole = $this->_usersManager->getRole($_SESSION["user"]["id"]);
            if ($this->_userRole != 1) {
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
        $isUserExists = $this->_usersManager->exists($_GET["id"]);
        if (!$isUserExists) {
            $this->_session->setFlash("Cet utilisateur n'existe pas.", "warning");
            header("Location: blog"); 
            exit();
        }

        // Mettre à jour les informations du profil
        if (!empty($_POST) && !empty($_POST["role"])) {
            $validation = true;  

            // Met à jour les informations du profil si validation est vraie
            if ($validation) {
                $this->_user = new \model\Users([
                    "id" => $_GET["id"],
                    "role" => $_POST["role"]
                ]);
                $this->_usersManager->updateRole($this->_user);
                $this->_session->setFlash("Le profil a été mis à jour.", "success");
            }
        }

        $this->_user = $this->_usersManager->get($_GET["id"]);

        require "view/backend/userView.php";
    }
}
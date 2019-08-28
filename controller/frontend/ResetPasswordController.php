<?php 
namespace controller\frontend;

class ResetPasswordController extends \controller\frontend\InscriptionController {
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->_validation = true;
        $this->init();
    }

    protected function init() {
        // Vérifie si informations dans variables POST et GET
        if (!empty($_POST) && isset($_GET["token"])) {
            $this->_user = new \model\Users([
                "email" => $_POST["email"],
                "pass" => $_POST["pass"]
            ]);
            $this->tokenCheck(); // Vérifie le token et s'il est toujours valide
            $this->passCheck(); // Vérifie le mot de passe
            $this->confirmPassCheck(); // Vérifie la confirmation du mot de passe

            // Si validation est vraie, met à jour le mot de passe 
            if ($this->_validation == true) {      
                // Récupère l'ID de l'utilisateur
                $this->_user = $this->_usersManager->get($_POST["email"]);
                $this->addInfosSession(); // Ajoute les infos de l"utilisateurs en session
                $this->_usersManager->addConnectionDate($this->_user); // Ajoute la date de connexion de l'utilisateur

                // Hachage du mot de passe
                $passHash = password_hash($_POST["pass"], PASSWORD_DEFAULT);
                // Créé une nouvelle entité user
                $this->_user = new \model\Users([
                    "id" => $this->_user->id(),
                    "pass" => $passHash
                ]);
                $this->_usersManager->updatePass($this->_user);

                $this->_session->setFlash ("Bienvenue sur le site !", "success");
                header("Location: blog");
                exit;
            }
        }
        require "view/frontend/resetPasswordView.php";
    }

    // Vérifie le token et s'il est toujours valide
    protected function tokenCheck() {
        // Vérifie si le token est existe
        $resetDate = $this->_usersManager->verifyToken($_GET["token"], $_POST["email"]);
        // Calcule l'intervalle entre le moment de demande de réinitialisation et maintenant
        $dateResetPassword = new \DateTime($resetDate, timezone_open("Europe/Paris"));
        $dateNow = new \DateTime("now", timezone_open("Europe/Paris"));
        $interval = date_timestamp_get($dateNow)-date_timestamp_get($dateResetPassword);
        $delay = 15 * 60; // 15 minutes x 60 secondes = 900 secondes
        // Vérifie si le token ou l'adresse email sont corrects
        if (!$resetDate) {
            $this->_session->setFlash ("Le lien de réinitialisation ou l'adresse email sont incorrects.", "danger");
            $this->_validation = false;
        }
        //  Vérifie si la demande de réinitialisation est inférieure à 15 minutes
        elseif ($interval>$delay) {
            $this->_session->setFlash ("Le lien de réinitialisation est périmé.", "danger");
            $this->_validation = false;
        }
    }
}
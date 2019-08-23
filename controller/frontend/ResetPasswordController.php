<?php 
namespace controller\frontend;
use  model\UsersManager;

class ResetPasswordController {

    protected   $_session,
                $_usersManager,
                $_user;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new UsersManager();
        $this->init();
    }

    protected function init() {
        // Vérifie si informations dans variables POST et GET
        if (!empty($_POST) && isset($_GET["token"])) {
            $validation = true;
        
            // Vérifie si le token est existe
            $resetDate = $this->_usersManager->verifyToken($_GET["token"], $_POST["email"]);
            
            // Calcule l'intervalle entre le moment de demande de réinitialisation et maintenant
            $dateResetPassword = new DateTime($resetDate, timezone_open("Europe/Paris"));
            $dateNow = new DateTime("now", timezone_open("Europe/Paris"));
            $interval = date_timestamp_get($dateNow)-date_timestamp_get($dateResetPassword);
            $delay = 15 * 60; // 15 minutes x 60 secondes = 900 secondes
            // Vérifie si le token ou l'adresse email sont corrects
            if (!$resetDate) {
                $this->_session->setFlash ("Le lien de réinitialisation ou l'adresse email sont incorrects.", "danger");
                $validation = false;
            }
            //  Vérifie si la demande de réinitialisation est inférieure à 15 minutes
            elseif ($interval>$delay) {
                $this->_session->setFlash ("Le lien de réinitialisation est périmé.", "danger");
                $validation = false;
            }
            // Vérifie si le champ nouveau mot de passe est vide
            if (empty($_POST["new_pass"])) {
                $this->_session->setFlash("Veuillez saisir votre nouveau mot de passe.", "danger");
                $validation = false;
            }
            // Vérifie si le nouveau mot de passe est valide (entre 6 et 20 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre, 1 caractère spécial)
            elseif (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{6,20}$#", $_POST["new_pass"])) {
            $this->_session->setFlash ("Le nouveau mot de passe n'est pas valide.", "danger");
            $validation = false;
            }
            // Vérifie si le champ confirmation nouveau mot de passe est vide
            if (empty($_POST["new_pass_confirm"])) {
                $this->_session->setFlash("Veuillez saisir la confirmation de votre nouveau mot de passe.", "danger");
                $validation = false;
            }  
            // Vérifie si la confirmation du mot de passe est identique
            elseif ($_POST["new_pass"] != ($_POST["new_pass_confirm"])) {
                $this->_session->setFlash ("Le mot de passe et la confirmation sont différents.", "danger");
                $validation = false;
            }
            // Si validation est vraie, met à jour le mot de passe 
            if ($validation) {      
                // Récupère l'ID de l'utilisateur
                $user = $this->_usersManager->get($_POST["email"]);
                // Enregistre les informations de l'utilisateurs en session
                $_SESSION["user"]["id"] = $user->id();
                $_SESSION["user"]["login"] = $user->login();
                $_SESSION["user"]["role"] = $user->role();
                $_SESSION["user"]["profil"] = $user->role_user();
                $_SESSION["user"]["name"] = $user->name();
                $_SESSION["user"]["surname"] = $user->surname();
                // Hachage du mot de passe
                $newPassHash = password_hash(htmlspecialchars($_POST["new_pass"]), PASSWORD_DEFAULT);
                // Créé une nouvelle entité user
                $user = new Users([
                    "id" => $user->id(),
                    "pass" => $newPassHash
                ]);
                $this->_usersManager->updatePass($user);

                // Récupère la date de dernière connexion de l'utilisateur
                $_SESSION["lastConnection"] = $this->_usersManager->getLastConnection($user);

                // Ajoute la date de connexion de l'utilisateur
                $this->_usersManager->addConnectionDate($user);

                $this->_session->setFlash ("Le mot de passe a été modifié.", "success");

                header("Location: blog");
                exit();
            }
        }
        require "view/frontend/resetPasswordView.php";
    }
}
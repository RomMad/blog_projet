<?php 
namespace controller\backend;

class NewuserController extends \controller\frontend\InscriptionController {
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->_validation = TRUE;
        $this->init();
    }

    protected function init() {
        // Génère un mot de passe aléatoire
        if (!isset($_POST["pass"])) {
            $pass = "!A" . bin2hex(random_bytes(4)) . "*";
        } else {
            $pass = $_POST["pass"];
        }
        // Vérifie si des informations ont été envoyées dans le formulaire
        if (!empty($_POST)) {
            $this->_user = new \model\Users([
                "login" => $_POST["login"],
                "email" => $_POST["email"],
                "pass" => $pass,
                "name" => $_POST["name"],
                "surname" => $_POST["surname"],
                "role" => $_POST["role"]
            ]);
            // Vérifie les informations
            $this->loginCheck(); // Vérifie le login
            $this->emailCheck(); // Vérifie l'email
            $this->passCheck(); // Vérifie le mot de passe
            // Si validation est vrai, valide l'inscription de l'utilisateur
            if ($this->_validation) {
                $this->addUser(); // Ajoute l'utilisateur
                $this->sendEmail(); // Envoie un email au nouvel utilisateur
            }                
        }
        require "view/backend/newUserView.php";
    }

    // Envoie un email au nouvel utilisateur
    protected function sendEmail() {
        // Génère un token
        $token = bin2hex(random_bytes(32));
        // Ajoute un token pour la réinitialisation
        $this->_usersManager->addToken($this->_user, $token);
        // Vérifie si on est en local ou en ligne
        if ($_SERVER["HTTP_HOST"] == "localhost") {
            $url = "http://localhost/blog";
        } else {
            $url = "https://leblog.romain-mad.fr/blog";
        }

        // Initialise l'email
        $link = $url . "/create-password-" . $token;
        $to = $this->_user->email();
        $subject = "Création de compte";
        $message = "
            <html>
                <head>
                    <title>Création de compte</title>
                </head>
                <body>
                    <p>Bonjour, </p>
                    <p>Un compte utilisateur a été créé pour vous. <br />
                    Veuillez cliquer sur le lien ci-dessous pour confirmer la création et personnaliser votre mot de passe : </p>
                    <a href=" . $link . ">" . $link . "</a>
                    <p>--<br />Ceci est un message automatique, merci de ne pas y répondre. </p>
                </body>
            </html>";
    
        $headers = array(
            "MIME-Version" => "1.0",
            "Content-type" => "text/html;charset=UTF-8",
            "From" => "Admin Blog <no-reply@gmail.com>",
            // "CC" => $cc,
            // "Bcc" => $bcc,
            "Reply-To" => "Admin Blog <romain.madelaine@gmail.com>",
            "X-Mailer" => "PHP/" . phpversion()
        );
        
        mail($to,$subject,$message,$headers);

        $this->_session->setFlash("L'utilisateur " . $this->_user->login() . " a été ajouté. <br />Un email lui a été envoyé.", "success");
    }
}
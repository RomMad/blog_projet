<?php 
namespace controller\frontend;

class ForgotPasswordController extends \controller\frontend\InscriptionController {

    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->_validation = TRUE;
        $this->init();
    }

    protected function init() {
        // Vérifie si information dans variable POST
        if (!empty($_POST)) {
            // Récupère l'ID de l'utilisateur et son password haché
            $this->_user = $this->_usersManager->get($_POST["email"]);
            // Vérifie si le champ email est vide
            if (empty($_POST["email"])) {
                $this->_session->setFlash("L'adresse email est vide", "warning");
                $this->_validation = FALSE;
            }
            // Vérifie si l'adresse email existe
            elseif (!$this->_user) {
                $this->_session->setFlash("Cette adresse email est inconnue", "danger");
                $this->_validation = FALSE;
            }
            // Génère un email avec un token si validation est vraie
            if ($this->_validation) {
                $this->sendEmail();
            }
        }
        require "view/frontend/forgotPasswordView.php";
    }

    protected function sendEmail() {
        // Génère un token
        $token = bin2hex(random_bytes(32));
        // Ajoute un token pour la réinitialisation
        $this->_usersManager->addToken($this->_user, $token);
        // Vérifie si on est en local ou en ligne
        if ($_SERVER["HTTP_HOST"] == "localhost") {
            $url = "http://localhost/blog_projet";
        } else {
            $url = "https://leblog.romain-mad.fr/blog_projet";
        }
        // Initialise l'email
        $link = $url . "/reset-password-" . $token;
        $to = $this->_user->email();
        $subject = "Demande de réinitialisation du mot de passe";
        $message = "
        <html>
            <head>
                <title>Mot de passe oublié</title>
            </head>
            <body>
                <p>Bonjour, </p>
                <p>Vous avez fait une demande de réinitialisation de votre mot de passe. <br />
                Veuillez cliquer sur le lien ci-dessous pour réinitiliaser votre mot de passe : </p>
                <a href=" . $link . ">" . $link . "</a>
                <p>Si vous n'êtes pas à l'origine de cette demande, merci d'ignorer ce message. </p>
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
        // Envoie l'email
        mail($to,$subject,$message,$headers);

        $this->_session->setFlash("Un email vient de vous être envoyé", "success");
    }
}
<?php 
function forgotPassword() {
    $session = new model\Session();
    $usersManager = new model\UsersManager();

    // Vérifie si information dans variable POST
    if (!empty($_POST)) {
        $validation = true;
        // Récupère l'ID de l'utilisateur et son password haché
        $user = $usersManager->get($_POST["email"]);
        // Vérifie si le champ email est vide
        if (empty($_POST["email"])) {
            $session->setFlash("Veuillez saisir une adresse email", "warning");
            $validation = false;
        }
        // Vérifie si l'adresse email existe
        elseif (!$user) {
            $session->setFlash("Cette adresse email est inconnue", "danger");
            $validation = false;
        }
        // Génère un email avec un token si validation est vraie
        if ($validation) {
            // Génère un token
            $token = bin2hex(random_bytes(32));
            // Ajoute un token pour la réinitialisation
            $usersManager->addToken($user, $token);
            // Vérifie si on est en local ou en ligne
            if ($_SERVER["HTTP_HOST"] == "localhost") {
                $url = "http://localhost/blog_projet";
            } else {
                $url = "https://leblog.romain-mad.fr/blog_projet";
            }
            
            // Initialise l'email
            $link = $url . "/reset-password-" . $token;
            $to = $user->email();
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

            $session->setFlash("Un email vient de vous être envoyé", "success");
        }
    }
    require "view/frontend/forgotPasswordView.php";
}
?>
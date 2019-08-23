<?php 
namespace controller\backend;

class NewuserController {

    protected   $_session,
                $_usersManager,
                $_user;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_usersManager = new \model\UsersManager();
        $this->init();
    }

    protected function init() {

        if (!isset($_POST["pass"])) {
            $pass = bin2hex(random_bytes(8));
        } else {
            $pass = $_POST["pass"];
        }

        // Vérifie si informations dans variable POST
        if (!empty($_POST)) {
            $this->_user = new \model\Users([
                "login" => $_POST["login"],
                "email" => $_POST["email"],
                "pass" => $pass,
                "name" => $_POST["name"],
                "surname" => $_POST["surname"],
                "role" => $_POST["role"]
            ]);

            $validation = true;

            // Vérifie si le login est déjà utilisé
            $loginUsed = $this->_usersManager->count(" u.login = '" . $this->_user->login() . "'");
            // Vérifie si l'adresse email est déjà utilisée
            $emailUsed = $this->_usersManager->count(" u.email = '" . $this->_user->email() . "'");

            // Vérifie si le champ login est vide
            if (empty($this->_user->login())) {
                $this->_session->setFlash("Le login est non renseigné.", "danger");
                $validation = false;
            }
            // Vérifie si le login est déjà utilisé
            elseif ($loginUsed) {
                $this->_session->setFlash("Ce login est déjà utilisé.", "danger");
                $validation = false;
            }
            // Vérifie si l'adresse email est déjà utilisée
            if (empty($this->_user->email())) {
                $this->_session->setFlash("L'adresse email est non renseignée.", "danger");
                $validation = false;
            }
            // Vérifie si l'adresse email est correcte
            elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->_user->email())) {
                $this->_session->setFlash("L'adresse \"" . $this->_user->email() . "\" est incorrecte.", "danger");
                $validation = false;
            }
            // Vérifie si l'adresse email est déjà utilisée
            elseif ($emailUsed) {
                $this->_session->setFlash("L'adresse email est déjà utilisée.", "danger");
                $validation = false;
            }
            // Vérifie si le champ mot de passe est vide
            if (empty($_POST["pass"])) {
                $this->_session->setFlash("Le mot de passe est non renseigné.", "danger");
                $validation = false;
            }
            // Si validation est vrai, valide l'inscription de l'utilisateur
            if ($validation) {
                // Hachage du mot de passe
                $passHash = password_hash($this->_user->pass(), PASSWORD_DEFAULT); 
                $this->_user->SetPass($passHash);
                // Insert les données dans la table users
                $this->_usersManager->add($this->_user);
                // Récupère l'ID de l'utilisateur et son password haché
                $this->_user = $this->_usersManager->verify($this->_user->login());
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

        require "view/backend/newUserView.php";
    }
}
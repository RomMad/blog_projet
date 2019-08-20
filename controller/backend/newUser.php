<?php 
function newuser() {
    
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();

    if (!isset($_POST["pass"])) {
        $bytes = random_bytes(8);
        $token = bin2hex($bytes);
        // $passHash = password_hash($token, PASSWORD_DEFAULT); // Hachage du mot de passe$token ;
    }

    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $user = new Users([
            "login" => $_POST["login"],
            "email" => $_POST["email"],
            "pass" => $_POST["pass"],
            "name" => $_POST["name"],
            "surname" => $_POST["surname"],
            "role" => $_POST["role"]
        ]);

        $validation = true;

        // Vérifie si le login est déjà utilisé
        $loginUsed = $usersManager->count(" u.login = '" . $user->login() . "'");
        // Vérifie si l'adresse email est déjà utilisée
        $emailUsed = $usersManager->count(" u.email = '" . $user->email() . "'");

        // Vérifie si le champ login est vide
        if (empty($user->login())) {
            $session->setFlash("Le login est non renseigné.", "danger");
            $validation = false;
        }
        // Vérifie si le login est déjà utilisé
        elseif ($loginUsed) {
            $session->setFlash("Ce login est déjà utilisé.", "danger");
            $validation = false;
        }
        // Vérifie si l'adresse email est déjà utilisée
        if (empty($user->email())) {
            $session->setFlash("L'adresse email est non renseignée.", "danger");
            $validation = false;
        }
        // Vérifie si l'adresse email est correcte
        elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user->email())) {
            $session->setFlash("L'adresse \"" . $user->email() . "\" est incorrecte.", "danger");
            $validation = false;
        }
        // Vérifie si l'adresse email est déjà utilisée
        elseif ($emailUsed) {
            $session->setFlash("L'adresse email est déjà utilisée.", "danger");
            $validation = false;
        }
        // Vérifie si le champ mot de passe est vide
        if (empty($_POST["pass"])) {
            $session->setFlash("Le mot de passe est non renseigné.", "danger");
            $validation = false;
        }
        // Si validation est vrai, valide l'inscription de l'utilisateur
        if ($validation) {
            // Hachage du mot de passe
            $passHash = password_hash($user->pass(), PASSWORD_DEFAULT); 
            $user->SetPass($passHash);
            // Insert les données dans la table users
            $usersManager->add($user);

            // Récupère l'ID de l'utilisateur et son password haché
            $user = $usersManager->verify($user->login());

            $usersManager->addToken($user, $token);
            // Initialise l'email
            $link = "http://localhost/blog_projet/reset-password-" . $user->pass();
            $to = $user->email();
            $subject = "Création de compte";
            $message = "
                <html>
                    <head>
                        <title>Création de compte</title>
                    </head>
                    <body>
                        <p>Bonjour, </p>
                        <p>Un compte utilisateur a été créé pour vous. <br />
                        Veuillez cliquer sur le lien ci-dessous pour confirmer la création et personnaliser votr mot de passe : </p>
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

            $session->setFlash("L'utilisateur " . $user->login() . " a été ajouté. <br />Un email lui a été envoyé.", "success");
            }
    }

    require "view/backend/newUserView.php";
}
<?php 
    session_start();

    require("connection_bdd.php");

    var_dump($_POST);
    // Vérifie si information dans variable POST
    if (!empty($_POST)) {
        $email = htmlspecialchars($_POST["email"]);

        // Récupère l'ID de l'utilisateur et son password haché
        $req = $bdd->prepare("SELECT ID FROM users WHERE email = ?");
        $req->execute(array($email));
        $dataUser = $req->fetch();

        // Vérifie si adresse email existe
        if ($dataUser) {
            $bytes = random_bytes(8);
            $token = bin2hex($bytes);

            $req = $bdd->prepare("INSERT INTO reset_passwords (user_ID, token) VALUES (:user_id, :token)");
            $req->execute(array(
                "user_id" => $dataUser["ID"],
                "token" => $token
            ));

            $link = "http://localhost/blog_projet/reset_password.php?token=" . $token;
            $to = $email;
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
            
            mail($to,$subject,$message,$headers);

            $message = "Un email vient de vous être envoyé.";
            $typeAlert = "success";
        } else {
            $message = "Cette adresse email est inconnue.";
            $typeAlert = "danger";
        };

        // Vérifie si le champ login est vide
        if (empty($email)) {
            $message = "Veuillez saisir une adresse email.";
            $typeAlert = "warning";
        };

        $_SESSION["flash"] = array(
            "msg" => $message,
            "type" =>  $typeAlert
        );
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">
        <section id="forgot-password" class="row">
                <form action="forgotpassword.php" method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 mx-auto text-center">
                    <h1 class="h3 mb-4 font-weight-normal">Mot de passe oublié</h1>
                    <p>Saisissez votre adresse e-mail afin de recevoir un e-mail pour réinitialiser votre mot de
                        passe.</p>
                    <label for="email" class="sr-only">Email</label>
                    <input type="text" name="email" id="email" class="form-control mb-4 shadow-sm" placeholder="Email"
                        autofocus="">
                    <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">

                    <?php include("msg_session_flash.php") ?>
                </form>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
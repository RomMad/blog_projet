<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$usersManager = new UsersManager();
$db = $usersManager->db();

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
        $bytes = random_bytes(8);
        $token = bin2hex($bytes);

        $req = $db->prepare("INSERT INTO reset_passwords (user_ID, token) VALUES (:user_id, :token)");
        $req->execute(array(
            "user_id" => $user->id(),
            "token" => $token
        ));
        // Initialise l'email
        $link = "http://localhost/blog_projet/reset_password.php?token=" . $token;
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
?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">
        <section id="forgot-password" class="row">
                <form action="forgotpassword.php" method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 mx-auto text-center">
                    
                <?php $session->flash(); // Message en session flash ?>      

                    <h1 class="h3 mb-4 font-weight-normal">Mot de passe oublié</h1>
                    <p>Saisissez votre adresse e-mail afin de recevoir un e-mail pour réinitialiser votre mot de
                        passe.</p>
                    <label for="email" class="sr-only">Email</label>
                    <input type="text" name="email" id="email" class="form-control mb-4 shadow-sm" placeholder="Email"
                        autofocus="">
                    <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">

                </form>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
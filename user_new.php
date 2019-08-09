<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$usersManager = new UsersManager();
$db = $usersManager->db();

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

        $req = $db->prepare("INSERT INTO reset_passwords (user_ID, token) VALUES (:user_id, :token)");
        $req->execute(array(
            "user_id" => $user->id(),
            "token" => $user->pass()
        ));
        // Initialise l'email
        $link = "http://localhost/blog_projet/reset_password.php?token=" . $user->pass();
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
?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="admin.php" class="text-blue">Administration</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ajout d'un utilisateur</li>
                </ol>
        </nav>

        <section id="inscription" class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

            <?php $session->flash(); // Message en session flash ?>

                <form action="user_new.php" method="post" class="col-md-12 card shadow mt-4">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Ajouter un nouvel utilisateur</h3>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="login" id="login" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($user) ? $user->login() : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($user) ? $user->email() : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="name" id="name" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($user) ? $user->name() : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="surname" id="surname" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($user) ? $user->surname() : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label">Rôle par défaut</label>
                                <div class="col-md-8">
                                    <select name="role" id="role" class="custom-select form-control shadow-sm">
                                        <option value="5" <?= isset($user) && $user->role() == 5 ? "selected" : "" ?>>Abonné</option>
                                        <option value="4" <?= isset($user) && $user->role() == 4 ? "selected" : "" ?>>Contributeur</option>
                                        <option value="3" <?= isset($user) && $user->role() == 3 ? "selected" : "" ?>>Auteur</option>
                                        <option value="2" <?= isset($user) && $user->role() == 2 ? "selected" : "" ?>>Editeur</option>
                                        <option value="1" <?= isset($user) && $user->role() == 1 ? "selected" : "" ?>>Administrateur</option>
                                    </select>
                                </div>
                            </div> 
                            <br />
                            <div class="form-group row">
                                <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-8">
                                    <div class="div-user-pass">
                                        <input type="password" name="pass" id="pass" class="form-control mb-4 shadow-sm"
                                        value="<?= isset($user) ? $user->pass() : $token ?>">
                                        <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Envoyer" id="validation" class="btn btn-blue shadow">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>
    <script src="js/show_password.js"></script>
    
</body>

</html
<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->db();

if (!isset($_POST["pass"])) {
    $bytes = random_bytes(8);
    $token = bin2hex($bytes);
    $pass = $token ;
}

$role = 5;

// Vérifie si informations dans variable POST
if (!empty($_POST)) {
    $login = htmlspecialchars($_POST["login"]);
    $email = htmlspecialchars($_POST["email"]);
    $name = htmlspecialchars($_POST["name"]);
    $surname = htmlspecialchars($_POST["surname"]);
    $role = htmlspecialchars($_POST["role"]);
    $pass = htmlspecialchars($_POST["pass"]);
    $validation = true;
    $message = "Attention :";
    $typeAlert = "danger";

    // Vérifie si le login est déjà utilisé
    $req = $db->prepare("SELECT * FROM users WHERE login = ? ");
    $req->execute([$login]);
    $loginExist = $req->fetch();
    // Vérifie si l'adresse email est déjà utilisée
    $req = $db->prepare("SELECT * FROM users WHERE email = ? ");
    $req->execute([$email]);
    $emailExist = $req->fetch();

    // Vérifie si le champ login est vide
    if (empty($login)) {
        $message = $message . "<li>Veuillez saisir un Login.</li>";
        $validation = false;
    }
    // Vérifie si le login est déjà utilisé
    if ($loginExist) {
        $message = $message . "<li>Ce login est déjà utilisé. Veuillez en utiliser un autre.</li>";
        $validation = false;
    }
    // Vérifie si l'adresse email est correcte
    if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
        $message = $message . "<li>L'adresse \"" . $email . "\" est incorrecte.</li>";
        $validation = false;
    }
    // Vérifie si l'adresse email est déjà utilisée
    if ($emailExist) {
        $message = $message . "<li>L'adresse email est déjà utilisée.</li>";
        $validation = false;
    }
    // Si validation est vrai, valide l'inscription de l'utilisateur
    if ($validation) {
        // Insert les données dans la table users
        $req = $db->prepare("INSERT INTO users(login, email, name, surname, role, pass) 
                                VALUES(:login, :email, :name, :surname, :role, :pass)");
        $req->execute(array(
            "login" => $login,
            "email" => $email,
            "name" => $name,
            "surname" => $surname,
            "role" => $role,
            "pass" => $pass
            ));

        // Récupère l'ID de l'utilisateur et son password haché
        $req = $db->prepare("SELECT ID FROM users WHERE email = ?");
        $req->execute(array($email));
        $dataUser = $req->fetch();

        $req = $db->prepare("INSERT INTO reset_passwords (user_ID, token) VALUES (:user_id, :token)");
        $req->execute(array(
            "user_id" => $dataUser["ID"],
            "token" => $pass
        ));

        $link = "http://localhost/blog_projet/reset_password.php?token=" . $pass;
        $to = $email;
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

            $typeAlert = "success";
            $message = "L'utilisateur a été ajouté. Un email lui a été envoyé.";

            // header("Refresh: 2; url=admin_users.php");
        }

        $session->setFlash($message, $typeAlert);
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
                                        value="<?= isset($_POST["login"]) ? htmlspecialchars($_POST["login"]) : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="name" id="name" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="surname" id="surname" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($_POST["surname"]) ? htmlspecialchars($_POST["surname"]) : "" ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="role" class="col-md-4 col-form-label">Rôle par défaut</label>
                                <div class="col-md-8">
                                    <select name="role" id="role" class="custom-select form-control shadow-sm">
                                        <option value="1" <?= $role == 1 ? "selected" : "" ?>>Administrateur</option>
                                        <option value="2" <?= $role == 2 ? "selected" : "" ?>>Editeur</option>
                                        <option value="3" <?= $role == 3 ? "selected" : "" ?>>Auteur</option>
                                        <option value="4" <?= $role == 4 ? "selected" : "" ?>>Contributeur</option>
                                        <option value="5" <?= $role == 5 ? "selected" : "" ?>>Abonné</option>
                                    </select>
                                </div>
                            </div> 
                            <br />
                            <div class="form-group row">
                                <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-8">
                                    <div class="div-user-pass">
                                        <input type="password" name="pass" id="pass" class="form-control mb-4 shadow-sm"
                                        value="<?= isset($_POST["pass"]) ? htmlspecialchars($_POST["pass"]) : $pass ?>">
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
<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$usersManager = new UsersManager();

// Vérifie si informations dans variable POST
if (!empty($_POST)) {
    $user = new Users([
        "login" => htmlspecialchars($_POST["login"]),
        "name" => htmlspecialchars($_POST["name"]),
        "surname" => htmlspecialchars($_POST["surname"]),
        "email" => htmlspecialchars($_POST["email"]),
        "birthdate" => !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL,
        "pass" => htmlspecialchars($_POST["pass"])
    ]);

    $validation = true;

    // Vérifie si le login est déjà utilisé
    $loginUsed = $usersManager->count(" u.login = '" . $user->login() . "'");
    // Vérifie si l'adresse email est déjà utilisée
    $emailUsed = $usersManager->count(" u.email = '" . $user->email() . "'");

    // Vérifie si le champ login est vide
    if (empty($user->login())) {
        $session->setFlash("Veuillez saisir un Login.", "danger");
        $validation = false;
    }
    // Vérifie si le login est déjà utilisé
    elseif ($loginUsed) {
        $session->setFlash("Ce login est déjà utilisé. Veuillez en utiliser un autre.", "danger");
        $validation = false;
    }
    // Vérifie si l'adresse email est vide
    if (empty($user->email())) {
        $session->setFlash("L'adresse email est vide.", "danger");
        $validation = false;
    } 
    // Vérifie si l'adresse email est correcte
    elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user->email())) {
        $session->setFlash("L'adresse \"" .$user->email() . "\" est incorrecte.", "danger");
        $validation = false;
    }
    // Vérifie si l'adresse email est déjà utilisée
    elseif ($emailUsed) {
        $session->setFlash("L'adresse email est déjà utilisée.", "danger");
        $validation = false;
    }
    // Vérifie si le mot de passe est correct
    // (?=.*[a-z])  : teste la présence d'une lettre minuscule
    // (?=.*[A-Z])  : teste la présence d'une lettre majuscule
    // (?=.*[0-9])  : teste la présence d'un chiffre de 0 à 9
    // .{6,}$       : teste si au moins 6 caractères
    if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $user->pass())) {
        $session->setFlash("Le mot de passe n'est pas valide.", "danger");
        $validation = false;
    }
    // Vérifie si la confirmation du mot de passe est identique
    elseif (empty($_POST["pass_confirm"])) {
        $session->setFlash("La confirmation du mot de passe est vide.", "danger");
        $validation = false;
    }
    // Vérifie si la confirmation du mot de passe est identique
    elseif ($user->pass()!=$_POST["pass_confirm"]) {
        $session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
        $validation = false;
    }
    // Si validation est vrai, valide l'inscription de l'utilisateur
    if ($validation) {
        // Hachage du mot de passe
        $passHash = password_hash($user->pass(), PASSWORD_DEFAULT); 
        $user->SetPass($passHash);
        // Insert les données dans la table users
        $usersManager->add($user);
        // Récupère l'ID de l'utilisateur
        $user = $usersManager->verify($user->login());

        // Ajoute les infos de l"utilisateurs dans la Session
        $_SESSION["userID"] =  $user->id();
        $_SESSION["userLogin"] = $user->login();
        $_SESSION["userRole"] = $user->role();

        $session->setFlash("L'inscription est réussie.", "success");
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <section id="inscription" class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

                <?php $session->flash(); // Message en session flash ?>      

                    <form action="inscription.php" method="post" class="col-md-12 card shadow mt-4">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Inscription</h3>
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
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="name" id="name" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="surname" id="surname" class="form-control mb-4 shadow-sm" 
                                        value="<?= isset($_POST["surname"]) ? htmlspecialchars($_POST["surname"]) : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-5">
                                    <div class="div-user-pass">
                                        <input type="password" name="pass" id="pass" class="form-control mb-4 shadow-sm">
                                        <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                <div class="col-md-5">
                                    <div class="div-user-pass">
                                        <input type="password" name="pass_confirm" id="pass_confirm" class="form-control mb-4 shadow-sm">
                                        <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Valider" id="validation" class="btn btn-blue shadow">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <small class="text-muted">Le mot de passe doit contenir au minimum :
                                            <ul>
                                                <li>6 caractères</li>
                                                <li>1 lettre minuscule</li>
                                                <li>1 lettre majuscule</li>
                                                <li>1 chiffre</li>
                                            </ul>
                                    </small>
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
    <script src="js/show_confirm_password.js"></script>
    
</body>

</html
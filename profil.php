<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$db = new Manager();
$db = $db->databaseConnection();
$usersManager = new UsersManager($db);

// Redirige vers la page de connexion si non connecté
if (empty($_SESSION["userID"])) {
    header("Location: connection.php");
    exit();
} else {
    // Récupère les informations de l'utilisateur
    $user = $usersManager->get($_SESSION["userID"]);
}

// Vérifie si informations dans variable POST
if (!empty($_POST)) {
    $validation = true;
    
    // Mettre à jour les informations du profil
    if (isset($_POST["login"])) {
        $user = new Users([
            "id" => $_SESSION["userID"],
            "login" => $_POST["login"],
            "email" => $_POST["email"],
            "name" => $_POST["name"],
            "surname" => $_POST["surname"],
            "birthdate" => $_POST["birthdate"],
            "role_user" => $_POST["role"]
        ]);
        // Compare le pass envoyé via le formulaire avec la base
        $isPasswordCorrect = password_verify($_POST["pass"], $usersManager->getPass($_SESSION["userID"])); 
        // Vérifie si le login est déjà pris par un autre utilisateur
        $loginUsed = $usersManager->count("login = '" . $user->login() . "' AND u.id != " . $_SESSION["userID"]);
        // Vérifie si l'email est déjà pris par un autre utilisateur
        $emailUsed = $usersManager->count("email = '" . $user->email() . "' AND u.id != " . $_SESSION["userID"]);
        // Vérifie si le champ login est vide
        if (empty($user->login())) {
            $session->setFlash("Veuillez saisir un login.", "danger");
            $validation = false;
        }
        // Vérifie si le login est déjà pris par un autre utilisateur
        elseif ($loginUsed) {
            $session->setFlash("Ce login est déjà utilisé. Veuillez en choisir un autre.", "danger");
            $validation = false;
        }
        // Vérifie si le champ login est vide
        if (empty($user->email())) {
            $session->setFlash("L'adresse email est obligatoire.", "danger");
            $validation = false;
        }
        // Vérifie si l'email est correct
        elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user->email())) {
            $session->setFlash("L'adresse email " . $user->email() . " est incorrecte.", "danger");
            $validation = false;
        }
        // Vérifie si l'email est déjà pris par un autre utilisateur
        elseif ($emailUsed) {
            $session->setFlash("Cette adresse email est déjà utilisée.", "danger");
            $validation = false;
        }
        // Vérifie si le champ mot de passe est vide
        if (empty($_POST["pass"])) {
            $session->setFlash("Veuillez saisir votre mot de passe.", "danger");
            $validation = false;
        }
        // Vérifie si le mot de passe est correct
        elseif (!$isPasswordCorrect) {
            $session->setFlash("Le mot de passe est incorrect.", "danger");
            $validation = false;
        }
        // Vérifie si le champ de confirmation du mot de passe est vide
        if (empty($_POST["pass_confirm"])) {
            $session->setFlash("Veuillez saisir la confirmation de votre mot de passe.", "danger");
            $validation = false;
        }
        // Vérifie si la confirmation du mot de passe est identique
        elseif ($_POST["pass"] != $_POST["pass_confirm"]) {
            $session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
            $validation = false;
        }
        // Met à jour les informations du profil si validation est vraie
        if ($validation) {
            $usersManager->updateProfil($user);
            $_SESSION["userLogin"] = $user->login();
            $session->setFlash("Le profil a été mis à jour.", "success");
        }
    }

    // Mettre à jour le mot de passe
    if (isset($_POST["old_pass"])) {
        // Compare le mot de passe envoyé via le formulaire avec la base
        $isPasswordCorrect = password_verify($_POST["old_pass"], $usersManager->getPass($_SESSION["userID"])); 
        // Vérifie si le champ ancien mot de passe est vide
        if (empty(($_POST["old_pass"]))) {
            $session->setFlash("Veuillez saisir votre ancien mot de passe.", "danger");
            $validation = false;
        }
        // Vérifie si l'ancien mot de passe est correct   
        elseif (!$isPasswordCorrect) {
            $session->setFlash("L'ancien mot de passe est incorrect.", "danger");
            $validation = false;
        }
        // Vérifie si le champ nouveau mot de passe est vide
        if (empty($_POST["new_pass"])) {
            $session->setFlash("Veuillez saisir votre nouveau mot de passe.", "danger");
            $validation = false;
        }
        // Vérifie si le nouveau mot de passe est valide (minimum 6 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre)
        elseif (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $_POST["new_pass"])) {
            $session->setFlash("Le nouveau mot de passe n'est pas valide.", "danger");
            $validation = false;
        }
        // Vérifie si le champ confirmation nouveau mot de passe est vide
        if (empty($_POST["new_pass_confirm"])) {
            $session->setFlash("Veuillez saisir la confirmation de votre nouveau mot de passe.", "danger");
            $validation = false;
        }       
        // Vérifie si la confirmation du mot de passe est identique
        elseif ($_POST["new_pass"] != $_POST["new_pass_confirm"]) {
            $session->setFlash("Le mot de passe et la confirmation sont différents.", "danger");
            $validation = false;
        }
        // Met à jour le mot de passe si validation est vraie
        if ($validation) {
            $newPassHash = password_hash($_POST["new_pass"], PASSWORD_DEFAULT); // Hachage du mot de passe
            $user = new Users([
                "id" => $_SESSION["userID"],
                "pass" => $newPassHash
            ]);
            $usersManager->updatePass($user);
            $session->setFlash("Le mot de passe a été mis à jour.", "success");
        }
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
            <ol class="breadcrumb bg-transparent">
                <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil</li>
            </ol>
    </nav>

        <section id="profil" class="row">

            <div class="col-sm-12 col-md-12 col-lg-12 mx-auto">

                <?php $session->flash(); // Message en session flash ?>
                

                <div class="row">
            
                    <div class="col-md-6 mt-4">
                        <form action="profil.php" method="post" class="col-md-12 card shadow">
                            <div class="form-group row">
                                <h2 class="card-header col-md-12 h2 bg-light text-dark">Profil</h2>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="login" class="col-md-4 col-form-label">Login</label>
                                        <div class="col-md-8">
                                            <input type="text" name="login" id="login" class="form-control mb-4" 
                                                value="<?= $user->login() ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                        <div class="col-md-8">
                                            <input type="text" name="email" id="email" class="form-control mb-4" 
                                                value="<?= $user->email() ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="name" class="col-md-4 col-form-label">Nom</label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" id="name" class="form-control mb-4"
                                                value="<?= $user->name() ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                        <div class="col-md-8">
                                            <input type="text" name="surname" id="surname" class="form-control mb-4"
                                                value="<?= $user->surname() ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                        <div class="col-md-5">
                                            <input type="date" name="birthdate" id="birthdate" class="form-control mb-4"
                                                value="<?= $user->birthdate() ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="role" class="col-md-4 col-form-label">Rôle</label>
                                        <div class="col-md-5">
                                             <input type="text" name="role" id="role" class="form-control mb-4" readonly
                                                value="<?= $user->role_user() ?>">
                                        </div>
                                    </div> 
                                    <div class="row">
                                        <label for="pass" class="col-md-4 col-form-label mt-4">Mot de passe</label>
                                        <div class="col-md-5">
                                            <div class="div-user-pass">
                                                <input type="password" name="pass" id="pass" class="form-control mt-4 mb-4" >
                                                <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                        <div class="col-md-5">
                                            <div class="div-user-pass">
                                                <input type="password" name="pass_confirm" id="pass_confirm" class="form-control mb-4" >
                                                <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <div class="float-right">
                                                <input type="submit" value="Mettre à jour" id="updateInfo" class="btn btn-blue shadow">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 offset-lg-1 col-lg-5 mt-4">
                        <form action="profil.php" method="post" class="col-md-12 card shadow">
                            <div class="form-group row">
                                <h2 class="card-header col-md-12 h2 bg-light text-dark">Mot de passe</h2>
                            </div>
                            <div class="row">
                                <label for="old_pass" class="col-md-6 col-form-label">Ancien mot de passe</label>
                                <div class="col-md-6">
                                    <input type="password" name="old_pass" id="old_pass" class="form-control mb-4" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <label for="new_pass" class="col-md-6 col-form-label">Nouveau mot de passe</label>
                                        <div class="col-md-6">
                                            <div class="div-user-pass">
                                                <input type="password" name="new_pass" id="new_pass" class="form-control mb-4">
                                                <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="new_pass_confirm" class="col-md-6 col-form-label">Confirmation nouveau mot de passe</label>
                                        <div class="col-md-6">
                                            <div class="div-user-pass">
                                                <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="form-control mb-4">
                                                <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <div class="float-right">
                                                <input type="submit" value="Mettre à jour" id="updatePassword"
                                                    class="btn btn-blue shadow">
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

                </div>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>
    <script src="js/show_password.js"></script>
    <script src="js/show_confirm_password.js"></script>

</body>

</html>
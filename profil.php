<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->db();

// Redirige vers la page de connexion si non connecté
if (empty($_SESSION["userID"])) {
    header("Location: connection.php");
} else {
    // Récupère les informations de l'utilisateur
    $req = $db->prepare("SELECT * FROM users WHERE ID =?");
    $req->execute(array($_SESSION["userID"]));
    $dataUser = $req->fetch();
}
// Récupère les informations du profil sauf en cas de mise à jour des informations
if (!isset($_POST["login"])) {
    $login = htmlspecialchars($dataUser["login"]);
    $email =  htmlspecialchars($dataUser["email"]);
    $name =  htmlspecialchars($dataUser["name"]);
    $surname = htmlspecialchars($dataUser["surname"]);
    $birthdate = htmlspecialchars($dataUser["birthdate"]);
    $role =  htmlspecialchars($dataUser["role"]);
}

// Vérifie si informations dans variable POST
if (!empty($_POST)) {
    $validation = true;
    $message = "";
    $typeAlert = "danger";
    
    // Met à jour des informations du profil
    if (isset($_POST["login"])) {
        $login = htmlspecialchars($_POST["login"]);
        $name = htmlspecialchars($_POST["name"]);
        $surname = htmlspecialchars($_POST["surname"]);
        $email = htmlspecialchars($_POST["email"]);
        $birthdate = !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL;
        $role = htmlspecialchars($_POST["role"]);
        $pass = htmlspecialchars($_POST["pass"]);
        $pass_confirm = htmlspecialchars($_POST["pass_confirm"]);
        $isPasswordCorrect = password_verify($pass, htmlspecialchars($dataUser["pass"])); // Compare le pass envoyé via le formulaire avec la base

        // Vérifie si le login est déjà pris par un autre utilisateur
        $req = $db->prepare("SELECT ID FROM users WHERE login = ? AND ID != ? ");
        $req->execute([
            $login,
            $_SESSION["userID"]
        ]);
        $loginExist = $req->fetch();
        // Vérifie si l'email est déjà pris par un autre utilisateur
        $req = $db->prepare("SELECT ID FROM users WHERE email = ? AND ID != ? ");
        $req->execute([
            $email,
            $_SESSION["userID"]
        ]);
        $emailExist = $req->fetch();

        // Vérifie si le champ login est vide
        if (empty($login)) {
            $message = $message . "<li>Veuillez saisir un login.</li>";
            $validation = false;
        }
        // Vérifie si le login est déjà pris par un autre utilisateur
        if ($loginExist) {
            $message = $message . "<li>Ce login est déjà utilisé. Veuillez en choisir un autre.</li>";
            $validation = false;
        }
        // Vérifie si le champ login est vide
        if (empty($email)) {
            $message = $message . "<li>L'adresse email est obligatoire.</li>";
            $validation = false;
        }
        // Vérifie si l'email est correct
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
            $message = $message . "<li>L'adresse email " . $email . " est incorrecte.</li>";
            $validation = false;
        }
        // Vérifie si l'email est déjà pris par un autre utilisateur
        if ($emailExist) {
            $message = $message . "<li>Cette adresse email est déjà utilisée.</li>";
            $validation = false;
        }
        // Vérifie si le mot de passe est correct
        if (!$isPasswordCorrect) {
            $message = $message . "<li>Le mot de passe est incorrect.</li>";
            $validation = false;
        }
        // Vérifie si la confirmation du mot de passe est identique
        if ($pass!=$pass_confirm) {
            $message = $message . "<li>Le mot de passe et la confirmation sont différents.</li>";
            $validation = false;
        }
        // Met à jour les informations du profil si validation est vraie
        if ($validation) {
            $req = $db->prepare("UPDATE users SET login = :new_login, email = :new_email, name = :new_name, surname = :new_surname, birthdate = :new_birthdate, role = :new_role, update_date = NOW() 
            WHERE ID = :ID");
            $req->execute(array(
                "new_login" => $login,
                "new_email" => $email,
                "new_name" => $name,
                "new_surname" => $surname,
                "new_birthdate" => $birthdate,
                "new_role" => $role,
                "ID" => $_SESSION["userID"]
                ));

            $_SESSION["userLogin"] = $login;
            $message = "Le profil est mis à jour.";
            $typeAlert = "success";
        }
    }

    // Met à jour le mot de passe
    if (isset($_POST["old_pass"])) {
        $old_pass = htmlspecialchars($_POST["old_pass"]);
        $new_pass = htmlspecialchars($_POST["new_pass"]);
        $new_pass_confirm = htmlspecialchars($_POST["new_pass_confirm"]);
        // Vérifie si l'ancien mot de passe est correct
        $isPasswordCorrect = password_verify($old_pass, $dataUser["pass"]); // Compare le mot de passe envoyé via le formulaire avec la base
        if (!$isPasswordCorrect) {
            $message = $message . "<li>L'ancien mot de passe est incorrect.</li>";
            $validation = false;
        }
        // Vérifie si le nouveau mot de passe est valide (minimum 6 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre)
        if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $new_pass)) {
            $message = $message . "<li>Le nouveau mot de passe n'est pas valide.</li>";
            $validation = false;
        }
        // Vérifie si la confirmation du mot de passe est identique
        if ($new_pass!=$new_pass_confirm) {
            $message = $message . "<li>Le mot de passe et la confirmation sont différents.</li>";
            $validation = false;
        }
        // Met à jour le mot de passe si validation est vraie
        if ($validation) {        
        $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT); // Hachage du mot de passe
        $req = $db->prepare("UPDATE users SET pass = :new_pass WHERE ID = :ID");                
        $req->execute(array(
            "new_pass" => $new_pass_hash,
            "ID" => $_SESSION["userID"]
            )); 

        $message = "Le mot de passe est mis à jour.";
        $typeAlert = "success";
        }
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
                                                value="<?= isset($login) ? $login : "" ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                        <div class="col-md-8">
                                            <input type="text" name="email" id="email" class="form-control mb-4" 
                                                value="<?= isset($email) ? $email : "" ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="name" class="col-md-4 col-form-label">Nom</label>
                                        <div class="col-md-8">
                                            <input type="text" name="name" id="name" class="form-control mb-4"
                                                value="<?= isset($name) ? $name : "" ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                        <div class="col-md-8">
                                            <input type="text" name="surname" id="surname" class="form-control mb-4"
                                                value="<?= isset($surname) ? $surname : "" ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                        <div class="col-md-5">
                                            <input type="date" name="birthdate" id="birthdate" class="form-control mb-4"
                                                value="<?= isset($birthdate) ? $birthdate : "" ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="role" class="col-md-4 col-form-label">Rôle</label>
                                        <div class="col-md-5">
                                            <select name="role" id="role" class="custom-select form-control shadow-sm">
                                                <option value="1" <?= isset($role) && $role == 1 ? "selected" : "" ?>>Administrateur</option>
                                                <option value="2" <?= isset($role) &&  $role == 2 ? "selected" : "" ?>>Editeur</option>
                                                <option value="3" <?= isset($role) &&  $role == 3 ? "selected" : "" ?>>Auteur</option>
                                                <option value="4" <?= isset($role) &&  $role == 4 ? "selected" : "" ?>>Contributeur</option>
                                                <option value="5" <?= isset($role) &&  $role == 5 ? "selected" : "" ?>>Abonné</option>
                                            </select>
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
                                <h2 class="card-header  col-md-12 h2 bg-light text-dark">Mot de passe</h2>
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
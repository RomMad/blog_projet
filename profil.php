<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION["user_ID"])) {
        header("Location: connection.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT * FROM users WHERE ID =?");
        $req->execute(array($_SESSION["user_ID"]));
        $dataUser = $req->fetch();
    };
    // Récupère les informations du profil sauf en cas de mise à jour des informations
    if (!isset($_POST["login"])) {
        $login = htmlspecialchars($dataUser["login"]);
        $email =  htmlspecialchars($dataUser["email"]);
        $name =  htmlspecialchars($dataUser["name"]);
        $surname = htmlspecialchars($dataUser["surname"]);
        $birthdate = htmlspecialchars($dataUser["birthdate"]);
        $status =  htmlspecialchars($dataUser["status"]);
    };

    var_dump($_POST);
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $validation = true;
        $msgProfil = "";
        $typeAlert = "danger";
        
        // Met à jour des informations du profil
        if (isset($_POST["login"])) {
            $login = htmlspecialchars($_POST["login"]);
            $name = htmlspecialchars($_POST["name"]);
            $surname = htmlspecialchars($_POST["surname"]);
            $email = htmlspecialchars($_POST["email"]);
            $birthdate = !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL;
            $status = htmlspecialchars($_POST["status"]);
            $pass = htmlspecialchars($_POST["pass"]);
            $pass_confirm = htmlspecialchars($_POST["pass_confirm"]);
            $isPasswordCorrect = password_verify($pass, htmlspecialchars($dataUser["pass"])); // Compare le pass envoyé via le formulaire avec la base

            // Vérifie si le login est déjà pris par un autre utilisateur
            $req = $bdd->prepare("SELECT ID FROM users WHERE login = ? AND ID != ? ");
            $req->execute([
                $login,
                $_SESSION["user_ID"]
            ]);
            $loginExist = $req->fetch();
            // Vérifie si l'email est déjà pris par un autre utilisateur
            $req = $bdd->prepare("SELECT ID FROM users WHERE email = ? AND ID != ? ");
            $req->execute([
                $email,
                $_SESSION["user_ID"]
            ]);
            $emailExist = $req->fetch();

            // Vérifie si le champ login est vide
            if (empty($login)) {
                $msgProfil = $msgProfil . "<li>Veuillez saisir un login.</li>";
                $validation = false;
            };
            // Vérifie si le login est déjà pris par un autre utilisateur
            if ($loginExist) {
                $msgProfil = $msgProfil . "<li>Ce login est déjà utilisé. Veuillez en choisir un autre.</li>";
                $validation = false;
            };
            // Vérifie si le champ login est vide
            if (empty($email)) {
                $msgProfil = $msgProfil . "<li>L'adresse email est obligatoire.</li>";
                $validation = false;
            };
            // Vérifie si l'email est correct
            if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
                $msgProfil = $msgProfil . "<li>L'adresse email " . $email . " est incorrecte.</li>";
                $validation = false;
            };
            // Vérifie si l'email est déjà pris par un autre utilisateur
            if ($emailExist) {
                $msgProfil = $msgProfil . "<li>Cette adresse email est déjà utilisée.</li>";
                $validation = false;
            };
            // Vérifie si le mot de passe est correct
            if (!$isPasswordCorrect) {
                $msgProfil = $msgProfil . "<li>Le mot de passe est incorrect.</li>";
                $validation = false;
            };
            // Vérifie si la confirmation du mot de passe est identique
            if ($pass!=$pass_confirm) {
                $msgProfil = $msgProfil . "<li>Le mot de passe et la confirmation sont différents.</li>";
                $validation = false;
            };
            // Met à jour les informations du profil si validation est vraie
            if ($validation) {
                $req = $bdd->prepare("UPDATE users SET login = :new_login, email = :new_email, name = :new_name, surname = :new_surname, birthdate = :new_birthdate, status = :new_status, date_update = NOW() 
                WHERE ID = :ID");
                $req->execute(array(
                    "new_login" => $login,
                    "new_email" => $email,
                    "new_name" => $name,
                    "new_surname" => $surname,
                    "new_birthdate" => $birthdate,
                    "new_status" => $status,
                    "ID" => $_SESSION["user_ID"]
                    ));

                $_SESSION["user_login"] = $login;
                $msgProfil = "Le profil est mis à jour.";
                $typeAlert = "success";
            };
        };

        // Met à jour le mot de passe
        if (isset($_POST["old_pass"])) {
            $old_pass = htmlspecialchars($_POST["old_pass"]);
            $new_pass = htmlspecialchars($_POST["new_pass"]);
            $new_pass_confirm = htmlspecialchars($_POST["new_pass_confirm"]);
            // Vérifie si l'ancien mot de passe est correct
            $isPasswordCorrect = password_verify($old_pass, $dataUser["pass"]); // Compare le mot de passe envoyé via le formulaire avec la base
            if (!$isPasswordCorrect) {
                $msgProfil = $msgProfil . "<li>L'ancien mot de passe est incorrect.</li>";
                $validation = false;
            };
            // Vérifie si le nouveau mot de passe est valide (minimum 6 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre)
            if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $new_pass)) {
                $msgProfil = $msgProfil . "<li>Le nouveau mot de passe n'est pas valide.</li>";
                $validation = false;
            };
            // Vérifie si la confirmation du mot de passe est identique
            if ($new_pass!=$new_pass_confirm) {
                $msgProfil = $msgProfil . "<li>Le mot de passe et la confirmation sont différents.</li>";
                $validation = false;
            };
            // Met à jour le mot de passe si validation est vraie
            if ($validation) {        
            $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT); // Hachage du mot de passe
            $req = $bdd->prepare("UPDATE users SET pass = :new_pass WHERE ID = :ID");                
            $req->execute(array(
                "new_pass" => $new_pass_hash,
                "ID" => $_SESSION["user_ID"]
                )); 

            $msgProfil = "Le mot de passe est mis à jour.";
            $typeAlert = "success";
            };
        };

        $_SESSION["flash"] = array(
            "msg" => $msgProfil,
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

        <section id="profil" class="row">

            <div class="col-sm-12 col-md-12 col-lg-12 mx-auto">

            <?php include("msg_session_flash.php") ?>

                <div class="row">
                    <form action="profil.php" method="post" class="col-md-6 card mt-4 shadow">
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
                                <div class="row">
                                    <label for="status" class="col-md-4 col-form-label">Type de profil</label>
                                    <div class="col-md-5">
                                        <input type="text" name="status" id="status" class="form-control mb-4"
                                            value="<?= isset($status) ? $status : "" ?>">
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
                                            <input type="submit" value="Mettre à jour" id="validation" class="btn btn-info shadow">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form action="profil.php" method="post" class="offset-md-1 col-md-5 card mt-4 shadow">
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
                                            <input type="submit" value="Mettre à jour" id="validation"
                                                class="btn btn-info shadow">
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
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
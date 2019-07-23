<?php 
    session_start();

    require("connection_bdd.php");

    var_dump($_POST);
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $login = htmlspecialchars($_POST["login"]);
        $name = htmlspecialchars($_POST["name"]);
        $surname = htmlspecialchars($_POST["surname"]);
        $email = htmlspecialchars($_POST["email"]);
        $birthdate = !empty($_POST["birthdate"]) ? htmlspecialchars($_POST["birthdate"]) : NULL;
        $pass = htmlspecialchars($_POST["pass"]);
        $pass_confirm = htmlspecialchars($_POST["pass_confirm"]);
        $validation = true;
        $msgInscription = "Attention :";
        $typeAlert = "danger";

        // Vérifie si le login est déjà utilisé
        $req = $bdd->prepare("SELECT * FROM users WHERE login = ? ");
        $req->execute([$login]);
        $loginExist = $req->fetch();
        // Vérifie si l'adresse email est déjà utilisée
        $req = $bdd->prepare("SELECT * FROM users WHERE email = ? ");
        $req->execute([$email]);
        $emailExist = $req->fetch();

        // Vérifie si le champ login est vide
        if (empty($login)) {
            $msgInscription = $msgInscription . "<li>Veuillez saisir un Login.</li>";
            $validation = false;
        };
        // Vérifie si le login est déjà utilisé
        if ($loginExist) {
            $msgInscription = $msgInscription . "<li>Ce login est déjà utilisé. Veuillez en utiliser un autre.</li>";
            $validation = false;
        };
        // Vérifie si l'adresse email est correcte
        if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $email)) {
            $msgInscription = $msgInscription . "<li>L'adresse \"" . $email . "\" est incorrecte.</li>";
            $validation = false;
        };
        // Vérifie si l'adresse email est déjà utilisée
        if ($emailExist) {
            $msgInscription = $msgInscription . "<li>L'adresse email est déjà utilisée.</li>";
            $validation = false;
        };
        // Vérifie si le mot de passe est correct
        // (?=.*[a-z])  : teste la présence d'une lettre minuscule
        // (?=.*[A-Z])  : teste la présence d'une lettre majuscule
        // (?=.*[0-9])  : teste la présence d'un chiffre de 0 à 9
        // .{6,}$       : teste si au moins 6 caractères
        if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $pass)) {
            $msgInscription = $msgInscription . "<li>Le mot de passe n'est pas valide.</li>";
            $validation = false;
        };
        // Vérifie si la confirmation du mot de passe est identique
        if ($pass!=$pass_confirm) {
            $msgInscription =  $msgInscription . "<li>Le mot de passe et la confirmation sont différents.</li>";
            $validation = false;
        };
        // Si validation est vrai, valide l'inscription de l'utilisateur
        if ($validation) {
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT); // Hachage du mot de passe
            // Insert les données dans la table users
            $req = $bdd->prepare("INSERT INTO users(login, email, name, surname, birthdate, pass) 
                                    VALUES(:login, :email, :name, :surname, :birthdate, :pass)");
            $req->execute(array(
                "login" => $login,
                "email" => $email,
                "name" => $name,
                "surname" => $surname,
                "birthdate" => $birthdate,
                "pass" => $pass_hash,
                ));
                // Récupère l'ID de l'utilisateur
                $req = $bdd->prepare("SELECT ID FROM users WHERE login = ? ");
                $req->execute([$login]);
                $idUser = $req->fetch();
                // Ajoute les infos de l"utilisateurs dans la Session
                $_SESSION["user_ID"] = $idUser["ID"];
                $_SESSION["user_login"] = $login;
                $typeAlert = "success";
                $msgInscription = "L'inscription est réussie.";

                header("Refresh: 2; url=index.php");
            };

        $_SESSION["flash"] = array(
            "msg" => $msgInscription,
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

        <section id="inscription" class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

            <?php include("msg_session_flash.php") ?>

                <form action="inscription.php" method="post" class="col-md-12 card shadow mt-4">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Inscription</h3>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="login" id="login" class="form-control mb-4">
                                </div>
                            </div>
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control mb-4">
                                </div>
                            </div>
                            <div class="row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="name" id="name" class="form-control mb-4">
                                </div>
                            </div>
                            <div class="row">
                                <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="surname" id="surname" class="form-control mb-4">
                                </div>
                            </div>
                            <!-- <div class="row">
                                <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                <div class="col-md-5">
                                    <input type="date" name="birthdate" id="birthdate" class="form-control mb-4">
                                </div>
                            </div> -->
                            <div class="row">
                                <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-5">
                                    <div class="div-user-pass">
                                        <input type="password" name="pass" id="pass" class="form-control mb-4">
                                        <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                <div class="col-md-5">
                                    <div class="div-user-pass">
                                        <input type="password" name="pass_confirm" id="pass_confirm" class="form-control mb-4">
                                        <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Valider" id="validation" class="btn btn-info shadow">
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
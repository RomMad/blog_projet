<?php 
    session_start();

    include("connection_bdd.php");

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
        if ($name) {
            // Vérifie si le login est déjà utilisé
            $req = $bdd->query("SELECT COUNT(*) as NbID FROM users WHERE user_login='$login'");
            $data = $req->fetch();
            if ($data["NbID"]>0) {
                $infoInscription = "Ce login est déjà utilisé. Veuillez en utiliser un autre.";
            } else {
                // Vérifie si la confirmation du mot de passe est identique
                if ($pass==$pass_confirm) {
                    $pass_hash = password_hash($pass, PASSWORD_DEFAULT); // Hachage du mot de passe
                // Insert les données dans la table users
                $req = $bdd->prepare("INSERT INTO users(user_login, user_email, user_name, user_surname, user_birthdate, user_pass) VALUES(:user_login, :user_email, :user_name, :user_surname, :user_birthdate, :user_pass)");
                $req->execute(array(
                    "user_login" => $login,
                    "user_email" => $email,
                    "user_name" => $name,
                    "user_surname" => $surname,
                    "user_birthdate" => $birthdate,
                    "user_pass" => $pass_hash,
                    ));
                    // Récupère l'ID de l'utilisateur
                    $req = $bdd->query("SELECT ID FROM users WHERE user_login ='$login'");
                    $data = $req->fetch();
                    // Ajoute les infos de l"utilisateurs dans la Session
                    $_SESSION["ID"] = $data["ID"];
                    $_SESSION["user_login"] = $login;
                    $infoInscription = "Inscription réussie.";
                    ?> 
                    <meta http-equiv="refresh" content="2;url=index.php"/>
                    <?php             
                } else {
                    $infoInscription = "Mot de passe et confirmation différents.";
                };
            };            
        } else {
            $infoInscription = "Veuillez saisir un Login.";
        };
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="inscription" class="row">
            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

            <?= isset($infoInscription) ? $infoInscription : "" ?>

                <form action="inscription.php" method="post" class="col-md-12 card shadow mt-4">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Inscription</h3>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="login" id="login" class="form-control mb-4" required>
                                </div>
                            </div>
                            <div class="row">
                                <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="email" name="email" id="email" class="form-control mb-4" required>
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
                            <div class="row">
                                <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                <div class="col-md-5">
                                    <input type="date" name="birthdate" id="birthdate" class="form-control mb-4">
                                </div>
                            </div>                            
                            <div class="row">
                                <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="pass" id="pass" class="form-control mb-4" required>
                                </div>
                            </div>
                            <div class="row">
                                <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="pass_confirm" id="pass_confirm" class="form-control mb-4" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Valider" id="validation" class="btn btn-info shadow">
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

</html
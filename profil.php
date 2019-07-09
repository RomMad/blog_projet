<?php 
    session_start();

    include("connection_bdd.php");
    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION)) {
        header("Location: connection.php");
    };

    var_dump($_POST);

    // Met à jour des informations du profil
    if (isset($_POST["user_login"])) {
        $user_login = htmlspecialchars($_POST["user_login"]);
        $user_name = htmlspecialchars($_POST["user_name"]);
        $user_surname = htmlspecialchars($_POST["user_surname"]);
        $user_email = htmlspecialchars($_POST["user_email"]);
        $user_birthdate = !empty($_POST["user_birthdate"]) ? htmlspecialchars($_POST["user_birthdate"]) : NULL;
        $user_status = htmlspecialchars($_POST["user_status"]);
        $user_pass = htmlspecialchars($_POST["user_pass"]);
        $user_pass_confirm = htmlspecialchars($_POST["user_pass_confirm"]);
        // 1) Vérifie si la confirmation du mot de passe est identique
        if ($user_pass!=$user_pass_confirm) {
            $infoProfil = "Le mot de passe et la confirmation sont différents.";
        } else {
            // 2) Récupère l'ID de l'utilisateur et de son pass haché
            $req = $bdd->prepare("SELECT ID, user_pass FROM users WHERE ID = ?");
            $req->execute(array($_SESSION["ID"]));
            $data = $req->fetch();
            // 3) Vérifie si le login et le mot de passe existent
            if (!$data) {
                $infoProfil = "Le mot de passe est incorrect.";
            } else {
                $isPasswordCorrect = password_verify($user_pass, htmlspecialchars($data["user_pass"])); // Compare le pass envoyé via le formulaire avec la base
                // 4) Vérifie si le mot de passe est correct
                if (!$isPasswordCorrect) {
                    $infoProfil = "Le mot de passe est incorrect.";
                } else {
                    // 5) Vérifie si l'email est correct
                    if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $user_email)) {
                        $infoProfil = "L'adresse \"" . $user_email . "\" est incorrecte.";
                    } else {
                        // 6) Met à jour les informations du profil
                        $req = $bdd->prepare("UPDATE users SET user_login = :new_user_login, user_name = :new_user_name, user_surname = :new_user_surname, user_email = :new_user_email, user_birthdate = :new_user_birthdate, user_status = :new_user_status, user_date_update = NOW() 
                        WHERE ID = :ID");
                        $req->execute(array(
                            "new_user_login" => $user_login,
                            "new_user_name" => $user_name,
                            "new_user_surname" => $user_surname,
                            "new_user_email" => $user_email,
                            "new_user_birthdate" => $user_birthdate,
                            "new_user_status" => $user_status,
                            "ID" => $_SESSION["ID"]
                            )); 
                        $_SESSION["user_login"] = $user_login;
                        $infoProfil = "Le profil est mis à jour.";
                    };
                };
            };
        };
    };

    // Mise à jour du mot de passe
    if (isset($_POST["old_pass"])) {
        $old_pass = htmlspecialchars($_POST["old_pass"]);
        $new_pass = htmlspecialchars($_POST["new_pass"]);
        $new_pass_confirm = htmlspecialchars($_POST["new_pass_confirm"]);
        // 1) Récupère le mot de passe haché de l'utilisateur
        $req = $bdd->prepare("SELECT user_pass FROM users WHERE ID = ?");
        $req->execute(array($_SESSION["ID"]));
        $data = $req->fetch();
        $isPasswordCorrect = password_verify($old_pass, $data["user_pass"]); // Compare le mot de passe envoyé via le formulaire avec la base
        // 2) Vérifie si l'ancien mot de passe est correct
        if (!$isPasswordCorrect) {
            $infoProfil = "L'ancien mot de passe est incorrect.";
        } else {
            // 3) Vérifie si le nouveau mot de passe est valide (minimum 6 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre)
            if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $new_pass)) {
                $infoProfil = "Le nouveau mot de passe n'est pas valide.";
            } else {
                // 4) Vérifie si la confirmation du mot de passe est identique
                if ($new_pass!=$new_pass_confirm) {
                    $infoProfil = "Le mot de passe et la confirmation sont différents.";
                } else {
                    $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT); // Hachage du mot de passe
                    // 5) Met à jour le mot de passe
                    $req = $bdd->prepare("UPDATE users SET user_pass = :new_pass WHERE ID = :ID");                
                    $req->execute(array(
                        "new_pass" => $new_pass_hash,
                        "ID" => $_SESSION["ID"]
                        )); 
                    $infoProfil = "Le mot de passe est mis à jour.";
                };
            };
        };
    };

    // Récupère les informations du profil
    if ((empty($_POST) && isset($_SESSION["ID"])) || isset($_POST["old_pass"])) {
        $req = $bdd->prepare("SELECT ID, user_login, user_name, user_surname, user_email, user_status, user_birthdate FROM users WHERE ID =?");
        $req->execute(array($_SESSION["ID"]));
        $data = $req->fetch();
        
        $user_login = htmlspecialchars($data["user_login"]);
        $user_name =  htmlspecialchars($data["user_name"]);
        $user_surname = htmlspecialchars($data["user_surname"]);
        $user_birthdate = htmlspecialchars($data["user_birthdate"]);
        $user_email =  htmlspecialchars($data["user_email"]);
        $user_status =  htmlspecialchars($data["user_status"]);
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="profil" class="row">

            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

                <?php  
                    if (isset($infoProfil)) {
                        echo $infoProfil;
                    ?>
                <?php 
                    }; 
                ?>
                
                <form action="profil.php" method="post" class="col-md-12 card shadow mt-4">
                    <div class="form-group row">
                        <h2 class="card-header col-md-12 h2 bg-light text-dark">Profil</h2>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="user_login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_login" id="user_login" class="form-control mb-4" required
                                        value="<?= isset($user_login) ? $user_login : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_email" class="col-md-4 col-form-label">Adresse email</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_email" id="user_email" class="form-control mb-4" required
                                        value="<?= isset($user_email) ? $user_email : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_name" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_name" id="user_name" class="form-control mb-4"
                                        value="<?= isset($user_name) ? $user_name : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_surname" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_surname" id="user_surname" class="form-control mb-4"
                                        value="<?= isset($user_surname) ? $user_surname : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                <div class="col-md-5">
                                    <input type="date" name="user_birthdate" id="user_birthdate" class="form-control mb-4"
                                        value="<?= isset($user_birthdate) ? $user_birthdate : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_status" class="col-md-4 col-form-label">Type de profil</label>
                                <div class="col-md-5">
                                    <input type="text" name="user_status" id="user_status" class="form-control mb-4"
                                        value="<?= isset($user_status) ? $user_status : "" ?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_pass" class="col-md-4 col-form-label mt-4">Mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="user_pass" id="user_pass" class="form-control mt-4 mb-4" required>
                                    <span class="fas fa-eye"></span>
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="user_pass_confirm" id="user_pass_confirm" class="form-control mb-4" required>
                                    <span class="fas fa-eye"></span>
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

                <form action="profil.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h2 class="card-header h4 col-md-12 h2 bg-light text-dark">Mise à jour du mot de passe</h2>
                    </div>
                    <div class="row">
                        <label for="old_pass" class="col-md-4 col-form-label">Ancien mot de passe</label>
                        <div class="col-md-5">
                            <input type="password" name="old_pass" id="old_pass" class="form-control mb-4">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="new_pass" class="col-md-4 col-form-label">Nouveau mot de passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="new_pass" id="new_pass" class="form-control mb-4">
                                </div>
                            </div>
                            <div class="row">
                                <label for="new_pass_confirm" class="col-md-4 col-form-label">Confirmation nouveau mot
                                    de
                                    passe</label>
                                <div class="col-md-5">
                                    <input type="password" name="new_pass_confirm" id="new_pass_confirm"
                                        class="form-control mb-4">
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
                                    Le mot de passe doit contenir au minimum :
                                    <ul>
                                        <li>6 caractères</li>
                                        <li>1 lettre minuscule</li>
                                        <li>1 lettre majuscule</li>
                                        <li>1 chiffre</li>
                                    </ul>
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
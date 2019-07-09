<?php 
    session_start();

    include("connection_bdd.php");

    var_dump($_POST);
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $user_login = htmlspecialchars($_POST["user_login"]);
        $user_pass = htmlspecialchars($_POST["user_pass"]);
        // Récupère l'ID de l'utilisateur et son password haché
        $req = $bdd->prepare("SELECT ID, user_pass FROM users WHERE user_login = ?");
        $req->execute(array($user_login));
        $data = $req->fetch();
        // Compare le password envoyé via le formulaire avec la base
        $isPasswordCorrect = password_verify($user_pass, $data["user_pass"]);
        // Vérifie si login et password existent
        if ($data && $isPasswordCorrect) {
            $_SESSION["ID"] = $data["ID"];
            $_SESSION["user_login"] = $user_login;
            $infoConnection = "Vous ête connecté.";
            ?> 
            <meta http-equiv="refresh" content="1;url=index.php"/>
            <?php
        } else {
            $infoConnection = "Login ou mot de passe incorrect.";
        };
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">
        <section id="connection" class="row">
            <form action="connection.php" method="post" class="form-signin mx-auto text-center">
                <h1 class="h3 mb-4 font-weight-normal">Merci de vous connecter</h1>
                <label for="user_login" class="sr-only">Login</label>
                <input type="text" name="user_login" id="user_login" class="form-control mb-2" placeholder="Login" required autofocus="">
                <label for="user_pass" class="sr-only">Mot de passe</label>
                <div id="div-user-pass">
                    <input type="password" name="user_pass" id="user_pass" class="form-control mb-4" placeholder="Mot de passe" required>
                    <span class="fas fa-eye"></span>
                </div>
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Se souvenir de moi
                    </label>
                </div>
                <input type="submit" value="Se connecter" id="validation" class="btn btn-lg btn-info btn-block shadow">
                <a href="inscription.php" class="btn btn-lg btn-info btn-block shadow mb-4">S'inscrire</a>

            <?= isset($infoConnection) ? $infoConnection : "" ?>

            <p class="mt-4 text-muted">© 2019</p>
            </form>
               </section>
    </div>
    
    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</html>
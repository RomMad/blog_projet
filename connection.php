<?php 

session_start();

include("connection_bdd.php");

var_dump($_POST);
// Vérification si informations dans variable POST
if (!empty($_POST)) {
    $user_login = htmlspecialchars($_POST['user_login']);
    $user_pass = htmlspecialchars($_POST['user_pass']);

    //  Récupération de l'utilisateur et de son pass hashé
    $req = $bdd->prepare('SELECT ID, user_pass FROM users WHERE user_login = :user_login');
    $req->execute(array(
        'user_login' => $user_login));
    $resultat = $req->fetch();

    // Comparaison du pass envoyé via le formulaire avec la base
    $isPasswordCorrect = password_verify($_POST['user_pass'], $resultat['user_pass']);
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="connection" class="row">

            <form action="connection.php" method="post" class="form-signin mx-auto text-center">
                <h1 class="h3 mb-3 font-weight-normal">Merci de vous connecter</h1>
                <label for="user_login" class="sr-only">Login</label>
                <input type="text" name="user_login" id="user_login" class="form-control" placeholder="Login" required autofocus="">
                <br />
                <label for="user_pass" class="sr-only">Mot de passe</label>
                <input type="password" name="user_pass" id="user_pass" class="form-control" placeholder="Mot de passe" required>
                <br />
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Se souvenir de moi
                    </label>
                </div>
                <button class="btn btn-lg btn-info btn-block" type="submit">Se connecter</button>
                <br />

            <?php include("connection_bdd.php"); ?>

        <?php 
            // Vérifie si Login et Password existent
            if (!empty($_POST)) {
                if (!$resultat)
                {
                    ?>
                    <p>Mauvais identifiant ou mot de passe !<p>
                    <?php
                }
                else
                {
                    if ($isPasswordCorrect) {
                        $_SESSION['ID'] = $resultat['ID'];
                        $_SESSION['user_login'] = $user_login;
                        ?>
                        <p>Vous êtes connecté !<p>
                        <!-- <meta http-equiv="refresh" content="1;url=index.php"/> -->
                        <?php
                    }
                    else {
                        ?>
                        <p>Mauvais identifiant ou mot de passe !<p>
                        <?php                    
                    }
                }
            } 

        ?>

                <p class="mt-5 mb-3 text-muted">© 2019</p>
            </form>
       
        </section>

    </div>

    <?php include("scripts.html"); ?>

</html>
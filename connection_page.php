<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.html"); ?>

    <div class="container">

        <section id="connection" class="row">

            <form action="connection_page.php" method="post" class="form-signin mx-auto text-center">
                <img class="mb-4" src="" alt="" width="72" height="72">
                <h1 class="h3 mb-3 font-weight-normal">Merci de vous connecter</h1>
                <label for="login" class="sr-only">Login</label>
                <input type="text" name="login" id="login" class="form-control" placeholder="Login" required autofocus="">
                <br />
                <label for="password" class="sr-only">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" required>
                <br />
                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Se souvenir de moi
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Se connecter</button>
                <br />

            <?php include("connection_bdd.php"); ?>

        <?php 
            // Vérifie si Login et Password existent
            if (isset($_POST['login'], $_POST['password'])) {
                $login = htmlspecialchars($_POST['login']);
                $password = htmlspecialchars($_POST['password']);

                //  Récupération de l'utilisateur et de son pass hashé
                $req = $bdd->prepare('SELECT id, pass FROM users WHERE pseudo = :pseudo');
                $req->execute(array(
                    'pseudo' => $login));
                $resultat = $req->fetch();

                // Comparaison du pass envoyé via le formulaire avec la base
                $isPasswordCorrect = password_verify($_POST['password'], $resultat['pass']);

                if (!$resultat)
                {
                    ?>
                    <p>Mauvais identifiant ou mot de passe !<p>
                    <?php
                }
                else
                {
                    if ($isPasswordCorrect) {
                        $_SESSION['id'] = $resultat['id'];
                        $_SESSION['login'] = $login;
                        ?>
                        <p>Vous êtes connecté !<p>
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
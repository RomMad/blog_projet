<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$usersManager = new UsersManager();
$db = $usersManager->db();

// Redirige vers la page d'accueil si l'utilisateur est déjà connecté
if (!empty($_SESSION["userID"])) {
    header("Location: index.php");
}

// Vérifie si informations dans variable POST
if (!empty($_POST)) {
    $validation = true;

    // Vérifie si le login est vide
    if (empty($_POST["login"]) || empty($_POST["pass"])) {
        $validation = false;
        $session->setFlash("Veuillez saisir votre login <br />et votre mot de passe.", "danger");
    } else {
        // Récupère l'ID de l'utilisateur et son password haché
        $user = $usersManager->verify(htmlspecialchars($_POST["login"]));
        // Si l'utilisateur existe, vérifie le mot de passe   
        if ($user) {
            $isPasswordCorrect = password_verify($_POST["pass"], $user->pass());// Compare le password envoyé via le formulaire avec la base  
        }
        if (!$user || !$isPasswordCorrect) {
            $validation = false;
            $session->setFlash("Login ou mot de passe incorrect.", "danger");
        }
    }

    if ($validation == true) {
        // Enregistre les informations de l'utilisateurs en session
        $_SESSION["userID"] = $user->id();
        $_SESSION["userLogin"] = htmlspecialchars($_POST["login"]);
        $_SESSION["userRole"] = $user->role();
        // Enregistre le login et le mot de passe en cookie si la case "Se souvenir de moi" est cochée
        if (isset($_POST["remember"])) {
            setcookie("user[login]", $_SESSION["userLogin"], time() + 365*24*3600, null,null, false, true);
            setcookie("user[pass]", htmlspecialchars($_POST["pass"]), time() + 365*24*3600, null,null, false, true);
        }
        // Ajoute la date de connexion de l'utilisateur dans la table dédiée
        $req = $db->prepare("INSERT INTO connections (user_ID) values(:user_ID)");
        $req->execute([
            "user_ID" => $user->id()
        ]);

        $session->setFlash("Vous êtes connecté.", "success");
        header("Location: index.php");
        exit;

    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">
        <section id="connection" class="row">
            <form action="connection.php" method="post" class="form-signin mx-auto text-center">

                <?php $session->flash(); // Message en session flash ?>      

                <h1 class="h3 mb-4 font-weight-normal">Merci de vous connecter</h1>
                <label for="login" class="sr-only">Login</label>
                <input type="text" name="login" id="login" class="form-control mb-2 shadow-sm" placeholder="Login" autofocus="" value="<?= isset($_COOKIE["user"]["login"]) ? $_COOKIE["user"]["login"] : "" ?>">
                <label for="pass" class="sr-only">Mot de passe</label>
                <div class="div-user-pass">
                    <input type="password" name="pass" id="pass" class="form-control mb-4 shadow-sm" placeholder="Mot de passe" value="<?= isset($_COOKIE["user"]["pass"]) ? $_COOKIE["user"]["pass"] : "" ?>">
                    <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                </div>
                <div class="checkbox mb-3">
                    <label for="remember">
                        <input type="checkbox" name="remember" id="remember" value="true"> Se souvenir de moi
                    </label>
                </div>
                <input type="submit" value="Se connecter" id="validation" class="btn btn-lg btn-blue btn-block mb-4 shadow">
                <a href="inscription.php" class="btn btn-lg btn-blue btn-block mb-4 shadow">S'inscrire</a>

                <a href="forgotpassword.php" class="text-blue mb-4">Login ou mot de passe oublié ?</a>

            <p class="mt-4 text-muted">© 2019</p>
            </form>
               </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>
    <script src="js/show_password.js"></script>

</body>

</html>
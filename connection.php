<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->db();

// Redirige vers la page d'accueil si l'utilisateur est déjà connecté
if (!empty($_SESSION["userID"])) {
    header("Location: index.php");
}

// Vérifie si informations dans variable POST
if (!empty($_POST)) {
    $login = htmlspecialchars($_POST["login"]);
    $pass = htmlspecialchars($_POST["pass"]);

    // Récupère l'ID de l'utilisateur et son password haché
    $req = $db->prepare("SELECT ID, pass, role FROM users WHERE login = ?");
    $req->execute(array($login));
    $dataUser = $req->fetch();

    // Vérifie si login et password existent   
    $isPasswordCorrect = password_verify($pass, $dataUser["pass"]);// Compare le password envoyé via le formulaire avec la base  
    if ($dataUser && $isPasswordCorrect) {
        $_SESSION["userID"] = htmlspecialchars($dataUser["ID"]);
        $_SESSION["userLogin"] = $login;
        $_SESSION["userRole"] = htmlspecialchars($dataUser["role"]);

        // Enregistre le login et le mot de passe en cookie si la case "Se souvenir de moi" est cochée
        if (isset($_POST["remember"])) {
            setcookie("user[login]", $login, time() + 365*24*3600, null,null, false, true);
            setcookie("user[pass]", $pass, time() + 365*24*3600, null,null, false, true);
        }

        // Ajoute la date de connexion de l'utilisateur dans la table dédiée
        $req = $db->prepare("INSERT INTO connections (user_ID) values(:user_ID)");
        $req->execute(array("user_ID" => htmlspecialchars($dataUser["ID"])));

        $message = "Vous êtes connecté.";
        $typeAlert = "success";
        header("Refresh: 2; url=index.php");
    } else {
        $message = "Login ou mot de passe incorrect.";
        $typeAlert = "danger";
    }

    // Vérifie si le champ login est vide
    if (empty($login)) {
        $message = "Veuillez saisir un Login.";
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
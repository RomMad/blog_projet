<?php include("connection_bdd.php"); ?>

<?php 
if (isset($_POST['login'], $_POST['password'])) {
    $login = htmlspecialchars($_POST['login']);
    $password = htmlspecialchars($_POST['password']);

    //  Récupération de l'utilisateur et de son pass hashé
    $req = $bdd->prepare('SELECT id, pass FROM membres WHERE pseudo = :pseudo');
    $req->execute(array(
        'pseudo' => $login));
    $resultat = $req->fetch();

    // Comparaison du pass envoyé via le formulaire avec la base
    $isPasswordCorrect = password_verify($_POST['password'], $resultat['pass']);

    if (!$resultat)
    {
        echo 'Mauvais identifiant ou mot de passe !';
    }
    else
    {
        if ($isPasswordCorrect) {
            session_start();
            $_SESSION['id'] = $resultat['id'];
            $_SESSION['login'] = $login;
            echo 'Vous êtes connecté !';
        }
        else {
            echo 'Mauvais identifiant ou mot de passe !';
        }
    }
}

// Redirige vers page de connexion
header('Location: connection_page.php');

?>
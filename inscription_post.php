<?php include("connection_bdd.php"); ?>

<?php
// Vérification de la validité des informations
if (isset($_POST['user_login'], $_POST['user_email'], $_POST['user_pass'])) {
    $user_login = htmlspecialchars($_POST['user_login']);
    $user_email = htmlspecialchars($_POST['user_email']);
    $user_pass_hash = password_hash(htmlspecialchars($_POST['user_pass']), PASSWORD_DEFAULT); // Hachage du mot de passe
    // Insert les données dans la table users
    $req = $bdd->prepare('INSERT INTO users(user_login, user_email, user_pass) VALUES(:user_login, :user_email, :user_pass)');
    $req->execute(array(
        'user_login' => $user_login,
        'user_email' => $user_email,
        'user_pass' => $user_pass_hash,
        ));

        $statusInscription = "Inscription réussie.";
    }
// Redirige vers page d'inscription
header('Location: inscription_page.php');

?>
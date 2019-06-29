<?php include("connection_bdd.php"); ?>

<?php
// Vérification de la validité des informations
if (isset($_POST['pseudo'], $_POST['email'], $_POST['password'])) {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $email = htmlspecialchars($_POST['email']);
    $password_hash = password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT); // Hachage du mot de passe
    // Insert les données dans la table users
    $req = $bdd->prepare('INSERT INTO users(pseudo, email, pass) VALUES(:pseudo, :email, :pass)');
    $req->execute(array(
        'pseudo' => $pseudo,
        'email' => $email,
        'pass' => $password_hash,
        ));

        $statusInscription = "Inscription réussie.";
    }
// Redirige vers page d'inscription
header('Location: inscription_page.php');

?>
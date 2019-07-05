<?php 

session_start();

include("connection_bdd.php");

var_dump($_POST);
// Vérification si informations dans variable POST

if (empty($_SESSION)) {
    echo "Vous devez vous connecter pour écrire un article.";
    // Redirige vers la page de connexion
    header('Location: connection.php');
};

if (!empty($_POST)) {
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
    // Redirige vers page d'inscription
    // header('Location: inscription_page.php');
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="post_form" class="row">

            <div class="col-sm-12 col-md-10 mx-auto">
                <form class="">
                    <h2>Nouvel article </h2>
                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input type="text" class="form-control" id="title">
                    </div>
                    <div class="form-group">
                        <label for="content">Contenu</label>
                        <textarea class="form-control" id="content" rows="10"></textarea>
                    </div>
                    <div class="form-group col-sm-4 col-md-2">
                        <label for="status">Statut</label>
                        <select class="form-control" id="status">
                            <option>Publier</option>
                            <option>Brouillon</option>
                        </select>
                    </div>
                    <div class="form-group float-right">
                                <input type="submit" value="Valider" id="validation" class="btn btn-primary shadow">
                </form>

                <?php  
                if (isset($statusInscription)) {
                    echo $statusInscription;
                };
            ?>

        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: connection.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT role FROM users WHERE ID =?");
        $req->execute(array($_SESSION["userID"]));
        $userRole = $req->fetch();
        if ($userRole["role"]!=0) {
            header("Location: index.php");
        };
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <div class="row">
            <div class="col-md-12 mx-auto mt-4">

                <h2 class="mb-4">Administration du site</h2>

            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mt-4">

                <div  class="list-group shadow">
                    <a href="admin-posts.php" class="list-group-item list-group-item-action text-info">Gestion des articles</a>
                    <a href="admin-comments.php" class="list-group-item list-group-item-action text-info">Gestion des commentaires</a>
                    <a href="admin-users.php" class="list-group-item list-group-item-action text-info">Gestion des articles</a>
                </div>

            </div>
        </div>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
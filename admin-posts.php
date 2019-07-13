<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page d'accueil si
    if (empty($_SESSION["user_ID"])) {
        header("Location: index.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT * FROM users WHERE ID =?");
        $req->execute(array($_SESSION["user_ID"]));
        $dataUser = $req->fetch();
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

                <h2 class="mb-4">Gestion des articles</h2>

            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mt-4">



            </div>
        </div>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
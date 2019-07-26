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
    if ($userRole["role"]!=1) {
        header("Location: index.php");
    };
};

var_dump($_POST);

if (!empty($_POST)) {
    if (isset($_POST["moderation"])) {
       $moderation = 1; 
    } else {
        $moderation = 0; 
    };
    $req = $bdd->prepare("UPDATE settings SET moderation = :moderation WHERE ID = 1 ");
    $req->execute(array(
        "moderation" => $moderation
    ));

    $msgAdmin = "Les paramètres ont été mis à jour.";
    $typeAlert = "success"; 

    $_SESSION["flash"] = array(
        "msg" => $msgAdmin,
        "type" =>  $typeAlert
    );
};
    // Récupère les paramètres
    $req = $bdd->prepare("SELECT * FROM settings");
    $req->execute(array());
    $dataSettings = $req->fetch();   
    echo $dataSettings["moderation"];

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
            <form action="admin.php" method="post" class="col-md-6 card shadow mt-4">
                <div class="form-group row">
                    <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Paramètres</h3>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="row">
                            <label for="moderation" class="col-md-4 col-form-label">Modération</label>
                            <div class="col-md-1">
                                <input type="checkbox" name="moderation" id="moderation" class="form-control mb-4" 
                                    value="true" <?= $dataSettings["moderation"] == 1 ? "checked" : "" ?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <input type="submit" name="validation" value="Valider" id="validation" class="btn btn-info shadow">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-md-6 mt-4">

                <div  class="list-group shadow">
                    <a href="admin_posts.php" class="list-group-item list-group-item-action text-info">Gestion des articles</a>
                    <a href="admin_comments.php" class="list-group-item list-group-item-action text-info">Gestion des commentaires</a>
                    <a href="admin_users.php" class="list-group-item list-group-item-action text-info">Gestion des articles</a>
                </div>

            </div>
        </div>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
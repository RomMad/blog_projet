<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["user_ID"])) {
        header("Location: index.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT status FROM users WHERE ID =?");
        $req->execute(array($_SESSION["user_ID"]));
        $statusUser = $req->fetch();
        if ($statusUser["status"]!=0) {
            header("Location: index.php");
        };
    };

    // Compte le nombre d'articles
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_Posts FROM posts");
    $req->execute(array());
    $nbPosts = $req->fetch();

    var_dump($_POST);
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $_SESSION["nbDisplayedPostsAdmin"] = htmlspecialchars($_POST["nbDisplayed"]);
    };
    if (!isset($_SESSION["nbDisplayedPostsAdmin"])) {
        $_SESSION["nbDisplayedPostsAdmin"] = 20;
    };
    $nbDisplayed = $_SESSION["nbDisplayedPostsAdmin"];

    var_dump($_GET);  
    // Vérification si informations dans variable GET
    if (!empty($_GET["page"])) {
        $page = htmlspecialchars($_GET["page"]);
        // Calcul le nombre de pages par rapport aux nombre d'articles
        $maxPost =  $page*$nbDisplayed;
        $minPost = $maxPost-$nbDisplayed;
    } else  {
        $page = 1;
        $minPost = 0;
        $maxPost = $nbDisplayed;
    };

    // Initialisation des variables pour la pagination
    $linkNbDisplayed= "admin-posts.php";
    $linkPagination= "admin-posts.php";
    $anchorPagination= "#table-admin-posts";
    $nbPages = ceil($nbPosts["nb_Posts"] / $nbDisplayed);
    require("pagination.php");

    // Récupère les derniers articles
    $req = $bdd->prepare("SELECT p.ID, p.title, p.user_login, u.login, p.status, 
    DATE_FORMAT(p.date_creation, \"%d/%m/%Y %H:%i\") AS date_creation_fr, 
    DATE_FORMAT(p.date_update, \"%d/%m/%Y %H:%i\") AS date_update_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.user_ID = u.ID
    ORDER BY p.date_creation DESC
    LIMIT  $minPost, $maxPost");
    $req->execute(array());

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <div class="row">
            <section id="table-admin-posts" class="col-md-12 mx-auto mt-4">

                <h2 class="mb-4">Gestion des articles</h2>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                        <th scope="col" class="align-middle">ID</th>
                        <th scope="col" class="align-middle">Titre</th>
                        <th scope="col" class="align-middle">Auteur</th>
                        <th scope="col" class="align-middle">Statut</th>
                        <th scope="col" class="align-middle">Date de création</th>
                        <th scope="col" class="align-middle">Date de mise à jour</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while ($dataPosts=$req->fetch()) {
                        ?>
                                <tr>
                                    <th scope="row"><a href="edit_post.php?post=<?= $dataPosts["ID"] ?>" class="text-info"><?= $dataPosts["ID"] ?></a></th>
                                    <td><a href="edit_post.php?post=<?= $dataPosts["ID"] ?>" class="text-info font-weight-bold"><?= $dataPosts["title"] ?></a></td>
                                    <td><?= $dataPosts["user_login"] ?></td>
                                    <td><?= $dataPosts["status"] ?></td>
                                    <td><?= $dataPosts["date_creation_fr"] ?></td>
                                    <td><?= $dataPosts["date_update_fr"] ?></td>
                                    </a>
                                </tr>
                        <?php
                            };
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
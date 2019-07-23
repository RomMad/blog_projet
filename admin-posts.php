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

    
    if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "title" || $_GET["orderBy"] == "author" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "date_creation" || $_GET["orderBy"] == "date_update_fr")) {
        $orderBy = htmlspecialchars($_GET["orderBy"]);
    } else if (!empty($_SESSION["adminPostsOrderBy"])) {
        $orderBy = $_SESSION["adminPostsOrderBy"];
    } else {
        $orderBy = "date_creation_fr";
    };

    if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
        $order = htmlspecialchars($_GET["order"]);
    } else if (!empty($_SESSION["adminPostsOrder"])) {
        $order = $_SESSION["adminPostsOrder"];
    } else {
        $order = "desc";
    };

    if ($orderBy != $_SESSION["adminPostsOrderBy"]) {
        $order = "asc";
    };

    $_SESSION["adminPostsOrderBy"] = $orderBy;
    $_SESSION["adminPostsOrder"] = $order;

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
    $linkNbDisplayed = "admin-posts.php?orderBy=" . $orderBy . "&order=" . $order. "&";
    $linkPagination = "admin-posts.php?orderBy=" . $orderBy . "&order=" . $order. "&";
    $anchorPagination = "#table-admin-posts";
    $nbPages = ceil($nbPosts["nb_Posts"] / $nbDisplayed);
    require("pagination.php");

    echo $orderBy . " " . $order;

    // Récupère les derniers articles
    $req = $bdd->prepare("SELECT p.ID, p.title, p.user_login AS author, u.login, p.status, 
    DATE_FORMAT(p.date_creation, \"%d/%m/%Y %H:%i\") AS date_creation_fr, 
    DATE_FORMAT(p.date_update, \"%d/%m/%Y %H:%i\") AS date_update_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.user_ID = u.ID
    ORDER BY $orderBy $order
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
                            <th scope="col" class="align-middle"><input type="checkbox" name="all-checkbox" id="all-checkbox" /><label for="all-checkbox"></label></th>
                            <th scope="col" class="align-middle">
                                <a href="admin-posts?orderBy=title&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Titre
                                <?php 
                                if ($orderBy == "title") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                            <th scope="col" class="align-middle">
                                <a href="admin-posts?orderBy=author&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Auteur
                                <?php 
                                if ($orderBy == "author") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                            <th scope="col" class="align-middle">
                                <a href="admin-posts?orderBy=status&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Statut
                                <?php 
                                if ($orderBy == "status") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                            <th scope="col" class="align-middle">
                                <a href="admin-posts?orderBy=date_creation&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de création
                                <?php 
                                if ($orderBy == "date_creation") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                            <th scope="col" class="align-middle">
                                <a href="admin-posts?orderBy=date_update_fr&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de mise à jour
                                <?php 
                                if ($orderBy == "date_update_fr") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            while ($dataPosts=$req->fetch()) {
                        ?>
                                <tr>
                                    <th scope="row"><input type="checkbox" name="<?= $dataPosts["ID"] ?>" id="<?= $dataPosts["ID"] ?>" class=""/><label for="<?= $dataPosts["ID"] ?>"></label></th>
                                    <td><a href="edit_post.php?post=<?= $dataPosts["ID"] ?>" class="text-info font-weight-bold"><?= $dataPosts["title"] ?></a></td>
                                    <td><?= $dataPosts["author"] ?></td>
                                    <td><?= $dataPosts["status"] ?></td>
                                    <td><?= $dataPosts["date_creation_fr"] ?></td>
                                    <td><?= $dataPosts["date_update_fr"] ?></td>
                                </tr>
                        <?php
                            };
                        ?>
                    </tbody>
                </table>
                
                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->
                
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: index.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT status FROM users WHERE ID =?");
        $req->execute(array($_SESSION["userID"]));
        $statusUser = $req->fetch();
        if ($statusUser["status"]!=0) {
            header("Location: index.php");
        };
    };

    var_dump($_POST);
    // Supprime les articles sélectionnés via une boucle
    if (isset($_POST["selectedPosts"])) {
        foreach ($_POST["selectedPosts"] as $selectedpost) {
            // $req = $bdd->prepare("DELETE FROM posts WHERE ID = ? ");
            // $req->execute(array($selectedpost));
        };
        // Compte le nombre d'articles supprimés pour adaptés l'affichage du message
        $nbSelectedPosts = count($_POST["selectedPosts"]);
        if ($nbSelectedPosts>1) {
            $msgAdmin = $nbSelectedPosts . " articles ont été supprimés.";
        } else {
            $msgAdmin = "L'article a été supprimé.";
        };
        $typeAlert = "warning"; 

        $_SESSION["flash"] = array(
            "msg" => $msgAdmin,
            "type" =>  $typeAlert
        );
    };

    // Compte le nombre d'articles
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_Posts FROM posts");
    $req->execute(array());
    $nbPosts = $req->fetch();

    // Vérification si informations dans variable POST
    if (!empty($_POST["nbDisplayed"])) {
        $nbDisplayed =  htmlspecialchars($_POST["nbDisplayed"]);
        $_SESSION["nbDisplayedPostsAdmin"] = $nbDisplayed;
    } else if (!empty($_SESSION["adminNbDisplayedPosts"])) {
        $nbDisplayed =  $_SESSION["adminNbDisplayedPosts"];
    } else {
        $nbDisplayed = 20;
    };
    var_dump($_GET);  
    // Vérifie l'ordre de tri par type
    if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "title" || $_GET["orderBy"] == "author" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "date_creation" || $_GET["orderBy"] == "date_update_fr")) {
        $orderBy = htmlspecialchars($_GET["orderBy"]);
    } else if (!empty($_SESSION["adminPostsOrderBy"])) {
        $orderBy = $_SESSION["adminPostsOrderBy"];
    } else {
        $orderBy = "date_creation_fr";
    };
    // Vérifie l'ordre de tri si ascendant ou descendant
    if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
        $order = htmlspecialchars($_GET["order"]);
    } else if (!empty($_SESSION["adminPostsOrder"])) {
        $order = $_SESSION["adminPostsOrder"];
    } else {
        $order = "desc";
    };
    // Si le tri par type vient de changer, alors le tri est toujours ascendant
    if (!empty($_SESSION["adminPostsOrder"]) && $orderBy != $_SESSION["adminPostsOrderBy"]) {
        $order = "asc";
    };
    // Enregistre les tris en SESSION
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

    // Récupère les articles
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
            <section id="table-admin-posts" class="col-md-12 mx-auto mt-4 table-admin">

                <h2 class="mb-4">Gestion des articles
                    <span class="badge badge-secondary font-weight-normal"><?= $nbPosts["nb_Posts"] ?> </span>
                </h2>
                
                <?php include("msg_session_flash.php") ?>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                <form action="<?= $linkNbDisplayed ?>" method="post">
                    <input type="submit" id="action_admin"  name="action" alt="Supprimer" class="btn btn-danger mb-2 shadow" 
                        value="Supprimer" onclick="if(window.confirm('Voulez-vous vraiment supprimer l\'article ?')){return true;}else{return false;}">
                    
                    <form action="<?= $linkNbDisplayed ?>" method="post" class="form-inline">
                        <label class="sr-only mr-2 col-form-label-sm" for="action">Filtrer</label>
                        <select name="action" id="action" class="custom-select mr-sm-2 form-control-sm" value="Par auteur" >
                            <option value="edit">Modifier</option>
                            <option value="erase">Supprimer</option>
                        </select>
                        <input type="submit" id="action_admin" class="btn btn-info form-control-sm pt-1" value="Filtrer">
                    </form>

                <table class="table table-bordered table-striped table-hover shadow">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="align-middle"><input type="checkbox" name="allSelectedPosts" id="all-checkbox" /><label for="allSelectedPosts"></label></th>
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
                                    <th scope="row"><input type="checkbox" name="selectedPosts[]" id="post<?= $dataPosts["ID"] ?>" value="<?= $dataPosts["ID"] ?>" class=""/><label for="selectedPosts[]"></label></th>
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
                </form>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->
                
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
<?php 
    session_start();

    require("connection_bdd.php");
    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: index.php");
    } else {
        // Récupère les informations de l'utilisateur
        $req = $bdd->prepare("SELECT role FROM users WHERE ID =?");
        $req->execute(array($_SESSION["userID"]));
        $userRole = $req->fetch();
        
        if ($userRole["role"]!=0) {
            header("Location: index.php");
        };
    };

    var_dump($_POST);
    // Supprime les commentaires sélectionnés via une boucle
    if (isset($_POST["selectedComments"])) {
        foreach ($_POST["selectedComments"] as $selectedComment) {
            $req = $bdd->prepare("DELETE FROM comments WHERE ID = ? ");
            $req->execute(array($selectedComment));
        };
        // Compte le nombre d'commentaires supprimés pour adaptés l'affichage du message
        $nbselectedComments = count($_POST["selectedComments"]);
        if ($nbselectedComments>1) {
            $msgAdmin = $nbselectedComments . " commentaires ont été supprimés.";
        } else {
            $msgAdmin = "Le commentaire a été supprimé.";
        };
        $typeAlert = "warning"; 

        $_SESSION["flash"] = array(
            "msg" => $msgAdmin,
            "type" =>  $typeAlert
        );
    };

    // Compte le nombre d'commentaires
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_comments FROM comments");
    $req->execute(array());
    $nbcomments = $req->fetch();

    // Vérification si informations dans variable comment
    if (!empty($_POST["nbDisplayed"])) {
        $nbDisplayed =  htmlspecialchars($_POST["nbDisplayed"]);
        $_SESSION["adminNbDisplayedComments"] = $nbDisplayed;
    } else if (!empty($_SESSION["adminNbDisplayedComments"])) {
        $nbDisplayed =  $_SESSION["adminNbDisplayedComments"];
    } else {
        $nbDisplayed = 20;
    };
    var_dump($_GET);  
    // Vérifie l'ordre de tri par type
    if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "content" || $_GET["orderBy"] == "author" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "creation_date" || $_GET["orderBy"] == "update_date_fr")) {
        $orderBy = htmlspecialchars($_GET["orderBy"]);
    } else if (!empty($_SESSION["adminCommentsOrderBy"])) {
        $orderBy = $_SESSION["adminCommentsOrderBy"];
    } else {
        $orderBy = "creation_date_fr";
    };
    // Vérifie l'ordre de tri si ascendant ou descendant
    if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
        $order = htmlspecialchars($_GET["order"]);
    } else if (!empty($_SESSION["adminCommentsOrder"])) {
        $order = $_SESSION["adminCommentsOrder"];
    } else {
        $order = "desc";
    };
    // Si le tri par type vient de changer, alors le tri est toujours ascendant
    if (!empty($_SESSION["adminCommentsOrder"]) && $orderBy != $_SESSION["adminCommentsOrderBy"]) {
        $order = "asc";
    };
    // Enregistre les tris en SESSION
    $_SESSION["adminCommentsOrderBy"] = $orderBy;
    $_SESSION["adminCommentsOrder"] = $order;

    // Vérification si informations dans variable GET
    if (!empty($_GET["page"])) {
        $page = htmlspecialchars($_GET["page"]);
        // Calcul le nombre de pages par rapport aux nombre d'commentaires
        $maxcomment =  $page*$nbDisplayed;
        $mincomment = $maxcomment-$nbDisplayed;
    } else  {
        $page = 1;
        $mincomment = 0;
        $maxcomment = $nbDisplayed;
    };
    
    // Initialisation des variables pour la pagination
    $linkNbDisplayed = "admin_comments.php?orderBy=" . $orderBy . "&order=" . $order. "&";
    $linkPagination = "admin_comments.php?orderBy=" . $orderBy . "&order=" . $order. "&";
    $anchorPagination = "#table-admin_comments";
    $nbPages = ceil($nbcomments["nb_comments"] / $nbDisplayed);
    require("pagination.php");

    // Récupère les commentaires
    $req = $bdd->prepare("SELECT c.ID, c.content, c.user_ID, u.login AS author, c.status, c.id_post, 
    DATE_FORMAT(c.creation_date, \"%d/%m/%Y %H:%i\") AS creation_date_fr, 
    DATE_FORMAT(c.update_date, \"%d/%m/%Y %H:%i\") AS update_date_fr 
    FROM comments c
    LEFT JOIN users u
    ON c.user_ID = u.ID
    ORDER BY $orderBy $order
    LIMIT  $mincomment, $maxcomment");
    $req->execute(array());

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <div class="row">
            <section id="table-admin_comments" class="col-md-12 mx-auto mt-4 table-admin">

                <h2 class="mb-4">Gestion des commentaires
                    <span class="badge badge-secondary font-weight-normal"><?= $nbcomments["nb_comments"] ?> </span>
                </h2>
                
                <?php include("msg_session_flash.php") ?>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                <form action="<?= $linkNbDisplayed ?>" method="post">
                    <input type="submit" id="action_admin" name="action" alt="Supprimer" class="btn btn-danger mb-2 shadow" 
                        value="Supprimer" onclick="if(window.confirm('Voulez-vous vraiment supprimer le commentaire ?')){return true;}else{return false;}">

                <table class="table table-bordered table-striped table-hover shadow">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="align-middle"><input type="checkbox" name="allselectedComments" id="all-checkbox" /><label for="allselectedComments"></label></th>
                            <th scope="col" class="align-middle">
                                <a href="admin_comments?orderBy=content&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Contenu
                                <?php 
                                if ($orderBy == "content") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                            <th scope="col" class="align-middle">
                                <a href="admin_comments?orderBy=author&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Auteur
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
                                <a href="admin_comments?orderBy=status&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Statut
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
                                <a href="admin_comments?orderBy=creation_date&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de création
                                <?php 
                                if ($orderBy == "creation_date") {
                                ?>
                                    <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                <?php   
                                }
                                ?>
                                </a>
                            </th>
                            <th scope="col" class="align-middle">
                                <a href="admin_comments?orderBy=update_date_fr&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de mise à jour
                                <?php 
                                if ($orderBy == "update_date_fr") {
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
                        while ($datacomments=$req->fetch()) {
                        ?>
                            <tr>
                                <th scope="row">
                                    <input type="checkbox" name="selectedComments[]" id="comment<?= $datacomments["ID"] ?>" value="<?= $datacomments["ID"] ?>" class=""/>
                                    <label for="selectedComments[]" class="sr-only">Sélectionné</label>
                                </th>
                                <td><a href="post.php?post=<?= $datacomments["id_post"] ?>" class="text-dark"><?= $datacomments["content"] ?></a></td>
                                <td><?= $datacomments["author"] ?></td>
                                <td><?= $datacomments["status"] ?></td>
                                <td><?= $datacomments["creation_date_fr"] ?></td>
                                <td><?= $datacomments["update_date_fr"] ?></td>
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
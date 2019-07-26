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
        
        if ($userRole["role"]!=1) {
            header("Location: index.php");
        };
    };

    $filter = "c.ID > 0";

    var_dump($_POST);
    if (!empty($_POST)) {
        if (!empty($_POST["action_apply"]) && isset($_POST["selectedComments"])) {
            // Supprime les commentaires sélectionnés via une boucle
            if ($_POST["action_apply"] == "delete" && isset($_POST["selectedComments"])) {
                foreach ($_POST["selectedComments"] as $selectedComment) {
                    $req = $bdd->prepare("DELETE FROM comments WHERE ID = ? ");
                    $req->execute(array($selectedComment));
                };
                // Compte le nombre de commentaires supprimés pour adaptés l'affichage du message
                $nbselectedComments = count($_POST["selectedComments"]);
                if ($nbselectedComments>1) {
                    $msgAdmin = $nbselectedComments . " commentaires ont été supprimés.";
                } else {
                    $msgAdmin = "Le commentaire a été supprimé.";
                };
                $typeAlert = "warning"; 
            };
            // Modère les commentaires sélectionnés via une boucle
            if ($_POST["action_apply"] == "moderate" && isset($_POST["selectedComments"])) {
                foreach ($_POST["selectedComments"] as $selectedComment) {
                    $req = $bdd->prepare("UPDATE comments SET status = 1 WHERE ID = ? ");
                    $req->execute(array($selectedComment));
                };
                // Compte le nombre de commentaires modérés pour adaptés l'affichage du message
                $nbselectedComments = count($_POST["selectedComments"]);
                if ($nbselectedComments>1) {
                    $msgAdmin = $nbselectedComments . " commentaires ont été modérés.";
                } else {
                    $msgAdmin = "Le commentaire a été modéré.";
                };
                $typeAlert = "success"; 
            };
            $_SESSION["flash"] = array(
                "msg" => $msgAdmin,
                "type" =>  $typeAlert
            );
        };
        // Enregistre le filtre
        if (isset($_POST["filter_status"])) {
            $filter = "c.status = " . htmlspecialchars($_POST["filter_status"]);
        };
    };

    // Compte le nombre de commentaires
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_Comments FROM comments");
    $req->execute(array());
    $nbComments = $req->fetch();
    $nbItems = $nbComments["nb_Comments"];

    // Vérification si informations dans variable comment
    if (!empty($_POST["nbDisplayed"])) {
        $nbDisplayed =  htmlspecialchars($_POST["nbDisplayed"]);
        setcookie("adminNbDisplayedComments", $nbDisplayed, time() + 365*24*3600, null, null, false, true);
    } else if (!empty($_COOKIE["adminNbDisplayedComments"])) {
        $nbDisplayed =  $_COOKIE["adminNbDisplayedComments"];
    } else {
        $nbDisplayed = 20;
    };
    var_dump($_GET);  
    // Vérifie l'ordre de tri par type
    if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "content" || $_GET["orderBy"] == "author" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "creation_date" || $_GET["orderBy"] == "update_date_fr")) {
        $orderBy = htmlspecialchars($_GET["orderBy"]);
    } else if (!empty($_COOKIE["adminCommentsOrderBy"])) {
        $orderBy = $_COOKIE["adminCommentsOrderBy"];
    } else {
        $orderBy = "creation_date_fr";
    };
    // Vérifie l'ordre de tri si ascendant ou descendant
    if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
        $order = htmlspecialchars($_GET["order"]);
    } else if (!empty($_COOKIE["adminCommentsOrder"])) {
        $order = $_COOKIE["adminCommentsOrder"];
    } else {
        $order = "desc";
    };
    // Si le tri par type vient de changer, alors le tri est toujours ascendant
    if (!empty($_COOKIE["adminCommentsOrder"]) && $orderBy != $_COOKIE["adminCommentsOrderBy"]) {
        $order = "asc";
    };
    // Enregistre les tris en COOKIES
    setcookie("adminCommentsOrderBy", $orderBy, time() + 365*24*3600, null, null, false, true);
    setcookie("adminCommentsOrder", $order, time() + 365*24*3600, null, null, false, true);

    // Vérification si informations dans variable GET
    if (!empty($_GET["page"])) {
        $page = htmlspecialchars($_GET["page"]);
        // Calcul le nombre de pages par rapport aux nombre de commentaires
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
    $nbPages = ceil($nbItems / $nbDisplayed);
    require("pagination.php");

    // Récupère les commentaires
    $req = $bdd->prepare("SELECT c.ID, c.id_post, c.content, c.user_ID, c.user_name AS author, u.login, c.status, c.nb_report, 
    DATE_FORMAT(c.report_date, \"%d/%m/%Y %H:%i\") AS report_date, 
    DATE_FORMAT(c.creation_date, \"%d/%m/%Y %H:%i\") AS creation_date_fr, 
    DATE_FORMAT(c.update_date, \"%d/%m/%Y %H:%i\") AS update_date_fr 
    FROM comments c
    LEFT JOIN users u
    ON c.user_ID = u.ID
    WHERE $filter 
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
                    <span class="badge badge-secondary font-weight-normal"><?= $nbComments["nb_Comments"] ?> </span>
                </h2>
                
                <?php include("msg_session_flash.php") ?>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                <form action="<?= $linkNbDisplayed ?>" method="post">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-form-label ml-2 mb-2 py-2" for="action">Action</label>
                                <select name="action_apply" id="action_apply" class="custom-select form-control mb-2 shadow" value="Par auteur">
                                    <option value="">--</option>
                                    <option value="moderate">Modérer</option>
                                    <option value="delete">Supprimer</option>
                                </select>
                            <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-info mb-2 py-1 shadow" 
                                value="Appliquer" onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                        </div>

                        <div class="col-md-6">
                            <label class="col-form-label ml-4  py-2" for="filter_status">Filtre</label>
                                <select name="filter_status" id="filter_status" class="custom-select form-control mb-2 shadow" value="Par auteur">
                                    <option value="">--Statut--</option>
                                    <option value="0">Non-modéré</option>
                                    <option value="1">Modéré</option>
                                    <option value="2">Signalé</option>
                                </select>
                            <input type="submit" id="filter" name="filter" alt="Filtrer" class="btn btn-info mb-2 py-1 shadow" value="Filtrer">
                        </div>
                    </div>

                    <div class="row">    
                        <div class="col-md-12 table-responsive">
                            <table class="table table-bordered table-striped table-hover shadow">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="align-middle">
                                        <input type="checkbox" name="allselectedComments" id="all-checkbox" />
                                        <label for="allselectedComments" class="sr-only">Tout sélectionner</label>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="admin_comments?orderBy=content&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Contenu du commentaire
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
                                        <a href="admin_comments?orderBy=report_date&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de signalement
                                        <?php 
                                        if ($orderBy == "report_date") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="admin_comments?orderBy=nb_report&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Nb de signalements
                                        <?php 
                                        if ($orderBy == "nb_report") {
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
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                while ($dataComments=$req->fetch()) {
                                ?>
                                    <tr>
                                        <th scope="row">
                                            <input type="checkbox" name="selectedComments[]" id="comment<?= $dataComments["ID"] ?>" value="<?= $dataComments["ID"] ?>" class=""/>
                                            <label for="selectedComments[]" class="sr-only">Sélectionner</label>
                                        </th>
                                        <td><a href="post.php?post=<?= $dataComments["id_post"] ?>" class="text-dark"><?= $dataComments["content"] ?></a></td>
                                        <td>
                                        <?php 
                                        if (!empty($dataComments["author"])) {
                                            echo $dataComments["author"];
                                        } else {
                                            if (!empty($dataComments["login"])) {
                                                echo $dataComments["login"];
                                            } else {
                                                echo "Anonyme";
                                            };
                                        };
                                        ?>
                                        </td>
                                        <td>
                                        <?php 
                                        switch($dataComments["status"]) {
                                            case 0:
                                            echo "Non-modéré";
                                            break;
                                            case 1:
                                            echo "Modéré";
                                            break;
                                            case 2:
                                            echo "Signalé";
                                            break;
                                            defaut:
                                            echo "Non-modéré";
                                        };
                                        ?>
                                        </td>
                                        <td><?= $dataComments["report_date"] ?></td>
                                        <td><?= $dataComments["nb_report"] ?></td>
                                        <td><?= $dataComments["creation_date_fr"] ?></td>
                                    </tr>
                                <?php
                                };
                                ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </form>

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->
                
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
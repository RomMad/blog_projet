<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$db = new Manager();
$db = $db->databaseConnection();
$usersManager = new UsersManager($db);
$commentsManager = new CommentsManager($db);

// Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
if (empty($_SESSION["userID"])) {
    header("Location: index.php");
} else {
    // Récupère le rôle de l'utilisateur
    $userRole = $usersManager->getRole($_SESSION["userID"]);
    if ($userRole != 1) {
        header("Location: index.php");
    }
}

if (!empty($_POST)) {
    if (!empty($_POST["action_apply"]) && isset($_POST["selectedComments"])) {
        // Supprime les commentaires sélectionnés via une boucle
        if ($_POST["action_apply"] == "delete" && isset($_POST["selectedComments"])) {
            foreach ($_POST["selectedComments"] as $selectedComment) {
                $commentsManager->delete($selectedComment);
            }
            // Compte le nombre de commentaires supprimés pour adaptés l'affichage du message
            $nbselectedComments = count($_POST["selectedComments"]);
            if ($nbselectedComments>1) {
                $session->setFlash($nbselectedComments . " commentaires ont été supprimés.", "warning");
            } else {
                $session->setFlash("Le commentaire a été supprimé", "warning");
            }
        }
        // Modère les commentaires sélectionnés via une boucle
        if ($_POST["action_apply"] == "moderate" && isset($_POST["selectedComments"])) {
            foreach ($_POST["selectedComments"] as $selectedComment) {
                $commentsManager->updateStatus($selectedComment, 1);
            }
            // Compte le nombre de commentaires modérés pour adaptés l'affichage du message
            $nbselectedComments = count($_POST["selectedComments"]);
            if ($nbselectedComments>1) {
                $session->setFlash($nbselectedComments . " commentaires ont été modérés.", "success");
            } else {
                $session->setFlash("Le commentaire a été modéré.", "success");
            }
        }
    }
    // Enregistre le filtre
    if (isset($_POST["filter_status"]) && $_POST["filter_status"] >= "0") {
        $_SESSION["filter_status"] = htmlspecialchars($_POST["filter_status"]);
        $_SESSION["filter"] = "status = " . $_SESSION["filter_status"];
    }
}

if (empty($_GET)) {
    $_SESSION["filter"] = "c.id > 0";
    $_SESSION["filter_status"] = NULL;
}

// Compte le nombre de commentaires
$nbItems = $commentsManager->count($_SESSION["filter"]);

// Vérifie l'ordre de tri par type
if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "content" || $_GET["orderBy"] == "user_name" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "report_date" || $_GET["orderBy"] == "nb_report" || $_GET["orderBy"] == "creation_date" )) {
    $orderBy = htmlspecialchars($_GET["orderBy"]);
} else if (!empty($_COOKIE["orderBy"]["adminComments"])) {
    $orderBy = $_COOKIE["orderBy"]["adminComments"];
} else {
    $orderBy = "creation_date";
}
// Vérifie l'ordre de tri si ascendant ou descendant
if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) {
    $order = htmlspecialchars($_GET["order"]);
} else if (!empty($_COOKIE["order"]["adminComments"])) {
    $order = $_COOKIE["order"]["adminComments"];
} else {
    $order = "desc";
}
// Si le tri par type vient de changer, alors le tri est toujours ascendant
if (!empty($_COOKIE["order"]["adminComments"]) && $orderBy != $_COOKIE["orderBy"]["adminComments"]) {
    $order = "asc";
}
// Enregistre les tris en COOKIES
setcookie("orderBy[adminComments]", $orderBy, time() + 365*24*3600, null, null, false, false);
setcookie("order[adminComments]", $order, time() + 365*24*3600, null, null, false, false);

// Initialise la pagination
$linkNbDisplayed = "admin_comments.php?orderBy=" . $orderBy . "&order=" . $order. "&";
$pagination = new Pagination("adminComments", $nbItems, $linkNbDisplayed, $linkNbDisplayed, "#table-admin_comments");

// Récupère les commentaires
$comments = $commentsManager->getlist($_SESSION["filter"], $orderBy, $order,  $pagination->_nbLimit, $pagination->_nbDisplayed);

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

    <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
                <li class="breadcrumb-item"><a href="admin.php" class="text-blue">Administration</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gestion des commentaires</li>
            </ol>
    </nav>

        <div class="row">
            <section id="table-admin_comments" class="col-md-12 mt-4 table-admin">

                <h2 class="mb-4">Gestion des commentaires
                    <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
                </h2>
                
                <?php 
                $session->flash(); // Message en session flash

                // Affiche les résultats si recherche
                if (isset($_POST["filter"])) {
                    echo "<p> " . $nbItems . " résultat(s).</p>";
                }
                ?>
                
                <form action="<?= $linkNbDisplayed ?>" method="post">
                    <div class="row">

                        <div class="col-md-6 mb-2">
                            <label class="sr-only col-form-label" for="action">Action</label>
                                <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow" value="Par auteur">
                                    <option value="">-- Action --</option>
                                    <option value="moderate">Modérer</option>
                                    <option value="delete">Supprimer</option>
                                </select>
                            <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-blue py-1 shadow" 
                                value="Appliquer" onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                        </div>

                        <div class="col-md-6 mb-2">
                            <label class="sr-only col-form-label" for="filter_status">Filtre</label>
                                <select name="filter_status" id="filter_status" class="custom-select form-control mr-1 shadow" value="Par auteur">
                                    <option value="">-- Statut --</option>
                                    <option <?= $_SESSION["filter_status"] == 0 &&  $_SESSION["filter_status"] != NULL ? "selected" : "" ?> value="0">Non-modéré</option>
                                    <option <?= $_SESSION["filter_status"] == 1 ? "selected" : "" ?> value="1">Modéré</option>
                                    <option <?= $_SESSION["filter_status"] == 2 ? "selected" : "" ?> value="2">Signalé</option>
                                </select>
                            <input type="submit" id="filter" name="filter" alt="Filtrer" class="btn btn-blue py-1 shadow" value="Filtrer">
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
                                        <a href="admin_comments?orderBy=user_name&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Auteur
                                        <?php 
                                        if ($orderBy == "user_name") {
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
                                if ($nbItems) {
                                    foreach ($comments as $comment) {
                                        ?>
                                        <tr>
                                            <th scope="row">
                                                <input type="checkbox" name="selectedComments[]" id="comment<?= $comment->id() ?>" value="<?= $comment->id() ?>" class=""/>
                                                <label for="selectedComments[]" class="sr-only">Sélectionner</label>
                                            </th>
                                            <td><a href="post_view.php?post_id=<?= $comment->post_id() ?>" class="text-dark"><?= $comment->content() ?></a></td>
                                            <td>
                                            <?php 
                                            if (!empty($comment->user_name())) {
                                                echo $comment->user_name();
                                            } else {
                                                if (!empty($comment->login())) {
                                                    echo $comment->login();
                                                } else {
                                                    echo "Anonyme";
                                                }
                                            }
                                            ?>
                                            </td>
                                            <td>
                                            <?php 
                                            switch($comment->status()) {
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
                                            }
                                            ?>
                                            </td>
                                            <td><?= $comment->report_date("") ?></td>
                                            <td><?= $comment->nb_report() ?></td>
                                            <td><?= $comment->creation_date("") ?></td>
                                        </tr>
                                <?php
                                    }   
                                }
                                ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </form>

                <?php $pagination->view(); ?> <!-- Ajoute la barre de pagination -->
                
            </section>
        </div>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$db = new Manager();
$db = $db->databaseConnection();
$usersManager = new UsersManager($db);
$postManager = new PostsManager($db);

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
    if (!empty($_POST["action_apply"]) && isset($_POST["selectedPosts"])) {
        // Supprime les articles sélectionnés via une boucle
        if ($_POST["action_apply"] == "delete") {
            foreach ($_POST["selectedPosts"] as $selectedPost) {
                $postManager->delete($selectedPost);
            }
            // Compte le nombre d'articles supprimés pour adaptés l'affichage du message
            $nbSelectedPosts = count($_POST["selectedPosts"]);
            if ($nbSelectedPosts > 1) {
                $session->setFlash($nbSelectedPosts . " articles ont été supprimés.", "warning");
            } else {
                $session->setFlash("L'article a été supprimé.", "warning");
            }
        }
        // Met en brouillon les articles sélectionnés via une boucle
        if ($_POST["action_apply"] == "Brouillon" || $_POST["action_apply"] == "Publié") {
            foreach ($_POST["selectedPosts"] as $selectedPost) {
                $postManager->updateStatus($selectedPost, $_POST["action_apply"]);
            }
            // Compte le nombre d'articles publiés pour adaptés l'affichage du message
            $selectedPosts = count($_POST["selectedPosts"]);
            if ($selectedPosts > 1) {
                $session->setFlash($selectedPosts . " articles ont été modifés.", "success");
            } else {
                $session->setFlash("L'article a été modifié.", "success");
            }
        }
    }
    // Si sélection d'un filtre 'rôle', enregistre le filtre
    if (!empty($_POST["filter_status"])) {
        $_SESSION["filter"] =  "status = '" . htmlspecialchars($_POST["filter_status"]) . "'";
    }
    // Si recherche, enregistre le filtre
    if (!empty($_POST["filter_search"])) {
        $_SESSION["filter_search"] = htmlspecialchars($_POST["search_post"]);
        $_SESSION["filter"] =  "title LIKE '%" .  $_SESSION["filter_search"] . "%' OR content LIKE '%" .  $_SESSION["filter_search"] . "%'";

    }
}

if (empty($_GET)) {
    $_SESSION["filter"] = "p.id > 0";
    $_SESSION["filter_search"] = "";
}

// Compte le nombre d'articles
$nbItems = $postManager->count($_SESSION["filter"]);

// Vérifie l'ordre de tri par type
if (!empty($_GET["orderBy"]) && ($_GET["orderBy"] == "title" || $_GET["orderBy"] == "author" || $_GET["orderBy"] == "status" || $_GET["orderBy"] == "creation_date" || $_GET["orderBy"] == "update_date")) {
    $orderBy = $_GET["orderBy"];
} else if (!empty($_COOKIE["orderBy"]["adminPosts"])) 
{
    $orderBy = $_COOKIE["orderBy"]["adminPosts"];
} else 
{
    $orderBy = "creation_date";
}
// Vérifie l'ordre de tri si ascendant ou descendant
if (!empty($_GET["order"]) && ($_GET["order"] == "desc" || $_GET["order"] == "asc")) 
{
    $order = $_GET["order"];
} else if (!empty($_COOKIE["order"]["adminPosts"])) 
{
    $order = $_COOKIE["order"]["adminPosts"];
} else {
    $order = "desc";
}
// Si le tri par type vient de changer, alors le tri est toujours ascendant
if (!empty($_COOKIE["order"]["adminPosts"]) && $orderBy != $_COOKIE["orderBy"]["adminPosts"]) 
{
    $order = "asc";
}

// Enregistre les tris en COOKIES
setcookie("orderBy[adminPosts]", $orderBy, time() + 365*24*3600, null, null, false, true);
setcookie("order[adminPosts]", $order, time() + 365*24*3600, null, null, false, true);

// Initialisation des variables pour la pagination
$typeItem = "adminPosts";
$linkNbDisplayed = "admin_posts.php?&orderBy=" . $orderBy . "&order=" . $order. "&";
$linkPagination = $linkNbDisplayed;
$anchorPagination = "#table_admin_posts";
require("pagination.php");

// Récupère les articles
$posts = $postManager->getlist($_SESSION["filter"], $orderBy, $order, $minLimit, $maxLimit);

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
                <li class="breadcrumb-item active" aria-current="page">Gestion des articles</li>
            </ol>
    </nav>

        <div class="row">
            <section id="table_admin_posts" class="col-md-12 mt-4 table-admin">

                <h2 class="mb-4">Gestion des articles
                    <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
                </h2>
                
                <?php 
                $session->flash(); // Message en session flash

                // Affiche les résultats si recherche
                if (isset($_POST["filter"]) || isset($_POST["filter_search"])) {
                    echo "<p> " . $nbItems . " résultat(s).</p>";
                }    
                ?>

                <form action="<?= $linkNbDisplayed ?>" method="post">
                    <div class="row">

                        <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                            <label class="sr-only col-form-label" for="action">Action</label>
                                <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow" value="Par auteur">
                                    <option value="">-- Action --</option>
                                    <option value="Brouillon">Mettre en brouillon</option>
                                    <option value="Publié">Publier</option>
                                    <option value="delete">Supprimer</option>
                                </select>
                            <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" 
                                value="OK" onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                        </div>
                        <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                            <label class="sr-only col-form-label" for="filter_status">Filtre</label>
                                <select name="filter_status" id="filter_status" class="custom-select form-control mr-1 shadow" value="Par auteur">
                                    <option value="">-- Statut --</option>
                                    <option value="brouillon">Brouillon</option>
                                    <option value="publié">Publié</option>
                                </select>
                            <input type="submit" id="filter" name="filter" alt="Filtrer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="Filtrer">
                        </div>
                        <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                                <label for="search_post"class="sr-only col-form-label">Recherche</label>
                                <input type="search" name="search_post" id="search_post" class="form-control mr-1 px-md-1 shadow" placeholder="Recherche" aria-label="Search" 
                                    value="<?= $_SESSION["filter_search"] ?>">
                                <input type="submit" id="filter_search" name="filter_search" alt="filter_search" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                        </div>
                    </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover shadow">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="align-middle"><input type="checkbox" name="allSelectedPosts" id="all-checkbox" /><label for="allSelectedPosts"></label></th>
                                <th scope="col" class="align-middle">
                                    <a href="admin_posts?orderBy=title&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Titre
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
                                    <a href="admin_posts?orderBy=user_name&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Auteur
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
                                    <a href="admin_posts?orderBy=status&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Statut
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
                                    <a href="admin_posts?orderBy=creation_date&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de création
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
                                    <a href="admin_posts?orderBy=update_date&order=<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de mise à jour
                                    <?php 
                                    if ($orderBy == "update_date") {
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
                            foreach ($posts as $post) {
                        ?>
                                    <tr>
                                        <th scope="row">
                                            <input type="checkbox" name="selectedPosts[]" id="post<?= $post->id() ?>" value="<?= $post->id() ?>" class=""/>
                                            <label for="selectedPosts[]" class="sr-only">Sélectionné</label>
                                        </th>
                                        <td><a href="post_view.php?post_id=<?= $post->id() ?>" class="text-blue font-weight-bold"><?= $post->title() ?></a></td>
                                        <td><?= $post->login() ?></td>
                                        <td><?= $post->status() ?></td>
                                        <td><?= $post->creation_date("") ?></td>
                                        <td><?= $post->update_date("") ?></td>
                                    </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
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
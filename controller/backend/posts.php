<?php 

function posts() {
    
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();
    $postsManager = new PostsManager();

    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["user"]["id"])) {
        header("Location: blog");
        exit();
    } else {
        // Récupère le rôle de l'utilisateur
        $userRole = $usersManager->getRole($_SESSION["user"]["id"]);
        if ($userRole != 1) {
            header("Location: blog");
            exit();
        }
    }

    if (!empty($_POST)) {
        if (!empty($_POST["action_apply"]) && isset($_POST["selectedPosts"])) {
            // Supprime les articles sélectionnés via une boucle
            if ($_POST["action_apply"] == "delete") {
                foreach ($_POST["selectedPosts"] as $selectedPost) {
                    $post = $postsManager->get($selectedPost);
                    $postsManager->delete($post);
                    $session->setFlash("L'article <b>" . $post->title() . "</b> a été supprimé.", "warning");
                }
            }
            // Met en brouillon les articles sélectionnés via une boucle
            if ($_POST["action_apply"] == "Brouillon" || $_POST["action_apply"] == "Publié") {
                foreach ($_POST["selectedPosts"] as $selectedPost) {
                    $post = $postsManager->get($selectedPost);
                    $post->setStatus($_POST["action_apply"]);
                    $postsManager->updateStatus($post);
                    $session->setFlash("L'article <b>" . $post->title() . "</b> a été modifié (" . $_POST["action_apply"] . ").", "warning");
                }
            }
        }

        $_SESSION["filter"] = "p.id > 0";
        // Si sélection d'un filtre 'rôle', enregistre le filtre
        if (!empty($_POST["filter_status"])) {
            $_SESSION["filter_status"] = htmlspecialchars($_POST["filter_status"]);
            $_SESSION["filter"] =  "status = '" . htmlspecialchars($_POST["filter_status"]) . "'";
        } else {
            $_SESSION["filter_status"] = NULL;
        }
        // Si recherche, enregistre le filtre
        if (!empty($_POST["search_post"])) {
            $_SESSION["search_post"] = htmlspecialchars($_POST["search_post"]);
            $_SESSION["filter"] = $_SESSION["filter"] . " AND (title LIKE '%" .  $_SESSION["search_post"] . "%' OR content LIKE '%" .  $_SESSION["search_post"] . "%')";
        }
    }

    if (empty($_POST) && !isset($_GET["order"])) {
        $_SESSION["filter_status"] = NULL;
        $_SESSION["search_post"] = "";
        $_SESSION["filter"] = "p.id > 0";
    }

    // Compte le nombre d'articles
    $nbItems = $postsManager->count($_SESSION["filter"]);

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

    // Initialise la pagination
    $linkNbDisplayed = "posts-orderBy-" . $orderBy . "-order-" . $order;
    $pagination = new Pagination("adminPosts", $nbItems, $linkNbDisplayed, $linkNbDisplayed . "-", "#table_admin_posts");

    // Récupère les articles
    $posts = $postsManager->getlist($_SESSION["filter"], $orderBy, $order, $pagination->_nbLimit, $pagination->_nbDisplayed);
    require "view/backend/postsView.php";
}
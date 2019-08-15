<?php 

function posts() {
    
    spl_autoload_register("loadClass");

    $session = new Session();
    $usersManager = new UsersManager();
    $postsManager = new PostsManager();

    // Redirige vers la page d'accueil si l'utilisateur n'est pas connecté et n'a pas les droits
    if (empty($_SESSION["userID"])) {
        header("Location: index.php");
        exit();
    } else {
        // Récupère le rôle de l'utilisateur
        $userRole = $usersManager->getRole($_SESSION["userID"]);
        if ($userRole != 1) {
            header("Location: index.php");
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
                    $postsManager->updateStatus($selectedPost, $_POST["action_apply"]);
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

    if (!isset($_POST["filter_search"]) && !isset($_POST["filter_role"]) || (isset($_POST["filter_role"]) && empty($_POST["filter_role"]))) {
        $_SESSION["filter"] = "p.id > 0";
        $_SESSION["filter_search"] = "";
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
    $linkNbDisplayed = "index.php?action=posts&&orderBy=" . $orderBy . "&order=" . $order. "&";
    $pagination = new Pagination("adminPosts", $nbItems, $linkNbDisplayed, $linkNbDisplayed, "#table_admin_posts");

    // Récupère les articles
    $posts = $postsManager->getlist($_SESSION["filter"], $orderBy, $order, $pagination->_nbLimit, $pagination->_nbDisplayed);
    
    require "view/backend/postsView.php";
}
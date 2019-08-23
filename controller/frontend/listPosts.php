<?php 
function listPosts() {
    $session = new model\Session();
    $settingsManager = new model\SettingsManager();
    $postsManager = new model\PostsManager();

    // Récupère les paramètres du site
    $settings = $settingsManager->get();
    $_SESSION["settings"] = $settings;

    // Si recherche, filtre les résultats
    $filter = "status = 'Publié'";
    if (isset($_GET["search"])) {
        $_SESSION["filter_search"] =  htmlspecialchars($_GET["search"]);
        $_SESSION["filter"] = "status = 'Publié' AND (title like '%". $_SESSION["filter_search"] . "%' OR content like '%" . $_SESSION["filter_search"] . "%')";
    }

    if (!isset($_GET["search"]) && !isset($_GET["page"]) && !isset($_POST["nbDisplayed"])) {
        $_SESSION["filter"] = "status = 'Publié'";
        $_SESSION["filter_search"] = "";
    }
    // Récupère le nombre d'articles
    $nbItems = $postsManager->count($_SESSION["filter"]);
    // Initialise la pagination
    $pagination = new model\Pagination("posts", $nbItems, "blog#blog", "blog-", "#blog");

    // Récupère les derniers articles
    $posts = $postsManager->getList($_SESSION["filter"], "p.creation_date", "desc", $pagination->_nbLimit, $pagination->_nbDisplayed);

    require "view/frontend/listPostsView.php";
}
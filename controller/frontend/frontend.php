<?php 
function listPosts() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $settingsManager = new SettingsManager();
    $postsManager = new PostsManager();

    // Récupère les paramètres du site
    $settings = $settingsManager->get();
    $_SESSION["settings"] = $settings;

    // Si recherche, filtre les résultats
    $filter = "status = 'Publié'";
    if (isset($_GET["search"])) {
        $_SESSION["filter_search"] =  htmlspecialchars($_GET["search"]);
        $_SESSION["filter"] = "status = 'Publié' AND title like '%". $_SESSION["filter_search"] . "%' OR content like '%" . $_SESSION["filter_search"] . "%'";
    }

    if (empty($_GET)) {
        $_SESSION["filter"] = "status = 'Publié'";
        $_SESSION["filter_search"] = "";
    }

    // Récupère le nombre d'articles
    $nbItems = $postsManager->count($_SESSION["filter"]);

    // Initialise la pagination
    $pagination = new Pagination("posts", $nbItems, "blog.php#blog", "blog.php?", "#blog");

    // Récupère les derniers articles
    $posts = $postsManager->getList($_SESSION["filter"], "p.creation_date", "DESC", $pagination->_nbLimit, $pagination->_nbDisplayed);

    require "view/frontend/listPostsView.php";

}

function loadClass($classname) {
    require "model/" . $classname . ".php";
}


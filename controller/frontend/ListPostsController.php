<?php 
namespace controller\frontend;

class ListPostsController {

    protected   $_session,
                $_postsManager,
                $_pagination;

    public function __construct($session) {
        $this->_session = $session;
        spl_autoload_register("loadClass");
        $this->_postsManager = new \model\PostsManager();
        $this->init();
    }

    protected function init() {
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
        $nbItems = $this->_postsManager->count($_SESSION["filter"]);
        // Initialise la pagination
        $this->_pagination = new \model\Pagination("posts", $nbItems, "blog#blog", "blog-", "#blog");
        // Récupère les derniers articles
        $posts = $this->_postsManager->getList($_SESSION["filter"], "p.creation_date", "desc", $this->_pagination->_nbLimit,  $this->_pagination->_nbDisplayed);

        require "view/frontend/listPostsView.php";
    }
}
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
        $filter = "status = 'Publié' AND p.publication_date <= NOW()";
        // Si recherche, filtre les résultats
        if (isset($_GET["search"])) {
            $_SESSION["filter_search"] =  htmlspecialchars($_GET["search"]);
            $_SESSION["filter"] =  $filter . " AND (title like '%". $_SESSION["filter_search"] . "%' OR content like '%" . $_SESSION["filter_search"] . "%')";
        } elseif (!isset($_GET["search"]) && !isset($_GET["page"]) && !isset($_POST["nbDisplayed"])) {
            $_SESSION["filter"] = $filter;
            $_SESSION["filter_search"] = "";
        } 
        // Récupère le nombre d'articles
        $nbItems = $this->_postsManager->count($_SESSION["filter"]);
        // Initialise la pagination
        $this->_pagination = new \model\Pagination("posts", $nbItems, "blog#blog", "blog-", "#blog");
        // Récupère les derniers articles
        $posts = $this->_postsManager->getList($_SESSION["filter"], "p.publication_date", "desc", $this->_pagination->_nbLimit,  $this->_pagination->_nbDisplayed);

        if (isset($_GET["search"])) {
        $this->_session->setFlash($nbItems . " résultat(s).", "light");
        }
        require "view/frontend/listPostsView.php";
    }
}
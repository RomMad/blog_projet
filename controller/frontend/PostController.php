<?php 
namespace controller\frontend;
use  model\PostsManager;
use  model\CommentsManager;
use  model\Comments;
use  model\Pagination;

class PostController {

    protected   $_session,
                $_postsManager,
                $_post,
                $_commentsManager,
                $_comment,
                $_comments,
                $_pagination;

    public function __construct($session) {
        $this->_session = $session;
        $this->_postsManager = new PostsManager();
        $this->_commentsManager = new CommentsManager();
        $this->init();
    }

    protected function init() {
        // Vérifie si l'article existe
        $this->_post = $this->_postsManager->get($_GET["id"]);
        if (!$this->_post) {
            $this->_session->setFlash("Cet article n'existe pas.", "warning");
            header("Location: blog"); 
            exit();
        }
        $postId = htmlspecialchars($_GET["id"]);
        $_SESSION["postID"] = $postId;

        // Vérifie les paramètres de modération
        if ($_SESSION["settings"]->moderation() == 0) {
            $filter = "status >= 1";
        } else {
            $filter = "status >= 2";
        }
            
        // Vérifie si informations dans variable POST
        if (!empty($_POST)) {
            if (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"] == 1 ) {
                $status = 2;
            } else {
                $status = 1;
            }

            if (isset($_SESSION["user"]["login"])) {
                $name = $_SESSION["user"]["login"];
            } else {
                $name = $_POST["name"];
            }

            if (isset($_POST["save_comment"])) {
                
                if (isset($_SESSION["user"]["id"])) {
                    $userId = $_SESSION["user"]["id"];
                } else {
                    $userId = NULL;
                }

                $validation = true;

                // Vérifie si le commentaire est vide
                if (empty($_POST["content"])) {
                    $this->_session->setFlash("Le commentaire est vide.", "danger");
                    $validation = false;
                }

                // Ajoute le commentaire si le commentaire n'est pas vide
                if ($validation) {
                     $this->_comment = new Comments([
                        "post_id" => $_SESSION["postID"],
                        "user_id" => $userId,
                        "user_name" => $name,
                        "content" => $_POST["content"],
                        "status" => $status
                    ]);
                    $this->_commentsManager->add( $this->_comment);
                    if ($_SESSION["settings"]->moderation() == 0 || (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"] == 1 )) {
                        $this->_session->setFlash("Le commentaire a été ajouté.", "success");
                    } else {
                        $this->_session->setFlash("Le commentaire est en attente de modération.", "info");
                    }
                }
            }
            // Modifie le commentaire
            if (isset($_POST["editComment"])) {
                 $this->_comment = new Comments([
                    "id" => $_GET["comment"],
                    "content" => $_POST["comment-form-content-" . $_GET["comment"]],
                    "status" => $status,
                ]);
                $this->_commentsManager->update( $this->_comment);
                $this->_session->setFlash("Le commentaire a été modifié.", "success");
            }
            header("Location: post-" . $postId . "#form-comment"); 
            exit();
        }

        // Supprime le commentaire
        if (isset($_GET["delete"]) && $_GET["delete"]=="true") {
             $this->_comment = new Comments([
                "id" => $_GET["comment"],
            ]);
            $this->_commentsManager->delete( $this->_comment);
            $this->_session->setFlash("Le commentaire a été supprimé.", "warning");
        }
        // Ajoute le signalement du commentaire
        if (isset($_GET["report"]) && $_GET["report"]=="true") {
             $this->_comment = new Comments([
                "id" => $_GET["comment"],
                "status" => 3,
            ]);
            $this->_commentsManager->report( $this->_comment);
            $this->_session->setFlash("Le commentaire a été signalé.", "warning");
        }

        // Récupère le post
        $this->_post = $this->_postsManager->get($postId); 

        // Compte le nombre de commentaires
        $nbItems = $this->_commentsManager->count("post_id = " . $postId . " AND " . $filter);

        // Initialise la pagination
        $linkNbDisplayed = "post-" . $postId;
        $this->_pagination = new Pagination("comments", $nbItems, "post-" . $postId . "#comments", "post-" . $postId . "-", "#comments");

        // Récupère les commentaires si le nombre > 0 
        if ($nbItems) {
             $this->_comments = $this->_commentsManager->getList("c.post_id = " . $postId . " AND " . $filter, "c.creation_date", "desc", $this->_pagination->_nbLimit, $this->_pagination->_nbDisplayed);
        }
        require "view/frontend/postView.php";
    }
}
<?php 
namespace controller\frontend;

class PostController {

    protected   $_session,
                $_postsManager,
                $_post,
                $_commentsManager,
                $_comment,
                $_comments,
                $_status,
                $_filter,
                $_pagination,
                $_validation;

    public function __construct($session) {
        $this->_session = $session;
        $this->_postsManager = new \model\PostsManager();
        $this->_commentsManager = new \model\CommentsManager();
        $this->_validation = true;
        $this->init();
    }

    protected function init() {
        $this->isPostExists(); // Vérifie si l'article existe
        $this->filterComments(); // Filtre les commentaires en fonction que l'activation de la modération

        // Vérifie si informations dans POST
        if (!empty($_POST)) {
            $this->postComment();
        }
        // Signale le commentaire si 'report' existe
        if (isset($_GET["report"])) {
            $this->reportComment();
        }
        // Supprime le commentaire si 'delete' existe
        if (isset($_GET["delete"])) {
            $this->deleteComment();
        }

        // Récupère le post
        $this->_post = $this->_postsManager->get($this->_post->id()); 

        // Compte le nombre de commentaires
        $nbItems = $this->_commentsManager->count("post_id = " . $this->_post->id() . " AND " . $this->_filter);

        // Initialise la pagination
        $linkNbDisplayed = "post-" . $this->_post->id();
        $this->_pagination = new \model\Pagination("comments", $nbItems, "post-" . $this->_post->id() . "#comments", "post-" . $this->_post->id() . "-", "#comments");

        // Récupère les commentaires si le nombre > 0 
        if ($nbItems) {
             $this->_comments = $this->_commentsManager->getList("c.post_id = " . $this->_post->id() . " AND " . $this->_filter, "c.creation_date", "desc", $this->_pagination->_nbLimit, $this->_pagination->_nbDisplayed);
        }
        require "view/frontend/postView.php";
    }

    // Vérifie si l'article existe
    protected function isPostExists() {
        $this->_post = $this->_postsManager->get($_GET["id"]);
        if (!$this->_post) {
            $this->_session->setFlash("Cet article n'existe pas.", "warning");
            header("Location: blog"); 
            exit;
        }
    }

    // Filtre les commentaires en fonction que l'activation de la modération
    protected function filterComments() {
        if ($_SESSION["settings"]->moderation() == 0) {
            $this->_filter = "status >= 1"; // Affiche tous les commentaires
        } else {
            $this->_filter = "status >= 2"; // Affiche seulement les commentaires modérés
        }
    }
    
    // Publie ou modifie un commentaire après validation
    protected function postComment() {
        // Vérifie le rôle l'utilisateur
        if (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"] == 1) {
            $this->_status = 2;
        } else {
            $this->_status = 1;
        }  
        // Si envoi d'un nouveau commenntaire
        if (isset($_POST["save-comment"])) {
            $this->validationComment($_POST["content"]);
            if ($this->_validation) {
                $this->addComment();
            }
        }
        // Si modifie un commentaire
        elseif (isset($_POST["edit-comment"])) {
            $this->validationComment($_POST["comment-form-content-" . $_GET["comment"]]);
            if ($this->_validation) {
                $this->updateComment();
            }
        }
    }

    // Vérifie la validité du commentaire
    protected function validationComment($content) {
        if (empty($content)) {
            $this->_session->setFlash("Le commentaire est vide.", "danger");
            $this->_validation = FALSE;
        }
        elseif (!preg_match("#[a-zA-Z]#", $content)) {
            $this->_session->setFlash("Le commentaire ne contient pas de texte.", "danger");
            $this->_validation = FALSE;
        }
        elseif (preg_match("#^.{0,9}$#", $content)) {
            $this->_session->setFlash("Le commentaire est trop court (10 caractères minimum).", "danger");
            $this->_validation = FALSE;
        }            
        elseif (strlen($content) > 1500) {
            $this->_session->setFlash("Le commentaire est trop long (1500 cactères maximum).", "danger");
            $this->_validation = FALSE;
        }
    }

    // Ajoute un commentaire
    protected function addComment() {
        $this->_comment = new \model\Comments([
            "post_id" => $this->_post->id(),
            "user_id" => isset($_SESSION["user"]["id"]) ? $_SESSION["user"]["id"] : NULL,
            "user_name" => isset($_SESSION["user"]["login"]) ? $_SESSION["user"]["login"] : $_POST["name"],
            "content" => $_POST["content"],
            "status" => $this->_status
        ]);
        $this->_commentsManager->add($this->_comment);
        if ($_SESSION["settings"]->moderation() == 0 || (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"] == 1)) {
            $this->_session->setFlash("Le commentaire a été ajouté.", "success");
        } else {
            $this->_session->setFlash("Le commentaire est en attente de modération.", "info");
        }
        header("Location: post-" . $this->_post->id() . "#form-comment"); 
        exit;
    }

    // Modifie un commentaire
    protected function updateComment() {
        $this->_comment = new \model\Comments([
            "id" => $_GET["comment"],
            "content" => $_POST["comment-form-content-" . $_GET["comment"]],
            "status" => $this->_status,
        ]);
        $this->_commentsManager->update($this->_comment);
        $this->_session->setFlash("Le commentaire a été modifié.", "success");
    }

    // Signale un commentaire
    protected function reportComment() {
        $this->_comment = new \model\Comments([
            "id" => $_GET["comment"],
            "status" => 3,
        ]);
        $this->_commentsManager->report($this->_comment);
        $this->_session->setFlash("Le commentaire a été signalé.", "warning");
    }

    // Supprime un commentaire
    protected function deleteComment() {
        $this->_comment = new \model\Comments([
            "id" => $_GET["comment"],
        ]);
        $this->_commentsManager->delete($this->_comment);
        $this->_session->setFlash("Le commentaire a été supprimé.", "warning");
    }
}
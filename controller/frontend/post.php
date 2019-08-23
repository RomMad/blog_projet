<?php 
function post() {
    $session = new model\Session();
    $settingsManager = new model\SettingsManager();
    $postsManager = new model\PostsManager();
    $commentsManager = new model\CommentsManager();

    // Vérifie si l'article existe
    $post = $postsManager->get($_GET["id"]);
    if (!$post) {
        $session->setFlash("Cet article n'existe pas.", "warning");
        header("Location: blog"); 
        exit();
    }
    $postId = htmlspecialchars($_GET["id"]);
    $_SESSION["postID"] = $postId;

    // Vérifie les paramètres de modération
    $settings = $settingsManager->get();
    if ($settings->moderation() == 0) {
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
                $session->setFlash("Le commentaire est vide.", "danger");
                $validation = false;
            }

            // Ajoute le commentaire si le commentaire n'est pas vide
            if ($validation) {
                $comment = new Comments([
                    "post_id" => $_SESSION["postID"],
                    "user_id" => $userId,
                    "user_name" => $name,
                    "content" => $_POST["content"],
                    "status" => $status
                ]);
                $commentsManager->add($comment);
                if ($settings->moderation() == 0 || (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"] == 1 )) {
                    $session->setFlash("Le commentaire a été ajouté.", "success");
                } else {
                    $session->setFlash("Le commentaire est en attente de modération.", "info");
                }
            }
        }
        // Modifie le commentaire
        if (isset($_POST["editComment"])) {
            $comment = new Comments([
                "id" => $_GET["comment"],
                "content" => $_POST["comment-form-content-" . $_GET["comment"]],
                "status" => $status,
            ]);
            $commentsManager->update($comment);
            $session->setFlash("Le commentaire a été modifié.", "success");
        }
        header("Location: post-" . $postId . "#form-comment"); 
        exit();
    }

    // Supprime le commentaire
    if (isset($_GET["delete"]) && $_GET["delete"]=="true") {
        $comment = new Comments([
            "id" => $_GET["comment"],
        ]);
        $commentsManager->delete($comment);
        $session->setFlash("Le commentaire a été supprimé.", "warning");
    }
    // Ajoute le signalement du commentaire
    if (isset($_GET["report"]) && $_GET["report"]=="true") {
        $comment = new Comments([
            "id" => $_GET["comment"],
            "status" => 2,
        ]);
        $commentsManager->report($comment);
        $session->setFlash("Le commentaire a été signalé.", "warning");
    }

    // Récupère le post
    $post = $postsManager->get($postId); 

    // Compte le nombre de commentaires
    $nbItems = $commentsManager->count("post_id = " . $postId . " AND " . $filter);

    // Initialise la pagination
    $linkNbDisplayed = "post-" . $postId;
    $pagination = new model\Pagination("comments", $nbItems, "post-" . $postId . "#comments", "post-" . $postId . "-", "#comments");

    // Récupère les commentaires si le nombre > 0 
    if ($nbItems) {
        $comments = $commentsManager->getList("c.post_id = " . $postId . " AND " . $filter, "c.creation_date", "desc", $pagination->_nbLimit, $pagination->_nbDisplayed);
    }
    require "view/frontend/postView.php";
}
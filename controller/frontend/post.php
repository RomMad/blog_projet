<?php 
function post() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $settingsManager = new SettingsManager();
    $postsManager = new PostsManager();
    $commentsManager = new CommentsManager();

    // Vérifie si l'article existe
    $post = $postsManager->get($_GET["id"]);
    if (!$post) {
        $session->setFlash("Cet article n'existe pas.", "warning");
        header("Location: blog"); 
        exit();
    }
    $post_id = htmlspecialchars($_GET["id"]);
    $_SESSION["postID"] = $post_id;

    // Vérifie les paramètres de modération
    $settings = $settingsManager->get();
    if ($settings->moderation() == 0) {
        $filter = "status >= 1";
    } else {
        $filter = "status >= 2";
    }
        
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        if (isset($_SESSION["userRole"]) && $_SESSION["userRole"] == 1 ) {
            $status = 2;
        } else {
            $status = 1;
        }

        if (isset($_SESSION["userLogin"])) {
            $name = $_SESSION["userLogin"];
        } else {
            $name = $_POST["name"];
        }

        if (isset($_POST["save_comment"])) {
            
            if (isset($_SESSION["userID"])) {
                $user_id = $_SESSION["userID"];
            } else {
                $user_id = NULL;
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
                    "user_id" => $user_id,
                    "user_name" => $name,
                    "content" => $_POST["content"],
                    "status" => $status
                ]);
                $commentsManager->add($comment);
                if ($settings->moderation() == 0 || (isset($_SESSION["userRole"]) && $_SESSION["userRole"] == 1 )) {
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
                "content" => $_POST["content"],
                "status" => $status,
            ]);
            $commentsManager->update($comment);
            $session->setFlash("Le commentaire a été modifié.", "success");
        }
    }

    //
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
    $post = $postsManager->get($post_id); 

    // Compte le nombre de commentaires
    $nbItems = $commentsManager->count("post_id = " . $post_id . " AND " . $filter);

    // Initialise la pagination
    $linkNbDisplayed = "post-" . $post_id;
    $pagination = new Pagination("comments", $nbItems, "post-" . $post_id . "#comments", "post-" . $post_id . "-", "#comments");

    // Récupère les commentaires si le nombre > 0 
    if ($nbItems) {
        $comments = $commentsManager->getList("c.post_id = " . $post_id . " AND " . $filter, "c.creation_date", "DESC", $pagination->_nbLimit, $pagination->_nbDisplayed);
    }
    require "view/frontend/postView.php";
}
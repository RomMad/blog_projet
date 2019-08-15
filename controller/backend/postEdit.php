<?php 
function postEdit() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $postsManager = new PostsManager();

    // Vérifie si l'article exite
    if (!empty($_GET["id"])) {
        $post = $postsManager->getUserId($_GET["id"]);
        if (!$post) {
            $session->setFlash("Cet article n'existe pas.", "warning");
            header("Location: index.php"); 
            exit();
        }
        // Vérifie si l'utilisateur a les droit d'accès ou si il est l'auteur de l'article
        if ($_SESSION["userRole"] > 2 && $_SESSION["userID"] != $post->user_id()) {
            $session->setFlash("Vous n'avez pas les droits pour accéder à cet article.", "warning");
            header("Location: index.php"); 
            exit();
        }
    }

    // Redirige vers la page de connexion si l'utilisateur n'a pas les droits
    if (!isset($_SESSION["userRole"]) || $_SESSION["userRole"]>4) {
        $session->setFlash("Vous n'avez pas les droits pour accéder à cet article.", "warning");
        header("Location: index.php?action=connection"); 
        exit();
    }

    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $post = new Posts([
            "title" => $_POST["title"],
            "content" => $_POST["post_content"],
            "status" => $_POST["status"],
            "id" => $_GET["id"],
            "user_id" => $_SESSION["userID"],
            "user_login" => $_SESSION["userLogin"],
        ]);
    
        // Supprime l'article
        if (isset($_POST["erase"]) && !empty($post->id())) {
            $postsManager->delete($post);
            $session->setFlash("L'article <b>". $post->title() . "</b> a été supprimé.", "warning");
            header("Location: index.php");
            exit();
        }

        $validation = true;
    
        // Vérifie si le titre est vide
        if (empty($post->title())) {
            $session->setFlash("Le titre de l'article est vide.", "danger");
            $validation = false;
        }
        // Vérifie si le contenu de l'article est vide
        if (empty($post->content("")) && $post->status() == "Publié") {
            $session->setFlash("L'article ne peut pas être publié si le contenu est vide.", "danger");
            $validation = false;
        }
        // Ajoute ou modifie l'article si le titre n'est pas vide
        if ($validation) {
            // Met à jour l'article si article existant
            if (isset($_POST["save"]) && !empty($post->id())) {
                $postsManager->update($post);
                $session->setFlash("Les modifications ont été enregistrées.", "success");
            }
            // Ajoute l'article si nouvel article
            if (isset($_POST["save"]) && empty($post->id())) {
                $postsManager->add($post);
                $session->setFlash("L'article a été enregistré.", "success");
                $post = $postsManager->lastCreate($_SESSION["userID"]);
            }
        }
    }

    // Récupère l'article si GET post_id existe
    if (!empty($_GET["id"])) {
        $post = $postsManager->get(htmlspecialchars($_GET["id"]));
    }

    require "view/backend/postEditView.php";
}
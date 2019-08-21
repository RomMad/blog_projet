<?php 
function postEdit() {
    spl_autoload_register("loadClass");

    $session = new Session();
    $postsManager = new PostsManager();

    // Vérifie si l'article existe
    if (isset($_GET["id"])) {
        $post = $postsManager->get($_GET["id"]);
        // Vérifie si l'utilisateur a les droit d'accès ou s'il est l'auteur de l'article
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] == 5 || ($_SESSION["user"]["role"] >= 3 && $_SESSION["user"]["id"] != $post->user_id())) {
            header("Location: error403"); 
            exit();
        }
        if (!$post) {
            $session->setFlash("Cet article n'existe pas.", "warning");
            header("Location: blog"); 
            exit();
        }
    }

    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $post = new Posts([
            "title" => $_POST["title"],
            "content" => $_POST["post_content"],
            "status" => $_SESSION["user"]["role"] <= 3 ? $_POST["status"] : "Brouillon",
            "id" => isset($_GET["id"]) ? $_GET["id"] : "",
            "user_id" => $_SESSION["user"]["id"],
            "user_login" => $_SESSION["user"]["login"],
        ]);
        // Supprime l'article
        if (isset($_POST["delete"]) && !empty($post->id() && $_SESSION["user"]["role"] <= 3)) {
            $postsManager->delete($post);
            $session->setFlash("L'article <b>". $post->title() . "</b> a été supprimé.", "warning");
            header("Location: blog");
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
                $post = $postsManager->lastCreate($_SESSION["user"]["id"]);
            }
        }
    }

    // Récupère l'article si GET post_id existe
    if (!empty($_GET["id"])) {
        $post = $postsManager->get(htmlspecialchars($_GET["id"]));
    }

    require "view/backend/postEditView.php";
}
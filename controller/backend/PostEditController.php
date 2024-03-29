<?php 
namespace controller\backend;

class PostEditController {

    protected   $_session,
                $_postsManager,
                $_post,
                $_validation;

    public function __construct($session) {
        $this->_session = $session;
        $this->_postsManager = new \model\PostsManager();
        $this->_validation = TRUE;
        $this->init();
    }

    protected function init() {
        // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        if (!isset($_SESSION["user"])) {
            header("Location: connection");
            exit; 
        } 
        // Redirige vers la page d'erreur 403 si l'utilisateur n'a pas les droits
        elseif ($_SESSION["user"]["role"] == 5) {
            header("Location: error403"); 
            exit;
        }
        // Vérifie si l'article existe
        if (isset($_GET["id"])) {
            $this->verifyIdPost();
        }
        // Vérifie les informations dans POST
        if (!empty($_POST)) {
            $this->post();
        }
        // Récupère l'article si GET id existe
        if (!empty($_GET["id"])) {
             $this->_post = $this->_postsManager->get($_GET["id"]);
        }
        require "view/backend/postEditView.php";
    }

    // Vérifie si l'article existe et si l'utilisateur a les droits
    protected function verifyIdPost() {
        $postUserId = $this->_postsManager->getUserId($_GET["id"]);
        if ($_SESSION["user"]["role"] >= 3 && $_SESSION["user"]["id"] !=  $postUserId) {
            header("Location: error403"); 
            exit;
        } elseif (!$postUserId) {
            $this->_session->setFlash("Cet article n'existe pas.", "warning");
            header("Location: blog"); 
            exit;
        }
    }

    // Récupère les données envoyées en post, les vérifie et met à jour si valide
    protected function post() {
         $this->_post = new \model\Posts([
            "title" => $_POST["title"],
            "content" => $_POST["post_content"],
            "status" => $_SESSION["user"]["role"] <= 3 ? $_POST["status"] : "Brouillon",
            "publication_date" => $this->publicationDate(),
            "id" => isset($_GET["id"]) ? $_GET["id"] : "",
            "user_id" => $_SESSION["user"]["id"],
            "user_login" => $_SESSION["user"]["login"],
        ]);
        // Supprime l'article
        if (isset($_POST["delete"]) && !empty($this->_post->id() && $_SESSION["user"]["role"] <= 3)) {
            $this->deletePost();
        }

        $this->validationPost();
        // Ajoute ou modifie l'article si le titre n'est pas vide
        if ($this->_validation) {
            $this->updatePost();
        }
    }

    // Retourne la date de publication au bon format si n'est pas vide
    protected function publicationDate() {
        if ($_POST["status"] == "Publié" || !empty($_POST["publication_date"])) {
            $date = date_format(new \DateTime($_POST["publication_date"], timezone_open("Europe/Paris")),"Y-m-d");
            $time = date_format(new \DateTime($_POST["publication_time"], timezone_open("Europe/Paris")),"H:i:s");
            return $date . " " . $time;
        } else {
            return NULL;
        }
    }

    protected function validationPost() {
        // Vérifie si le titre est vide
        if (empty($this->_post->title())) {
            $this->_session->setFlash("Le titre de l'article est vide.", "danger");
            $this->_validation = FALSE;
        }
        // Vérifie si le titre fait moins de 10 caractères
        elseif (strlen($this->_post->title()) < 10) {
            $this->_session->setFlash("Le titre est trop court (10 caractères maximum).", "danger");
            $this->_validation = FALSE;
        }  
        // Vérifie si le titre fait plus de 50 caractères
        elseif (strlen($this->_post->title()) > 255) {
            $this->_session->setFlash("Le titre est trop long (maximum 255 caractères).", "danger");
            $this->_validation = FALSE;
        }  
        // Vérifie si le contenu de l'article est vide
        if (empty($this->_post->content()) &&  $this->_post->status() == "Publié") {
            $this->_session->setFlash("L'article ne peut pas être publié si le contenu est vide.", "danger");
            $this->_validation = FALSE;
        }
    }
    
    // Crée ou met à jour l'article si article existant
    protected function updatePost() {
        if (isset($_POST["save"]) && !empty($this->_post->id())) {
            $this->_postsManager->update($this->_post);
            $this->_session->setFlash("Les modifications ont été enregistrées.", "success");
        }
        // Ajoute l'article si nouvel article
        elseif (isset($_POST["save"]) && empty($this->_post->id())) {
            $this->_postsManager->add($this->_post);
            $this->_session->setFlash("L'article a été enregistré.", "success");
            $postId = $this->_postsManager->lastCreate($_SESSION["user"]["id"]);
            header("Location: edit-post-" . $postId);
            exit;
        }
    }
    
    // Supprime l'article
    protected function deletePost() {
        $this->_postsManager->delete($this->_post);
        $this->_session->setFlash("L'article <b>".  $this->_post->title() . "</b> a été supprimé.", "warning");
        header("Location: blog");
        exit;
    }
}
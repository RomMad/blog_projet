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
        $this->_validation = true;
        $this->init();
    }

    protected function init() {
        // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        if (!isset($_SESSION["user"])) {
            header("Location: connection");
            exit(); 
        } 
        // Redirige vers la page d'erreur 403 si l'utilisateur n'a pas les droits
        elseif ($_SESSION["user"]["role"] == 5) {
            header("Location: error403"); 
            exit();
        }
        // Vérifie si l'article existe
        elseif (isset($_GET["id"])) {
             $this->_post = $this->_postsManager->get($_GET["id"]);
            // Redirige vers la page d'erreur 403 si l'utilisateur n'a pas les droits
            if ($_SESSION["user"]["role"] >= 3 && $_SESSION["user"]["id"] !=  $this->_post->user_id()) {
                header("Location: error403"); 
                exit();
            } elseif (! $this->_post) {
                $this->_session->setFlash("Cet article n'existe pas.", "warning");
                header("Location: blog"); 
                exit();
            }
        }

        // Vérification si informations dans variable POST
        if (!empty($_POST)) {
             $this->_post = new \model\Posts([
                "title" => $_POST["title"],
                "content" => $_POST["post_content"],
                "status" => $_SESSION["user"]["role"] <= 3 ? $_POST["status"] : "Brouillon",
                "publication_date" => $_POST["publication_date"],
                "id" => isset($_GET["id"]) ? $_GET["id"] : "",
                "user_id" => $_SESSION["user"]["id"],
                "user_login" => $_SESSION["user"]["login"],
            ]);
            // Supprime l'article
            if (isset($_POST["delete"]) && !empty($this->_post->id() && $_SESSION["user"]["role"] <= 3)) {
                $this->_postsManager->delete($this->_post);
                $this->_session->setFlash("L'article <b>".  $this->_post->title() . "</b> a été supprimé.", "warning");
                header("Location: blog");
                exit();
            }

            // Vérifie si le titre est vide
            if (empty($this->_post->title())) {
                $this->_session->setFlash("Le titre de l'article est vide.", "danger");
                $this->_validation = false;
            }
            // Vérifie si le contenu de l'article est vide
            if (empty($this->_post->content("")) &&  $this->_post->status() == "Publié") {
                $this->_session->setFlash("L'article ne peut pas être publié si le contenu est vide.", "danger");
                $this->_validation = false;
            }
            // Vérifie si la date de publication est vide
            if ($_POST["status"] == "Publié" && empty($_POST["publication_date"])) {
                $this->_post->Setpublication_date("Y-m-d H:i");
            } elseif (!empty($_POST["publication_date"])) {
                $time = date_format(new \DateTime($_POST["publication_time"]),"H:i:s");
                $publicationDate = $_POST["publication_date"] . " " . $time;
                $this->_post->Setpublication_date($publicationDate);
            }
            // Ajoute ou modifie l'article si le titre n'est pas vide
            if ($this->_validation) {
                // Met à jour l'article si article existant
                if (isset($_POST["save"]) && !empty($this->_post->id())) {
                    $this->_postsManager->update($this->_post);
                    $this->_session->setFlash("Les modifications ont été enregistrées.", "success");
                }
                // Ajoute l'article si nouvel article
                elseif (isset($_POST["save"]) && empty($this->_post->id())) {
                    $this->_postsManager->add($this->_post);
                    $this->_session->setFlash("L'article a été enregistré.", "success");
                     $this->_post = $this->_postsManager->lastCreate($_SESSION["user"]["id"]);
                }
            }
        }
        // Récupère l'article si GET id existe
        if (!empty($_GET["id"])) {
             $this->_post = $this->_postsManager->get($_GET["id"]);
        }
        require "view/backend/postEditView.php";
    }
}
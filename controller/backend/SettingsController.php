<?php 
namespace controller\backend;

class SettingsController {

    protected   $_session,
                $_settingsManager,
                $_settings,
                $_validation;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_settingsManager = new \model\SettingsManager();
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
        if ($_SESSION["user"]["role"] != 1) {
            header("Location: error403"); 
            exit;
        }

        if (!empty($_POST)) {
            $this->postSettings(); // Récupère les données en post, les vérifie et met à jour si valide
        } else  {
        // Récupère les paramètres
        $this->_settings = $this->_settingsManager->get();
        }
        require "view/backend/settingsView.php";
    }

    // Récupère les données en post, les vérifie et met à jour si valide
    protected function postSettings() {         
        $this->_settings = new \model\Settings([
            "blog_name" => $_POST["blog_name"],
            "title" => $_POST["title"],
            "admin_email" => $_POST["admin_email"],
            "default_role" => $_POST["default_role"],
            "moderation" =>  isset($_POST["moderation"]) ? TRUE : FALSE,
            "posts_by_row" => $_POST["posts_by_row"],
            "style_blog" => $_POST["style_blog"]
        ]);

        $this->validationSettings(); // Vérifie la validité des informations

        if (!empty($_FILES["logoFile"]["name"])) {
            $this->upLoadFile(); // Télécharge un fichier pour le logo
        }
        // Met à jour les données si validation est vrai
        if ($this->_validation == TRUE) {
            $this->updateSettings(); // Met à jour les paramètres
        } 
    }

    // Vérifie la validité des informations
    protected function validationSettings() {
        $this->blogNameCheck();
        $this->titleCheck();
        $this->emailCheck();
        $this->postsByRowCheck();
    }

    // Vérifie la validité du nom du blog
    protected function blogNameCheck() {
        // Vérifie si le nom du blog est vide
        if (empty($this->_settings->blog_name())) {
            $this->_session->setFlash("Le nom du blog est vide.", "danger");
            $this->_validation = FALSE;
        }            
        // Vérifie si le nom du blog fait moins de 10 caractères
        elseif (strlen($this->_settings->blog_name()) < 10) {
            $this->_session->setFlash("Le nom du blog est trop court (10 caractères maximum).", "danger");
            $this->_validation = FALSE;
        }    
        // Vérifie si le nom du blog fait plus de 50 caractères
        elseif (strlen($this->_settings->blog_name()) > 50) {
            $this->_session->setFlash("Le nom du blog est trop long (50 caractères maximum).", "danger");
            $this->_validation = FALSE;
        }
    }

    // Vérifie la validité du titre
    protected function titleCheck() {
        // Vérifie si le titre est vide
        if (empty($this->_settings->title())) {
            $this->_session->setFlash("Le titre est vide.", "danger");
            $this->_validation = FALSE;
        }            
        // Vérifie si le titre fait moins de 10 caractères
        elseif (strlen($this->_settings->title()) < 10) {
            $this->_session->setFlash("Le titre est trop court (10 caractères maximum).", "danger");
            $this->_validation = FALSE;
        }  
        // Vérifie si le titre fait plus de 50 caractères
        elseif (strlen($this->_settings->title()) > 50) {
            $this->_session->setFlash("Le titre est trop long (maximum 50 caractères).", "danger");
            $this->_validation = FALSE;
        }        
    }

    // Vérifie la validité de l'email
    protected function emailCheck() {
        // Vérifie si l'adresse email est vide
        if (empty($this->_settings->admin_email())) {
            $this->_session->setFlash("L'adresse email est vide.", "danger");
            $this->_validation = FALSE;
        }          
        // Vérifie si l'adresse email est correcte
        elseif (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->_settings->admin_email())) {
            $this->_session->setFlash("L'adresse \"" . $this->_settings->admin_email() . "\" est incorrecte.", "danger");
            $this->_validation = FALSE;
        }
    }

    // Vérifie la valdité du nombre de posts par ligne
    protected function postsByRowCheck() {
        if ($this->_settings->posts_by_row() <= 0 || $this->_settings->posts_by_row() > 2) {
            $this->_session->setFlash("Le nombre de posts par ligne est incorrect.", "danger");
            $this->_validation = FALSE;
        }
    }

    // Télécharge un fichier pour le logo
    protected function upLoadFile() {
        $toFolder = "uploads/"; // Nom du dossier d'enregistrement
        $validExtensions = array("png", "gif", "jpg", "jpeg"); // extensions autorisées
        $maxSize = 2000000; // taille maximum (en octets) : 2 Mo
        $optimizeImage = new \model\OptimizeImage($_FILES["logoFile"], $toFolder, $validExtensions, $maxSize);
        // Vérifie s'il n'y a pas d'erreur
        if ($_FILES["logoFile"]["error"] != 0){
            $this->_session->setFlash("Une erreur s'est produite. Le fichier n'a pas pu être téléchargé.", "danger");
            $this->_validation = FALSE;
        } elseif(!in_array($optimizeImage->fileExtension(), $validExtensions)) {
            $this->_session->setFlash("Vous devez télécharger un fichier de type png, gif, jpg ou jpeg.", "danger");
            $this->_validation = FALSE;
        } elseif ($optimizeImage->fileSize() > $maxSize) {
            $this->_session->setFlash("La taille du fichier dépasse la limite autorisée (2Mo).", "danger");
            $this->_validation = FALSE;
        } 
        if ($this->_validation == TRUE) {
            $createIcon = $optimizeImage->createIcon("public/images/logo.ico");
            $this->_session->setFlash($createIcon, "success");
        } else {
            $this->_session->setFlash("Le fichier n'a pas pu être téléchargé.", "danger");
            $this->_validation = FALSE;
        }
    }

    // Met à jour les paramètres
    protected function updateSettings() {
        $this->_settingsManager->update($this->_settings);
        $_SESSION["settings"] = $this->_settings;
        $this->_session->setFlash("Les paramètres ont été mis à jour.", "success");
        header("Location: settings");
        exit;
    }
}
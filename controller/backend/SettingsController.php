<?php 
namespace controller\backend;

class SettingsController {

    protected   $_session,
                $_settingsManager,
                $_settings;
                
    public function __construct($session) {
        $this->_session = $session;
        $this->_settingsManager = new \model\SettingsManager();
        $this->init();
    }

    protected function init() {

        // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        if (!isset($_SESSION["user"])) {
            header("Location: connection");
            exit();
        } 
        // Redirige vers la page d'erreur 403 si l'utilisateur n'a pas les droits
        if ($_SESSION["user"]["role"] != 1) {
            header("Location: error403"); 
            exit();
        }

        if (!empty($_POST)) {
            $validation = true;
            $this->_settings = new \model\Settings([
                "blog_name" => $_POST["blog_name"],
                "title" => $_POST["title"],
                "admin_email" => $_POST["admin_email"],
                "default_role" => $_POST["default_role"],
                "moderation" =>  isset($_POST["moderation"]) ? true : false,
                "posts_by_row" => $_POST["posts_by_row"],
                "style_blog" => $_POST["style_blog"]
            ]);
            // Vérifie si le nom du blog ne fait pas plus de 50 caractères
            if (iconv_strlen($this->_settings->blog_name()) > 50) {
                $this->_session->setFlash("Le nom du blog est trop long (maximum 50 caractères)", "danger");
                $validation = false;
            }
            // Vérifie si le nom du blog ne fait pas plus de 50 caractères
            if (iconv_strlen($this->_settings->title()) > 50) {
                $this->_session->setFlash("Le titre est trop long (maximum 50 caractères)", "danger");
                $validation = false;
            }
            // Vérifie si l'adresse email est correcte
            if (!preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $this->_settings->admin_email())) {
                $this->_session->setFlash("L'adresse \"" . $this->_settings->admin_email() . "\" est incorrecte.", "danger");
                $validation = false;
            }
            // Vérifie le nombre de posts par ligne
            if ($this->_settings->posts_by_row() <= 0 || $this->_settings->posts_by_row() > 2) {
                $this->_session->setFlash("Le nombre de posts par ligne est incorrect.", "danger");
                $validation = false;
            }
            if (!empty($_FILES["logoFile"]["name"])) {
                $toFolder = "uploads/"; // Nom du dossier d'enregistrement
                $validExtensions = array("png", "gif", "jpg", "jpeg"); // extensions autorisées
                $maxSize = 2000000; // taille maximum (en octets) : 2 Mo
                $optimizeImage = new \model\OptimizeImage($_FILES["logoFile"], $toFolder, $validExtensions, $maxSize);
                // Vérifie s'il n'y a pas d'erreur
                if ($_FILES["logoFile"]["error"] != 0){
                    $this->_session->setFlash("Une erreur s'est produite. Le fichier n'a pas pu être téléchargé.", "danger");
                    $validation = false;
                } elseif(!in_array($optimizeImage->fileExtension(), $validExtensions)) {
                    $this->_session->setFlash("Vous devez télécharger un fichier de type png, gif, jpg ou jpeg.", "danger");
                    $validation = false;
                } elseif ($optimizeImage->fileSize() > $maxSize) {
                    $this->_session->setFlash("La taille du fichier dépasse la limite autorisée (2Mo).", "danger");
                    $validation = false;
                } 
                if ($validation == true) {
                    $createIcon = $optimizeImage->createIcon("public/images/logo.ico");
                    $this->_session->setFlash($createIcon, "success");
                } else {
                    $this->_session->setFlash("Le fichier n'a pas pu être téléchargé.", "danger");
                    $validation = false;
                }
            }
            // Met à jour les données si validation est vrai
            if ($validation == true) {
                $this->_settingsManager->update($this->_settings);
                $_SESSION["settings"] = $this->_settings;
                $this->_session->setFlash("Les paramètres ont été mis à jour.", "success");
            }  
        } else  {
        // Récupère les paramètres
        $this->_settings = $this->_settingsManager->get();
        }
        require "view/backend/settingsView.php";
    }
}
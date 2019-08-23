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
                "admin_email" => $_POST["admin_email"],
                "default_role" => $_POST["default_role"],
                "moderation" =>  isset($_POST["moderation"]) ? true : false,
                "posts_by_row" => $_POST["posts_by_row"],
            ]);
            // Vérifie si le nom du blog ne fait pas plus de 50 caractères
            if (iconv_strlen($this->_settings->blog_name()) > 50) {
                $this->_session->setFlash("Le nom du blog est trop long (maximum 50 caractères)", "danger");
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
                $validExtensions = array("png", "gif", "jpg", "jpeg"); // extensions autorisées
                $infoFile = pathinfo($_FILES["logoFile"]["name"]);
                $extensionFile = $infoFile["extension"];
                $maxSize = 2000000; // taille maximum (en octets) : 2 Mo
                $size = filesize($_FILES["logoFile"]["tmp_name"]); // taille du fichier
                $nameFile = basename($_FILES["logoFile"]["name"]);
                $translate = array(
                    "é" => "e",
                    "è" => "e",
                    "à" => "a",
                    "ç" => "c",
                    "'" => "_",
                    );
                $nameFile = strtr($_FILES["logoFile"]["name"], $translate); // remplace les lettres accentuées par les non accentuées
                // ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ => AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy
                $nameFile = preg_replace("/([^.a-zA-Z0-9]+)/i", "-", $nameFile); //remplace tout ce qui n'est pas une lettre ou chiffre par un tirer (-)
                $nameFile = date("Y_m_d_His") . "_" . $nameFile;
                $folder = "uploads/"; // Nom du dossier d'enregistrement
                // Vérifie s'il n'y a pas d'erreur
                if ($_FILES["logoFile"]["error"] != 0){
                    $this->_session->setFlash("Une erreur s'est produite. Le fichier n'a pas pu être téléchargé.", "danger");
                    $validation = false;
                } elseif(!in_array($extensionFile, $validExtensions)) {
                    $this->_session->setFlash("Vous devez télécharger un fichier de type png, gif, jpg ou jpeg.", "danger");
                    $validation = false;
                } elseif ($size > $maxSize) {
                    $this->_session->setFlash("La taille du fichier dépasse la limite autorisée (2Mo).", "danger");
                    $validation = false;
                } elseif (!move_uploaded_file($_FILES["logoFile"]["tmp_name"], $folder . $nameFile)) {
                        $this->_session->setFlash("Le fichier n'a pas pu être téléchargé.", "danger");
                        $validation = false;
                } else {
                $this->_session->setFlash("Le fichier a été téléchargé.", "success");
                $validation = false;
                }
            }
            // Met à jour les données si validation est vrai
            if ($validation == true) {
                $this->_settingsManager->update($this->_settings);
                $_SESSION["settings"]->setBlog_name($this->_settings->blog_name());
                $this->_session->setFlash("Les paramètres ont été mis à jour.", "success");
            }  
        } else  {
        // Récupère les paramètres
        $this->_settings = $this->_settingsManager->get();
        }
        require "view/backend/settingsView.php";
    }
}
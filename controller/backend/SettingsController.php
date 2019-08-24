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
                $validExtensions = array("png", "gif", "jpg", "jpeg"); // extensions autorisées
                $infoFile = pathinfo($_FILES["logoFile"]["name"]);
                $extensionFile = $infoFile["extension"];
                $maxSize = 5000000; // taille maximum (en octets) : 2 Mo
                $size = filesize($_FILES["logoFile"]["tmp_name"]); // taille du fichier
                $file = basename($_FILES["logoFile"]["name"]);
                $translate = array(
                    "é" => "e",
                    "è" => "e",
                    "à" => "a",
                    "ç" => "c",
                    "'" => "_",
                    );
                $file = strtr($_FILES["logoFile"]["name"], $translate); // remplace les lettres accentuées par les non accentuées
                // ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ => AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy
                $file = preg_replace("/([^.a-zA-Z0-9]+)/i", "-", $file); //remplace tout ce qui n'est pas une lettre ou chiffre par un tirer (-)
                $file = date("Y_m_d_His") . "_" . $file;
                $nameFile = str_replace("." . $extensionFile, "", $file);
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
                } 
                if (!$validation == true) {
                    $this->_session->setFlash("Le fichier n'a pas pu être téléchargé.", "danger");
                    $validation = false;
                } else {
                    $file = $nameFile . "-orignal." . $extensionFile;
                    move_uploaded_file($_FILES["logoFile"]["tmp_name"], $folder . $file);

                    // Tinify
                    require_once("vendor/tinify/lib/Tinify/Exception.php");
                    require_once("vendor/tinify/lib/Tinify/ResultMeta.php");
                    require_once("vendor/tinify/lib/Tinify/Result.php");
                    require_once("vendor/tinify/lib/Tinify/Source.php");
                    require_once("vendor/tinify/lib/Tinify/Client.php");
                    require_once("vendor/tinify/lib/Tinify.php");
                    \Tinify\setKey("4zjwW4FvW8RS04JrqCZshmlF18V01sDH");

                    try {
                        // Use the Tinify API client.
                    } catch(\Tinify\AccountException $e) {
                        print("The error message is: " . $e->getMessage());
                        // Verify your API key and account limit.
                    } catch(\Tinify\ClientException $e) {
                        // Check your source image and request options.
                    } catch(\Tinify\ServerException $e) {
                        // Temporary issue with the Tinify API.
                    } catch(\Tinify\ConnectionException $e) {
                        // A network connection error occurred.
                    } catch(Exception $e) {
                        // Something else went wrong, unrelated to the Tinify API.
                    }
           
                    $source = \Tinify\fromFile($folder . $file);
                    $optimizedFile = $nameFile . "-optimized." . $extensionFile;
                    $newfile = $source->toFile($folder . $optimizedFile);
                    $copyrighted = $source->preserve("copyright", "creation");
                    $copyrighted->toFile($optimizedFile);
                    // $message = "L'image a été compressée et enregistrée aux formats : ";
                    // $imageFormats = array([
                    //     "method" => "fit",
                    //     "width" => 800,
                    //     "height" => 450
                    // ], 
                    // [
                    //     "method" => "cover",
                    //     "width" => 800,
                    //     "height" => 450
                    // ], 
                    // [
                    //     "method" => "thumb",
                    //     "width" => 150,
                    //     "height" => 150
                    // ]);
                    // // Boucle pour les enregistrer les différents formats d'images
                    // foreach($imageFormats as $imageFormat) {
                    //     $source = \Tinify\fromFile($folder . $optimizedFile);
                    //     $resized = $source->resize(array(
                    //         "method" => $imageFormat["method"],
                    //         "width" => $imageFormat["width"],
                    //         "height" => $imageFormat["height"]
                    //     ));
                    //     $resized->toFile($folder . $nameFile . "-" . $imageFormat["method"] . "-" . $imageFormat["width"] . "x" . $imageFormat["height"] ."." . $extensionFile);
                    //     $message = $message . $imageFormat["method"] . " (" . $imageFormat["width"] . "x" . $imageFormat["height"] . "), ";
                    // }

                    // Crée l'icon pour le logo
                    // $compressionsThisMonth = \Tinify\compressionCount();
                    // $message = $message . "<br/>" . $compressionsThisMonth . "/500 compressions réalisées au cours du mois.<br/>";
                    $source = \Tinify\fromFile($folder . $optimizedFile);
                    $resized = $source->resize(array(
                        "method" => "thumb",
                        "width" => 64,
                        "height" => 64
                    ));
                    $resized->toFile("public/images/logo.ico");

                    $this->_session->setFlash("Le logo a été mis à jour.", "success");
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
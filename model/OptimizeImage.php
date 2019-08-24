<?php
namespace model;
// Une classe pour compresser et optimiser les images avec Tinify
class OptimizeImage {

    private $_file,
            $_fileInfos,
            $_fileBaseName,
            $_fileName,
            $_fileExtension,
            $_fileSize,
            $_toFolder,
            $_validExtensions,
            $_maxSize,
            $_originalFile,
            $_optimizedFile;

    public function __construct($file, $toFolder, $validExtensions, $maxSize) {
        require_once("vendor/tinify/lib/Tinify/Exception.php");
        require_once("vendor/tinify/lib/Tinify/ResultMeta.php");
        require_once("vendor/tinify/lib/Tinify/Result.php");
        require_once("vendor/tinify/lib/Tinify/Source.php");
        require_once("vendor/tinify/lib/Tinify/Client.php");
        require_once("vendor/tinify/lib/Tinify.php");

        \Tinify\setKey("4zjwW4FvW8RS04JrqCZshmlF18V01sDH");
        
        $this->_file = $file;
        $this->_toFolder = $toFolder;
        $this->_validExtensions = $validExtensions;
        $this->_maxSize = $maxSize;
        $this->init();
    }

    private function init() {
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

        $this->_fileInfos = pathinfo($this->_file["name"]); // Informations sur le fichier
        $this->_fileBaseName = basename($this->_fileInfos["basename"]); // Nom complet du fichier avec l'extension
        $this->_fileName = $this->_fileInfos["filename"]; // Nom du fichier sans extension
        $this->_fileExtension = $this->_fileInfos["extension"]; // Extension du fichier
        $this->_fileSize = filesize($this->_file["tmp_name"]); // Taille du fichier
        $translate = array(
            "é" => "e",
            "è" => "e",
            "à" => "a",
            "ç" => "c",
            "'" => "_",
        ); // ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ => AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy
        $this->_fileName = strtr($this->_fileName, $translate); // remplace les lettres accentuées par les non accentuées
        $this->_fileName = preg_replace("/([^.a-zA-Z0-9]+)/i", "-", $this->_fileName); //remplace tout ce qui n'est pas une lettre ou chiffre par un tirer (-)
        $this->_fileName = date("Y_m_d_His") . "_" . $this->_fileName;
        $this->_fileBaseName = $this->_fileName . "." . $this->_fileExtension;
        $this->_originalFile = $this->_fileName . "-original." . $this->_fileExtension;
    }
    // Compresse l'image
    public function compressImage() {
        move_uploaded_file($this->_file["tmp_name"], $this->_toFolder . $this->_originalFile);
        $source = \Tinify\fromFile($this->_toFolder . $this->_originalFile);
        $this->_optimizedFile = $this->_fileName . "-optimized." . $this->_fileExtension;
        $newfile = $source->toFile($this->_toFolder . $this->_optimizedFile);
    }
    // Redimmensionne l'image
    public function resizeImage($method, $width, $height) {
        if (!$this->_optimizedFile) {
            $this->compressImage();
         }
        // Method : fit (ex 800x450), cover (ex: 800x450), thumb (ex: 150x150)  
        $source = \Tinify\fromFile($this->_toFolder . $this->_optimizedFile);
        $resized = $source->resize([
            "method" => $method,
            "width" => $width,
            "height" => $height
        ]);
        $resized->toFile($this->_toFolder . $this->_fileName . "-" . $method . "-" . $width . "x" . $height ."." . $this->_fileExtension);
        return "L'image a été redimesionnée avec la méthode \"" . $method . "\".";
    }
    // Créé une icone
    public function createIcon($toFolder) {
        if (!$this->_optimizedFile) {
           $this->compressImage();
        }
        $source = \Tinify\fromFile($this->_toFolder . $this->_optimizedFile);
        $resized = $source->resize(array(
            "method" => "thumb",
            "width" => 64,
            "height" => 64
        ));
        $resized->toFile($toFolder);
        return "Le nouveau logo a été enregistré.";
    }
    // Récupère les métadonnées
    public function preserveMetadata() {
        if (!$this->_optimizedFile) {
            $this->compressImage();
         }
        $copyrighted = $source->preserve("copyright", "creation");
        $copyrighted->toFile($this->_toFolder . $this->_optimizedFile);
        return "Les métadonnées ont été récupérées.";
    }
    // Donne le nombre de compressions réalisées au cours du mois
    public function compressionCount() {
         $compressionsThisMonth = \Tinify\compressionCount();      
         return $compressionsThisMonth . "/500 compressions réalisées au cours du mois.";
    }

    // Getters
    public function fileName() {
        return $this->_fileName;
    }
    public function fileExtension() {
        return $this->_fileExtension;
    }
    public function fileSize() {
        return $this->_fileSize;
    }
    public function originalFile() {
        return $this->_originalFile;
    }
}
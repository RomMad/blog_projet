<?php
class DatabaseConnection
{
    //informations de connexion
    private $_dbHost,
            $_dbName,
            $_dbUser,
            $_dbPass,
            $_db;
   
    public function __construct() {
        // Vérifie si on est local ou en ligne
        if ($_SERVER["HTTP_HOST"] == "localhost") {
            $this->_dbHost = "localhost";
            $this->_dbName = "blog_projet";
            $this->_dbUser = "root";
            $this->_dbPass = "";
        } else {
            $this->_dbHost = "db5000134112.hosting-data.io";
            $this->_dbname = "dbs129050";
            $this->_dbUser = "dbu50459";
            $this->_dbPass = "!J3anF0r730r0ch3*";   
        }
        $this->_connecte = false;
        $this->connection();
    }

    private function connection()
    {
        // Connexion à la base de données
        try
        {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $this->_db = new PDO("mysql:host=" . $this->_dbHost . ";dbname=" . $this->_dbName . ";charset=utf8", $this->_dbUser, $this->_dbPass, $pdo_options);
        }
        catch(Exception $e) {
            // En cas d'erreur, on affiche un message et on arrête tout
            die("Erreur : ".$e->getMessage());
        }
    } 
 
    public function db() 
    {
        return $this->_db;
    }
}
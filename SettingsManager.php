<?php
class SettingsManager {

    private $_db; // Instance de PDO

    public function __construct($db) {
        // $this->_db = $this->databaseConnection();
        $this->setDb($db);
    }
    // Récupère les paramètres
    public function get() {
        $req = $this->_db->prepare("SELECT * FROM settings");
        $req->execute();
        $settings = $req->fetch();
        return new Settings($settings);
    }
    // Met à jour les paramètres
    public function update(Settings $settings) {
        $req = $this->_db->prepare("UPDATE settings 
        SET blog_name = :blog_name, admin_email = :admin_email, default_role = :default_role, moderation = :moderation");
        $req->execute([
            "blog_name" => $settings->blog_name(),
            "admin_email" => $settings->admin_email(),
            "default_role" => $settings->default_role(),
            "moderation" => $settings->moderation()
        ]);
    }
    
    public function setDb(PDO $db)
    {
        $this->_db = $db;
    }   
}
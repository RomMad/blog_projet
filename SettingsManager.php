<?php
class SettingsManager {

    private $_db; // Instance de PDO

    public function __construct($db) {
        // $this->_db = $this->databaseConnection();
        $this->setDb($db);
    }

    public function get() {
        $req = $this->_db->prepare("SELECT * FROM settings WHERE id = 1 LIMIT 0, 1");
        $req->execute();
        $settings = $req->fetch();
        return new Settings($settings);
    }

    public function update(Settings $settings) {
        $req = $this->_db->prepare("UPDATE settings 
        SET blog_name = :blog_name, admin_email = :admin_email, default_role = :default_role, moderation = :moderation 
        WHERE id = 1");
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



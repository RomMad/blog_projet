<?php
class SettingsManager extends Manager {

    public function __construct() {
        $this->_db = $this->databaseConnection();
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
        SET blog_name = :blog_name, admin_email = :admin_email, default_role = :default_role, moderation = :moderation, posts_by_row = :posts_by_row");
        $req->execute([
            "blog_name" => $settings->blog_name(),
            "admin_email" => $settings->admin_email(),
            "default_role" => $settings->default_role(),
            "moderation" => $settings->moderation(),
            "posts_by_row" => $settings->posts_by_row()
        ]);
    }
}
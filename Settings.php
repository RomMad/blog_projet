<?php 
class Settings extends Manager {

    private $_blog_name,
            $_admin_email,
            $_default_role,
            $_moderation;

    public function __construct(array $datas) {
        $this->hydrate($datas);
    }

    public function hydrate(array $datas) {
        foreach ($datas as $key => $value) {
            $method = "set" . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    //  Getters 
    public function blog_name() {
        return htmlspecialchars($this->_blog_name);
    }
    public function admin_email() {
        return htmlspecialchars($this->_admin_email);
    }
    public function default_role() {
        return $this->_default_role;
    }
    public function moderation() {
        return $this->_moderation;
    }

    // Setters 
    public function setBlog_name($blog_name) {
        if (is_string($blog_name)) {
            $this->_blog_name = $blog_name;
        } else {
            echo "Erreur nom du blog <br />";
        }
    }
    public function setAdmin_email($admin_email) {
        if (is_string($admin_email)) {
            $this->_admin_email = $admin_email;
        } else {
            echo "Erreur adresse email <br />";
        }
    }
    public function setDefault_role($default_role) {
        $default_role = (int) $default_role;
        if ($default_role >= 1 && $default_role <= 5) {
            $this->_default_role = $default_role;
        } else {
            echo "Erreur rôle par défaut <br />";
        }
    }
    public function setModeration($moderation) {
        $moderation = (int) $moderation;
        if ($moderation == 0 || $moderation == 1) {
            $this->_moderation = $moderation;
        } else {
            echo "Erreur modération <br />";
        }
    }

}

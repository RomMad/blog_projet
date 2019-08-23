<?php
namespace model;

class Settings {

    protected   $_blog_name,
                $_admin_email,
                $_default_role,
                $_moderation,
                $_posts_by_row;

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
    public function posts_by_row() {
        return $this->_posts_by_row;
    }

    // Setters 
    public function setBlog_name($blog_name) {
        if (is_string($blog_name)) {
            if (iconv_strlen($blog_name) < 50) {
                $this->_blog_name = $blog_name;
            } else {
                $this->_blog_name = substr($blog_name, 0, 50);
            }
        }
    }
    public function setAdmin_email($admin_email) {
        if (is_string($admin_email)) {
            if (iconv_strlen($admin_email) < 50) {
                $this->_admin_email = $admin_email;
            } else {
                $this->_admin_email = substr($admin_email, 0, 50);
            }
        }
    }
    public function setDefault_role($default_role) {
        $default_role = (int) $default_role;
        if ($default_role >= 1 && $default_role <= 5) {
            $this->_default_role = $default_role;
        } else {
            $this->_default_role = 5;
        }
    }
    public function setModeration($moderation) {
        $moderation = (int) $moderation;
        if ($moderation == 0 || $moderation == 1) {
            $this->_moderation = $moderation;
        } else {
            $this->_moderation = 1;
        }
    }
    public function setPosts_by_row($posts_by_row) {
        $posts_by_row = (int) $posts_by_row;
        if ($posts_by_row >= 1 && $posts_by_row <= 3) {
            $this->_posts_by_row = $posts_by_row;
        } else {
            $this->_posts_by_row = 1;
        }
    }
}

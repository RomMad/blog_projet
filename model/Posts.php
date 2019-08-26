<?php
namespace model;

class Posts extends Model {

    protected   $_title,
                $_user_id,
                $_user_login,
                $_login,
                $_content,
                $_status,
                $_publication_date,
                $_comment_admin;

    public function __construct(array $datas) {
        $this->hydrate($datas);
    }

    // Getters
    public function title() {
        return htmlspecialchars($this->_title);
    }

    public function user_id() {
        return $this->_user_id;
    }

    public function user_login() {
        return htmlspecialchars($this->_user_login);
    }

    public function login() {
        return htmlspecialchars($this->_login);
    }

    public function content($format) {
        if ($format == "html_format") {
            return $this->_content;
        } elseif ($format == "raw_format") {
            return nl2br(strip_tags($this->_content));
        } else {
            return ($this->_content);
        }
    }
    public function status() {
        return htmlspecialchars($this->_status);
    }
    
    public function publication_date($format = "datetime") {
        date_default_timezone_set("Europe/Paris");
        if (!empty($this->_publication_date)) {
            $publication_date = new \DateTime($this->_publication_date);
        }
        if ($format == "special_format") {
            return date_format($publication_date,"d/m/Y à H:i");
        }
        if ($format == "datetime") {
            return date_format($publication_date,"d/m/Y H:i");
        } elseif ($format == "date") {
            return date_format($publication_date,"Y-m-d");
        } elseif ($format == "time") {
            return date_format($publication_date,"H:i");
        } else {
            return $this->_publication_date;
        }
    }

    public function comment_admin() {
        return htmlspecialchars($this->_comment_admin);
    }


    // Setters
    public function setId($id) {
        $id = (int) $id;
        if ($id > 0) {
            $this->_id = $id;
        }
    }
    public function setTitle($title) {
        if (is_string($title)) {
            if (iconv_strlen($title) <= 255) {
                $this->_title = $title;
            } else {
                $this->_title = substr($title, 0, 255);
            }
        }
    }
    public function setUser_Id($user_id) {
        $user_id = (int) $user_id;
        if ($user_id > 0) {
            $this->_user_id = $user_id;
        }
    }
    public function setUser_login($user_login) {
        if (is_string($user_login)) {
            $this->_user_login = substr($user_login, 0, 50);
        }
    }
    public function setLogin($login) {
        if (is_string($login)) {
            $this->_login = substr($login, 0, 50);
        }
    }
    public function setContent($content) {
        if (is_string($content)) {
            $this->_content = $content;
        }
    }
    public function setStatus($status) {
        if ($status == "Publié") {
            $this->_status = $status;
        } else {
            $this->_status = "Brouillon";
        }
    }
    public function setPublication_date($publication_date) {
        date_default_timezone_set("Europe/Paris");
        if (!empty($publication_date)) {
            $this->_publication_date = date($publication_date);
            $isDate = $this->validateDate($publication_date, "Y-m-d H:i:s");
            if ($isDate) {
                $this->_publication_date = date($publication_date);
            } else {
                $this->_publication_date = date("Y-m-d H:i");
            }
        } else {
            $this->_publication_date = NULL;
        }
    }
    public function setComment_admin($comment_admin) {
        if (is_string($comment_admin)) {
            if (iconv_strlen($comment_admin) <= 255) {
                $this->_comment_admin = $comment_admin;
            } else {
                $this->_comment_admin = substr($comment_admin, 0, 255);
            }
        }
    }
}
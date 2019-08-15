<?php 
class Posts extends Model {

    protected   $_title,
                $_user_id,
                $_user_login,
                $_login,
                $_content,
                $_status;

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
            return ($this->_content);
        } elseif ($format == "raw_format") {
            return nl2br(strip_tags(($this->_content)));
        } else {
            return ($this->_content);
        }
    }
    public function status() {
        return htmlspecialchars($this->_status);
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
                // $this->setFlash("Le titre a été tronqué (maximum 255 caractères).", "warning");
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
            $this->_user_login = $user_login;
        }
    }
    public function setLogin($login) {
        if (is_string($login)) {
            $this->_login = $login;
        }
    }
    public function setContent($content) {
        if (is_string($content)) {
            $this->_content = ($content);
        }
    }
    public function setStatus($status) {
        if (is_string($status) && ($status == "Publié" || $status == "Brouillon")) {
            $this->_status = $status;
        }
    }
}
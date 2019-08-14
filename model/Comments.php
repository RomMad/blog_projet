<?php
class Comments extends Session {

    protected   $_id,
                $_post_id,
                $_user_id,
                $_user_name,
                $_login,
                $_content,
                $_status,
                $_report_date,
                $_nb_report,
                $_creation_date,
                $_update_date;

    public function __construct(array $datas) {
        $this->hydrate($datas);
    }

    public function hydrate (array $datas) {
        foreach($datas as $key => $value) {
            $method = "set" . ucfirst($key); // récupère le nom du setter correspondant à l'attribut
            if (method_exists($this, $method)) { // vérifie si le setter correspondant existe
                $this->$method($value); // si oui, appelle le setter
            }
        }
    }

    // Getters
    public function id() {
        return $this->_id;
    }
    public function post_id() {
        return $this->_post_id;
    }
    public function user_id() {
        return $this->_user_id;
    }
    public function user_name() {
        return htmlspecialchars($this->_user_name);
    }
    public function login() {
        return htmlspecialchars($this->_login);
    }
    public function content() {
        return htmlspecialchars($this->_content);
    }
    public function status() {
        return $this->_status;
    }
    public function report_date() {
        return $this->_report_date;
    }
    public function nb_report() {
        return $this->_nb_report;

    }
    public function creation_date() {
        $creation_date = new DateTime($this->_creation_date);
        if (!empty($format) && $format == "special_format") {
            return date_format($creation_date,"d/m/Y à H:i");
        } else {
            return date_format($creation_date,"d/m/Y H:i");
        }
    }
    public function update_date() {
        $update_date = new DateTime($this->_update_date);
        if (!empty($format) && $format == "special_format") {
            return date_format($update_date,"d/m/Y à H:i");
        } else {
            return date_format($update_date,"d/m/Y H:i");
        }
    }

    // Setters
    public function setId($id) {
        $id = (int) $id;
        if ($id > 0) {
            $this->_id = $id;
        }
    }
    public function setPost_id($post_id) {
        $post_id = (int) $post_id;
        if ($post_id > 0) {
            $this->_post_id = $post_id;
        }
    }
    public function setUser_id($user_id) {
        $user_id = (int) $user_id;
        if ($user_id > 0) {
            $this->_user_id = $user_id;
        }
    }
    public function setUser_name($user_name) {
        if (is_string($user_name)) {
            $this->_user_name = $user_name;
        }
    }
    public function setLogin($login) {
        if (is_string($login)) {
            $this->_login = $login;
        }
    }
    public function setContent($content) {
        if (is_string($content)) {
            $this->_content = $content;
        }
    }
    public function setStatus($status) {
        $status = (int) $status;
        if ($status >= 0 && $status <=2) {
            $this->_status =  $status;
        }
    }
    public function setReport_date($report_date) {
            $this->_report_date =  $report_date;
    }
    public function setNb_report($nb_report) {
        $nb_report = (int) $nb_report;
        if ($nb_report >= 0) {
            $this->_nb_report =  $nb_report;
        }
    }
    public function setCreation_date($creation_date) {
        $this->_creation_date =  $creation_date;
    }
    public function setUpdate_date($update_date) {
        $this->_update_date =  $update_date;
    }
}
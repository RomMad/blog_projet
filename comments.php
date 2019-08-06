<?php
class Comments {

    private $_id,
            $_post_id,
            $_user_id,
            $_user_name,
            $_content,
            $_status,
            $_report_date,
            $_nb_report,
            $_creation_date,
            $_update_date;

    public function __construct(array $datas) {
        $this->hydrate($datas);
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
        return $this->_user_name;
    }
    public function login() {
        return $this->_login;
    }
    public function content() {
        return $this->_content;
    }

    public function status() {
        return $this->_status;
    }

    public function report_date() {
        return $this->_report_date;
    }

    public function nb_report() {
        return $this->nb_report;
    }

    public function creation_date() {
        return $this->_creation_date;
    }

    public function update_date() {
        return $this->_update_date;
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

    public function setContent($content) {
        if (is_string($content)) {
            $this->_content = $content;
        }
    }
    public function setStatus($status) {
        $status = (int) $status;
        if ($status >= 0 && $status <=2) {
            $this->_status = $status;
        }
    }

    public function setReport_date($report_date) {
        $isDate = $this->validateDate($report_date, "Y-m-d H:i:s");
        if ($isDate) {
            $report_date = new DateTime($report_date);
            $this->_report_date = date_format($report_date,"d/m/Y H:i");
        } else {
            echo "Erreur dans le format de la date. ";
        }
    }

    public function setNb_report($nb_report) {
        $nb_report = (int) $nb_report;
        if ($nb_report >= 0) {
            $this->_b_report = $nb_report;
        }
    }

    public function setCreation_date($creation_date) {
        $isDate = $this->validateDate($creation_date, "Y-m-d H:i:s");
        if ($isDate) {
            $creation_date = new DateTime($creation_date);
            $this->_creation_date = date_format($creation_date,"d/m/Y H:i");
        } else {
            echo "Erreur dans le format de la date. ";
        }
    }
    
    public function setUpdate_date($update_date) {
        $isDate = $this->validateDate($update_date, "Y-m-d H:i:s");
        if ($isDate) {
            $update_date = new DateTime($update_date);
            $this->_update_date = $update_date->format("d/m/Y H:i");
        }
        echo "Erreur dans le format de la date. ";
    }

    // Vérifie si la date est valide
    private function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
}


<?php
namespace model;
use DateTime;

class Model {

    protected   $_id,
                $_creation_date,
                $_update_date;
            

    public function __construct(array $datas) {
        $this->hydrate($datas);
    }

    public function hydrate(array $datas) {
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

    public function creation_date($format) {
        $creation_date = new DateTime($this->_creation_date);
        if (!empty($format) && $format == "special_format") {
            return date_format($creation_date,"d/m/Y à H:i");
        } else {
            return date_format($creation_date,"d/m/Y H:i");
        }
    }

    public function update_date($format) {
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

    public function setCreation_date($creation_date) {
        $isDate = $this->validateDate($creation_date, "Y-m-d H:i:s");
        if ($isDate) {
            $this->_creation_date = $creation_date;
        }
    }

    public function setUpdate_date($update_date) {
        $this->_update_date = $update_date;
    }
    
    // Vérifie si la date est valide
    protected function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
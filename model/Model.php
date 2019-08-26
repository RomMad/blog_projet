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
    public function creation_date($format = "Y-m-d H:i") {
        $creation_date = new \DateTime($this->_creation_date);
        return $this->formatDate($creation_date, $format);
    }
    public function update_date($format = "Y-m-d H:i") {
        $update_date = new \DateTime($this->_update_date);
        return $this->formatDate($update_date, $format);
    }

    // Setters
    public function setId($id) {
        $id = (int) $id;
        if ($id > 0) {
            $this->_id = $id;
        }
    }
    public function setCreation_date($creation_date) {
            $this->_creation_date = $creation_date;
    }
    public function setUpdate_date($update_date) {
        $this->_update_date = $update_date;
    }

    // Donne la date selon le format choisi
    public function formatDate($date, $format = "Y-m-d H:i") {
        switch ($format) {
            case "datetime_special":
                return date_format($date,"d/m/Y à H:i");
                break;
            case "datetime_fr":
                return date_format($date,"d/m/Y H:i");
                break;
            case "datetime":
                return date_format($date,"Y-m-d H:i");
                break;
            case "date":
                return date_format($date,"Y-m-d");
                break;
            case "time":
                return date_format($date,"H:i");
                break;                
            default:
                return date_format($date,"Y-m-d H:i");
            break;
        }
    }

    // Vérifie si la date est valide
    protected function validateDate($date, $format = "Y-m-d H:i:s") {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}
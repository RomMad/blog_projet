<?php 
class Users {
    
    private $_id,
            $_login,
            $_name,
            $_surname,
            $_birthdate,
            $_pass,
            $_email,
            $_role,
            $_registration_date,
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
    public function login() {
        return $this->_login;
    }
    public function name() {
        return $this->_surname;
    }
    public function surname() {
        return $this->_surname;
    }
    public function birthdate() {
        return $this->_birthdate;
    }
    public function pass() {
        return $this->_pass;
    }
    public function email() {
        return $this->_email;
    }
    public function role() {
        return $this->_role;
    }
    public function registration_date() {
        return $this->_registration_date; 
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
    public function setlogin($login) {
        if (is_string($login)) {
            $this->_login = $login;
        }
    }
    public function setName($name) {
        if (is_string($name)) {
            $this->_name = $name;
        }
    }
    public function setSurname($surname) {
        if (is_string($surname)) {
            $this->_surname = $surname;
        }
    }
    public function setBirthdate($birthdate) {
        $isDate = $this->validateDate($birthdate, "Y-m-d H:i:s");
        if ($isDate) {
            $birthdate = new DateTime($birthdate);
            $this->_birthdate = $birthdate->format("d/m/Y H:i");
        }
    }
    public function setPass($pass) {
        if (is_string($pass)) {
            $this->_pass = $pass;
        } else {
            echo "erreur pass";
        }
    }
    public function setEmail($email) {
        if (is_string($email)) {
            $this->_email = $email;
        } else {
            echo "erreur email";
        }
    }
    public function setRole($role) {
        $role = (int) $role;
        if ($role >= 1 && $role <= 5) {
            $this->_role = $role;
        } else {
            echo "erreur role";
        }
    }
    public function setRegistration_date($registration_date) {
        $isDate = $this->validateDate($registration_date, "Y-m-d H:i:s");
        if ($isDate) {
            $registration_date = new DateTime($registration_date);
            $this->_registration_date = $registration_date->format("d/m/Y H:i");
        }
    }
    public function setUpdate_date($update_date) {
        $isDate = $this->validateDate($update_date, "Y-m-d H:i:s");
        if ($isDate) {
            $update_date = new DateTime($update_date);
            $this->_update_date = $update_date->format("d/m/Y H:i");
        }
    }

    // Vérifie si la date est valide
    private function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

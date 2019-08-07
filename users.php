<?php 
class Posts {
    
    private $_id,
            $_login,
            $_name,
            $_surname,
            $_birthdate,
            $_pass,
            $_email,
            $_role,
            $registration_date,
            $_update_date;
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
    public function setlogin() {
        if (is_string($login)) {
            $this->_login = $login;
        }
    }
    public function setName() {
        if (is_string($name)) {
            $this->_name = $name;
        }
    }
    public function setSurname() {
        if (is_string($surname)) {
            $this->_surname = $surname;
        }
    }
    public function setBirthdate() {
        $isDate = $this->validateDate($birthdate, "Y-m-d H:i:s");
        if ($isDate) {
            $birthdate = new DateTime($birthdate);
            $this->_birthdate = $birthdate->format("d/m/Y H:i");
        }
    public function setPass() {
        if (is_string($pass)) {
            $this->_pass = $pass;
        }
    }
    public function setEmail() {
        if (is_string($email)) {
            $this->_email = $email;
        }
    }
    public function setRole() {
        $role = (int) $role;
        if ($role >= 1 && $role <= 5) {
            $this->_role = $role;
        }
    }
    public function setRegistration_date() {
        $isDate = $this->validateDate($registration_date, "Y-m-d H:i:s");
        if ($isDate) {
            $registration_date = new DateTime($registration_date);
            $this->_registration_date = $registration_date->format("d/m/Y H:i");
        }
    public function setUpdate_date() {
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

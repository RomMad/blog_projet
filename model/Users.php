<?php 
class Users extends Model {
    
    protected   $_login,
                $_email,
                $_pass,
                $_name,
                $_surname,
                $_birthdate,
                $_role,
                $_roleUser,
                $_remember_me,
                $_registration_date;

    public function __construct(array $datas) {
        $this->hydrate($datas);
    }

    // Getters
    public function login() {
        return htmlspecialchars($this->_login);
    }
    public function email() {
        return htmlspecialchars($this->_email);
    }
    public function pass() {
        return htmlspecialchars($this->_pass);
    }
    public function name() {
        return htmlspecialchars($this->_name);
    }
    public function surname() {
        return htmlspecialchars($this->_surname);
    }
    public function birthdate() {
        return $this->_birthdate;
    }

    public function role() {
        return $this->_role;
    }
    public function role_user() {
        return $this->_roleUser;
    }
    public function remember() {
        return $this->_remember;
    }    
    public function registration_date() {
        $registration_date = new DateTime($this->_registration_date);
        if (!empty($format) && $format == "special_format") {
            return date_format($registration_date,"d/m/Y à H:i");
        } else {
            return date_format($registration_date,"d/m/Y H:i");
        }
    }

    // Setters
    public function setlogin($login) {
        if (preg_match("#^[a-zA-Z0-9_.-]{5,20}$#",$login)) {
            $this->_login = $login;
        } else {
            // $this->setFlash("Le login est incorrect : entre 5 et 20 caractères (lettres ou chiffres).", "danger");       
        }
    }
    public function setEmail($email) {
        if (is_string($email)) {
            $this->_email = $email;
        } else {
            return "Erreur email <br />";
        }
    }
    public function setPass($pass) {
        if (is_string($pass)) {
            $this->_pass = $pass;
        } else {
            return "Erreur pass <br />";
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
        if (!empty($birthdate)) {
            $isDate = $this->validateDate($birthdate, "Y-m-d");
            if ($isDate) {
                $this->_birthdate = date($birthdate);
            } else {
                return "Erreur date de naissance <br />";
            }
        }
    }
    public function setRole($role) {
        $role = (int) $role;
        if ($role >= 1 && $role <= 5) {
            $this->_role = $role;
        } else {
            return "Erreur role <br />";
        }
    }
    public function setRole_user($roleUser) {
        if (is_string($roleUser)) {
            $this->_roleUser = $roleUser;
        }
    }
    public function setRemember($remember) {
        if (is_bool($remember)) {
            $this->_remember = $remember;
        }
    }    
    public function setRegistration_date($registration_date) {
        $this->_registration_date = $registration_date;
    }
}

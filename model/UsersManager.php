<?php
class UsersManager extends Manager {

    public function __construct() {
        $this->_db = $this->databaseConnection();
    }

    // Ajout d'un utilisateur
    public function add(Users $user) {
        $req =$this->_db->prepare("INSERT INTO users(login, email, name, surname, birthdate, pass) 
                                VALUES(:login, :email, :name, :surname, :birthdate, :pass)");
        $req->execute([
            "login" => $user->login(),
            "pass" => $user->pass(),
            "email" => $user->email(),
            "name" => $user->name(),
            "surname" => $user->surname(),
            "birthdate" => $user->birthdate()
        ]);
    }

    // Contrôle l'utilisateur
    public function verify($info) {
        // Vérifie si c'est une adresse email
        if (preg_match("#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#", $info)) {
            $filter = "email = '" . $info . "'";
        } 
        // Sinon c'est le login
        else {
            $filter = "login = '" . $info . "'";
        }
        $req = $this->_db->prepare("SELECT u.id, u.login, u.email, u.pass,  u.name, u.surname, u.role, r.role_user
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id 
            WHERE $filter");
        $req->execute();
        $user = $req->fetch();
        if ($user) {
            return new Users($user);
        }
    }

    // Vérifie si l'utilisateur
    public function exists($id) {
        $isUserExists = $this->_db->query("SELECT COUNT(*) FROM users WHERE id = " . $id)->fetchColumn();
        return $isUserExists;
    }

    // Récupère un utilisateur
    public function get($info) {
        if (is_numeric($info)) {
            $info = (int) $info;
            $filter = "u.id = '" . $info . "'";
        } else {
            $filter = "u.email = '" . $info . "'";
        }
        $req = $this->_db->prepare("SELECT u.id, u.login, u.email, u.name, u.surname, u.birthdate, u.role, r.role_user
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id 
            WHERE $filter");
        $req->execute();
        $user = $req->fetch();
        if ($user) {
            return new Users($user);
        }
    }
    // Récupère le role de l'utilisateur
    public function getRole($id) {
        if (is_numeric($id)) {
            $info = (int) $id;
            $req = $this->_db->prepare("SELECT role FROM users WHERE id = $id");
            $req->execute();
            $user = $req->fetch();
            return  $user["role"];
        }
    }
    // Récupère le mot de passe haché de l'utilisateur
    public function getPass($id) {
        if (is_numeric($id)) {
            $info = (int) $id;
            $req = $this->_db->prepare("SELECT pass FROM users WHERE id = $id");
            $req->execute();
            $user = $req->fetch();
            return  $user["pass"];
        }
    }
    // Récupère le dernier utilisateur créé
    public function lastCreate() {

    }

    // Récupère une liste d'utilisateurs
    public function getList($filter, $orderBy, $order, $nbLimit, $nbOffset) {
        $req = $this->_db->prepare("SELECT u.id, u.login, u.name, u.surname, u.birthdate, u.email, u.role, r.role_user, u.registration_date, u.update_date
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id
            WHERE $filter 
            ORDER BY $orderBy $order
            LIMIT  $nbLimit, $nbOffset");
        $req->execute();

        while ($datas = $req->fetch()) {
            $users[] = new Users($datas);
        }
        if (isset($users)) {
            return $users;
        }
    }

    // Modifie le profil de l'utilisateur
    public function updateProfil(Users $user) {
        $req = $this->_db->prepare("UPDATE users SET login = :newLogin, email = :newEmail, name = :newName, surname = :newSurname, birthdate = :newBirthdate, update_date = NOW() 
            WHERE id = :id");
        $req->execute([
            "id" => $user->id(),
            "newLogin" => $user->login(),
            "newEmail" => $user->email(),
            "newName" => $user->name(),
            "newSurname" => $user->surname(),
            "newBirthdate" => $user->birthdate(),
        ]);
    }

    // Modifie le mot de passe de l'utilisateur
    public function updatePass(Users $user) {
        $req = $this->_db->prepare("UPDATE users SET pass = :newPass, update_date = NOW() WHERE id = :id");
        $req->execute([
            "id" => $user->id(),
            "newPass" => $user->pass()
        ]);
    }

    // Modifie le rôle de l'utilisateur
    public function updateRole(Users $user) {
            $req = $this->_db->prepare("UPDATE users SET role = :newRole, update_date = NOW() WHERE id = :id");
            $req->execute([
                "id" => $user->id(),
                "newRole" => $user->role()
            ]);
    }

    // Met le "remember" de la connection en VRAI
    public function rememberTrue($id) {
        if (is_numeric($id)) {
            $info = (int) $id;
            $req = $this->_db->prepare("UPDATE users SET remember = :remember WHERE id = :id");
            $req->execute([
                "id" => $id,
                "remember" => true
            ]);
        }
    }
    // Supprime un utilisateur
    public function delete(Users $user) {
            $req = $this->_db->prepare("DELETE FROM users WHERE id = ? ");
            $req->execute([
                $user->id()
            ]);
    }

    //  Compte le nombre d'utilisateurs
    public function count($filter) {
        $req = $this->_db->prepare("SELECT COUNT(*), u.login, u.email, u.role, r.id 
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id  
            WHERE $filter");
        $req->execute();
        $nbUsers = $req->fetchColumn();
        return $nbUsers;
    }
}
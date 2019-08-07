<?php
class UsersManager {

    private $_db;

    public function __construct($db) {
        $this->setDb($db);
    }

    // Ajout d'un utilisateur
    public function add(Users $user) {

    }

    // Contrôle l'utilisateur
    public function verify($login) {
        $req = $this->_db->prepare("SELECT * FROM users WHERE login = ?");
        $req->execute([
            $login
        ]);
        $user = $req->fetch();
        if (!empty($user)) {
            return new Users($user);
        }
    }

    // Récupère un utilisateur
    public function get($id) {
        $req = $this->_db->prepare("SELECT u.login, u.name, u.surname, u.birthdate, u.email, u.role, r.role_user
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id 
            WHERE u.id = $id");
        $req->execute([
            $id 
        ]);
        $user = $req->fetch();
        return new Users($user);
    }
    // Récupère un utilisateur
    public function getRole($id) {
        $req = $this->_db->prepare("SELECT role FROM users WHERE id = $id");
        $req->execute([
            $id 
        ]);
        $user = $req->fetch();
        return new Users($user);
    }

    // Récupère le dernier utilisateur créé
    public function lastCreate() {

    }

    // Récupère une liste d'utilisateurs
    public function getList($filter, $orderBy, $order, $minLimit, $maxLimit) {
        $req = $this->_db->prepare("SELECT u.id, u.login, u.name, u.surname, u.birthdate, u.email, u.role, u.registration_date, u.update_date
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id
            WHERE $filter 
            ORDER BY $orderBy $order
            LIMIT  $minLimit, $maxLimit");
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
        $req = $this->_db->prepare("UPDATE users SET login = :newLogin, pass = :newPass, email = :newEmail, name = :newName, surname = :newSurname, birthdate = :newBirthdate, update_date = NOW() 
            WHERE id = :id");
        $req->execute([
            "id" => $user->id(),
            "newLogin" => $user->login(),
            "newPass" => $user->pass(),
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
    public function updateRole($id, $role) {
        $req = $this->_db->prepare("UPDATE users SET role = :newRole, update_date = NOW() WHERE id = :id");
        $req->execute([
            "id" => $id,
            "newRole" => $role
        ]);
    }
    // Supprime un utilisateur
    public function delete($id) {
        $req = $this->_db->prepare("DELETE FROM users WHERE id = ? ");
        $req->execute([
            $id
        ]);
    }

    //  Compte le nombre d'utilisateurs
    public function count($filter) {
        $req = $this->_db->prepare("SELECT COUNT(*) AS nb_Users, u.role, r.id 
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.id  
            WHERE $filter");
        $req->execute();
        $nbUsers = $req->fetchColumn();
        return $nbUsers;
    }

    private function setDb(PDO $db){
        $this->_db = $db;
      }
}
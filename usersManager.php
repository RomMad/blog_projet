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
        $req = $this->_db->prepare("SELECT * FROM users WHERE id = $id");
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
        $req = $this->_db->prepare("SELECT u.ID, u.login, u.name, u.surname, u.birthdate, u.email, u.role, u.registration_date, u.update_date
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.ID
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

    // Met à jour un utilisateur
    public function update(Users $user) {
        $req = $this->_db->prepare("UPDATE users SET role = 1 WHERE ID = ? ");
        $req->execute(array($selectedUser));
    }

    // Supprime un utilisateur
    public function delete($id) {
        $req = $this->_db->prepare("DELETE FROM users WHERE ID = ? ");
        $req->execute([
            $id
        ]);
    }

    //  Compte le nombre d'utilisateurs
    public function count($filter) {
        $req = $this->_db->prepare("SELECT COUNT(*) AS nb_Users, u.role, r.ID 
            FROM users u
            LEFT JOIN user_role r
            ON u.role = r.ID  
            WHERE $filter");
        $req->execute();
        $nbUsers = $req->fetchColumn();
        return $nbUsers;
    }

    private function setDb(PDO $db){
        $this->_db = $db;
      }
}
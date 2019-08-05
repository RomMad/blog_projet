<?php 
class Posts {

    private $_id,
            $_title,
            $_user_id,
            $_user_login,
            $_login,
            $_content,
            $_status,
            $_creation_date,
            $_update_date;
            
    const UNAUTHORIZED = 1;
    const CREATE_POST = 2;
    const READ_POST = 3;
    const EDIT_POST = 4;
    const DELETE_POST = 5;

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

    public function createPost(Users $user) {
        // Vérifie si l'utilisateur a les droits pour créer un article
        if ($user->role() >4) {
            return self::UNAUTHORIZED;
        }   
        // Informe l'utilisateur que l'article a été créé

    }
    
    public function readPost(Posts $post) {
        // Tout le monde peut lire le post
    }

    public function updatePost(Users $user, Posts $post) {
        // Vérifie si l'utilisateur a les droits droits pour modifier l'article
        if ($user->id() != $this->_user_id) {
            return self::UNAUTHORIZED;
        }
        // Informe l'utilisateur de la mise à jour de l'article

    }

    public function deletePost (User $user, Posts $post) {
        // Vérifie si l'utilisateur a les droits pour supprimer l'article
        if ($user->id() != $this->_user_id || $user->role() >1) {
            return self::UNAUTHORIZED;
        }
        // Informe l'utilisateur que l'article a été supprimé

    }


    // Getters
    public function id() {
        return $this->_id;
    }

    public function title() {
        return $this->_title;
    }

    public function user_id() {
        return $this->_user_id;
    }

    public function user_login() {
        return $this->_user_login;
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

    public function setTitle($title) {
        if (is_string($title)) {
            $this->_title = $title;
        }
    }

    public function setUser_Id($user_id) {
        $user_id = (int) $user_id;
        if ($user_id > 0) {
            $this->_user_id = $user_id;
        }
    }
    public function setUser_login($user_login) {
        if (is_string($user_login)) {
            $this->_user_login = $user_login;
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
        if (is_string($status) && ($status == "Publié" || $status == "Brouillon")) {
            $this->_status = $status;
        }
    }

    public function setCreation_date($creation_date) {
        $isDate = $this->validateDate($creation_date, "d/m/Y à H:i");
        if ($isDate) {
            $this->_creation_date = $creation_date;
        } else {
            echo "Erreur dans le format de la date !";
        }
    }
    
    public function setUpdate_date($update_date) {
        $isDate = $this->validateDate($update_date, "d/m/Y à H:i");
        if ($isDate) {
            $this->_update_date = $update_date;
        }
    }
    
    private function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}
<?php
class PostsManager {

    private $_db; // Instance de PDO

    public function __construct($db) {
        $this->setDb($db);
    }

    // Méthode d'ajout d'un article
    public function add(Posts $post) {
        // Prépare la requête d'insertion
        $req = $this->_db->prepare("INSERT INTO posts(user_ID, user_login, title, content, status) 
            VALUES(:user_ID, :user_login, :title, :content, :status)");
        // Exécute la requête
        $req->execute([
            "user_ID" => $post->user_ID(),
            "user_login" => $post->user_login(),
            "title" => $post->title(),
            "content" => $post->content(),
            "status" => $post->status()
        ]);
        // Hydrate l'article passé en paramètre avec assignation de son identifiant
        $post->hydrate([
            "id" => $this->_db->lastInsertId()
        ]);
        return "L'article a été enregistré.";
    }
    // Méthode de lecture d'un article
    public function get($id) {
        $req = $this->_db->prepare("SELECT p.ID, p.title, p.user_ID,  p.user_login, u.login, p.content, p.status, 
        DATE_FORMAT(p.creation_date, '%d/%m/%Y à %H:%i') AS creation_date, 
        DATE_FORMAT(p.update_date, '%d/%m/%Y à %H:%i') AS update_date
        FROM posts p
        LEFT JOIN users u
        ON p.user_ID = u.ID
        WHERE p.ID = ? ");
        $req->execute([
            $id
        ]);
        $dataPost = $req->fetch();
        return new Posts($dataPost);
    }
    // Méthode de lecture d'un article
    public function lastCreate($user_id) {
        $req = $this->_db->prepare("SELECT p.ID, p.title, p.user_ID, u.login, p.content, p.status, 
        DATE_FORMAT(p.creation_date, \"%d/%m/%Y à %H:%i\") AS creation_date, 
        DATE_FORMAT(p.update_date, \"%d/%m/%Y à %H:%i\") AS update_date 
        FROM posts p 
        LEFT JOIN users u 
        ON p.user_ID = u.ID 
        WHERE p.user_ID = ? 
        ORDER BY p.ID DESC 
        LIMIT 0, 1");
        $req->execute([
            $user_id
        ]);
        $dataPost = $req->fetch();
        return new Posts($dataPost);
    }
    // Méthode de récupération d'une liste d'articles
    public function getList($filter, $minPost, $maxPost) {
        $req = $this->_db->prepare("SELECT p.ID, p.title, p.user_ID, p.user_login, u.login, p.status, 
            IF(CHAR_LENGTH(p.content) > 1200, CONCAT(SUBSTRING(p.content, 1, 1200), ' [...]'), p.content) AS content, 
            DATE_FORMAT(p.creation_date, '%d/%m/%Y à %H:%i') AS creation_date, 
            DATE_FORMAT(p.update_date, '%d/%m/%Y à %H:%i') AS update_date
            FROM posts p
            LEFT JOIN users u
            ON p.user_ID = u.ID
            WHERE status = 'publié' $filter 
            ORDER BY p.creation_date DESC 
            LIMIT  $minPost, $maxPost");
        $req->execute();

        while ($datas = $req->fetch()) {
            $dataPosts[] = new Posts($datas);
        }
        return $dataPosts;
    }
    // Méthode de mise à jour d'un article
    public function update(Posts $post) {
        $req = $this->_db->prepare("UPDATE posts SET title = :new_title, content = :new_content, status = :new_status, update_date = NOW() WHERE ID = :post_ID");
        $req->execute([
            "new_title" => $post->title(),
            "new_content" => $post->content(),
            "new_status" => $post->status(),
            "post_ID" => $post->id()
        ]);
        return "L'article a été modifié.";
    }
    // Méthode de suppresion d'un article
    public function delete($id) {
        $req = $this->_db->prepare("DELETE FROM posts WHERE ID = ? ");
        $req->execute([
            $id
        ]);
        return "L'article a été supprimé.";
    }
    // Méthode qui coimpte le nombre d'articles
    public function count($filter) {
        // Prépare une requête COUNT()
        $req = $this->_db->prepare("SELECT COUNT(*) AS nb_Posts FROM posts 
            WHERE status = 'publié' $filter");
        $req->execute();
        $nbPosts = $req->fetch();
        //  Retourne le nombre d'enregistrements
        return  $nbPosts["nb_Posts"];
    }

    public function setDb(PDO $db)
    {
      $this->_db = $db;
    }

}
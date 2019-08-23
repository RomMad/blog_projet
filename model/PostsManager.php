<?php
namespace model;

class PostsManager extends Manager {

    public function __construct() {
        $this->_db = $this->databaseConnection();
    }

    // Méthode d'ajout d'un article
    public function add(Posts $post) {
        $req = $this->_db->prepare("INSERT INTO posts(user_id, user_login, title, content, status) 
            VALUES(:user_id, :user_login, :title, :content, :status)");
        $req->execute([
            "user_id" => $post->user_id(),
            "user_login" => $post->user_login(),
            "title" => $post->title(),
            "content" => $post->content(""),
            "status" => $post->status()
        ]);
        // Hydrate l'article passé en paramètre avec assignation de son identifiant
        $post->hydrate([
            "id" => $this->_db->lastInsertId()
        ]);
        return "L'article a été enregistré.";
    }
    // Méthode qui compte le nombre d'articles
    public function count($filter) {
        if (is_string($filter)) {
            $req = $this->_db->prepare("SELECT COUNT(*) FROM posts p WHERE " . $filter);
            $req->execute();
            $nbPosts = $req->fetchColumn();
            return $nbPosts;
        }
    }
    // Vérifie si l'article existe
    public function exists($id) {
        if (is_numeric($id) && (int) $id > 0) {
            $isPostExists = $this->_db->query("SELECT COUNT(*) FROM posts WHERE id = " . $id)->fetchColumn();
            return $isPostExists;
        }
    }
    // Méthode de lecture d'un article
    public function getUserId($id) {
        if (is_numeric($id) && (int) $id > 0) {
            $req = $this->_db->prepare("SELECT user_id FROM posts WHERE id = ?");
            $req->execute([
                $id
            ]);
            $post = $req->fetch();
            if (isset($post)) {
                return new Posts($post);
            }
        }
    }    
    // Méthode de lecture d'un article
    public function get($id) {
        if (is_numeric($id) && (int) $id > 0) {
            $req = $this->_db->prepare("SELECT p.id, p.title, p.user_id, p.user_login, u.login, p.content, p.status, p.creation_date, p.update_date 
                FROM posts p
                LEFT JOIN users u
                ON p.user_id = u.id
                WHERE p.id = ?");
            $req->execute([
                $id
            ]);
            $post = $req->fetch();
            if (!empty($post)) {
            return new Posts($post);
            } else {
                return FALSE;
            }
        }
    }
    // Méthode de lecture d'un article
    public function lastCreate($userId) {
        if (is_numeric($userId) && (int) $userId > 0) {
            $req = $this->_db->prepare("SELECT p.id, p.title, p.user_id, u.login, p.content, p.status, p.creation_date, p.update_date 
            FROM posts p 
            LEFT JOIN users u 
            ON p.user_id = u.id 
            WHERE p.user_id = ? 
            ORDER BY p.id desc 
            LIMIT 0, 1");
            $req->execute([
                $userId
            ]);
            $post = $req->fetch();
            return new Posts($post);
        }
    }
    // Méthode de récupération d'une liste d'articles
    public function getList($filter, $orderBy, $order, $nbLimit, $nbPosts) {
        if (is_string($filter) && is_string($orderBy) && ($order == "asc" || $order == "desc") && ((int) $nbLimit >= 0) && ((int) $nbPosts > 0)) {
            $req = $this->_db->prepare("SELECT p.id, p.title, p.user_id, p.user_login, u.login, p.status, p.creation_date, p.update_date, 
                IF(CHAR_LENGTH(p.content) > 1500, CONCAT(SUBSTRING(p.content, 1, 1500), '[...]'), p.content) AS content
                FROM posts p
                LEFT JOIN users u
                ON p.user_id = u.id
                WHERE $filter 
                ORDER BY $orderBy $order
                LIMIT $nbLimit, $nbPosts");
            $req->execute();
            while ($datas = $req->fetch()) {
                $posts[] = new Posts($datas);
            }
            if (isset($posts)) {
                return $posts;
            }
        }
    }
    // Méthode de mise à jour d'un article
    public function update(Posts $post) {
        $req = $this->_db->prepare("UPDATE posts SET title = :newTitle, content = :newContent, status = :newStatus, update_date = NOW() WHERE id = :postId");
        $req->execute([
            "newTitle" => $post->title(),
            "newContent" => $post->content(""),
            "newStatus" => $post->status(),
            "postId" => $post->id()
        ]);
        return "L'article a été modifié.";
    }
    // Méthode de mise à jour du statut d'un article
    public function updateStatus(Posts $post) {
        $req = $this->_db->prepare("UPDATE posts SET status = :newStatus WHERE id = :id");
        $req->execute([
            "id" => $post->id(),
            "newStatus" => $post->status(),
        ]);
    }
    // Méthode de suppresion d'un article
    public function delete(Posts $post) {
        $req = $this->_db->prepare("DELETE FROM posts WHERE id = :id");
        $req->execute([
            "id" => $post->id()
        ]);    
    }
}
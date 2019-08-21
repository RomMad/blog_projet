<?php
class CommentsManager extends Manager {

    public function __construct() {
        $this->_db = $this->databaseConnection();
    }

    // Ajoute un commentaire
    public function add(Comments $comment) {
        $req = $this->_db->prepare("INSERT INTO comments(post_id, user_id, user_name, content, status) 
        VALUES(:post_id, :user_ID, :user_name, :content, :status)");
        $req->execute([
            "post_id" => $comment->post_id(),
            "user_ID" => $comment->user_id(),
            "user_name" => $comment->user_name(),
            "content" => $comment->content(),
            "status" => $comment->status()
        ]);
        // Hydrate le commentaire passé en paramètre avec assignation de son identifiant
        $comment->hydrate([
            "id" => $this->_db->lastInsertId()
        ]);
        return "Le commentaire a été enregistré.";
    }

    // Récupère un commentaire
    public function get($id) {
        if (is_numeric($id) && (int) $id > 0) {
            $req = $this->_db->prepare("SELECT c.id, c.user_id, c.user_name, u.login, c.content, c.status, c.creation_date, c.update_date 
                FROM comments c  
                LEFT JOIN users u 
                ON c.user_id = u.id
                WHERE c.id = ? ");
            $req->execute([
                $id
            ]);
            $comment = $req->fetch();
            return new Comments($comment);
        }
    }

    // Récupère une liste de commentaires
    public function getList($filter, $orderBy, $order, $nbLimit, $nbComments) {
        if (is_string($filter) && is_string($orderBy) && ($order == "asc" || $order == "desc") && ((int) $nbLimit >= 0) && ((int) $nbComments > 0)) {
            $req = $this->_db->prepare("SELECT c.id, c.post_id, c.user_id, u.login, c.user_name, c.content, c.status, c.report_date, c.nb_report, c.creation_date, c.update_date
                FROM comments c
                LEFT JOIN users u
                ON c.user_id = u.id
                WHERE $filter 
                ORDER BY $orderBy $order 
                LIMIT $nbLimit, $nbComments");
            $req->execute();
            while ($datas = $req->fetch()) {
                $comments[] = new Comments($datas);
            }
            if (isset($comments)) {
                return $comments;
            }
        }
    }

    // Met à jour un commentaire
    public function update(Comments $comment) {
        $req = $this->_db->prepare("UPDATE comments SET content = :newContent, status = :newStatus, update_date = NOW() WHERE id = :id");
        $req->execute([
            "id" => $comment->id(),
            "newContent" => $comment->content(),
            "newStatus" => $comment->status()
        ]);
        return "Le commentaire a été modifié.";
    }

    // Met à jour le statut d'un commentaire
    public function updateStatus(Comments $comment) {
        $req = $this->_db->prepare("UPDATE comments SET status = :newStatus WHERE id = :id ");
        $req->execute([
            "id" =>  $comment->id(),
            "newStatus" => $comment->status()
        ]);
    }

    // Signale un commentaire
    public function report(Comments $comment) {
        $req = $this->_db->prepare("UPDATE comments SET status = :newStatus, nb_report = nb_report + 1, report_date = NOW() WHERE id = :id");
        $req->execute([
            "id" =>  $comment->id(),
            "newStatus" => $comment->status()
        ]);
        return "Le commentaire a été signalé.";
    }

    // Supprime un commentaire
    public function delete(Comments $comment) {
        $req = $this->_db->prepare("DELETE FROM comments WHERE id = :id ");
        $req->execute([
            "id" =>  $comment->id(),
        ]);
        return "Le commentaire a été supprimé.";
    }

    // Compte le nombre de commentaires
    public function count($filter) {
        if (is_string($filter)) {
            $req = $this->_db->prepare("SELECT COUNT(*) FROM comments c WHERE $filter");
            $req->execute();
            $nbComments = $req->fetchColumn();
            return $nbComments;
        }
    }
}
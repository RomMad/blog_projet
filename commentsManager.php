<?php
class CommentsManager extends Manager {

    public function __construct() {
        $this->_db = $this->databaseConnection();
    }

    // Méthode d'ajout d'un commentaire
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
    // Méthode de lecture d'un commentaire
    public function get($id) {
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
    // Méthode de lecture d'un commentaire
    // public function lastCreate($user_id) {
    //     $req = $this->_db->prepare("SELECT c.id, c.user_ID, u.login, c.content, c.status, c.creation_date, c.update_date 
    //         FROM comments c 
    //         LEFT JOIN users u 
    //         ON c.user_ID = u.id 
    //         WHERE c.user_ID = ? 
    //         ORDER BY c.id DESC 
    //         LIMIT 0, 1");
    //     $req->execute([
    //         $user_id
    //     ]);
    //     $comment = $req->fetch();
    //     return new Comments($comment);
    // }
    // Méthode de récupération d'une liste d'commentaires
    public function getList($filter, $orderBy, $order, $minLimit, $maxLimit) {
        $req = $this->_db->prepare("SELECT c.id, c.post_id, c.user_id, u.login, c.user_name, c.content, c.status, c.report_date, c.nb_report, c.creation_date, c.update_date
            FROM comments c
            LEFT JOIN users u
            ON c.user_id = u.id
            WHERE $filter 
            ORDER BY $orderBy $order 
            LIMIT $minLimit, $maxLimit");
        $req->execute();
        while ($datas = $req->fetch()) {
            $comments[] = new Comments($datas);
        }
        return $comments;
    }
    // Méthode de mise à jour d'un commentaire
    public function update(Comments $comment) {
        $req = $this->_db->prepare("UPDATE comments SET content = :newContent, status = :newStatus, update_date = NOW() WHERE id = :id");
        $req->execute([
            "id" => $comment->id(),
            "newContent" => $comment->content(),
            "newStatus" => $comment->status()
        ]);
        return "Le commentaire a été modifié.";
    }
    // Méthode de mise à jour du statut d'un commentaire
    public function updateStatus($id, $status) {
        $req = $this->_db->prepare("UPDATE comments SET status = :newStatus WHERE id = :id ");
        $req->execute([
            "id" => $id,
            "newStatus" => $status
        ]);
    }
    // Méthode pour signaler un commentaire
    public function report(Comments $comment) {
        $req = $this->_db->prepare("UPDATE comments SET status = :newStatus, nb_report = nb_report + 1, report_date = NOW() WHERE id = :id");
        $req->execute([
            "id" =>  $comment->id(),
            "newStatus" => $comment->status()
        ]);
        return "Le commentaire a été signalé.";
    }
    // Méthode de suppresion d'un commentaire
    public function delete($id) {
        $req = $this->_db->prepare("DELETE FROM comments WHERE id = $id ");
        $req->execute();
        return "Le commentaire a été supprimé.";
    }
    // Méthode qui compte le nombre de commentaires
    public function count($filter) {
        $req = $this->_db->prepare("SELECT COUNT(*) FROM comments c WHERE $filter");
        $req->execute();
        $nbComments = $req->fetchColumn();
        return $nbComments;
    }
}
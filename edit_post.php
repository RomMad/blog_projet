<?php 
    session_start();

    include("connection_bdd.php");
    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION)) {
        header("Location: connection.php");
    };

    var_dump($_POST);    
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $title = htmlspecialchars($_POST["title"]);
        $content = htmlspecialchars($_POST["content"]);
        $post_ID = htmlspecialchars($_POST["post_ID"]);
        $user_ID = htmlspecialchars($_SESSION["user_ID"]);
        $user_login  = htmlspecialchars($_SESSION["user_login"]);
        $status = htmlspecialchars($_POST["status"]);
        $date_creation = htmlspecialchars($_POST["date_creation"]);
        $date_update = htmlspecialchars($_POST["date_update"]);

        // Ajoute l'article si nouvel article
        if (isset($_POST["save"]) && empty($_POST["post_ID"])) {
            $req = $bdd->prepare("INSERT INTO posts(user_ID, user_login, title, content, status) 
            VALUES(:user_ID, :user_login, :title, :content, :status)");
            $req->execute(array(
                "user_ID" => $user_ID,
                "user_login" => $user_login,
                "title" => $title,
                "content" => $content,
                "status" => $status
                ));
            $infoPost = "Article enregistré.";
            // Récupère l'article
            $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, u.login, p.content, p.status, DATE_FORMAT(p.date_creation, \"%d/%m/%Y %H:%i\") AS date_creation_fr, DATE_FORMAT(p.date_update, \"%d/%m/%Y %H:%i\") AS date_update_fr 
            FROM posts p
            LEFT JOIN users u
            ON p.user_ID = u.ID
            WHERE p.user_ID =?  
            ORDER BY p.ID DESC 
            LIMIT 0, 1");
            $req->execute(array($user_ID));
            $data = $req->fetch();
    
            $title = $data["title"];
            $content = $data["content"];
            $post_ID = $data["ID"];
            $post_user_ID  = $data["login"];
            $date_creation = $data["date_creation_fr"];
            $date_update = $data["date_update_fr"];
            $status = $data["status"];
        };

        // Met à jour l'article si article existant
        if (isset($_POST["save"]) && !empty($_POST["post_ID"])) {
            $req = $bdd->prepare("UPDATE posts SET title = :new_title, content = :new_content, status = :new_status, date_update = NOW() WHERE ID = :post_ID");
            $req->execute(array(
                "new_title" => $title,
                "new_content" => $content,
                "new_status" => $status,
                "post_ID" => $post_ID
                ));     
            $infoPost = "Article modifié.";
            date_default_timezone_set('Europe/Paris');
            $date_update = date("d/m/Y H:i");
        };

        // Supprime l'article
        if (isset($_POST["erase"]) && !empty($_POST["post_ID"])) {
            $req = $bdd->prepare("DELETE FROM posts WHERE ID = ? ");
            $req->execute(array($post_ID));
            $infoPost = "Article supprimé.";
            header("Refresh: 2; url=blog.php");
        };
    };

    // 
    var_dump($_GET);
    if (!empty($_GET["post"])) {
        $idPost = htmlspecialchars($_GET["post"]);
        // Récupère l'article
        $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, u.login, p.content, p.status, DATE_FORMAT(p.date_creation, \"%d/%m/%Y %H:%i\") AS date_creation_fr, DATE_FORMAT(p.date_update, \"%d/%m/%Y %H:%i\") AS date_update_fr 
        FROM posts p
        LEFT JOIN users u
        ON p.user_ID = u.ID
        WHERE p.ID=?");
        $req->execute(array($idPost));
        $data = $req->fetch();

        $title = $data["title"];
        $content = $data["content"];
        $post_ID = $data["ID"];
        $post_user_ID  = $data["login"];
        $date_creation = $data["date_creation_fr"];
        $date_update = $data["date_update_fr"];
        $status = $data["status"];
    };

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <section id="post_form" class="row">
            <div class="col-sm-12 col-md-10 mx-auto">
                <form action="edit_post.php" method="post" class="">
                    <h2 class="mb-4">Edition d'article</h2>
                    <?php  
                    if (isset($infoPost)) {
                    ?>
                    <div id="info-edit-post" class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $infoPost ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php 
                    };
                    ?>

                    <div class="row">
                        <div class="col-sm-12 col-md-8">
                            <div class="form-group">
                                <label for="title">Titre</label>
                                <input type="text" name="title" class="form-control" id="title" value="<?= isset($title) ? $title : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="content">Contenu</label>
                                <textarea name="content" class="form-control" id="content" rows="12"><?= isset($content) ? $content : "" ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6 offset-md-1 col-md-3">
                        <div class="form-group">
                                <label for="post_ID">ID</label>
                                <input type="text" name="post_ID" class="form-control" id="post_ID" readonly value="<?= isset($post_ID) ? $post_ID : "" ?>">
                        </div>
                            <div class="form-group">
                                <label for="post_user_ID">Auteur</label>
                                <input type="text" name="post_user_ID" class="form-control" id="post_user_ID" readonly value="<?= isset($post_user_ID) ? $post_user_ID : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="date_creation">Date de création</label>
                                <input type="text" name="date_creation" class="form-control" id="date_creation" readonly value="<?= isset($date_creation) ? $date_creation : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="date_update">Date de mise à jour</label>
                                <input type="text" name="date_update" class="form-control" id="date_update" readonly value="<?= isset($date_update) ? $date_update : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Statut</label>
                                <select name="status" class="form-control" id="status">
                                    <option <?php if (isset($status) && $status=="Publié") { ?> selected <?php } ?> >Publié</option>
                                    <option <?php if (isset($status) && $status=="Brouillon") { ?> selected <?php } ?> >Brouillon</option>
                                </select>
                            </div>
                            <div class="form-group float-right">
                                <input type="submit" id="save"  name="save" value="Enregistrer" class="btn btn-info mb-2 shadow">
                                <?php 
                                    if (isset($post_ID)) { 
                                ?>
                                <input type="submit" id="erase"  name="erase" alt="Supprimer l'article" class="btn btn-danger mb-2 shadow" 
                                value="Supprimer" onclick="if(window.confirm('Voulez-vous vraiment supprimer l\'article ?')){return true;}else{return false;}">
                                <?php 
                                    }; 
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</html
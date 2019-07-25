<?php 
    session_start();

    include("connection_bdd.php");
    // Redirige vers la page de connexion si l'utilisateur n'a pas les droits
    if (!isset($_SESSION["userRole"]) || $_SESSION["userRole"]>4) {
        header("Location: connection.php");
    };

    var_dump($_POST);    
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $title = htmlspecialchars($_POST["title"]);
        $content = htmlspecialchars($_POST["post_content"]);
        $post_ID = htmlspecialchars($_POST["post_ID"]);
        $user_ID = htmlspecialchars($_SESSION["userID"]);
        $user_login  = htmlspecialchars($_SESSION["userLogin"]);
        $status = htmlspecialchars($_POST["status"]);
        $creation_date = htmlspecialchars($_POST["creation_date"]);
        $update_date = htmlspecialchars($_POST["update_date"]);
        $typeAlert = "info";
        $validation = true;

        // Vérifie si le titre est vide
        if (empty($title)) {
            $msgPost = "Le titre de l'article est vide.";
            $typeAlert = "danger";
            $validation = false;
        };

        // Ajoute ou modifie l'article si le titre n'est pas vide
        if ($validation) {
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
                $msgPost = "L'article a été enregistré.";
            };

            // Met à jour l'article si article existant
            if (isset($_POST["save"]) && !empty($_POST["post_ID"])) {
                $req = $bdd->prepare("UPDATE posts SET title = :new_title, content = :new_content, status = :new_status, update_date = NOW() WHERE ID = :post_ID");
                $req->execute(array(
                    "new_title" => $title,
                    "new_content" => $content,
                    "new_status" => $status,
                    "post_ID" => $post_ID
                    ));     
                $msgPost = "L'article a été modifié.";
            };

            // Récupère l'article
            $req = $bdd->prepare("SELECT p.ID, p.user_ID, u.login, DATE_FORMAT(p.creation_date, \"%d/%m/%Y %H:%i\") AS creation_date_fr, DATE_FORMAT(p.update_date, \"%d/%m/%Y %H:%i\") AS update_date_fr 
            FROM posts p
            LEFT JOIN users u
            ON p.user_ID = u.ID
            WHERE p.user_ID =?  
            ORDER BY p.ID DESC 
            LIMIT 0, 1");
            $req->execute(array($user_ID));
            $dataPost = $req->fetch();
    
            $post_ID = htmlspecialchars($dataPost["ID"]);
            $post_user_ID  = htmlspecialchars($dataPost["login"]);
            $creation_date = htmlspecialchars($dataPost["creation_date_fr"]);
            $update_date = htmlspecialchars($dataPost["update_date_fr"]);
        };

        // Supprime l'article
        if (isset($_POST["erase"]) && !empty($_POST["post_ID"])) {
            $req = $bdd->prepare("DELETE FROM posts WHERE ID = ? ");
            $req->execute(array($post_ID));
            $msgPost = "L'article a été supprimé.";
            $typeAlert = "warning";
            header("Refresh: 2; url=blog.php");
        };

        $_SESSION["flash"] = array(
            "msg" => $msgPost,
            "type" =>  $typeAlert
        );

    };

    // 
    var_dump($_GET);
    if (!empty($_GET["post"])) {
        $idPost = htmlspecialchars($_GET["post"]);
        // Récupère l'article
        $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, u.login, p.content, p.status, DATE_FORMAT(p.creation_date, \"%d/%m/%Y %H:%i\") AS creation_date_fr, DATE_FORMAT(p.update_date, \"%d/%m/%Y %H:%i\") AS update_date_fr 
        FROM posts p
        LEFT JOIN users u
        ON p.user_ID = u.ID
        WHERE p.ID=?");
        $req->execute(array($idPost));
        $dataPost = $req->fetch();

        $title = $dataPost["title"];
        $content = $dataPost["content"];
        $post_ID = $dataPost["ID"];
        $post_user_ID  = $dataPost["login"];
        $creation_date = $dataPost["creation_date_fr"];
        $update_date = $dataPost["update_date_fr"];
        $status = $dataPost["status"];
    };

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <section id="post_form" class="row">
            <div class="col-sm-12 col-md-12 mx-auto">

                <form action="edit_post.php" method="post" class="">

                    <h2 class="mb-4">Edition d'article</h2>

                    <?php include("msg_session_flash.php") ?>

                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="title">Titre</label>
                                <input type="text" name="title" class="form-control shadow-sm" id="title" value="<?= isset($title) ? $title : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_content" class="d-none">Contenu</label>
                                <textarea name="post_content" class="form-control shadow-sm" id="post_content" rows="12"><?= isset($content) ? $content : "" ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">
                        <div class="form-group">
                                <label for="post_ID">ID</label>
                                <input type="text" name="post_ID" class="form-control shadow-sm" id="post_ID" readonly value="<?= isset($post_ID) ? $post_ID : "" ?>">
                        </div>
                            <div class="form-group">
                                <label for="post_user_ID">Auteur</label>
                                <input type="text" name="post_user_ID" class="form-control shadow-sm" id="post_user_ID" readonly value="<?= isset($post_user_ID) ? $post_user_ID : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="creation_date">Date de création</label>
                                <input type="text" name="creation_date" class="form-control shadow-sm" id="creation_date" readonly value="<?= isset($creation_date) ? $creation_date : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="update_date">Date de mise à jour</label>
                                <input type="text" name="update_date" class="form-control shadow-sm" id="update_date" readonly value="<?= isset($update_date) ? $update_date : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Statut</label>
                                <select name="status" class="form-control shadow-sm" id="status">
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

</body>

</html
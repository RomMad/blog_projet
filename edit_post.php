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
        $post_title = htmlspecialchars($_POST["post_title"]);
        $post_content = htmlspecialchars($_POST["post_content"]);
        $post_ID = htmlspecialchars($_POST["post_ID"]);
        $post_user_ID = htmlspecialchars($_SESSION["ID"]);
        $post_author  = htmlspecialchars($_POST["post_author"]);
        $post_status = htmlspecialchars($_POST["post_status"]);
        $post_date_creation = htmlspecialchars($_POST["post_date_creation"]);
        $post_date_update = htmlspecialchars($_POST["post_date_update"]);

        // Met à jour l'article si article existant
        if (!empty($_POST["post_ID"])) {
            $req = $bdd->prepare("UPDATE posts SET post_title = :new_post_title, post_content = :new_post_content, post_status = :new_post_status, post_date_update = NOW() WHERE ID = :post_ID");
            $req->execute(array(
                "new_post_title" => $post_title,
                "new_post_content" => $post_content,
                "new_post_status" => $post_status,
                "post_ID" => $post_ID
                ));     
            $infoPost = "Article modifié.";
        } else {
            // Ajoute l'article si nouvel article
            $req = $bdd->prepare("INSERT INTO posts(post_author, post_title, post_content, post_status) 
            VALUES(:post_author, :post_title, :post_content, :post_status)");
            $req->execute(array(
                "post_author" => $post_user_ID,
                "post_title" => $post_title,
                "post_content" => $post_content,
                "post_status" => $post_status
                ));
            $infoPost = "Article enregistré.";
            // Récupère l'article
            $req = $bdd->prepare("SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, p.post_status, DATE_FORMAT(p.post_date_creation, \"%d/%m/%Y %H:%i\") AS post_date_creation_fr, DATE_FORMAT(p.post_date_update, \"%d/%m/%Y %H:%i\") AS post_date_update_fr 
            FROM posts p
            LEFT JOIN users u
            ON p.post_author = u.ID
            WHERE p.post_author =?  
            ORDER BY p.ID DESC 
            LIMIT 0, 1");
            $req->execute(array($post_user_ID));
            $data = $req->fetch();
    
            $post_title = $data["post_title"];
            $post_content = $data["post_content"];
            $post_ID = $data["ID"];
            $post_author  = $data["user_login"];
            $post_date_creation = $data["post_date_creation_fr"];
            $post_date_update = $data["post_date_update_fr"];
            $post_status = $data["post_status"];
        };
    };
    // 
    var_dump($_GET);
    if (!empty($_GET["post"])) {
        $idPost = htmlspecialchars($_GET["post"]);
        // Récupère l'article
        $req = $bdd->prepare("SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, p.post_status, DATE_FORMAT(p.post_date_creation, \"%d/%m/%Y %H:%i\") AS post_date_creation_fr, DATE_FORMAT(p.post_date_update, \"%d/%m/%Y %H:%i\") AS post_date_update_fr 
        FROM posts p
        LEFT JOIN users u
        ON p.post_author = u.ID
        WHERE p.ID=?");
        $req->execute(array($idPost));
        $data = $req->fetch();

        $post_title = $data["post_title"];
        $post_content = $data["post_content"];
        $post_ID = $data["ID"];
        $post_author  = $data["user_login"];
        $post_date_creation = $data["post_date_creation_fr"];
        $post_date_update = $data["post_date_update_fr"];
        $post_status = $data["post_status"];
    };

?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

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
                                <label for="post_title">Titre</label>
                                <input type="text" name="post_title" class="form-control" id="post_title" value="<?= isset($post_title) ? $post_title : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_content">Contenu</label>
                                <textarea name="post_content" class="form-control" id="post_content" rows="12"><?= isset($post_content) ? $post_content : "" ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6 offset-md-1 col-md-3">
                        <div class="form-group">
                                <label for="post_ID">ID</label>
                                <input type="text" name="post_ID" class="form-control" id="post_ID" readonly value="<?= isset($post_ID) ? $post_ID : "" ?>">
                        </div>
                            <div class="form-group">
                                <label for="post_author">Auteur</label>
                                <input type="text" name="post_author" class="form-control" id="post_author" readonly value="<?= isset($post_author) ? $post_author : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_date_creation">Date de création</label>
                                <input type="text" name="post_date_creation" class="form-control" id="post_date_creation" readonly value="<?= isset($post_date_creation) ? $post_date_creation : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_date_update">Date de mise à jour</label>
                                <input type="text" name="post_date_update" class="form-control" id="post_date_update" readonly value="<?= isset($post_date_update) ? $post_date_update : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_status">Statut</label>
                                <select name="post_status" class="form-control" id="post_status">
                                    <option <?php if (isset($post_status) && $post_status=="Publié") { ?> selected <?php } ?> >Publié</option>
                                    <option <?php if (isset($post_status) && $post_status=="Brouillon") { ?> selected <?php } ?> >Brouillon</option>
                                </select>
                            </div>
                            <div class="form-group float-right">
                                <input type="submit" value="Enregistrer" id="save" class="btn btn-info shadow">
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
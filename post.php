<?php 
    session_start(); 

    include("connection_bdd.php");

    var_dump($_POST);    
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        if (isset($_SESSION["ID"])) {
            $user_ID = $_SESSION["ID"];
        } else {
            $user_ID = 0;
        };

        $comment_content = htmlspecialchars($_POST["comment_content"]);
            // Ajoute le commentaire
            $req = $bdd->prepare("INSERT INTO comments(id_post, comment_user_ID, comment_content) 
            VALUES(:id_post, :comment_user_ID, :comment_content)");
            $req->execute(array(
                "id_post" => $_SESSION["post_ID"],
                "comment_user_ID" =>  $user_ID,
                "comment_content" => $_POST["comment_content"]
                ));
    };

    var_dump($_GET);
    if (!empty($_GET)) {
        $post_ID = htmlspecialchars($_GET["post"]);
        $_SESSION["post_ID"] = $post_ID;
    } else {
        $post_ID = $_SESSION["post_ID"];
    };

    if (isset($_GET["comment"]) && isset($_GET["action"]) && $_GET["action"]="erase") {
        $comment_ID = htmlspecialchars($_GET["comment"]);
        $req = $bdd->query("DELETE FROM comments WHERE ID ='$comment_ID'");
    };

    // Récupère le post
    $req = $bdd->prepare("SELECT p.ID, p.post_title, p.post_user_ID, u.user_login, p.post_content, 
    DATE_FORMAT(p.post_date_creation, \"%d/%m/%Y à %H:%i\") AS post_date_creation_fr, 
    DATE_FORMAT(p.post_date_update, \"%d/%m/%Y à %H:%i\") AS post_date_update_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.post_user_ID = u.ID
    WHERE p.ID=?");
    $req->execute(array($post_ID));
    $data = $req->fetch();

    // Compte le nombre commentaires
    $req = $bdd->query("SELECT COUNT(*) as nbID FROM comments WHERE id_post ='$post_ID' AND comment_status <2");
    $count = $req->fetch();
    if ($count["nbID"]==0) {
        $infoComments = "Aucun commentaire.";
    } else  {
        // Récupère les commentaires
        $req = $bdd->prepare("SELECT c.ID, c.comment_user_ID, u.user_login, c.comment_content, c.comment_status, 
        DATE_FORMAT(c.comment_date_creation, \"%d/%m/%Y à %H:%i\") AS comment_date_creation_fr 
        FROM comments c
        LEFT JOIN users u
        ON c.comment_user_ID = u.ID
        WHERE c.id_post = :post_ID AND c.comment_status < :comment_status 
        ORDER BY c.comment_date_creation DESC
        LIMIT 0, 10");
        $req->execute(array(
            "post_ID" => $post_ID,
            "comment_status" => 2
        ));
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">
        <section id="post">
            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h1><?= htmlspecialchars($data["post_title"]) ?></h1>
                    <em>Créé le <?= $data["post_date_creation_fr"] ?> par <a class="text-info" href=""> <?= htmlspecialchars($data["user_login"]) ?> </a> et modifié le <?= $data["post_date_update_fr"] ?></em>
                    <?php
                    if (isset($_SESSION["ID"]) && $_SESSION["ID"]==$data["post_user_ID"]) { ?>
                        <a class="text-info a-edit-post" href="edit_post.php?post=<?= $data["ID"] ?>"><span class="far fa-edit"> Modifier</a>
                    <?php }; ?>
                </div>
                <div class="card-body text-body">
                <?= nl2br(htmlspecialchars($data["post_content"])) ?>
                </div>
            </div>
            <?php 
            if (isset($_SESSION["ID"]) && $_SESSION["ID"]==$data["post_user_ID"]) { ?>
                <a class="text-info" href="edit_post.php?post=<?= $post_ID ?>"><span class="far fa-edit"> Modifier l'article<a> <?php 
            }; ?>
            <!-- Formuulaire d'ajout d'un commentaire -->
            <div class="row">
                <form action="post.php" method="post" class="col-sm-12 col-md-6 mt-4">
                    <h2 class="h3">Nouveau commentaire</h2>
                    <div class="form-group">
                        <label for="comment_content"></label>
                        <textarea name="comment_content" class="form-control" id="comment_content" rows="4"></textarea>
                    </div>
                    <div class="form-group float-right">
                        <input type="submit" value="Envoyer" id="save" class="btn btn-info shadow">
                    </div>
                </form>
            </div>
            <!-- Affiche les commentaires -->
            <div class="row">
                <div class="col-sm-12 col-md-6 mt-2">
                    <h2 class="h3 mb-4">Commentaires</h2>
                    <p> <?= isset($infoComments) ? $infoComments : "" ?> </p>
                    <?php 
                        while ($data = $req->fetch()) {
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <?php 
                                        if (!empty($data["user_login"])) {
                                            $user_login = htmlspecialchars($data["user_login"]);
                                            } else {
                                            $user_login = "Anonyme";
                                            };
                                    ?>
                                    <p><strong><?= $user_login ?></strong>, le <?= $data["comment_date_creation_fr"] ?></p>
                                    <p><?= nl2br(htmlspecialchars($data["comment_content"])) ?></p>
                                    <?php                        
                                        if (isset($_SESSION["ID"]) && $_SESSION["ID"]==$data["comment_user_ID"]) { ?>
                                            <div>
                                                <a href="post.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?= $data["ID"] ?>&action=erase" onclick="if(window.confirm('Voulez-vous vraiment supprimer le commentaire ?', 'Demande de confirmation')){return true;}else{return false;}"><span class="fas fa-times text-danger"></span></a>
                                            </div>
                                        <?php
                                        };
                                        ?>
                                </div>
                            </div>
                        <?php
                        }
                    ?>
                </div>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</html
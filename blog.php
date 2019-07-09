<?php 
    session_start();

    var_dump($_SESSION);  

    include("connection_bdd.php"); 
    // Récupère les derniers posts
    $req = $bdd->prepare("SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, p.post_status, DATE_FORMAT(p.post_date_creation, \"%d/%m/%Y à %H:%i\") AS post_date_creation_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.post_author = u.ID
    WHERE p.post_status = ? || p.post_status = ? 
    ORDER BY p.post_date_creation DESC 
    LIMIT 0, 5");
    $req->execute(array("Publié", "Brouillon"));

?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="blog">

            <?php
            while ($data = $req->fetch())
            {
            ?>
            <div class="card">
                <div class="card-header bg-dark text-light">
                    <a class="text-info" href="post.php?post=<?= $data["ID"] ?>"><h3>
                        <?= htmlspecialchars($data["post_title"]) ?>
                    </h3></a>
                    <em>Créé le <?= $data["post_date_creation_fr"] ?> par <a class="text-info" href=""> <?= htmlspecialchars($data["user_login"]) ?> </a></em>
                    <?php 
                    if (isset($_SESSION["ID"]) && $_SESSION["ID"]==$data["post_author"]) { ?>
                        <a class="text-info a-edit-post" href="edit_post.php?post=<?= $data["ID"] ?>"><span class="far fa-edit"></span> Modifier</a>
                    <?php }; ?>
                </div>
                <div class="card-body text-body">
                    <?= nl2br(htmlspecialchars($data["post_content"])) ?>
                    <div class="mt-4">
                        <em><a class="text-info mt-4" href="post.php?post=<?= $data["ID"] ?>"><span class="fas fa-ellipsis-h"></span> En voir plus</a></em>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>  
            <div class="mt-4">
                <a class="text-info" href="edit_post.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article<a>
            </div>
        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
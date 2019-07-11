<?php 
    session_start();

    var_dump($_SESSION);  

    require("connection_bdd.php"); 
    // Récupère les derniers posts
    $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, p.user_login, u.login, p.content, p.status, DATE_FORMAT(p.date_creation, \"%d/%m/%Y à %H:%i\") AS date_creation_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.user_ID = u.ID
    WHERE p.status = ? || p.status = ? 
    ORDER BY p.date_creation DESC 
    LIMIT 0, 5");
    $req->execute(array("Publié", "Brouillon"));

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <section id="blog">

            <?php
                while ($data = $req->fetch()) {
                    $post_ID = htmlspecialchars($data["ID"]);
                    $title = htmlspecialchars($data["title"]);
                    $user_ID = htmlspecialchars($data["user_ID"]);
                    $user_login = htmlspecialchars($data["user_login"]);
                    $login = htmlspecialchars($data["login"]);
                    $content = $data["content"];
                    $date_creation_fr = htmlspecialchars($data["date_creation_fr"]);
            ?>
            
            <div class="card">
                <div class="card-header bg-dark text-light">
                    <a class="text-info" href="post.php?post=<?= $post_ID ?>"><h3>
                        <?= $title ?>
                    </h3></a>
                    <em>Créé le <?= $date_creation_fr ?> par <a class="text-info" href=""> <?= !empty($user_login) ? $user_login : $user_login ?> </a></em>
                    <?php 
                    if (isset($_SESSION["user_ID"]) && $_SESSION["user_ID"]==$user_ID) { ?>
                        <a class="text-info a-edit-post" href="edit_post.php?post=<?= $post_ID ?>"><span class="far fa-edit"></span> Modifier</a>
                    <?php }; ?>
                </div>
                <div class="card-body text-body">
                    <?= $content ?>
                    <div class="mt-4">
                        <em><a class="text-info mt-4" href="post.php?post=<?= $post_ID ?>"><span class="fas fa-ellipsis-h"></span> En voir plus</a></em>
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

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</html
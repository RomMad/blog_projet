<?php 
    session_start();

    include("connection_bdd.php"); 
    // Récupère les derniers posts
    $req = $bdd->query('SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, DATE_FORMAT(p.post_date_creation, \'%d/%m/%Y à %H:%i\') AS post_date_creation_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.post_author = u.ID
    ORDER BY p.post_date_creation DESC 
    LIMIT 0, 5');

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
                    <a href="post.php?post=<?= $data['ID'] ?>"><h3>
                        <?= htmlspecialchars($data['post_title']) ?>
                    </h3></a>
                    <em>Créé le <?= $data['post_date_creation_fr'] ?> par <a href=""> <?= htmlspecialchars($data['user_login']) ?> </a></em>
                </div>
                <div class="card-body text-body">
                <?= nl2br(htmlspecialchars($data['post_content'])) ?>
                <br /><br />
                <em><a href="post.php?post=<?= $data['ID'] ?>">En voir plus</a></em>
                </div>
            </div>
            <?php
            }
            ?>  
            <br />
            <a href="edit_post.php?type=1">Rédiger un nouvel article<a>

        </section>

    </div>
<
    <?php include("scripts.html"); ?>

</html
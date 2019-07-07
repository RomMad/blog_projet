<?php session_start(); 

include("connection_bdd.php");

var_dump($_GET);

$post = htmlspecialchars($_GET['post']);

// Récupère le post
$req = $bdd->prepare('SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, DATE_FORMAT(p.post_date_creation, \'%d/%m/%Y à %H:%i\') AS post_date_creation_fr 
FROM posts p
LEFT JOIN users u
ON p.post_author = u.ID
WHERE p.ID=?');
$req->execute(array($post));
$data = $req->fetch();

// Récupération des commentaires
$req = $bdd->prepare('SELECT u.user_login, c.comment_content, DATE_FORMAT(c.comment_date_creation, \'%d/%m/%Y à %H:%i\') AS date_comment_fr 
FROM comments c
LEFT JOIN users u
ON c.comment_author = u.ID
WHERE c.id_post=?
ORDER BY c.comment_date_creation');
$req->execute(array($post));

?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="blog" class="">

            <div class="card">
                <div class="card-header bg-dark text-light">
                    <a href="post_page.php?post=<?= $data['ID'] ?>"><h3>
                        <?= htmlspecialchars($data['post_title']) ?>
                    </h3></a>
                    <em>Créé le <?= $data['post_date_creation_fr'] ?> par <a href=""> <?= htmlspecialchars($data['user_login']) ?> </a></em>
                </div>
                <div class="card-body text-body">
                <?= nl2br(htmlspecialchars($data['post_content'])) ?>
                </div>
            </div>

            <h2>Commentaires</h2>

            <?php
            while ($data = $req->fetch())
            {
            ?>
            <p><strong><?= htmlspecialchars($data['user_login']) ?></strong>, le <?= $data['date_comment_fr'] ?></p>
            <p><?= nl2br(htmlspecialchars($data['comment_content'])) ?></p>
            <?php
            }
            ?>

            <br />
            <a href="edit_post.php?post=<?= $post ?>">Modifier l'article<a>
            <br />
            <a href="edit_post.php?edit=0">Rédiger un nouvel article<a>

        </section>

    </div>
<
    <?php include("scripts.html"); ?>

</html
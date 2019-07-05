<?php session_start(); 

include("connection_bdd.php");

var_dump($_GET);

// Récupère les derniers posts
$req = $bdd->prepare('SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, DATE_FORMAT(p.post_date_creation, \'%d/%m/%Y à %Hh%imn\') AS post_date_creation_fr 
FROM posts p
LEFT JOIN users u
ON p.post_author = u.ID
WHERE p.ID=?');
$req->execute(array($_GET['post']));
$data = $req->fetch();

// Récupération des commentaires
$req = $bdd->prepare('SELECT u.user_login, c.comment_content, DATE_FORMAT(c.comment_date_creation, \'%d/%m/%Y à %Hh%imin%ss\') AS date_comment_fr 
FROM comments c
LEFT JOIN users u
ON c.comment_author = u.ID
WHERE c.id_post=?
ORDER BY c.comment_date_creation');
$req->execute(array($_GET['post']));

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
                    <a href="post_page.php?post=<?php echo $data['ID']; ?>"><h3>
                        <?php echo htmlspecialchars($data['post_title']); ?>
                    </h3></a>
                    <em>Créé le <?php echo $data['post_date_creation_fr']; ?> par <a href=""> <?php echo htmlspecialchars($data['user_login']); ?> </a></em>
                </div>
                <div class="card-body text-body">
                <?php
                // Affiche le contenu du post
                echo nl2br(htmlspecialchars($data['post_content']));
                ?>
                </div>
            </div>

            <h2>Commentaires</h2>

            <?php
            while ($data = $req->fetch())
            {
            ?>
            <p><strong><?php echo htmlspecialchars($data['user_login']); ?></strong> le <?php echo $data['date_comment_fr']; ?></p>
            <p><?php echo nl2br(htmlspecialchars($data['comment_content'])); ?></p>
            <?php
            }
            ?>

            <br />
            <a href="new_post_page">Rédiger un nouvel article<a>

        </section>

    </div>
<
    <?php include("scripts.html"); ?>

</html
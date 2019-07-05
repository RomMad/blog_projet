<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="blog" class="">

        <?php include("connection_bdd.php"); ?>

            <?php
            // Récupère les derniers posts
            $req = $bdd->prepare('SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, DATE_FORMAT(p.post_date_creation, \'%d/%m/%Y à %Hh%imn\') AS post_date_creation_fr 
            FROM posts p
            LEFT JOIN users u
            ON p.post_author = u.ID
            WHERE p.ID=?');
            $req->execute(array($_GET['post']));
            $data = $req->fetch();
            ?>
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
            <?php
            ?>  
            <br />
            <a href="new_post_page">Rédiger un nouvel article<a>

        </section>

    </div>
<
    <?php include("scripts.html"); ?>

</html
<?php session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="inscription" class="row">

        <?php include("connection_bdd.php"); ?>

            <?php
            // Récupère les derniers posts
            $req = $bdd->query('SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, DATE_FORMAT(p.post_date_creation, \'%d/%m/%Y à %Hh%imn\') AS post_date_creation_fr 
            FROM posts p
            LEFT JOIN users u
            ON p.post_author = u.ID
            ORDER BY p.post_date_creation 
            DESC LIMIT 0, 10');

            while ($data = $req->fetch())
            {
            ?>
            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h3>
                        <?php echo htmlspecialchars($data['post_title']); ?>
                    </h3>
                    <em>Le <?php echo $data['post_date_creation_fr']; ?> par <a href=""> <?php echo $data['user_login']; ?> </a></em>
                </div>
                <div class="card-body text-body">
                <?php
                // Affiche le contenu du post
                echo nl2br(htmlspecialchars($data['post_content']));
                ?>
                </div>
            </div>
            <?php
            } // Fin de la boucle des posts
            $req->closeCursor();
            ?>  
        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
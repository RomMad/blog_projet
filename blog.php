<?php 
    session_start();

    var_dump($_SESSION);  

    require("connection_bdd.php"); 
    // Compte le nombre d'articles
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_Posts FROM posts WHERE status = ? || status = ? ");
    $req->execute(array("Publié", "Brouillon"));
    $nbPosts = $req->fetch();

    var_dump($_POST);
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $_SESSION["nbDisplayedPosts"] = htmlspecialchars($_POST["nbDisplayed"]);
    };
    if (!isset($_SESSION["nbDisplayedPosts"])) {
        $_SESSION["nbDisplayedPosts"] = 5;
    };
    $nbDisplayed = $_SESSION["nbDisplayedPosts"];

    var_dump($_GET);  
    // Vérification si informations dans variable GET
    if (!empty($_GET["page"])) {
        $page = htmlspecialchars($_GET["page"]);
        // Calcul le nombre de pages par rapport aux nombre d'articles
        $maxPost =  $page*$nbDisplayed;
        $minPost = $maxPost-$nbDisplayed;
    } else  {
        $page = 1;
        $minPost = 0;
        $maxPost = $nbDisplayed;
    };

    // Initialisation des variables pour la pagination
    $linkNbDisplayed= "blog.php#blog";
    $linkPagination= "blog.php?";
    $anchorPagination= "#blog";
    $nbPages = ceil($nbPosts["nb_Posts"] / $nbDisplayed);
    require("pagination.php");

    // Récupère les derniers articles
    $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, p.user_login, u.login, p.content, p.status, DATE_FORMAT(p.creation_date, \"%d/%m/%Y à %H:%i\") AS creation_date_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.user_ID = u.ID
    WHERE p.status = :status1 || p.status = :status2 
    ORDER BY p.creation_date DESC 
    LIMIT  $minPost, $maxPost");
    $req->execute(array(
        "status1" => "Publié", 
        "status2" => "Brouillon"
    ));

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <section id="blog">
            <?php 
            // Vérifie si l'utilisateur a les droits pour écrire un article
            if (isset($_SESSION["userRole"]) && $_SESSION["userRole"]<5) {
            ?> 
                <div class="mt-4 mb-4">
                    <a class="text-info" href="edit_post.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article</a>
                </div>
            <?php
            };
            ?>
            <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

            <?php
                while ($dataPosts = $req->fetch()) {
                    $post_ID = htmlspecialchars($dataPosts["ID"]);
                    $title = htmlspecialchars($dataPosts["title"]);
                    $user_ID = htmlspecialchars($dataPosts["user_ID"]);
                    $user_login = htmlspecialchars($dataPosts["user_login"]);
                    $login = htmlspecialchars($dataPosts["login"]);
                    $content = html_entity_decode($dataPosts["content"]);
                    $creation_date_fr = htmlspecialchars($dataPosts["creation_date_fr"]);
            ?>
            
            <div class="card shadow">
                <div class="card-header bg-dark text-light">
                    <a class="text-info" href="post.php?post=<?= $post_ID ?>">
                        <h3 class="mt-1"><?= $title ?></h3>
                    </a>
                    <em>Créé le <?= $creation_date_fr ?> par <a class="text-info" href=""> <?= !empty($user_login) ? $user_login : $user_login ?> </a></em>
                    <?php 
                    if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$user_ID) { ?>
                        <a class="text-info a-edit-post" href="edit_post.php?post=<?= $post_ID ?>"><span class="far fa-edit"></span> Modifier</a>
                    <?php }; ?>
                </div>
                <div class="card-body text-body">
                    <div class="post_content">
                        <?= $content ?>
                    </div>
                    <div class="">
                        <a href="post.php?post=<?= $post_ID ?>" class="btn btn-outline-info">Continuer la lecture <span class="fas fa-angle-right"></span></a>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>  

            <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

            <div class="mt-4 mb-4">
                <a class="text-info" href="edit_post.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article</a>
            </div>

            </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$postsManager = new Postsmanager();

// Si recherche, filtre les résultats
$filter = "status = 'Publié'";
if (!empty($_GET["search"])) {
    $filter = $filter . " AND title like '%" . htmlspecialchars($_GET["search"]) . "%' OR content like '%" . htmlspecialchars($_GET["search"]) . "%'";
}
// Récupère le nombre d'articles
$nbItems = $postsManager->count($filter);

// Initialisation des variables pour la pagination
$typeItem = "posts";
$linkNbDisplayed = "blog.php#blog";
$linkPagination = "blog.php?";
$anchorPagination = "#blog";
require("pagination.php");
// $pagination = new Pagination($nbItems, $nbDisplayed, "blog.php#blog", "blog.php?", "#blog");

// Récupère les derniers articles
$posts = $postsManager->getList($filter, "p.creation_date", "DESC", $minLimit, $maxLimit);

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <section id="blog" class="row">

            <div class="col-md-12">

            <?php 
            // Vérifie si l'utilisateur a les droits pour écrire un article
            if (isset($_SESSION["userRole"]) && $_SESSION["userRole"]<5) {
            ?> 
                <div class="mt-4 mb-4">
                    <a class="text-blue" href="post_edit.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article</a>
                </div>
            <?php
            }
            // Affiche les résultats si recherche
            if (!empty($_GET["search"])) {
                echo "<p> " . $nbItems . " résultat(s).</p>";
            }    

            $session->flash(); // Message en session flash

            include("nav_pagination.php"); // Ajoute la barre de pagination

            ?>
            <div class="row">

                <?php
                if ($nbItems) {
                    foreach ($posts as $post) {
                    ?>
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header bg-dark text-light">
                                <a class="text-blue" href="post_view.php?post=<?= $post->id() ?>">
                                    <h3 class="mt-1"><?= $post->title() ?></h3>
                                </a>
                                <em>Créé le <?= str_replace(' ', ' à ', $post->creation_date()) ?> par <a class="text-blue" href=""><?= $post->user_login() ?></a></em>
                                <?php if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$post->user_id()) { ?>
                                    <a class="text-blue a-edit-post" href="post_edit.php?post=<?= $post->id() ?>"><span class="far fa-edit"></span> Modifier</a>
                                <?php } ?>
                            </div>
                            <div class="card-body text-body">
                                <div class="post_content"><?= nl2br(strip_tags(htmlspecialchars_decode($post->content()))) ?>
                                <?php if (strlen($post->content()) > 1200) { ?> <!-- Si le contenu est > à 1200 caractères, affiche le bouton 'Continuer la lecture' et ajoute un effet fade out -->
                                    <span class="post-fade-out"></span>
                                </div>
                                <div>
                                    <a href="post_view.php?post=<?= $post->id() ?>" class="btn btn-outline-blue mt-2">Continuer la lecture 
                                        <span class="fas fa-angle-right"></span>
                                    </a>
                                <?php
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    }
                }
                ?>

            </div>

            <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

            <div class="mt-4 mb-4">
                <a class="text-blue" href="post_edit.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article</a>
            </div>

            </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
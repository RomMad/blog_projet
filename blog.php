<?php 
// ini_set("display_errors",1);
// error_reporting(E_ALL);	

function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

session_start();

require("connection_db.php");

$manager = new Postsmanager($db);

// Si recherche, filtre les résultats
if (!empty($_GET["search"])) {
    $filter = "AND title like \'%" . htmlspecialchars($_GET["search"]) . "%' OR content like '%" . htmlspecialchars($_GET["search"]) . "%'";
} else {
    $filter = "";
}
// Compte le nombre d'articles
$nbItems = $manager->count($filter);

// Vérification si informations dans variable POST
if (!empty($_POST)) {
    $nbDisplayed =  htmlspecialchars($_POST["nbDisplayed"]);
    setcookie("pagination[nbDisplayedPosts]", $nbDisplayed, time() + 365*24*3600, null, null, false, false);
} else if (!empty($_COOKIE["pagination"]["nbDisplayedPosts"])) {
    $nbDisplayed = $_COOKIE["pagination"]["nbDisplayedPosts"];
} else {
    $nbDisplayed = 10;
}
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
}

// Initialisation des variables pour la pagination
$linkNbDisplayed= "blog.php#blog";
$linkPagination= "blog.php?";
$anchorPagination= "#blog";
$nbPages = ceil($nbItems / $nbDisplayed);
require("pagination.php");

// Récupère les derniers articles
$dataPosts = $manager->getList($filter, $minPost, $maxPost);

// var_dump($_COOKIE);
// var_dump($_POST);
// var_dump($_GET);

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
                    <a class="text-blue" href="edit_post.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article</a>
                </div>
            <?php
            }
            // Affiche les résultats si recherche
            if (!empty($_GET["search"])) {                
                echo "<p> " . $nbItems . " résultat(s).</p>";
            }    
            ?>

            <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

            <div class="row">

                <?php
                if ($nbItems) {
                    foreach ($dataPosts as $dataPost) {
                    ?>
                    <div class="col-md-12">
                        <div class="card shadow">
                            <div class="card-header bg-dark text-light">
                                <a class="text-blue" href="post_view.php?post=<?= $dataPost->id() ?>">
                                    <h3 class="mt-1"><?= $dataPost->title() ?></h3>
                                </a>
                                <em>Créé le <?= $dataPost->creation_date() ?> par <a class="text-blue" href=""><?=  $dataPost->user_login() ?></a></em>
                                <?php 
                                if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataPost->user_id()) { ?>
                                    <a class="text-blue a-edit-post" href="edit_post.php?post=<?= $dataPost->id() ?>"><span class="far fa-edit"></span> Modifier</a>
                                <?php 
                                } 
                                ?>
                            </div>
                            <div class="card-body text-body">
                                <div class="post_content"><?= strip_tags(htmlspecialchars_decode($dataPost->content())) ?></div>
                                    <div>
                                        <a href="post_view.php?post=<?= $dataPost->id() ?>" class="btn btn-outline-blue">Continuer la lecture 
                                            <span class="fas fa-angle-right"></span>
                                        </a>
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
                <a class="text-blue" href="edit_post.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article</a>
            </div>

            </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
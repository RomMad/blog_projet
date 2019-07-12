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
        $_SESSION["nbDisplayedPosts"] = htmlspecialchars($_POST["nbDisplayedPosts"]);
    };
    if (!isset($_SESSION["nbDisplayedPosts"])) {
        $_SESSION["nbDisplayedPosts"] = 5;
    };
    $nbDisplayedPosts = $_SESSION["nbDisplayedPosts"];

    var_dump($_GET);  
    // Vérification si informations dans variable GET
    if (!empty($_GET["page"])) {
        $page = htmlspecialchars($_GET["page"]);
        // Calcul le nombre de pages par rapport aux nombre d'articles
        $maxPost =  $page*$nbDisplayedPosts;
        $minPost = $maxPost-$nbDisplayedPosts;
    } else  {
        $page = 1;
        $minPost = 0;
        $maxPost = $nbDisplayedPosts;
    };

    $link= "blog.php";
    $ancre= "";
    $nbPages = ceil($nbPosts["nb_Posts"] / $nbDisplayedPosts);
    $pageLink_1 = $page-1;
    $pageLink_2 = $page;
    $pageLink_3 = $page+1;
    $activepageLink_1 = "";
    $activepageLink_2 = "active";
    $activepageLink_3 = "";


    if ($page<$nbPages) {
        $nextPage = $page+1;
        $nextPageLink = "";
        $nextPageColorLink = "text-info";
    } else {
        $nextPage = $page;
        $nextPageLink = "disabled";
        $nextPageColorLink = "";
        $pageLink_1 = $page-2;
        $pageLink_2 = $page-1;
        $pageLink_3 = $page;
        $activepageLink_1 = "";
        $activepageLink_2 = "";
        $activepageLink_3 = "active disabled";
    };
    if ($page==1) {
     $prevPage = 1;
     $prevPageLink = "disabled";
     $prevPageColorLink = "";
     $pageLink_1 = $page;
     $pageLink_2 = $page+1;
     $pageLink_3 = $page+2;    
     $activepageLink_1 = "active disabled";
     $activepageLink_2 = "";
     $activepageLink_3 = ""; 
     };
    if ($page>1) {
        $pageLink_1 = $page-1;
        $pageLink_2 = $page;
        $pageLink_3 = $page+1;
        $prevPage = $page-1;
        $prevPageLink = "";
        $prevPageColorLink = "text-info";
    };
  
    if ($nbPages==2 && $page==2) {
         $nextPage = $page;
         $nextPageLink = "disabled";
         $nextPageColorLink = "";
         $pageLink_1 = $page-1;
         $pageLink_2 = $page;
         $activepageLink_1 = "";
         $activepageLink_2 = "active disabled";
    };
    if ($page==$nbPages && $page!=2) {
         $nextPage = $page;
         $nextPageLink = "disabled";
         $nextPageColorLink = "";
         $pageLink_1 = $page-2;
         $pageLink_2 = $page-1;
         $pageLink_3 = $page;
         $activepageLink_1 = "";
         $activepageLink_2 = "";
         $activepageLink_3 = "active disabled";
    };  

    // Récupère les derniers articles
    $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, p.user_login, u.login, p.content, p.status, DATE_FORMAT(p.date_creation, \"%d/%m/%Y à %H:%i\") AS date_creation_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.user_ID = u.ID
    WHERE p.status = :status1 || p.status = :status2 
    ORDER BY p.date_creation DESC 
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

            <div class="row">
                <div class="col-md-6">
                    <form action="blog.php" method="post" class="form-inline">
                        <label class="mr-2" for="nbDisplayedPosts">Nb d'articles affichés</label>
                        <select name="nbDisplayedPosts" id="nbDisplayedPosts" class="custom-select mr-sm-2" >
                            <option value="5" <?= $nbDisplayedPosts==5 ? "selected" : "" ?> >5</option>
                            <option value="10" <?= $nbDisplayedPosts==10 ? "selected" : "" ?> >10</option>
                            <option value="15" <?= $nbDisplayedPosts==15 ? "selected" : "" ?> >15</option>
                            <option value="20" <?= $nbDisplayedPosts==20 ? "selected" : "" ?> >20</option>
                        </select>
                        <button type="submit" class="btn btn-info">OK</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->
                </div>
            </div>

            <?php
                while ($data = $req->fetch()) {
                    $post_ID = htmlspecialchars($data["ID"]);
                    $title = htmlspecialchars($data["title"]);
                    $user_ID = htmlspecialchars($data["user_ID"]);
                    $user_login = htmlspecialchars($data["user_login"]);
                    $login = htmlspecialchars($data["login"]);
                    $content = html_entity_decode($data["content"]);
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
            <div class="mt-4">
                <a class="text-info" href="edit_post.php?type=1"><span class="far fa-file"></span> Rédiger un nouvel article<a>
            </div>

            <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

            </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</html
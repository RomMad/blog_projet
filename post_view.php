<?php 

function loadClass($classname) 
{
    require $classname . ".php";
}

spl_autoload_register("loadClass");

session_start();

$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->db();

$postManager = new Postsmanager($db);

if (!empty($_GET["post"])) {
    $post_ID = htmlspecialchars($_GET["post"]);
    $_SESSION["postID"] = $post_ID;
} else {
    $post_ID = $_SESSION["postID"];
}

// Récupère les paramètres de modération
$req = $db->prepare("SELECT moderation FROM settings");
$req->execute(array());
$dataSettings = $req->fetch();
if ($dataSettings["moderation"] == 0) {
    $filter = "status >= 0";  
} else {
    $filter = "status > 0";  
}
    
// Vérifie si informations dans variable POST
if (!empty($_POST)) {

    if (isset($_SESSION["userRole"]) && $_SESSION["userRole"] == 1 ) {
        $status = 1;
    } else {
        $status = 0;
    }

    if (isset($_POST["save_comment"])) {
        
        if (isset($_SESSION["userID"])) {
            $user_ID = $_SESSION["userID"];
        } else {
            $user_ID = NULL;
        }

        $msgComment = "";
        $typeAlert = "success";
        $validation = true;

        // Vérifie si le commentaire est vide
        if (empty($_POST["content"])) {
            $msgComment = "Le commentaire est vide.";
            $typeAlert = "danger";
            $validation = false;
        }

        // Ajoute le commentaire si le commentaire n'est pas vide
        if ($validation) {
            $req = $db->prepare("INSERT INTO comments(id_post, user_ID, user_name, content, status) 
            VALUES(:id_post, :user_ID, :user_name, :content, :status)");
            $req->execute(array(
                "id_post" => $_SESSION["postID"],
                "user_ID" =>  $user_ID,
                "user_name" =>  htmlspecialchars($_POST["name"]),
                "content" => htmlspecialchars($_POST["content"]),
                "status" =>  $status
            ));
            if ($dataSettings["moderation"] == 0 || (isset($_SESSION["userRole"]) && $_SESSION["userRole"] == 1 )) {
                $msgComment = "Le commentaire a été ajouté.";
            } else {
                $msgComment = "Le commentaire est en attente de modération.";
                $typeAlert = "info";
            }
        }
    }
    // Modifie le commentaire
    if (isset($_POST["edit_comment"])) {
        $req = $db->prepare("UPDATE comments SET content = :new_content, status = :new_status, update_date = NOW() WHERE ID = :ID");
        $req->execute(array(
            "new_content" => htmlspecialchars($_POST["content"]),
            "new_status" => $status,
            "ID" => htmlspecialchars($_GET["comment"])
        ));

        $msgComment = "Le commentaire a été modifié.";
        $typeAlert = "success";
    }
}

//
if (isset($_GET["action"]) && $_GET["action"]=="erase") {
    $req = $db->prepare("DELETE FROM comments WHERE ID = ?");
    $req->execute(array(
        htmlspecialchars($_GET["comment"])
    ));

    $msgComment = "Le commentaire a été supprimé.";
    $typeAlert = "warning";
}
// Ajoute le signalement du commentaire
if (isset($_GET["action"]) && $_GET["action"]=="report") {
    $req = $db->prepare("UPDATE comments SET status = :new_status, nb_report = nb_report + 1, report_date = NOW() WHERE ID = :ID");
    $req->execute(array(
        "new_status" => 2,
        "ID" => htmlspecialchars($_GET["comment"])
    ));

    $msgComment = "Le commentaire a été signalé.";
    $typeAlert = "warning";
}

if (isset($msgComment)) {
    $_SESSION["flash"] = array(
        "msg" => $msgComment,
        "type" =>  $typeAlert
    );
}

// Récupère le post
$dataPost = $postManager->get($post_ID); 

// Compte le nombre de commentaires
$req = $db->prepare("SELECT COUNT(*) AS nb_Comments FROM comments WHERE id_post = ? AND $filter");
$req->execute([
    $post_ID
]);
$nbComments = $req->fetch();
$nbItems = $nbComments["nb_Comments"];

if (!empty($_POST["nbDisplayed"])) {
    $nbDisplayed =  htmlspecialchars($_POST["nbDisplayed"]);
    setcookie("pagination[nbDisplayedComments]", $nbDisplayed, time() + 365*24*3600, null, null, false, true);
} else if (!empty($_COOKIE["pagination"]["nbDisplayedComments"])) {
    $nbDisplayed = $_COOKIE["pagination"]["nbDisplayedComments"];
} else {
    $nbDisplayed = 10;
}

if (!empty($_GET["page"])) {
    $page = htmlspecialchars($_GET["page"]);
    // Calcul le nombre de pages par rapport aux nombre d'articles
    $maxComment =  $page*$nbDisplayed;
    $minComment = $maxComment-$nbDisplayed;
} else  {
    $page = 1;
    $minComment = 0;
    $maxComment = $nbDisplayed;
}

// Initialisation des variables pour la pagination
$linkNbDisplayed= "post_view.php?" . $post_ID . "#form-comment";
$linkPagination= "post_view.php?";
$anchorPagination= "#comments";
$nbPages = ceil($nbItems / $nbDisplayed);
require("pagination.php");

// Vérifie s'il y a des commentaires
$req = $db->prepare("SELECT ID FROM comments WHERE id_post = ? AND $filter ");
$req->execute([
    $post_ID
    ]);
$commentsExist = $req->fetch();

if (!$commentsExist) {
    $infoComments = "Aucun commentaire.";
} else  {
    // Récupère les commentaires
    $req = $db->prepare("SELECT c.ID, c.user_ID, u.login, c.user_name, c.content, c.status, 
    DATE_FORMAT(c.creation_date, \"%d/%m/%Y à %H:%i\") AS creation_date_fr,
    DATE_FORMAT(c.update_date, \"%d/%m/%Y à %H:%i\") AS update_date_fr 
    FROM comments c
    LEFT JOIN users u
    ON c.user_ID = u.ID
    WHERE c.id_post = :post_ID AND $filter 
    ORDER BY c.creation_date DESC
    LIMIT  $minComment,  $maxComment");
    $req->execute(array(
        "post_ID" => $post_ID
    ));
}

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent">
                <li class="breadcrumb-item"><a href="blog.php" class="text-blue">Blog</a></li>
                <li class="breadcrumb-item active" aria-current="page">Article</li>
            </ol>
        </nav>

        <!-- Affichage de l'article -->
        <section id="post">
                <div class="card shadow">
                    <div class="card-header bg-dark text-light">
                        <h1 class="h2 mt-2 mb-3"><?= $dataPost->title() ?></h1>
                        <em>Créé le <?= str_replace(' ', ' à ', $dataPost->creation_date()) ?> par <a class="text-blue" href=""> <?= $dataPost->login() ?> </a> (Modifié le <?= str_replace(' ', ' à ', $dataPost->update_date()) ?>)</em>
                        <?php
                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]== $dataPost->user_id()) { ?>
                            <a class="text-blue a-edit-post" href="post_edit.php?post=<?=  $dataPost->id() ?>"><span class="far fa-edit"></span> Modifier</a>
                        <?php } ?>
                        <a href="#comments" class="badge badge-blue ml-2"> <span class="badge badge-light"><?= $nbComments["nb_Comments"] ?> </span></a>
                    </div>
                    <div class="card-body text-body">
                    <?= html_entity_decode($dataPost->content()) ?>
                    </div>
                </div>
                <?php 
                    if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataPost->user_id()) { 
                ?>
                        <a class="text-blue" href="post_edit.php?post=<?= $post_ID ?>"><span class="far fa-edit"></span> Modifier l'article</a> 
                <?php 
                } 
                ?>
        </section>

        <!-- Formulaire d'ajout d'un commentaire -->
        <section id="form-comment" class="mt-4">

        <?php include("msg_session_flash.php") ?>

            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-6">
                    <h2 class="h3 mb-4">Nouveau commentaire</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <form action="post_view.php?post=<?= $post_ID ?>#form-comment" method="post" class="px-3">
                                <?php 
                                    if (!isset($_SESSION["userID"])) { 
                                ?>
                                <div class="row">
                                    <label for="name" class="col-md-4 col-form-label">Nom</label>
                                    <input type="text" name="name" id="name" class="col-md-8 form-control mb-4 shadow-sm" value="">
                                </div>
                                <?php
                                }
                                ?>
                                <div class="form-group row">
                                    <label for="content" class="sr-only">Contenu du message</label>
                                    <textarea name="content" class="col-md-12 form-control shadow-sm" id="content" rows="4"></textarea>
                                </div>
                                <div class="form-group row float-right">
                                    <input type="submit" value="Envoyer" name="save_comment" id="save_comment" class="btn btn-blue shadow">
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        </section>

        <!-- Affiche les commentaires -->
        <section id="comments">
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-6 mt-2">
            
                    <h2 class="h3 mb-4">Commentaires</h2>
                    <p> <?= isset($infoComments) ? $infoComments : "" ?> </p>

                    <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                    <?php 
                        while ($dataComment = $req->fetch()) {
                    ?>
                            <!--  Affiche le commentaire -->
                            <div id="comment-<?=  $dataComment["ID"] ?>" class="comment card shadow">
                                <div class="card-body">
                                    <?php 
                                        if (!empty($dataComment["login"])) {
                                            $user_login = $dataComment["login"];
                                            } else {
                                                if (!empty($dataComment["user_name"])) {
                                                    $user_login = $dataComment["user_name"];
                                                } else {
                                                    $user_login = "Anonyme";
                                                }
                                            }
                                    ?>
                                    <p><strong><?= $user_login ?></strong>, le <?= $dataComment["creation_date_fr"] ?>
                                    <?php
                                    if ($dataComment["update_date_fr"] != $dataComment["creation_date_fr"]) {
                                        echo "(Modifié le " . $dataComment["update_date_fr"] . ")";
                                    }
                                    ?>
                                    </p>
                                    <div class="comment-content position relative"><?= nl2br($dataComment["content"]) ?>
                                        <span class="comment-fade-out d-none"></span>
                                    </div>
                                        <?php                        
                                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataComment["user_ID"]) { 
                                        ?>
                                            <div>
                                                <a href="post_view.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?=  $dataComment["ID"] ?>&action=erase#form-comment" 
                                                    onclick="if(window.confirm('Voulez-vous vraiment supprimer ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                                    <span class="fas fa-times text-danger"></span>
                                                </a>
                                            </div>
                                        <?php
                                        } else {
                                            if($dataComment["status"]==2) {
                                        ?>
                                                <div class="report-comment"><span class="fas fa-flag text-danger"></span></div>
                                        <?php                                       
                                            } else {
                                        ?>
                                            <div class="report-comment">
                                                <a href="post_view.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?=  $dataComment["ID"] ?>&action=report#form-comment" 
                                                    onclick="if(window.confirm('Voulez-vous vraiment signaler ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                                    <span class="far fa-flag text-warning"> Signaler</span>
                                                </a>
                                            </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php                        
                                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataComment["user_ID"]) { 
                                        ?>
                                            <div class="edit-comment mt-3">
                                                <a href="#comment-<?= $dataComment["ID"] ?>"><span class="far fa-edit text-blue"> Modifier</span></a>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                     <div id="form-edit-comment-<?= $dataComment["ID"] ?>"class="form-edit-comment d-none">
                                        <form action="post_view.php?post=<?= $post_ID ?>&comment=<?= $dataComment["ID"] ?>&action=edit#form-comment" method="post">
                                            <div class="form-group">
                                                <label for="content"></label>
                                                <textarea name="content" class="form-control shadow-sm" id="content" rows="4"><?= $dataComment["content"] ?></textarea>
                                            </div>
                                            <div class="form-group float-right">
                                                <input type="submit" value="Modifier" name="edit_comment" id="edit-<?= $dataComment["ID"] ?>" class="btn btn-blue shadow">
                                                <button value="Annuler" id="cancel_edit-comment-<?= $dataComment["ID"] ?>" class="cancel-edit-comment btn btn-secondary shadow">Annuler</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                            </div>
                    <?php
                        }
                    ?>

                    <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                </div>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>
    <script src="js/see_more_comment.js"></script>

</body>

</html
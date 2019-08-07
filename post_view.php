<?php 

function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$postsManager = new Postsmanager();
$commentsManager = new CommentsManager();
$db = $postsManager->db();

if (!empty($_GET["post"])) {
    $post_id = htmlspecialchars($_GET["post"]);
    $_SESSION["postID"] = $post_id;
} else {
    $post_id = $_SESSION["postID"];
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
            $user_id = $_SESSION["userID"];
        } else {
            $user_id = NULL;
        }

        $validation = true;

        // Vérifie si le commentaire est vide
        if (empty($_POST["content"])) {
            $session->setFlash("Le commentaire est vide.", "danger");
            $validation = false;
        }

        // Ajoute le commentaire si le commentaire n'est pas vide
        if ($validation) {
            $comment = new Comments([
                "post_id" => $_SESSION["postID"],
                "user_id" => $user_id,
                "user_name" => htmlspecialchars($_POST["name"]),
                "content" => htmlspecialchars($_POST["content"]),
                "status" => $status
            ]);
            $commentsManager->add($comment);
            if ($dataSettings["moderation"] == 0 || (isset($_SESSION["userRole"]) && $_SESSION["userRole"] == 1 )) {
                $session->setFlash("Le commentaire a été ajouté.", "success");
            } else {
                $session->setFlash("Le commentaire est en attente de modération.", "info");
            }
        }
    }
    // Modifie le commentaire
    if (isset($_POST["edit_comment"])) {
        $comment = new Comments([
            "id" => htmlspecialchars($_GET["comment"]),
            "content" => htmlspecialchars($_POST["content"]),
            "status" => $status,
        ]);
        $commentsManager->update($comment);
        $session->setFlash("Le commentaire a été modifié.", "success");
    }
}

//
if (isset($_GET["action"]) && $_GET["action"]=="erase") {
    $commentsManager->delete(htmlspecialchars($_GET["comment"]));
    $session->setFlash("Le commentaire a été supprimé.", "warning");
}
// Ajoute le signalement du commentaire
if (isset($_GET["action"]) && $_GET["action"]=="report") {
    $comment = new Comments([
        "id" => htmlspecialchars($_GET["comment"]),
        "status" => 2,
    ]);
    $commentsManager->report($comment);
    $session->setFlash("Le commentaire a été signalé.", "warning");
}

// Récupère le post
$post = $postsManager->get($post_id); 

// Compte le nombre de commentaires
$nbItems = $commentsManager->count("post_id = " . $post_id . " AND " . $filter);

// Initialisation de la pagination
$typeItem = "posts";
$linkNbDisplayed= "post_view.php?" . $post_id . "#form-comment";
$linkPagination= "post_view.php?";
$anchorPagination= "#comments";
require("pagination.php");

// Vérifie s'il y a des commentaires
if ($nbItems == 0) {
    $infoComments = "Aucun commentaire.";
} else {
    // Récupère les commentaires
    $comments = $commentsManager->getList("c.post_id = " . $post_id . " AND " . $filter, "c.creation_date", "DESC", $minLimit, $maxLimit);

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
                        <h1 class="h2 mt-2 mb-3"><?= $post->title() ?></h1>
                        <em>Créé le <?= str_replace(' ', ' à ', $post->creation_date()) ?> par <a class="text-blue" href=""> <?= $post->login() ?> </a> (Modifié le <?= str_replace(' ', ' à ', $post->update_date()) ?>)</em>
                        <?php
                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]== $post->user_id()) {
                        ?>
                            <a class="text-blue a-edit-post" href="post_edit.php?post=<?=  $post->id() ?>"><span class="far fa-edit"></span> Modifier</a>
                        <?php 
                        } 
                        ?>
                        <a href="#comments" class="badge badge-blue ml-2"> <span class="badge badge-light"><?= $nbItems ?> </span></a>
                    </div>
                    <div class="card-body text-body">
                    <?= html_entity_decode($post->content()) ?>
                    </div>
                </div>
                <?php 
                if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$post->user_id()) {
                ?>
                        <a class="text-blue" href="post_edit.php?post=<?= $post_id ?>"><span class="far fa-edit"></span> Modifier l'article</a> 
                <?php 
                } 
                ?>
        </section>

        <!-- Formulaire d'ajout d'un commentaire -->
        <section id="form-comment" class="mt-4">

            <?php $session->flash(); // Message en session flash ?>      

            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-6">
                    <h2 class="h3 mb-4">Nouveau commentaire</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <form action="post_view.php?post=<?= $post_id ?>#form-comment" method="post" class="px-3">
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
                        foreach ($comments as $comment) {
                    ?>
                            <!--  Affiche le commentaire -->
                            <div id="comment-<?= $comment->id() ?>" class="comment card shadow">
                                <div class="card-body">
                                    <?php 
                                        if (!empty($comment->login())) {
                                            $user_login = $comment->login();
                                            } else {
                                                if (!empty($comment->user_name())) {
                                                    $user_login = $comment->user_name();
                                                } else {
                                                    $user_login = "Anonyme";
                                                }
                                            }
                                    ?>
                                    <p><strong><?= $user_login ?></strong>, le <?= str_replace(' ', ' à ', $comment->creation_date()) ?>
                                    <?php
                                    if ($comment->update_date() != $comment->creation_date()) {
                                        echo "(Modifié le " . str_replace(' ', ' à ', $comment->update_date()) . ")";
                                    }
                                    ?>
                                    </p>
                                    <div class="comment-content position relative"><?= nl2br($comment->content()) ?>
                                        <span class="comment-fade-out d-none"></span>
                                    </div>
                                        <?php
                                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$comment->user_id()) {
                                        ?>
                                            <div>
                                                <a href="post_view.php?post=<?= isset($post_id) ? $post_id : "" ?>&comment=<?= $comment->id() ?>&action=erase#form-comment" 
                                                    onclick="if(window.confirm('Voulez-vous vraiment supprimer ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                                    <span class="fas fa-times text-danger"></span>
                                                </a>
                                            </div>
                                        <?php
                                        } else {
                                            if($comment->status()==2) {
                                        ?>
                                                <div class="report-comment"><span class="fas fa-flag text-danger"></span></div>
                                        <?php
                                            } else {
                                        ?>
                                            <div class="report-comment">
                                                <a href="post_view.php?post=<?= isset($post_id) ? $post_id : "" ?>&comment=<?= $comment->id() ?>&action=report#form-comment" 
                                                    onclick="if(window.confirm('Voulez-vous vraiment signaler ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                                    <span class="far fa-flag text-warning"> Signaler</span>
                                                </a>
                                            </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        if (isset($_SESSION["userID"]) && $_SESSION["userID"] == $comment->user_id()) {
                                        ?>
                                            <div class="edit-comment mt-3">
                                                <a href="#comment-<?= $comment->id() ?>"><span class="far fa-edit text-blue"> Modifier</span></a>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                     <div id="form-edit-comment-<?= $comment->id() ?>"class="form-edit-comment d-none">
                                        <form action="post_view.php?post=<?= $post_id ?>&comment=<?= $comment->id() ?>&action=edit#form-comment" method="post">
                                            <div class="form-group">
                                                <label for="content"></label>
                                                <textarea name="content" class="form-control shadow-sm" id="content" rows="4"><?= $comment->content() ?></textarea>
                                            </div>
                                            <div class="form-group float-right">
                                                <input type="submit" value="Modifier" name="edit_comment" id="edit-<?= $comment->id() ?>" class="btn btn-blue shadow">
                                                <button value="Annuler" id="cancel_edit-comment-<?= $comment->id() ?>" class="cancel-edit-comment btn btn-secondary shadow">Annuler</button>
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
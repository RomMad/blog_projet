<?php 
    session_start(); 

    require("connection_bdd.php");

    var_dump($_GET);
    if (!empty($_GET["post"])) {
        $post_ID = htmlspecialchars($_GET["post"]);
        $_SESSION["postID"] = $post_ID;
    } else {
        $post_ID = $_SESSION["postID"];
    };

    var_dump($_POST);    
    // Vérification si informations dans variable POST
    if (!empty($_POST["content"])) {
        
        if (isset($_SESSION["userID"])) {
            $user_ID = $_SESSION["userID"];
        } else {
            $user_ID = NULL;
        };

        $content = htmlspecialchars($_POST["content"]);
        $name = htmlspecialchars($_POST["name"]);
        $msgComment = "Le commentaire a été ajouté.";
        $typeAlert = "success";
        $validation = true;

        // Vérifie si le commentaire est vide
        if (empty($content)) {
            $msgComment = "Le commentaire est vide.";
            $typeAlert = "danger";
            $validation = false;
        };

        // Ajoute le commentaire si le commentaire n'est pas vide
        if ($validation) {
        $req = $bdd->prepare("INSERT INTO comments(id_post, user_ID, user_name, content) 
        VALUES(:id_post, :user_ID, :user_name, :content)");
        $req->execute(array(
            "id_post" => $_SESSION["postID"],
            "user_ID" =>  $user_ID,
            "user_name" => $name,
            "content" => $_POST["content"]
            ));
        };
    };

    if (isset($_GET["comment"]) && isset($_GET["action"]) && $_GET["action"]=="erase") {
        $ID = htmlspecialchars($_GET["comment"]);
        $req = $bdd->prepare("DELETE FROM comments WHERE ID = ?");
        $req->execute(array($ID));

        $msgComment = "Le commentaire a été supprimé.";
        $typeAlert = "warning";
    };

    if (isset($_GET["comment"]) && isset($_GET["action"]) && $_GET["action"]=="report") {
        $ID = htmlspecialchars($_GET["comment"]);
        $req = $bdd->prepare("UPDATE comments SET status = :new_status, nb_report = nb_report + 1, report_date = NOW() WHERE ID = :ID");
        $req->execute(array(
            "new_status" => 2,
            "ID" => $ID
        ));

        $msgComment = "Le commentaire a été signalé.";
        $typeAlert = "warning";
    };

    if (isset($msgComment)) {
        $_SESSION["flash"] = array(
            "msg" => $msgComment,
            "type" =>  $typeAlert
        );
    };



    // Récupère le post
    $req = $bdd->prepare("SELECT p.ID, p.title, p.user_ID, u.login, p.content, 
    DATE_FORMAT(p.creation_date, \"%d/%m/%Y à %H:%i\") AS creation_date_fr, 
    DATE_FORMAT(p.update_date, \"%d/%m/%Y à %H:%i\") AS update_date_fr 
    FROM posts p
    LEFT JOIN users u
    ON p.user_ID = u.ID
    WHERE p.ID=?");
    $req->execute(array($post_ID));
    $dataPost = $req->fetch();

    // Compte le nombre de commentaires
    $req = $bdd->prepare("SELECT COUNT(*) AS nb_comments FROM comments WHERE id_post = ? AND status >= ? ");
    $req->execute([$post_ID,0]);
    $nbComments = $req->fetch();

    if (!empty($_POST["nbDisplayed"])) {
        $_SESSION["nbDisplayedComments"] = htmlspecialchars($_POST["nbDisplayed"]);
    };
    if (!isset($_SESSION["nbDisplayedComments"])) {
        $_SESSION["nbDisplayedComments"] = 5;
    };
    $nbDisplayed = $_SESSION["nbDisplayedComments"];


  if (!empty($_GET["page"])) {
      $page = htmlspecialchars($_GET["page"]);
      // Calcul le nombre de pages par rapport aux nombre d'articles
      $maxComment =  $page*$nbDisplayed;
      $minComment = $maxComment-$nbDisplayed;
  } else  {
      $page = 1;
      $minComment = 0;
      $maxComment = $nbDisplayed;
  };
  
    // Initialisation des variables pour la pagination
    $linkNbDisplayed= "post.php?" . $post_ID . "#form-comment";
    $linkPagination= "post.php?";
    $anchorPagination= "#comments";
    $nbPages = ceil($nbComments["nb_comments"] / $nbDisplayed);
    require("pagination.php");

    // Vérifie s'il y a des commentaires
    $req = $bdd->prepare("SELECT ID FROM comments WHERE id_post = ? AND status >= ? ");
    $req->execute([$post_ID,0]);
    $commentsExist = $req->fetch();

    if (!$commentsExist) {
        $infoComments = "Aucun commentaire.";
    } else  {
        // Récupère les commentaires
        $req = $bdd->prepare("SELECT c.ID, c.user_ID, u.login, c.user_name, c.content, c.status, 
        DATE_FORMAT(c.creation_date, \"%d/%m/%Y à %H:%i\") AS creation_date_fr 
        FROM comments c
        LEFT JOIN users u
        ON c.user_ID = u.ID
        WHERE c.id_post = :post_ID AND c.status >= :status 
        ORDER BY c.creation_date DESC
        LIMIT  $minComment,  $maxComment");
        $req->execute(array(
            "post_ID" => $post_ID,
            "status" => 0
        ));
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">

        <!-- Affichage de l'article -->
        <section id="post">
                <div class="card shadow">
                    <div class="card-header bg-dark text-light">
                        <h1 class="h2 mt-2 mb-3"><?= htmlspecialchars($dataPost["title"]) ?></h1>
                        <em>Créé le <?= htmlspecialchars($dataPost["creation_date_fr"]) ?> par <a class="text-info" href=""> <?= htmlspecialchars($dataPost["login"]) ?> </a> et modifié le <?=  htmlspecialchars($dataPost["update_date_fr"]) ?></em>
                        <?php
                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataPost["user_ID"]) { ?>
                            <a class="text-info a-edit-post" href="edit_post.php?post=<?=  htmlspecialchars($dataPost["ID"]) ?>"><span class="far fa-edit"></span> Modifier</a>
                        <?php }; ?>
                        <a href="#comments" class="badge badge-info ml-2 font-weight-normal">Commentaires <span class="badge badge-light"><?= $nbComments["nb_comments"] ?> </span></a>
                    </div>
                    <div class="card-body text-body">
                    <?= html_entity_decode($dataPost["content"]) ?>
                    </div>
                </div>
                <?php 
                    if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataPost["user_ID"]) { 
                ?>
                        <a class="text-info" href="edit_post.php?post=<?= $post_ID ?>"><span class="far fa-edit"></span> Modifier l'article</a> 
                <?php 
                }; 
                ?>
        </section>

        <!-- Formulaire d'ajout d'un commentaire -->
        <section id="form-comment">

        <?php include("msg_session_flash.php") ?>

            <div class="row">
                <form action="post.php?post=<?= $post_ID ?>#form-comment" method="post" class="col-sm-12 col-md-6 mt-4">
                    <h2 class="h3 mb-4">Nouveau commentaire</h2>
                    <div class="form-group">
                        <div class="row">
                            <label for="name" class="col-md-4 col-form-label">Nom</label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control mb-4 shadow-sm" value="<?= isset($_SESSION["userLogin"]) ? $_SESSION["userLogin"] : "" ?>">
                            </div>
                        </div>
                        <label for="content"></label>
                        <textarea name="content" class="form-control shadow-sm" id="content" rows="4"></textarea>
                    </div>
                    <div class="form-group float-right">
                        <input type="submit" value="Envoyer" id="save" class="btn btn-info shadow">
                    </div>
                </form>
            </div>
        </section>

        <!-- Affiche les commentaires -->
        <section id="comments">
            <div class="row">
                <div class="col-sm-12 col-md-6 mt-2">

                <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->
            
                    <h2 class="h3 mb-4">Commentaires</h2>
                    <p> <?= isset($infoComments) ? $infoComments : "" ?> </p>
                    <?php 
                        while ($dataComment = $req->fetch()) {
                    ?>
                            <div class="card shadow">
                                <div class="card-body position relative">
                                    <?php 
                                        if (!empty($dataComment["login"])) {
                                            $user_login = htmlspecialchars($dataComment["login"]);
                                            } else {
                                                if (!empty($dataComment["user_name"])) {
                                                    $user_login = htmlspecialchars($dataComment["user_name"]);
                                                } else {
                                                    $user_login = "Anonyme";
                                                };
                                            };
                                    ?>
                                    <p><strong><?= $user_login ?></strong>, le <?= $dataComment["creation_date_fr"] ?></p>
                                    <p><?= nl2br(htmlspecialchars($dataComment["content"])) ?></p>
                                    <?php                        
                                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$dataComment["user_ID"]) { 
                                    ?>
                                            <div>
                                                <a href="post.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?=  htmlspecialchars($dataComment["ID"]) ?>&action=erase#form-comment" onclick="if(window.confirm('Voulez-vous vraiment supprimer ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}"><span class="fas fa-times text-danger"></span></a>
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
                                                <a href="post.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?=  htmlspecialchars($dataComment["ID"]) ?>&action=report#form-comment" onclick="if(window.confirm('Voulez-vous vraiment signaler ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}"><span class="far fa-flag text-info"> Signaler</span></a>
                                            </div>
                                        <?php
                                            };
                                        };
                                        ?>
                                </div>
                            </div>
                    <?php
                        };
                    ?>

                    <?php include("nav_pagination.php"); ?> <!-- Ajoute la barre de pagination -->

                </div>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html
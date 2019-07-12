<?php 
    session_start(); 

    require("connection_bdd.php");

    var_dump($_GET);
    if (!empty($_GET["post"])) {
        $post_ID = htmlspecialchars($_GET["post"]);
        $_SESSION["post_ID"] = $post_ID;
    } else {
        $post_ID = $_SESSION["post_ID"];
    };

    var_dump($_POST);    
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        if (isset($_SESSION["user_ID"])) {
            $user_ID = $_SESSION["user_ID"];
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
            "id_post" => $_SESSION["post_ID"],
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
        $req = $bdd->prepare("UPDATE comments SET status = :new_status, nb_report = nb_report + 1, date_report = NOW() WHERE ID = :ID");
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
    DATE_FORMAT(p.date_creation, \"%d/%m/%Y à %H:%i\") AS date_creation_fr, 
    DATE_FORMAT(p.date_update, \"%d/%m/%Y à %H:%i\") AS date_update_fr 
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
  echo $nbComments["nb_comments"];

  if (!empty($_GET["page"])) {
      $page = htmlspecialchars($_GET["page"]);
      // Calcul le nombre de pages par rapport aux nombre d'articles
      $maxComment =  $page*5;
      $minComment = $maxComment-5;
  } else  {
      $page = 1;
      $minComment = 0;
      $maxComment = 5;
  };
  
  $link= "post.php";
  $ancre= "#comments";
  $nbPages = ceil($nbComments["nb_comments"] / 5);
  $pageLink_1 = $page;
  $pageLink_2 = $page+1;
  $pageLink_3 = $page+2;
  $activepageLink_1 = "";
  $activepageLink_2 = "active disabled";
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

    // Vérifie s'il y a des commentaires
    $req = $bdd->prepare("SELECT ID FROM comments WHERE id_post = ? AND status >= ? ");
    $req->execute([$post_ID,0]);
    $commentsExist = $req->fetch();

    if (!$commentsExist) {
        $infoComments = "Aucun commentaire.";
    } else  {
        // Récupère les commentaires
        $req = $bdd->prepare("SELECT c.ID, c.user_ID, u.login, c.user_name, c.content, c.status, 
        DATE_FORMAT(c.date_creation, \"%d/%m/%Y à %H:%i\") AS date_creation_fr 
        FROM comments c
        LEFT JOIN users u
        ON c.user_ID = u.ID
        WHERE c.id_post = :post_ID AND c.status >= :status 
        ORDER BY c.date_creation DESC
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
                <div class="card">
                    <div class="card-header bg-dark text-light">
                        <h1><?= htmlspecialchars($dataPost["title"]) ?></h1>
                        <em>Créé le <?=  htmlspecialchars($dataPost["date_creation_fr"]) ?> par <a class="text-info" href=""> <?= htmlspecialchars($dataPost["login"]) ?> </a> et modifié le <?=  htmlspecialchars($dataPost["date_update_fr"]) ?></em>
                        <?php
                        if (isset($_SESSION["user_ID"]) && $_SESSION["user_ID"]==$dataPost["user_ID"]) { ?>
                            <a class="text-info a-edit-post" href="edit_post.php?post=<?=  htmlspecialchars($dataPost["ID"]) ?>"><span class="far fa-edit"></span> Modifier</a>
                        <?php }; ?>
                    </div>
                    <div class="card-body text-body">
                    <?= html_entity_decode($dataPost["content"]) ?>
                    </div>
                </div>
                <?php 
                    if (isset($_SESSION["user_ID"]) && $_SESSION["user_ID"]==$dataPost["user_ID"]) { 
                ?>
                        <a class="text-info" href="edit_post.php?post=<?= $post_ID ?>"><span class="far fa-edit"></span> Modifier l'article</a> 
                <?php 
                }; 
                ?>
        </section>

        <!-- Formulaire d'ajout d'un commentaire -->
        <section id="form-comment">
            <?php 
                if (isset($_SESSION["flash"])) {
                    ?>
                    <div id="msg-profil" class="alert alert-<?= $_SESSION["flash"]["type"] ?> alert-dismissible fade show" role="alert">                     
                        <?= $_SESSION["flash"]["msg"] ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button> 
                    </div>
                    <?php
                    unset($_SESSION["flash"]);
                };
            ?>
            <div class="row">
                <form action="post.php?post=<?= $post_ID ?>#form-comment" method="post" class="col-sm-12 col-md-6 mt-4">
                    <h2 class="h3 mb-4">Nouveau commentaire</h2>
                    <div class="form-group">
                        <div class="row">
                            <label for="name" class="col-md-4 col-form-label">Nom</label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control mb-4" value="<?= isset($_SESSION["user_login"]) ? $_SESSION["user_login"] : "" ?>">
                            </div>
                        </div>
                        <label for="content"></label>
                        <textarea name="content" class="form-control" id="content" rows="4"></textarea>
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
                            <div class="card">
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
                                    <p><strong><?= $user_login ?></strong>, le <?= $dataComment["date_creation_fr"] ?></p>
                                    <p><?= nl2br(htmlspecialchars($dataComment["content"])) ?></p>
                                    <?php                        
                                        if (isset($_SESSION["user_ID"]) && $_SESSION["user_ID"]==$dataComment["user_ID"]) { 
                                    ?>
                                            <div>
                                                <a href="post.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?=  htmlspecialchars($dataComment["ID"]) ?>&action=erase#form-comment" 
                                                    onclick="if(window.confirm('Voulez-vous vraiment supprimer ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                                    <span class="fas fa-times text-danger"></span>
                                                </a>
                                            </div>
                                        <?php
                                        } else {
                                        ?>
                                            <div class="report-comment">
                                                <a href="post.php?post=<?= isset($post_ID) ? $post_ID : "" ?>&comment=<?=  htmlspecialchars($dataComment["ID"]) ?>&action=report#form-comment" 
                                                    onclick="if(window.confirm('Voulez-vous vraiment signaler ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                                    <span class="far fa-flag text-info"> Signaler</span>
                                                </a>
                                            </div>
                                        <?php
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

</html
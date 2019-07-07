<?php 
    session_start();

    include("connection_bdd.php");

    if (empty($_SESSION)) {
        echo "Vous devez vous connecter pour écrire un article.";
        // Redirige vers la page de connexion
        header('Location: connection.php');
    };

    var_dump($_POST);
    if (!empty($_GET)) {
        $idPost = htmlspecialchars($_GET['post']);
        // Récupère le post
        $req = $bdd->prepare('SELECT p.ID, p.post_title, p.post_author, u.user_login, p.post_content, p.post_status, DATE_FORMAT(p.post_date_creation, \'%d/%m/%Y à %Hh%imn\') AS post_date_creation_fr, DATE_FORMAT(p.post_date_update, \'%d/%m/%Y à %Hh%imn\') AS post_date_update_fr 
        FROM posts p
        LEFT JOIN users u
        ON p.post_author = u.ID
        WHERE p.ID=?');
        $req->execute(array($idPost));
        $data = $req->fetch();

        $post_title = $data['post_title'];
        $post_content = $data['post_content'];
        $post_status = $data['post_status'];
        $post_author  = $data['user_login'];
        $post_date_creation = $data['post_date_creation_fr'];
        $post_date_update = $data['post_date_update_fr'];
    };

    var_dump($_POST);    
    // Vérification si informations dans variable POST
    if (!empty($_POST)) {
        $post_author = htmlspecialchars($_SESSION['ID']);
        $post_title = htmlspecialchars($_POST['post_title']);
        $post_content = htmlspecialchars($_POST['post_content']);
        $post_status = htmlspecialchars($_POST['post_status']);

        // Insert les données dans la table posts
        $req = $bdd->prepare('INSERT INTO posts(post_author, post_title, post_content, post_status) 
        VALUES(:post_author, :post_title, :post_content, :post_status)');
        $req->execute(array(
            'post_author' => $post_author,
            'post_title' => $post_title,
            'post_content' => $post_content,
            'post_status' => $post_status
            ));
        $statusPost = "Article enregistré.";
    };
?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="post_form" class="row">

            <div class="col-sm-12 col-md-10 mx-auto">
                <form action="edit_post.php" method="post" class="">
                    <h2>Edition d'article </h2>
                    <div class="form-group">
                        <label for="post_title">Titre</label>
                        <input type="text" name="post_title" class="form-control" id="post_title" value="<?= isset($post_title) ? $post_title : ''?>">
                    </div>
                    <div class="form-group">
                        <label for="post_content">Contenu</label>
                        <textarea name="post_content" class="form-control" id="post_content" rows="10"> <?= isset($post_content) ? $post_content : ''?></textarea>
                    </div>
                    <div class="form-group col-sm-4 col-md-2">
                        <label for="post_status">Statut</label>
                        <select name="post_status" class="form-control" id="post_status">
                            <option>Publié</option>
                            <option>Brouillon</option>
                            <option value="<?= isset($post_status) ? $post_status : '' ?>" selected ><?= isset($post_status) ? $post_status : '' ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="post_author">Auteur</label>
                        <input type="text" name="post_author" class="form-control" id="post_author" value="<?= isset($post_author) ? $post_author : ''?>">
                    </div>
                    <div class="form-group">
                        <label for="post_date_creation">Date de création</label>
                        <input type="text" name="post_date_creation" class="form-control" id="post_date_creation" value="<?= isset($post_date_creation) ? $post_date_creation : ''?>">
                    </div>
                    <div class="form-group">
                        <label for="post_date_update">Date de mise à jour</label>
                        <input type="text" name="post_date_update" class="form-control" id="post_date_update" value="<?= isset($post_date_update) ? $post_date_update : ''?>">
                    </div>
                    <div class="form-group float-right">
                        <input type="submit" value="Enregistrer" id="save" class="btn btn-primary shadow">
                    </div>
                </form>

                <?php  
                if (isset($statusPost)) {
                    echo $statusPost;
                };
                ?>

            </div>
        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
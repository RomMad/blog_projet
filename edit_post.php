<?php 
    session_start();

    include("connection_bdd.php");

    var_dump($_POST);

    if (empty($_SESSION)) {
        echo "Vous devez vous connecter pour écrire un article.";
        // Redirige vers la page de connexion
        header('Location: connection.php');
    };
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
                    <h2>Nouvel article </h2>
                    <div class="form-group">
                        <label for="post-post_title">Titre</label>
                        <input type="text" name="post_title" class="form-control" id="post_title">
                    </div>
                    <div class="form-group">
                        <label for="post_content">Contenu</label>
                        <textarea name="post_content" class="form-control" id="post_content" rows="10"></textarea>
                    </div>
                    <div class="form-group col-sm-4 col-md-2">
                        <label for="post_status">Statut</label>
                        <select name="post_status" class="form-control" id="post_status">
                            <option>Publié</option>
                            <option>Brouillon</option>
                        </select>
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

        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
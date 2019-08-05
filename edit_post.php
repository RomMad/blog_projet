<?php 

function loadClass($classname) 
{
    require $classname . ".php";
}

spl_autoload_register("loadClass");

session_start();

require("connection_db.php");

$manager = new Postsmanager($db);

// Redirige vers la page de connexion si l'utilisateur n'a pas les droits
if (!isset($_SESSION["userRole"]) || $_SESSION["userRole"]>4) 
{
    header("Location: connection.php");
}

// Vérification si informations dans variable POST
if (!empty($_POST)) 
{
    $typeAlert = "info";
    $validation = true;

    // Vérifie si le titre est vide
    if (empty($_POST["title"])) 
    {
        $message = "Le titre de l'article est vide.";
        $typeAlert = "danger";
        $validation = false;
    }

    // Ajoute ou modifie l'article si le titre n'est pas vide
    if ($validation) 
    {
        // Ajoute l'article si nouvel article
        if (isset($_POST["save"]) && empty($_POST["post_ID"])) 
        {
            $post = new Posts ([
                "title" => htmlspecialchars($_POST["title"]),
                "content" => htmlspecialchars($_POST["post_content"]),
                "user_id" => htmlspecialchars($_SESSION["userID"]),
                "user_login"  => htmlspecialchars($_SESSION["userLogin"]),
                "status" => htmlspecialchars($_POST["status"]),
            ]);
            $manager->add($post);
            $message = "L'article a été enregistré.";
            $dataPost = $manager->lastcreate($_SESSION["userID"]);
        }

        // Met à jour l'article si article existant
        if (isset($_POST["save"]) && !empty($_POST["post_ID"])) 
        {
            $post = new Posts ([
                "title" => htmlspecialchars($_POST["title"]),
                "content" => htmlspecialchars($_POST["post_content"]),
                "status" => htmlspecialchars($_POST["status"]),
                "id" => htmlspecialchars($_POST["post_ID"]),
            ]);
            $manager->update($post);
            $message = "L'article a été modifié.";
        }
    }

    // Supprime l'article
    if (isset($_POST["erase"]) && !empty($_POST["post_ID"])) 
    {
        $manager->delete((htmlspecialchars($_POST["post_ID"])));
        $message = "L'article \"" . htmlspecialchars($_POST["title"]) . "\" a été supprimé.";
        $typeAlert = "warning";
        header("Location: blog.php");
    }

    $_SESSION["flash"] = array(
        "msg" => $message,
        "type" =>  $typeAlert
    );

}

// Récupère l'article si GET post existe
if (!empty($_GET["post"])) 
{
    $dataPost = $manager->get(htmlspecialchars($_GET["post"]));
    // Vérifie si l'utilisateur est l'auteur de l'article
    if ($_SESSION["userRole"]>2 && $_SESSION["userID"] != $dataPost->user_id() ) 
    {
        $message = "Vous n'avez pas les droits pour accéder à cet article";
        $typeAlert = "warning";
        header("Location: blog.php");
    }
}

var_dump($_SESSION);
var_dump($_POST);

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
                <li class="breadcrumb-item"><a href="post_view.php?post=<?= isset($_GET["post"]) ? htmlspecialchars($_GET["post"]) : "" ?>" class="text-blue">Article</a></li>
                <li class="breadcrumb-item active" aria-current="page">Édition</li>
            </ol>
        </nav>

        <section id="post_form" class="row">
            <div class="col-sm-12 col-md-12 mx-auto">

                <form action="edit_post.php?post=<?= isset($_GET["post"]) ? htmlspecialchars($_GET["post"]) : "" ?>" method="post" class="">

                    <h2 class="mb-4">Édition d'article</h2>

                    <?php include("msg_session_flash.php") ?>

                    <div class="row">
                        <div class="col-sm-6 col-md-10">
                            <div class="form-group">
                                <label for="title">Titre</label>
                                <input type="text" name="title" class="form-control shadow-sm" id="title" value="<?= isset($dataPost) ? $dataPost->title() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_content" class="d-none">Contenu</label>
                                <textarea name="post_content" class="form-control shadow-sm" id="post_content" rows="12"><?= isset($dataPost) ? html_entity_decode($dataPost->content()) : "" ?></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-2">
                            <div class="form-group sr-only">
                                    <label for="post_ID"">ID</label>
                                    <input type="text" name="post_ID" class="form-control shadow-sm" id="post_ID" readonly value="<?= isset($dataPost) ? $dataPost->id() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="post_user_ID">Auteur</label>
                                <input type="text" name="post_user_ID" class="form-control shadow-sm" id="post_user_ID" readonly value="<?= isset($dataPost) ? $dataPost->user_login() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="creation_date">Date de création</label>
                                <input type="text" name="creation_date" class="form-control shadow-sm" id="creation_date" readonly value="<?= isset($dataPost) ? $dataPost->creation_date() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="update_date">Date de mise à jour</label>
                                <input type="text" name="update_date" class="form-control shadow-sm" id="update_date" readonly value="<?= isset($dataPost) ? $dataPost->update_date() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Statut</label>
                                <select name="status" class="form-control shadow-sm" id="status">
                                    <option <?php if (isset($dataPost) && $dataPost->status()=="Publié") { ?> selected <?php } ?>>Publié</option>
                                    <option <?php if (isset($dataPost) && $dataPost->status()=="Brouillon") { ?> selected <?php } ?>>Brouillon</option>
                                </select>
                            </div>
                            <div class="form-group float-right">
                                <input type="submit" id="save" name="save" value="Enregistrer" class="btn btn-block btn-blue mb-2 shadow">
                                <?php 
                                    if ( isset($_GET["post"])) { 
                                ?>
                                <input type="submit" id="erase" name="erase" alt="Supprimer l'article" class="btn btn-block btn-danger mb-2 shadow" 
                                value="Supprimer" onclick="if(window.confirm('Voulez-vous vraiment supprimer l\'article ?')){return true;}else{return false;}">
                                <?php 
                                    } 
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>

    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html
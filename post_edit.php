<?php 
function loadClass($classname) {
    require $classname . ".php";
}

spl_autoload_register("loadClass");

$session = new Session();
$postsManager = new Postsmanager();

// Vérifie si l'article exite
if (!empty($_GET["post_id"])) {
    $post = $postsManager->getUserId($_GET["post_id"]);
    if (!$post) {
        $session->setFlash("Cet article n'existe pas.", "warning");
        header("Location: blog.php"); 
        die;
    }
    // Vérifie si l'utilisateur a les droit d'accès ou si il est l'auteur de l'article
    if ($_SESSION["userRole"] > 2 && $_SESSION["userID"] != $post["user_id"]) {
        $session->setFlash("Vous n'avez pas les droits pour accéder à cet article", "warning");
        header("Location: blog.php"); 
        die;
    }
}

// Redirige vers la page de connexion si l'utilisateur n'a pas les droits
if (!isset($_SESSION["userRole"]) || $_SESSION["userRole"]>4) {
    $session->setFlash("Vous n'avez pas les droits pour accéder à cet article", "warning");
    header("Location: connection.php"); 
    die;
}

// Vérification si informations dans variable POST
if (!empty($_POST)) {
    $post = new Posts([
        "title" => htmlspecialchars($_POST["title"]),
        "content" => htmlspecialchars($_POST["post_content"]),
        "status" => htmlspecialchars($_POST["status"]),
        "id" => htmlspecialchars($_GET["post_id"]),
        "user_id" => htmlspecialchars($_SESSION["userID"]),
        "user_login" => htmlspecialchars($_SESSION["userLogin"]),
    ]);

    $validation = true;

    // Vérifie si le titre est vide
    if (empty($post->title())) {
        $session->setFlash("Le titre de l'article est vide.", "danger");
        $validation = false;
    }
    // Vérifie si le contenu de l'article est vide
    if (empty($post->content()) && $post->status() == "Publié") {
        $session->setFlash("L'article ne peut pas être publié si le contenu est vide.", "danger");
        $validation = false;
    }
    // Ajoute ou modifie l'article si le titre n'est pas vide
    if ($validation) {
        // Met à jour l'article si article existant
        if (isset($_POST["save"]) && !empty($post->id())) {
            $postsManager->update($post);
            $session->setFlash("Les modifications ont été enregistrées.", "success");
        }
        // Ajoute l'article si nouvel article
        if (isset($_POST["save"]) && empty($post->id())) {
            $postsManager->add($post);
            $session->setFlash("L'article a été enregistré.", "success");
            $post = $postsManager->lastCreate($_SESSION["userID"]);
        }
    }
    // Supprime l'article
    if (isset($_POST["erase"]) && !empty($post->id())) {
        $postsManager->delete(htmlspecialchars($_GET["post_id"]));
        $session->setFlash("L'article \"" . $post->title() . "\" a été supprimé.", "warning");
        header("Location: blog.php");
    }
}

// Récupère l'article si GET post_id existe
if (!empty($_GET["post_id"])) {
    $post = $postsManager->get(htmlspecialchars($_GET["post_id"]));
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
                <li class="breadcrumb-item"><a href="post_view.php?post_id=<?= isset($_GET["post_id"]) ? $post->id() : "" ?>" class="text-blue">Article</a></li>
                <li class="breadcrumb-item active" aria-current="page">Édition</li>
            </ol>
        </nav>

        <section id="post_form" class="row">
            <div class="col-sm-12 col-md-12 mx-auto">

                <form action="post_edit.php?post_id=<?= isset($_GET["post_id"]) ? $post->id() : "" ?>" method="post" class="">

                    <h2 class="mb-4">Édition d'article</h2>

                    <?php $session->flash(); // Message en session flash ?>

                    <div class="row">
                        <div class="col-md-12 col-lg-10">
                            <div class="form-group">
                                <label for="title" class="sr-only">Titre</label>
                                <input type="text" name="title" class="form-control font-weight-bolder shadow-sm" id="title" value="<?= isset($post) ? $post->title() : "" ?>" placeholder="Saisissez le titre" autofocus>
                            </div>
                            <div class="form-group">
                                <label for="post_content" class="sr-only">Contenu</label>
                                <textarea name="post_content" class="form-control shadow-sm" id="post_content" rows="12"><?= isset($post) ? html_entity_decode($post->content()) : "" ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-2">
                            <div class="form-group">
                                <label for="post_user_id">Auteur</label>
                                <input type="text" name="post_user_id" class="form-control shadow-sm" id="post_user_id" readonly value="<?= isset($post) ? $post->login() : $_SESSION["userLogin"] ?>">
                            </div>
                            <div class="form-group">
                                <label for="creation_date">Date de création</label>
                                <input type="text" name="creation_date" class="form-control shadow-sm" id="creation_date" readonly value="<?= isset($post) ? $post->creation_date() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="update_date">Date de mise à jour</label>
                                <input type="text" name="update_date" class="form-control shadow-sm" id="update_date" readonly value="<?= isset($post) ? $post->update_date() : "" ?>">
                            </div>
                            <div class="form-group">
                                <label for="status">Statut</label>
                                <select name="status" class="form-control shadow-sm" id="status">
                                    <option <?= isset($post) && $post->status()=="Brouillon" ? "selected" : "" ?>>Brouillon</option>
                                    <option <?= isset($post) && $post->status()=="Publié" ? "selected" : "" ?>>Publié</option>
                                </select>
                            </div>
                            <div class="form-group float-right">
                                <input type="submit" id="save" name="save" value="Enregistrer" class="btn btn-block btn-blue mb-2 shadow">
                                <?php 
                                if (isset($_GET["post_id"])) { 
                                ?>
                                <input type="submit" id="erase" name="erase" alt="Supprimer l'article" class="btn btn-block btn-danger mb-2 shadow" 
                                value="Supprimer" onclick="if(window.confirm('Voulez-vous vraiment supprimer l\'article ?')){return true;} else{return false;}">
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

</html>
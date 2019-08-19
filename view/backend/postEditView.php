<?php  $title = $_SESSION["blog_name"] . " - Édition d'article";

if (isset($_GET["id"])) { 
    $title = $title  . " - " . $post->title(); 
}
?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Blog</a></li>
            <li class="breadcrumb-item"><a href="post-<?= isset($_GET["id"]) ? $post->id() : "" ?>"class="text-blue">Article</a></li>
            <li class="breadcrumb-item active" aria-current="page">Édition</li>
        </ol>
    </nav>

    <section id="post_form" class="row min-vh-80">
        <div class="col-sm-12 col-md-12 m-auto">

            <form action="edit-post<?= isset($post) ? "-" . $post->id() : "" ?>" method="post">
                <h2 class="mb-4">Édition d'article</h2>

                <?php $session->flash(); ?>

                <div class="row">
                    <div class="col-md-12 col-lg-10">
                        <div class="form-group">
                            <label for="title" class="sr-only">Titre</label>
                            <input type="text" name="title" class="form-control font-weight-bolder shadow-sm" id="title"
                                value="<?= isset($post) ? $post->title() : "" ?>" placeholder="Saisissez le titre" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="post_content" class="sr-only">Contenu de l'article</label>
                            <textarea name="post_content" class="form-control shadow-sm" id="post_content"
                                rows="12"><?= isset($post) ? $post->content("html_format") : "" ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-2">
                        <div class="form-group">
                            <label for="post_user_id">Auteur</label>
                            <input type="text" name="post_user_id" class="form-control shadow-sm" id="post_user_id"
                                readonly value="<?= isset($post) ? $post->login() : $_SESSION["user"]["login"] ?>">
                        </div>
                        <div class="form-group">
                            <label for="creation_date">Date de création</label>
                            <input type="text" name="creation_date" class="form-control shadow-sm" id="creation_date"
                                readonly value="<?= isset($post) ? $post->creation_date("") : "" ?>">
                        </div>
                        <div class="form-group">
                            <label for="update_date">Date de mise à jour</label>
                            <input type="text" name="update_date" class="form-control shadow-sm" id="update_date"
                                readonly value="<?= isset($post) ? $post->update_date("") : "" ?>">
                        </div>
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <?php if ($_SESSION["user"]["role"] < 4) { ?>
                            <select name="status" class="form-control shadow-sm" id="status">
                                <option <?= isset($post) && $post->status()=="Brouillon" ? "selected" : "" ?>>Brouillon</option>
                                <option <?= isset($post) && $post->status()=="Publié" ? "selected" : "" ?>>Publié</option>
                            </select>
                            <?php } else { ?>
                            <input type="text" name="status" class="form-control shadow-sm" id="status"
                                readonly value="<?= isset($post) ? $post->status() : "" ?>">
                            <?php } ?> 
                        </div>
                        <div class="form-group float-right">
                            <input type="submit" id="save" name="save" value="Enregistrer" class="btn btn-block btn-blue mb-2 shadow">
                            <?php 
                            if (isset($_GET["id"]) && $_SESSION["user"]["role"] < 4) { 
                            ?>
                            <input type="submit" id="delete" name="delete" alt="Supprimer l'article" class="btn btn-block btn-danger mb-2 shadow" value="Supprimer"
                                onclick="if(window.confirm('Voulez-vous vraiment supprimer l\'article ?')){return true;} else{return false;}">
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

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
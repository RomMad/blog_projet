<?php $title = $_SESSION["settings"]->blog_name() . " - Édition d'article";

if (isset($_GET["id"])) { 
    $title = $title . " - " . $this->_post->title(); 
}
?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Blog</a></li>
            <li class="breadcrumb-item"><a href="post-<?= isset($_GET["id"]) ? $this->_post->id() : "" ?>"class="text-blue">Article</a></li>
            <li class="breadcrumb-item active" aria-current="page">Édition</li>
        </ol>
    </nav>

    <section id="post_form" class="row min-vh-80">
        <div class="col-sm-12 col-md-12 m-auto">
            <form action="edit-post<?= isset($this->_post) ? "-" . $this->_post->id() : "" ?>" method="post">
                <h2 class="mb-4">Édition d'article</h2>

                <?= $this->_session->flash() ?>

                <div class="row">
                    <div class="col-md-12 col-lg-9">
                        <div class="form-group">
                            <label for="title" class="sr-only">Titre</label>
                            <input type="text" name="title" class="form-control font-weight-bolder shadow-sm" id="title"
                                value="<?= isset($this->_post) ? $this->_post->title() : "" ?>" required placeholder="Saisissez le titre" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="post_content" class="sr-only">Contenu de l'article</label>
                            <textarea name="post_content" class="form-control shadow-sm" id="post_content"
                                rows="12"><?= isset($this->_post) ? $this->_post->content("html_format") : "" ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-3 d-flex justify-content-lg-end">
                        <div id="info-post">
                            <div class="form-group row">
                                <div class="col-xs-6 col-sm-6 col-lg-12">
                                    <input type="submit" id="save" name="save" value="<?= isset($this->_post) ? "Modifier" : "Enregistrer" ?>" class="btn btn-block btn-blue mb-2 shadow">
                                    <?php if (isset($this->_post) && $_SESSION["user"]["role"] < 4) { ?>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-lg-12">
                                    <input type="submit" id="delete" name="delete" alt="Supprimer l'article" class="btn btn-block btn-danger mb-2 shadow" value="Supprimer"
                                        onclick="if(window.confirm('Voulez-vous vraiment supprimer l\'article ?')){return true;} else{return false;}">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 col-md-4 col-lg-12">
                                    <label for="status">Statut</label>
                                    <?php if ($_SESSION["user"]["role"] < 4) { ?>
                                    <select name="status" class="form-control shadow-sm" id="status" required>
                                        <option <?= isset($this->_post) && $this->_post->status() == "Brouillon" ? "selected" : "" ?>>Brouillon</option>
                                        <option <?= isset($this->_post) && $this->_post->status() == "Publié" ? "selected" : "" ?>>Publié</option>
                                    </select>
                                    <?php } else { ?>
                                    <input type="text" name="status" class="form-control shadow-sm" id="status"
                                        readonly value="<?= isset($this->_post) ? $this->_post->status() : "" ?>">
                                    <?php } ?> 
                                </div>
                                <div class="form-group col-sm-6 col-md-4 col-lg-12">
                                    <label for="publication_date">Date de publication</label>
                                    <input type="date" name="publication_date" class="form-control mb-1 shadow-sm" id="publication_date"
                                        value="<?= isset($this->_post) && !empty($this->_post->publication_date()) ? $this->_post->publication_date("date") : NULL ?>">
                                    <label for="publication_date" class="sr-only">Heure de publication</label>
                                    <input type="time" name="publication_time" class="form-control shadow-sm" id="publication_time"
                                        value="<?= isset($this->_post) && !empty($this->_post->publication_date()) ? $this->_post->publication_date("time") : NULL ?>">
                                </div>
                                <div class="form-group col-sm-6 col-md-4 col-lg-12">
                                    <label for="post_user_id">Auteur</label>
                                    <input type="text" name="post_user_id" class="form-control shadow-sm" id="post_user_id"
                                        readonly value="<?= isset($this->_post) ? $this->_post->login() : $_SESSION["user"]["login"] ?>">
                                </div>
                                <div class="form-group col-sm-6 col-md-4 col-lg-12">
                                    <label for="creation_date">Date de création</label>
                                    <input type="text" name="creation_date" class="form-control shadow-sm" id="creation_date"
                                        readonly value="<?= isset($this->_post) && !empty($this->_post->id()) ? $this->_post->creation_date("datetime_fr") : "" ?>">
                                </div>
                                <div class="form-group col-sm-6 col-md-4 col-lg-12">
                                    <label for="update_date">Date de modification</label>
                                    <input type="text" name="update_date" class="form-control shadow-sm" id="update_date"
                                        readonly value="<?= isset($this->_post) && !empty($this->_post->id()) ? $this->_post->update_date("datetime_fr") : "" ?>">
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
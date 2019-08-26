<?php  $title = $_SESSION["settings"]->blog_name() . " - Administration"; ?>

<?php ob_start(); ?>

<div class="container">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Paramètres généraux</li>
        </ol>
    </nav>

    <section>
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4">Paramètres généraux</h2>
            </div>
        </div>

        <?php $this->_session->flash(); ?>

        <div class="row">

            <div class="col-md-8 col-lg-6 mt-4">
                <form action="settings" method="post" enctype="multipart/form-data" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Paramètres</h3>
                    </div>
                    <div class="form-group row">
                        <label for="blog_name" class="col-md-4 col-form-label">Nom du site</label>
                        <div class="col-md-8">
                            <input type="text" name="blog_name" id="blog_name" class="form-control mb-4 shadow-sm" required
                                value="<?= $this->_settings->blog_name() ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="title" class="col-md-4 col-form-label">Titre du blog</label>
                        <div class="col-md-8">
                            <input type="text" name="title" id="title" class="form-control mb-4 shadow-sm" required
                                value="<?= $this->_settings->title() ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="admin_email" class="col-md-4 col-form-label">Adresse email</label>
                        <div class="col-md-8">
                            <input type="text" name="admin_email" id="admin_email" class="form-control mb-4 shadow-sm" required
                                value="<?= $this->_settings->admin_email() ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="default_role" class="col-md-4 col-form-label">Rôle par défaut des utilisateurs</label>
                        <div class="col-md-8">
                            <select name="default_role" id="default_role" class="custom-select form-control w-50 shadow-sm" required>
                                <option value="1" <?= $this->_settings->default_role() == 1 ? "selected" : "" ?>>Administrateur</option>
                                <option value="2" <?= $this->_settings->default_role() == 2 ? "selected" : "" ?>>Editeur</option>
                                <option value="3" <?= $this->_settings->default_role() == 3 ? "selected" : "" ?>>Auteur</option>
                                <option value="4" <?= $this->_settings->default_role() == 4 ? "selected" : "" ?>>Contributeur</option>
                                <option value="5" <?= $this->_settings->default_role() == 5 ? "selected" : "" ?>>Abonné</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label for="style_blog" class="col-md-4 col-form-label">Style du blog</label>
                        <div class="col-md-8">
                            <select name="style_blog" id="style_blog" class="custom-select form-control w-50 shadow-sm" required>
                                <option value="light" <?= $this->_settings->style_blog() == "light" ? "selected" : "" ?>>Clair</option>
                                <option value="dark" <?= $this->_settings->style_blog() == "dark" ? "selected" : "" ?>>Foncé</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="posts_by_row" class="col-md-4 col-form-label">Nombre d'articles par rangée</label>
                        <div class="col-md-8">
                            <select name="posts_by_row" id="posts_by_row" class="custom-select form-control w-50 shadow-sm" required>
                                <option value="1" <?= $this->_settings->posts_by_row() == 1 ? "selected" : "" ?>>1 article</option>
                                <option value="2" <?= $this->_settings->posts_by_row() == 2 ? "selected" : "" ?>>2 articles</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <div class="col-md-4 col-form-label">Logo</div>
                        <div class="col-md-8">
                            <div class="custom-file">
                                <input type="file" name="logoFile" id="logoFile" class="custom-file-input">
                                <label class="custom-file-label" for="logoFile">Choisir un fichier</label>
                            </div>      
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-xs-4 col-sm-4">Modération</div>
                        <div class="col-xs-8 col-sm-8">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="moderation" id="moderation" class="custom-control-input checkbox"
                                    value="true" <?= $this->_settings->moderation() == 1 ? "checked" : "" ?>/>
                                <label for="moderation" class="custom-control-label" ><span class="sr-only">Modération<span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="float-right">
                                <input type="submit" name="validation" value="Valider" id="validation" class="btn btn-blue shadow">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4 offset-lg-2 col-lg-4 mt-4">
                <div class="list-group shadow">
                    <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Navigation</h3>
                    <a href="posts" class="list-group-item list-group-item-action text-blue">Gestion des articles</a>
                    <a href="comments" class="list-group-item list-group-item-action text-blue">Gestion des commentaires</a>
                    <a href="users" class="list-group-item list-group-item-action text-blue">Gestion des utilisateurs</a>
                    <a href="new-user" class="list-group-item list-group-item-action text-blue">Ajouter un utilisateur</a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
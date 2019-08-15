<?php  $title = $_SESSION["blog_name"] . " - Administration"; ?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Administration</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-12 mt-4">

            <h2 class="mb-4">Administration</h2>

        </div>
    </div>

    <?php $session->flash(); // Message en session flash ?>

    <div class="row">

        <div class="col-md-8 col-lg-6 mt-4">
            <form action="index.php?action=settings" method="post" class="col-md-12 card shadow">
                <div class="form-group row">
                    <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Paramètres</h3>
                </div>
                <div class="form-group row">
                    <label for="blog_name" class="col-md-4 col-form-label">Titre du blog</label>
                    <div class="col-md-8">
                        <input type="text" name="blog_name" id="blog_name" class="form-control mb-4 shadow-sm"
                            value="<?= $settings->blog_name() ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="admin_email" class="col-md-4 col-form-label">Adresse email</label>
                    <div class="col-md-8">
                        <input type="text" name="admin_email" id="admin_email" class="form-control mb-4 shadow-sm"
                            value="<?= $settings->admin_email() ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="default_role" class="col-md-4 col-form-label">Rôle par défaut des utilisateurs</label>
                    <div class="col-md-8">
                        <select name="default_role" id="default_role" class="custom-select form-control shadow-sm">
                            <option value="1" <?= $settings->default_role() == 1 ? "selected" : "" ?>>Administrateur</option>
                            <option value="2" <?= $settings->default_role() == 2 ? "selected" : "" ?>>Editeur</option>
                            <option value="3" <?= $settings->default_role() == 3 ? "selected" : "" ?>>Auteur</option>
                            <option value="4" <?= $settings->default_role() == 4 ? "selected" : "" ?>>Contributeur</option>
                            <option value="5" <?= $settings->default_role() == 5 ? "selected" : "" ?>>Abonné</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="posts_by_row" class="col-md-4 col-form-label">Nombre d'articles par rangée</label>
                    <div class="col-md-8">
                        <select name="posts_by_row" id="posts_by_row" class="custom-select form-control shadow-sm">
                            <option value="1" <?= $settings->posts_by_row() == 1 ? "selected" : "" ?>>1</option>
                            <option value="2" <?= $settings->posts_by_row() == 2 ? "selected" : "" ?>>2</option>
                            <option value="3" <?= $settings->posts_by_row() == 3 ? "selected" : "" ?>>3</option>
                            <option value="4" <?= $settings->posts_by_row() == 4 ? "selected" : "" ?>>4</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-4">Modération</div>
                    <div class="col-md-8">
                        <div class="form-check">
                            <input type="checkbox" name="moderation" id="moderation" class="form-check-input"
                                value="true" <?= $settings->moderation() == 1 ? "checked" : "" ?>>
                            <label for="moderation" class="form-check-label sr-only">Modération</label>
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
                <a href="index.php?action=posts" class="list-group-item list-group-item-action text-blue">Gestion des articles</a>
                <a href="index.php?action=comments" class="list-group-item list-group-item-action text-blue">Gestion des commentaires</a>
                <a href="index.php?action=users" class="list-group-item list-group-item-action text-blue">Gestion des utilisateurs</a>
                <a href="index.php?action=newUser" class="list-group-item list-group-item-action text-blue">Ajouter un utilisateur</a>
            </div>
        </div>

    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
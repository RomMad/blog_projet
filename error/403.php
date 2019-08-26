<?php  $title = "Erreur 403 | " . $_SESSION["settings"]->blog_name() ?>

<?php ob_start(); ?>

<div class="container">
    <div class="row min-vh-80">
        <div class="col-sm-10 col-md-8 col-lg-6 m-auto text-center">
            <h1>Accès refusé</h1>
            <p class="my-4">Vous n'avez pas les droits pour acccéder à cette page.</p>
            <a href="/blog_projet/connection" class="btn btn-blue mt-2 p-2 shadow">Se connecter</a>
            <a href="/blog_projet/blog" class="btn btn-blue mt-2 p-2 shadow">Revenir à l'accueil</a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
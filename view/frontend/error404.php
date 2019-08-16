<?php  $title = $_SESSION["blog_name"] . " - Erreur 404" ?>

<?php ob_start(); ?>

<div class="container height-full">

    <!-- <section id="profil" class="row"> -->
        <div class="col-sm-10 col-md-8 col-lg-6 m-auto text-center">
            <h1 class="">Erreur 404<br />Cette page n'existe pas</h1>
            <p class="my-4">La page que vous recherchez est introuvable.</p>
            <a href="blog" class="btn btn-blue mt-2 p-2 shadow">Revenir à l'accueil</a>
        </div>
    <!-- </section> -->

</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
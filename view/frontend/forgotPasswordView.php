<?php  $title = $_SESSION["blog_name"] . " - Mot de passe oublié"; ?>

<?php ob_start(); ?>

<div class="container">

    <section id="forgot-password" class="row">
        <form action="index.php?action=forgotPassword" method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 mx-auto text-center">

            <?php $session->flash(); // Message en session flash ?>

            <h1 class="h3 mb-4 font-weight-normal">Mot de passe oublié</h1>
            <p>Saisissez votre adresse e-mail afin de recevoir un e-mail pour réinitialiser votre mot de passe.</p>
            <label for="email" class="sr-only">Email</label>
            <input type="text" name="email" id="email" class="form-control mb-4 shadow-sm" placeholder="Email" autofocus="">
            <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">
        </form>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>

<script> seePassword = new SeePassword(); </script>
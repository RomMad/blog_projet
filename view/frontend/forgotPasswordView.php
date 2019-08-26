<?php  $title = $_SESSION["settings"]->blog_name() . " - Mot de passe oublié"; ?>

<?php ob_start(); ?>

<div class="container">

    <section class="row min-vh-80">
        <form action="forgotPassword" method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 m-auto text-center">

            <?php $this->_session->flash(); // Message en session flash ?>

            <h1 class="h3 mb-4 font-weight-normal">Mot de passe oublié</h1>
            <p>Saisissez votre adresse e-mail afin de recevoir un e-mail pour réinitialiser votre mot de passe.</p>
            <label for="email" class="sr-only">Email</label>
            <input type="text" name="email" id="email" class="form-control mb-4 shadow-sm" placeholder="Email" required autofocus="">
            <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">
        </form>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php $script ="<script> seePassword = new SeePassword(); </script>"; ?>

<?php require "view/template.php"; ?>
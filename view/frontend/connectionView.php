<?php  $title = $_SESSION["settings"]->blog_name() . " - Connexion" ?>

<?php ob_start(); ?>

<div class="container">
    <section id="connection" class="row min-vh-80">
        <form action="connection" method="post" class="form-signin m-auto text-center">

            <?= $this->_session->flash() ?>

            <h1 class="h3 mb-4 font-weight-normal">Merci de vous connecter</h1>
            <label for="login" class="sr-only">Login ou adresse email</label>
            <input type="text" name="login" id="login" class="form-control mb-2 shadow-sm" placeholder="Login ou adresse email" required
                autofocus="" value="<?= isset($_COOKIE["user"]["login"]) ? $_COOKIE["user"]["login"] : "" ?>">
            <label for="pass" class="sr-only">Mot de passe</label>
            <div class="password-group">
                <input type="password" name="pass" id="pass" class="password form-control mb-3 shadow-sm" placeholder="Mot de passe" required>
                <span class="show-password fas fa-eye"></span>
            </div>
            <div class="checkbox mb-3">
                <label for="remember">
                    <input type="checkbox" name="remember" id="remember" value="TRUE"> Se souvenir de moi
                </label>
            </div>
            <input type="submit" value="Se connecter" id="validation" class="btn btn-lg btn-blue btn-block mb-4 shadow">
            <a href="inscription" class="btn btn-lg btn-blue btn-block mb-4 shadow">S'inscrire</a>
            <a href="forgotPassword" class="text-blue mb-4">Login ou mot de passe oublié ?</a>
            <p class="mt-3 mb-0 text-muted">© 2019</p>
        </form>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php $script ="<script> seePassword = new SeePassword(); </script>"; ?>

<?php require "view/template.php"; ?>
<?php  $title = $_SESSION["blog_name"] . " - Connexion" ?>

<?php ob_start(); ?>

<div class="container">
    <section id="connection" class="row height-full">
        <form action="connection" method="post" class="form-signin m-auto text-center">

            <?php $session->flash(); ?>

            <h1 class="h3 mb-4 font-weight-normal">Merci de vous connecter</h1>
            <label for="login" class="sr-only">Login ou adresse email</label>
            <input type="text" name="login" id="login" class="form-control mb-2 shadow-sm" placeholder="Login ou adresse email" 
                autofocus="" value="<?= isset($_COOKIE["user"]["login"]) ? $_COOKIE["user"]["login"] : "" ?>">
            <label for="pass" class="sr-only">Mot de passe</label>
            <div class="div-user-pass">
                <input type="password" name="pass" id="pass" class="form-control mb-3 shadow-sm" placeholder="Mot de passe">
                <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
            </div>
            <div class="checkbox mb-3">
                <label for="remember">
                    <input type="checkbox" name="remember" id="remember" value="true"> Se souvenir de moi
                </label>
            </div>
            <input type="submit" value="Se connecter" id="validation" class="btn btn-lg btn-blue btn-block mb-4 shadow">
            <a href="inscription" class="btn btn-lg btn-blue btn-block mb-4 shadow">S'inscrire</a>

            <a href="forgotPassword" class="text-blue mb-4">Login ou mot de passe oublié ?</a>

            <p class="mt-3 mb-0 text-muted">© 2019</p>
        </form>
    </section>
</div>

<script> seePassword = new SeePassword(); </script>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
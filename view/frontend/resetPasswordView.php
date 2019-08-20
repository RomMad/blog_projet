<?php  $title = $_SESSION["blog_name"] . " - Réinitialisation du mot de passe"; ?>

<?php ob_start(); ?>

<div class="container">

    <section class="row min-vh-80">
        <form action="reset-password-<?= isset($_GET["token"]) ? htmlspecialchars($_GET["token"]) : "" ?>"
            method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 m-auto">

            <?php $session->flash(); // Message en session flash ?>

            <h1 class="h3 mb-4 font-weight-normal text-center">Réinitialisation du mot de passe</h1>
            <label for="email" class="sr-only">Email</label>
            <input type="email" name="email" id="email" class="form-control mb-4" placeholder="Email"
                value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
            <label for="new_pass" class="sr-only">Mot de passe</label>
            <div class="password-group">
                <input type="password" name="new_pass" id="new_pass" class="password form-control mb-2 shadow-sm"
                    placeholder="Nouveau mot de passe">
                    <span class="show-password fas fa-eye"></span>
            </div>
            <div class="password-group">
                <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="password form-control mb-4"
                    placeholder="Confirmation du mot de passe">
                    <span class="show-password fas fa-eye"></span>
            </div>
            <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">

        </form>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php $script ="<script> seePassword = new SeePassword(); </script>"; ?>

<?php require "view/template.php"; ?>
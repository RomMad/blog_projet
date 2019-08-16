<?php  $title = $_SESSION["blog_name"] . " - Réinitialisation du mot de passe"; ?>

<?php ob_start(); ?>

<div class="container">

    <section id="reset-password" class="row">
        <form action="resetPassword-<?= isset($_GET["token"]) ? htmlspecialchars($_GET["token"]) : "" ?>"
            method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 mx-auto mt-4 mb-4">

            <?php $session->flash(); // Message en session flash ?>

            <h1 class="h3 mb-4 font-weight-normal text-center">Réinitialisation du mot de passe</h1>
            <label for="email" class="sr-only">Email</label>
            <input type="email" name="email" id="email" class="form-control mb-4" placeholder="Email"
                value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
            <label for="new_pass" class="sr-only">Mot de passe</label>
            <div class="div-user-pass">
                <input type="password" name="new_pass" id="new_pass" class="form-control mb-2 shadow-sm"
                    placeholder="Nouveau mot de passe">
                <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
            </div>
            <div class="div-user-pass">
                <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="form-control mb-4"
                    placeholder="Confirmation du mot de passe">
                <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
            </div>
            <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">

        </form>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>

<script> seePassword = new SeePassword(); </script>
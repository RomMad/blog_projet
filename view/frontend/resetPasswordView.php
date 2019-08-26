<?php  $title = $_SESSION["settings"]->blog_name() . " - Mot de passe"; ?>

<?php ob_start(); ?>

<div class="container">

    <section class="row min-vh-80">
        <form action="reset-password-<?= isset($_GET["token"]) ? htmlspecialchars($_GET["token"]) : "" ?>"
            method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 m-auto">

            <?= $this->_session->flash() ?>

            <h1 class="h3 mb-4 font-weight-normal text-center"><?= $_GET["action"] == "reset-password" ? "Réinitialisation du mot de passe" : "Création du mot de passe" ?></h1>
            <label for="email" class="sr-only">Email</label>
            <input type="email" name="email" id="email" class="form-control mb-4" placeholder="Email" required 
                value="<?= isset($this->_user) ? $this->_user->email() : "" ?>">
            <div class="password-group">
                <label for="pass" class="sr-only">Mot de passe</label>
                <input type="password" name="pass" id="pass" class="password form-control mb-2 shadow-sm"
                    placeholder="Nouveau mot de passe" required>
                    <span class="show-password fas fa-eye"></span>
            </div>
            <div class="password-group">
                <label for="pass_confirm" class="sr-only">Confirmation du mot de passe</label>
                <input type="password" name="pass_confirm" id="pass_confirm" class="password form-control mb-4"
                    placeholder="Confirmation du mot de passe" required>
                    <span class="show-password fas fa-eye"></span>
            </div>
            <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-blue btn-block mb-4 shadow">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-muted small">Le mot de passe doit respecter les critères suivants :
                        <ul>
                            <li>entre 6 et 20 caractères</li>
                            <li>au moins 1 lettre minuscule</li>
                            <li>au moins 1 lettre majuscule</li>
                            <li>au moins 1 chiffre</li>
                            <li>au moins 1 caractère spécial (?!*(){}[]-+=&<>§$...)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php $script ="<script> seePassword = new SeePassword(); </script>"; ?>

<?php require "view/template.php"; ?>
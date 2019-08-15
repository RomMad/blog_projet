<?php  $title = $_SESSION["blog_name"] . " - Inscription"; ?>

<?php ob_start(); ?>

<div class="container">

    <section id="inscription" class="row">
        <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

            <?php $session->flash(); ?>

            <form action="index.php?action=inscription" method="post" class="col-md-12 card shadow mt-4">
                <div class="form-group row">
                    <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Inscription</h3>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="row">
                            <label for="login" class="col-md-4 col-form-label">Login</label>
                            <div class="col-md-8">
                                <input type="text" name="login" id="login" class="form-control mb-4 shadow-sm"
                                    value="<?= isset($_POST["login"]) ? htmlspecialchars($_POST["login"]) : "" ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                            <div class="col-md-8">
                                <input type="email" name="email" id="email" class="form-control mb-4 shadow-sm"
                                    value="<?= isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "" ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-md-4 col-form-label">Nom</label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control mb-4 shadow-sm"
                                    value="<?= isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : "" ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                            <div class="col-md-8">
                                <input type="text" name="surname" id="surname" class="form-control mb-4 shadow-sm"
                                    value="<?= isset($_POST["surname"]) ? htmlspecialchars($_POST["surname"]) : "" ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                            <div class="col-md-5">
                                <div class="div-user-pass">
                                    <input type="password" name="pass" id="pass" class="form-control mb-4 shadow-sm">
                                    <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                            <div class="col-md-5">
                                <div class="div-user-pass">
                                    <input type="password" name="pass_confirm" id="pass_confirm" class="form-control mb-4 shadow-sm">
                                    <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <input type="submit" value="Valider" id="validation" class="btn btn-blue shadow">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <small class="text-muted">Le mot de passe doit contenir au minimum :
                                    <ul>
                                        <li>6 caractères</li>
                                        <li>1 lettre minuscule</li>
                                        <li>1 lettre majuscule</li>
                                        <li>1 chiffre</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>

<script> seePassword = new SeePassword(); </script>
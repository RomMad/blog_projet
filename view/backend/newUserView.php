<?php  $title = "Jean Forteroche | Le blog"; ?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
                <li class="breadcrumb-item"><a href="index.php?action=settings" class="text-blue">Administration</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ajout d'un utilisateur</li>
            </ol>
    </nav>

    <section id="inscription" class="row">
        <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

        <?php $session->flash(); // Message en session flash ?>

            <form action="index.php?action=newUser" method="post" class="col-md-12 card shadow mt-4">
                <div class="form-group row">
                    <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Ajouter un nouvel utilisateur</h3>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="row">
                            <label for="login" class="col-md-4 col-form-label">Login</label>
                            <div class="col-md-8">
                                <input type="text" name="login" id="login" class="form-control mb-4 shadow-sm" 
                                    value="<?= isset($user) ? $user->login() : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                            <div class="col-md-8">
                                <input type="email" name="email" id="email" class="form-control mb-4 shadow-sm" 
                                    value="<?= isset($user) ? $user->email() : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label">Nom</label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control mb-4 shadow-sm" 
                                    value="<?= isset($user) ? $user->name() : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                            <div class="col-md-8">
                                <input type="text" name="surname" id="surname" class="form-control mb-4 shadow-sm" 
                                    value="<?= isset($user) ? $user->surname() : "" ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label">Rôle par défaut</label>
                            <div class="col-md-8">
                                <select name="role" id="role" class="custom-select form-control shadow-sm">
                                    <option value="5" <?= isset($user) && $user->role() == 5 ? "selected" : "" ?>>Abonné</option>
                                    <option value="4" <?= isset($user) && $user->role() == 4 ? "selected" : "" ?>>Contributeur</option>
                                    <option value="3" <?= isset($user) && $user->role() == 3 ? "selected" : "" ?>>Auteur</option>
                                    <option value="2" <?= isset($user) && $user->role() == 2 ? "selected" : "" ?>>Editeur</option>
                                    <option value="1" <?= isset($user) && $user->role() == 1 ? "selected" : "" ?>>Administrateur</option>
                                </select>
                            </div>
                        </div> 
                        <br />
                        <div class="form-group row">
                            <label for="pass" class="col-md-4 col-form-label">Mot de passe</label>
                            <div class="col-md-8">
                                <div class="div-user-pass">
                                    <input type="password" name="pass" id="pass" class="form-control mb-4 shadow-sm"
                                    value="<?= isset($user) ? $user->pass() : $token ?>">
                                    <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <input type="submit" value="Envoyer" id="validation" class="btn btn-blue shadow">
                                </div>
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
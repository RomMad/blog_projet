<?php $title = "Jean Forteroche | Le blog - Connexion" ?>

<?php ob_start(); ?>

<div class="container">

<nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
</nav>

    <section id="profil" class="row">

        <div class="col-sm-12 col-md-12 col-lg-12 mx-auto">

            <?php $session->flash(); // Message en session flash ?>
            

            <div class="row">
        
                <div class="col-md-6 mt-4">
                    <form action="index.php?action=profil" method="post" class="col-md-12 card shadow">
                        <div class="form-group row">
                            <h2 class="card-header col-md-12 h2 bg-light text-dark">Profil</h2>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="login" class="col-md-4 col-form-label">Login</label>
                                    <div class="col-md-8">
                                        <input type="text" name="login" id="login" class="form-control mb-4" 
                                            value="<?= $user->login() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                    <div class="col-md-8">
                                        <input type="text" name="email" id="email" class="form-control mb-4" 
                                            value="<?= $user->email() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-md-4 col-form-label">Nom</label>
                                    <div class="col-md-8">
                                        <input type="text" name="name" id="name" class="form-control mb-4"
                                            value="<?= $user->name() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                    <div class="col-md-8">
                                        <input type="text" name="surname" id="surname" class="form-control mb-4"
                                            value="<?= $user->surname() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                    <div class="col-md-5">
                                        <input type="date" name="birthdate" id="birthdate" class="form-control mb-4"
                                            value="<?= $user->birthdate() ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="role" class="col-md-4 col-form-label">Rôle</label>
                                    <div class="col-md-5">
                                            <input type="text" name="role" id="role" class="form-control mb-4" readonly
                                            value="<?= $user->role_user() ?>">
                                    </div>
                                </div> 
                                <div class="row">
                                    <label for="pass" class="col-md-4 col-form-label mt-4">Mot de passe</label>
                                    <div class="col-md-5">
                                        <div class="div-user-pass">
                                            <input type="password" name="pass" id="pass" class="form-control mt-4 mb-4" >
                                            <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                    <div class="col-md-5">
                                        <div class="div-user-pass">
                                            <input type="password" name="pass_confirm" id="pass_confirm" class="form-control mb-4" >
                                            <div id="showConfirmPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <input type="submit" value="Mettre à jour" id="updateInfo" class="btn btn-blue shadow">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-6 offset-lg-1 col-lg-5 mt-4">
                    <form action="index.php?action=profil" method="post" class="col-md-12 card shadow">
                        <div class="form-group row">
                            <h2 class="card-header col-md-12 h2 bg-light text-dark">Mot de passe</h2>
                        </div>
                        <div class="row">
                            <label for="old_pass" class="col-md-6 col-form-label">Ancien mot de passe</label>
                            <div class="col-md-6">
                                <input type="password" name="old_pass" id="old_pass" class="form-control mb-4" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="new_pass" class="col-md-6 col-form-label">Nouveau mot de passe</label>
                                    <div class="col-md-6">
                                        <div class="div-user-pass">
                                            <input type="password" name="new_pass" id="new_pass" class="form-control mb-4">
                                            <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="new_pass_confirm" class="col-md-6 col-form-label">Confirmation nouveau mot de passe</label>
                                    <div class="col-md-6">
                                        <div class="div-user-pass">
                                            <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="form-control mb-4">
                                            <div id="showPassword" class="icon-eye"><span class="fas fa-eye"></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <input type="submit" value="Mettre à jour" id="updatePassword"
                                                class="btn btn-blue shadow">
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

            </div>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
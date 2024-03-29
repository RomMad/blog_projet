<?php  $title = $_SESSION["settings"]->blog_name() . " - Profil" ?>

<?php ob_start(); ?>

<div class="container">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
    </nav>

    <section id="profil" class="row">
        <div class="col-md-12 mx-auto mb-4">

            <?= $this->_session->flash() ?>

            <div class="row">
            
                <div class="col-md-6 mb-4">
                    <form action="profil" method="post" class="col-md-12 card h-100 shadow">
                        <div class="form-group row">
                            <h2 class="card-header col-md-12 h2 bg-light text-dark">Profil</h2>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="login" class="col-md-4 col-form-label">Login</label>
                                    <div class="col-md-8">
                                        <input type="text" name="login" id="login" class="form-control mb-4"  required value="<?= $this->_user->login() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                                    <div class="col-md-8">
                                        <input type="text" name="email" id="email" class="form-control mb-4" required value="<?= $this->_user->email() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="name" class="col-md-4 col-form-label">Nom</label>
                                    <div class="col-md-8">
                                        <input type="text" name="name" id="name" class="form-control mb-4" required value="<?= $this->_user->name() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                                    <div class="col-md-8">
                                        <input type="text" name="surname" id="surname" class="form-control mb-4" required value="<?= $this->_user->surname() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                                    <div class="col-md-5">
                                        <input type="date" name="birthdate" id="birthdate" class="form-control mb-4" value="<?= $this->_user->birthdate() ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="role" class="col-md-4 col-form-label">Rôle</label>
                                    <div class="col-md-5">
                                        <input type="text" name="role" id="role" class="form-control mb-4" readonly value="<?= $this->_user->role_user() ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="pass" class="col-md-4 col-form-label mt-4">Mot de passe</label>
                                    <div class="col-md-5">
                                        <div class="password-group">
                                            <input type="password" name="pass" id="pass" class="password form-control mt-4 mb-4" required>
                                            <span class="show-password fas fa-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="pass_confirm" class="col-md-4 col-form-label">Confirmation mot de passe</label>
                                    <div class="col-md-5">
                                        <div class="password-group">
                                            <input type="password" name="pass_confirm" id="pass_confirm" class="password form-control mb-4" required>
                                            <span class="show-password fas fa-eye"></span>
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

                <div class="col-md-6 mb-4">
                    <form action="profil" method="post" class="col-md-12 card h-100 shadow">
                        <div class="form-group row">
                            <h2 class="card-header col-md-12 h2 bg-light text-dark">Mot de passe</h2>
                        </div>
                        <div class="row">
                            <label for="old_pass" class="col-md-6 col-form-label">Ancien mot de passe</label>
                            <div class="col-md-6">
                                <div class="password-group">
                                    <input type="password" name="old_pass" id="old_pass" class="password form-control mb-4" required>
                                    <span class="show-password fas fa-eye"></span>
                                </div>
                            </div>  
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="new_pass" class="col-md-6 col-form-label">Nouveau mot de passe</label>
                                    <div class="col-md-6">
                                        <div class="password-group">
                                            <input type="password" name="new_pass" id="new_pass" class="password form-control mb-4" required>
                                            <span class="show-password fas fa-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="new_pass_confirm" class="col-md-6 col-form-label">Confirmation nouveau mot de passe</label>
                                    <div class="col-md-6">
                                        <div class="password-group">
                                            <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="password form-control mb-4" required>
                                            <span class="show-password fas fa-eye"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="float-right">
                                            <input type="submit" value="Mettre à jour" id="updatePassword" class="btn btn-blue shadow">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-4">
                    <a href="profil-delete_cookies" class="ml-2 text-blue" onclick="if(window.confirm('Voulez-vous vraiment supprimer vos cookies ?', 'Demande de confirmation')){return true;}else{return false;}">
                        Supprimer tous les cookies</a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php $content = ob_get_clean(); ?>

<?php $script ="<script> seePassword = new SeePassword(); </script>"; ?>

<?php require "view/template.php"; ?>
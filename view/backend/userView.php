<?php  $title = $_SESSION["settings"]->blog_name() . " - Utilisateur" ?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item"><a href="users" class="text-blue">Gestion des utilisateurs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Utilisateur</li>
        </ol>
    </nav>

    <section id="profil" class="row height-full justify-content-md-center">

        <div class="col-sm-10 col-md-8 col-lg-6 mx-auto mt-4">

            <?php $this->_session->flash(); // Message en session flash ?>

            <form action="user-<?= $this->_user->id() ?>" method="post" class="col-md-12 card shadow">
                <div class="form-group row">
                    <h2 class="card-header col-md-12 h2 bg-light text-dark">Utilisateur</h2>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="row">
                            <label for="login" class="col-md-4 col-form-label">Login</label>
                            <div class="col-md-8">
                                <input type="text" name="login" id="login" class="form-control mb-4" readonly value="<?= $this->_user->login() ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="email" class="col-md-4 col-form-label">Adresse email</label>
                            <div class="col-md-8">
                                <input type="text" name="email" id="email" class="form-control mb-4" readonly value="<?= $this->_user->email() ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="name" class="col-md-4 col-form-label">Nom</label>
                            <div class="col-md-8">
                                <input type="text" name="name" id="name" class="form-control mb-4" readonly value="<?= $this->_user->name() ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="surname" class="col-md-4 col-form-label">Prénom</label>
                            <div class="col-md-8">
                                <input type="text" name="surname" id="surname" class="form-control mb-4" readonly value="<?= $this->_user->surname() ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="birthdate" class="col-md-4 col-form-label">Date de naissance</label>
                            <div class="col-md-6">
                                <input type="date" name="birthdate" id="birthdate" class="form-control mb-4" readonly value="<?= $this->_user->birthdate() ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="role" class="col-md-4 col-form-label">Rôle</label>
                            <div class="col-md-6">
                                <select name="role" id="role" class="form-control shadow-sm" required>
                                    <option value="5" <?= $this->_user->role() == 5 ? "selected" : "" ?>>Abonné</option>
                                    <option value="4" <?= $this->_user->role() == 4 ? "selected" : "" ?>>Contributeur</option>
                                    <option value="3" <?= $this->_user->role() == 3 ? "selected" : "" ?>>Auteur</option>
                                    <option value="2" <?= $this->_user->role() == 2 ? "selected" : "" ?>>Editeur</option>
                                    <option value="1" <?= $this->_user->role() == 1 ? "selected" : "" ?>>Administrateur</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <input type="submit" value="Mettre à jour" id="updateInfo"
                                        class="btn btn-blue shadow">
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
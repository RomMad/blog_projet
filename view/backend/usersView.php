<?php  $title = $_SESSION["blog_name"] . " - Gestion des utilisateurs"; ?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item"><a href="settings" class="text-blue">Administration</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestion des utilisateurs</li>
        </ol>
    </nav>

    <div class="row height-full">
        <section id="table-admin_users" class="col-md-12 table-admin">

            <h2 class="mb-4">Gestion des utilisateurs
                <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
            </h2>

            <?php 
            $session->flash(); // Message en session flash

            // Affiche les résultats si recherche
            if (isset($_POST["filter"]) || isset($_POST["filter_search"])) {
                echo "<p> " . $nbItems . " résultat(s).</p>";
            }    
            ?>

            <form action="<?= $linkNbDisplayed ?>" method="post" class="">
                <div class="row">

                    <div class="col-md-4 form-inline mb-2 px-lg-3">
                        <label class="sr-only col-form-label" for="action">Action</label>
                        <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow" value="Par auteur">
                            <option value="">-- Action --</option>
                            <option value="delete">Supprimer</option>
                        </select>
                        <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK"
                            onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                    </div>

                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label class="sr-only col-form-label" for="filter_role">Filtre</label>
                        <select name="filter_role" id="filter_role" class="custom-select form-control mr-1 shadow"
                            value="Par auteur">
                            <option <?= $_SESSION["filter_role"] == NULL ? "selected" : "" ?> value="">-- Rôle --</option>
                            <option <?= $_SESSION["filter_role"] == 1 ? "selected" : "" ?> value="1">Administrateur</option>
                            <option <?= $_SESSION["filter_role"] == 2 ? "selected" : "" ?> value="2">Editeur</option>
                            <option <?= $_SESSION["filter_role"] == 3 ? "selected" : "" ?> value="3">Auteur</option>
                            <option <?= $_SESSION["filter_role"] == 4 ? "selected" : "" ?> value="4">Contributeur</option>
                            <option <?= $_SESSION["filter_role"] == 5 ? "selected" : "" ?> value="5">Abonné</option>
                        </select>
                        <input type="submit" id="filter" name="filter" alt="Filtrer"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="Filtrer">
                    </div>
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label for="search_user" class="sr-only col-form-label">Recherche</label>
                        <input type="search" name="search_user" id="search_user" class="form-control px-md-1 mr-1 shadow" 
                            placeholder="Recherche" aria-label="Search" value="<?= $_SESSION["filter_search"] ?>">
                        <input type="submit" id="filter_search" name="filter_search" alt="filter_search"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered table-striped table-hover shadow">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="align-middle">
                                        <input type="checkbox" name="allselectedUsers" id="all-checkbox" />
                                        <label for="allselectedUsers" class="sr-only">Tout sélectionner</label>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="users-orderBy-login-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Login
                                        <?php 
                                        if ($orderBy == "login") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="users-orderBy-name-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Nom
                                        <?php 
                                        if ($orderBy == "name") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="users-orderBy-surname-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Prénom
                                        <?php 
                                        if ($orderBy == "surname") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="users-orderBy-email-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Email
                                        <?php 
                                        if ($orderBy == "email") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="users-orderBy-role-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Rôle
                                        <?php 
                                        if ($orderBy == "role") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="users-orderBy-registration_date-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Date d'enregistrement
                                        <?php 
                                        if ($orderBy == "registration_date") {
                                        ?>
                                            <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                        }
                                        ?>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            if ($nbItems) {
                                foreach ($users as $user) {
                            ?>
                                <tr>
                                    <th scope="row">
                                        <input type="checkbox" name="selectedUsers[]" id="User<?= $user->id() ?>" value="<?= $user->id() ?>" class="" />
                                        <label for="selectedUsers[]" class="sr-only">Sélectionner</label>
                                    </th>
                                    <td><a href="user-<?= $user->id() ?>" class="text-blue"><?= $user->login() ?></a></td>
                                    <td><?= $user->name() ?></td>
                                    <td><?= $user->surname() ?></td>
                                    <td><?= $user->email() ?></td>
                                    <td><?= $user->role_user() ?></td>
                                    <td><?= $user->registration_date("") ?></td>
                                </tr>
                                <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            <?php $pagination->view(); ?>

        </section>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
<?php  $title = $_SESSION["settings"]->blog_name() . " - Gestion des articles"; ?>

<?php ob_start(); ?>

<div class="container">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item"><a href="settings" class="text-blue">Administration</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestion des articles</li>
        </ol>
    </nav>

    <div class="row min-vh-80">
        <section id="table_admin_posts" class="col-md-12 table-admin">
            <h2 class="mb-4">Gestion des articles
                <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
            </h2>

            <?php 
            $this->_session->flash();
            // Affiche les résultats si recherche
            if (isset($_POST["filter"]) || isset($_POST["filter_search"])) { echo "<p> " . $nbItems . " résultat(s).</p>";}    
            ?>

            <form action="<?= $linkNbDisplayed ?>" method="post">
                <div class="row">
                    <?php if ($_SESSION["user"]["role"] < 3) { ?>
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label class="sr-only col-form-label" for="action">Action</label>
                        <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow" value="Par auteur">
                            <option value="">-- Action --</option>
                            <option value="Brouillon">Mettre en brouillon</option>
                            <option value="Publié">Publier</option>
                            <option value="delete">Supprimer</option>
                        </select>
                        <input type="submit" id="apply" name="apply" alt="Appliquer"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK"
                            onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                    </div>
                    <?php } ?>
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label class="sr-only col-form-label" for="filter_status">Filtre</label>
                        <select name="filter_status" id="filter_status" class="custom-select form-control mr-1 shadow"
                            value="Par auteur">
                            <option <?= empty($_SESSION["filter_status"]) ? "selected" : "" ?> value="">-- Statut --</option>
                            <option <?= $_SESSION["filter_status"] == "brouillon" ? "selected" : "" ?> value="brouillon">Brouillon</option>
                            <option <?= $_SESSION["filter_status"] == "publié" ? "selected" : "" ?> value="publié">Publié</option>
                        </select>
                        <input type="submit" id="filter" name="filter" alt="Filtrer"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                    </div>
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label for="search_post" class="sr-only col-form-label">Recherche</label>
                        <input type="search" name="search_post" id="search_post"
                            class="form-control mr-1 px-md-1 shadow" placeholder="Recherche" aria-label="Search"
                            value="<?= $_SESSION["search_post"] ?>">
                        <input type="submit" id="filter_search" name="filter_search" alt="filter_search"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover shadow">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="align-middle th-width-20px">
                                    <div class="custom-control custom-checkbox" data-toggle="tooltip" data-placement="top" title="Tout sélectionner">
                                        <input type="checkbox" name="select-all" id="select-all" class="custom-control-input" />
                                        <label class="custom-control-label" for="select-all"><span class="sr-only">Tout sélectionner<span></label>
                                    </div>
                                </th>
                                <th scope="col" class="align-middle">
                                    <a href="posts-orderBy-title-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Titre
                                        <?php if ($orderBy == "title") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle th-width-150px">
                                    <a href="posts-orderBy-user_login-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Auteur
                                        <?php if ($orderBy == "user_login") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle th-width-100px">
                                    <a href="posts-orderBy-status-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Statut
                                        <?php if ($orderBy == "status") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle th-width-100px">
                                    <a href="posts-orderBy-creation_date-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de création
                                        <?php if ($orderBy == "creation_date") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle th-width-120px">
                                    <a href="posts-orderBy-update_date-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de mise<br />à jour
                                        <?php if ($orderBy == "update_date") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                    </a>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        if ($nbItems) {
                            foreach ($posts as $post) {
                            ?>
                            <tr>
                                <th scope="row">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input checkbox" name="selectedPosts[]" id="post-<?= $post->id() ?>"
                                            value="<?= $post->id() ?>" class="checkbox" />
                                        <label class="custom-control-label" for="post-<?= $post->id() ?>"><span class="sr-only">Sélectionner<span></label>
                                    </div>
                                </th>
                                <td><a href="post-<?= $post->id() ?>"
                                        class="text-blue font-weight-bold"><?= $post->title() ?></a></td>
                                <td><?= $post->login() ?></td>
                                <td><?= $post->status() ?></td>
                                <td><?= $post->creation_date("") ?></td>
                                <td><?= $post->update_date("") ?></td>
                            </tr>
                        <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </form>
            <?php $this->_pagination->view(TRUE, TRUE); ?> <!-- Ajoute la barre de pagination -->
        </section>
    </div>
</div>

<?php $script ="<script> selectAllCheckboxes = new SelectAllCheckboxes() </script>"; ?>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
<?php  $title = "Jean Forteroche | Le blog - Gestion des articles"; ?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="index.php" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item"><a href="index.php?action=settings" class="text-blue">Administration</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestion des articles</li>
        </ol>
    </nav>

    <div class="row">
        <section id="table_admin_posts" class="col-md-12 mt-4 table-admin">

            <h2 class="mb-4">Gestion des articles
                <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
            </h2>

            <?php 
            $session->flash(); // Message en session flash

            // Affiche les résultats si recherche
            if (isset($_POST["filter"]) || isset($_POST["filter_search"])) {
                echo "<p> " . $nbItems . " résultat(s).</p>";
            }    
            ?>

            <form action="<?= $linkNbDisplayed ?>" method="post">
                <div class="row">
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label class="sr-only col-form-label" for="action">Action</label>
                        <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow"
                            value="Par auteur">
                            <option value="">-- Action --</option>
                            <option value="Brouillon">Mettre en brouillon</option>
                            <option value="Publié">Publier</option>
                            <option value="delete">Supprimer</option>
                        </select>
                        <input type="submit" id="apply" name="apply" alt="Appliquer"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK"
                            onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                    </div>
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label class="sr-only col-form-label" for="filter_status">Filtre</label>
                        <select name="filter_status" id="filter_status" class="custom-select form-control mr-1 shadow"
                            value="Par auteur">
                            <option value="">-- Statut --</option>
                            <option value="brouillon">Brouillon</option>
                            <option value="publié">Publié</option>
                        </select>
                        <input type="submit" id="filter" name="filter" alt="Filtrer"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="Filtrer">
                    </div>
                    <div class="col-md-4 form-inline mb-2 px-md-1 px-lg-3">
                        <label for="search_post" class="sr-only col-form-label">Recherche</label>
                        <input type="search" name="search_post" id="search_post"
                            class="form-control mr-1 px-md-1 shadow" placeholder="Recherche" aria-label="Search"
                            value="<?= $_SESSION["filter_search"] ?>">
                        <input type="submit" id="filter_search" name="filter_search" alt="filter_search"
                            class="btn btn-blue px-lg-3 px-md-2 py-1 shadow" value="OK">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover shadow">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" class="align-middle"><input type="checkbox" name="allSelectedPosts"
                                        id="all-checkbox" /><label for="allSelectedPosts"></label></th>
                                <th scope="col" class="align-middle">
                                    <a href="index.php?action=posts&orderBy=title&order=<?= $order == "desc" ? "asc" : "desc" ?>"
                                        class="sorting-indicator text-white">Titre
                                        <?php 
                                if ($orderBy == "title") {
                                ?>
                                        <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                }
                                ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle">
                                    <a href="index.php?action=posts&orderBy=user_name&order=<?= $order == "desc" ? "asc" : "desc" ?>"
                                        class="sorting-indicator text-white">Auteur
                                        <?php 
                                if ($orderBy == "user_name") {
                                ?>
                                        <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                }
                                ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle">
                                    <a href="index.php?action=posts&orderBy=status&order=<?= $order == "desc" ? "asc" : "desc" ?>"
                                        class="sorting-indicator text-white">Statut
                                        <?php 
                                if ($orderBy == "status") {
                                ?>
                                        <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                }
                                ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle">
                                    <a href="index.php?action=posts&orderBy=creation_date&order=<?= $order == "desc" ? "asc" : "desc" ?>"
                                        class="sorting-indicator text-white">Date de création
                                        <?php 
                                if ($orderBy == "creation_date") {
                                ?>
                                        <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span>
                                        <?php   
                                }
                                ?>
                                    </a>
                                </th>
                                <th scope="col" class="align-middle">
                                    <a href="index.php?action=posts&orderBy=update_date&order=<?= $order == "desc" ? "asc" : "desc" ?>"
                                        class="sorting-indicator text-white">Date de mise à jour
                                <?php 
                                if ($orderBy == "update_date") {
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
                        foreach ($posts as $post) {
                    ?>
                            <tr>
                                <th scope="row">
                                    <input type="checkbox" name="selectedPosts[]" id="post<?= $post->id() ?>"
                                        value="<?= $post->id() ?>" class="" />
                                    <label for="selectedPosts[]" class="sr-only">Sélectionné</label>
                                </th>
                                <td><a href="index.php?action=post&id=<?= $post->id() ?>"
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

            <?php $pagination->view(); ?> <!-- Ajoute la barre de pagination -->

        </section>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
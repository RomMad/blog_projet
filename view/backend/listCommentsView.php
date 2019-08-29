<?php  $title = $_SESSION["settings"]->blog_name() . " - Gestion des commentaires"; ?>

<?php ob_start(); ?>

<div class="container">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Accueil</a></li>
            <li class="breadcrumb-item"><a href="settings" class="text-blue">Administration</a></li>
            <li class="breadcrumb-item active" aria-current="page">Gestion des commentaires</li>
        </ol>
    </nav>

    <div class="row min-vh-80">
        <section id="table-admin_comments" class="col-md-12 table-admin">
            <h2 class="mb-4">Gestion des commentaires
                <span class="badge badge-secondary font-weight-normal"><?= $nbItems ?> </span>
            </h2>

            <?= $this->_session->flash() ?>

            <form action="<?= $linkNbDisplayed ?>" method="post">
                <div class="row">

                    <div class="col-md-6 mb-2">
                        <label class="sr-only col-form-label" for="action">Action</label>
                        <select name="action_apply" id="action_apply" class="custom-select form-control mr-1 shadow"
                            value="Par auteur">
                            <option value="">-- Action --</option>
                            <option value="moderate">Modérer</option>
                            <option value="delete">Supprimer</option>
                        </select>
                        <input type="submit" id="apply" name="apply" alt="Appliquer" class="btn btn-blue py-1 shadow"
                            value="Appliquer"
                            onclick="if(window.confirm('Confirmer l\'action ?')){return true;}else{return false;}">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="sr-only col-form-label" for="filter_status">Filtre</label>
                        <select name="filter_status" id="filter_status" class="custom-select form-control mr-1 shadow"
                            value="Par auteur">
                            <option value="">-- Statut --</option>
                            <option <?= $_SESSION["filter_status"] == 1 ? "selected" : "" ?> value="1">Non-modéré</option>
                            <option <?= $_SESSION["filter_status"] == 2 ? "selected" : "" ?> value="2">Modéré</option>
                            <option <?= $_SESSION["filter_status"] == 3 ? "selected" : "" ?> value="3">Signalé</option>
                        </select>
                        <input type="submit" id="filter" name="filter" alt="Filtrer" class="btn btn-blue py-1 shadow"
                            value="Filtrer">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered table-striped table-hover shadow">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col" class="align-middle th-width-20px">
                                        <div class="custom-control custom-checkbox" data-toggle="tooltip" data-placement="top" title="Tout sélectionner">
                                            <input type="checkbox" name="select-all" id="select-all" class="custom-control-input"/>
                                            <label class="custom-control-label" for="select-all"><span class="sr-only">Tout sélectionner<span></label>
                                        </div>
                                    </th>
                                    <th scope="col" class="align-middle">
                                        <a href="comments-orderBy-content-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Contenu du commentaire
                                            <?php if ($orderBy == "content") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle th-width-120px">
                                        <a href="comments-orderBy-user_name-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Auteur
                                            <?php if ($orderBy == "user_name") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle th-width-100px">
                                        <a href="comments-orderBy-status-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Statut
                                            <?php if ($orderBy == "status") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle th-width-130px">
                                        <a href="comments-orderBy-report_date-order-<?= $order == "desc" ? "asc" : "desc" ?>"
                                            class="sorting-indicator text-white">Date de signalement
                                            <?php if ($orderBy == "report_date") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle th-width-130px">
                                        <a href="comments-orderBy-nb_report-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Nb de signalements
                                            <?php if ($orderBy == "nb_report") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                        </a>
                                    </th>
                                    <th scope="col" class="align-middle th-width-100px">
                                        <a href="comments-orderBy-creation_date-order-<?= $order == "desc" ? "asc" : "desc" ?>" class="sorting-indicator text-white">Date de création
                                            <?php if ($orderBy == "creation_date") { ?> <span class="fas fa-caret-<?= $order == "desc" ? "up" : "down" ?>"></span> <?php } ?>
                                        </a>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            if ($nbItems) {
                                foreach ($comments as $comment) {
                                ?>
                                <tr>
                                    <th scope="row">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input checkbox" name="selectedComments[]" id="comment-<?= $comment->id() ?>"
                                                value="<?= $comment->id() ?>" class="checkbox" />
                                            <label class="custom-control-label" for="comment-<?= $comment->id() ?>"><span class="sr-only">Sélectionner<span></label>
                                        </div>
                                    </th>
                                    <td><a href="post-<?= $comment->post_id() ?>" class="text-dark"><?= $comment->content() ?></a></td>
                                    <td>
                                    <?php 
                                    if (!empty($comment->user_name())) {
                                        echo $comment->user_name();
                                    } elseif (!empty($comment->login())) {
                                        echo $comment->login();
                                    } else {
                                        echo "Anonyme";
                                    }
                                    ?>
                                    </td>
                                    <td>
                                    <?php 
                                    switch($comment->status()) {
                                        case 1:
                                        echo "Non-modéré";
                                        break;
                                        case 2:
                                        echo "Modéré";
                                        break;
                                        case 3:
                                        echo "Signalé";
                                        break;
                                        default:
                                        echo "Non-modéré";
                                    }
                                    ?>
                                    </td>
                                    <td><?= $comment->report_date("datetime_fr") ?></td>
                                    <td><?= $comment->nb_report() ?></td>
                                    <td><?= $comment->creation_date("datetime_fr") ?></td>
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
            <?php $this->_pagination->view(); ?> <!-- Ajoute la barre de pagination -->
        </section>
    </div>
</div>

<?php $script ="<script> selectAllCheckboxes = new SelectAllCheckboxes() </script>"; ?>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
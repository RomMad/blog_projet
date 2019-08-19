<?php  $title = $_SESSION["blog_name"] . " - " . $post->title(); ?>

<?php ob_start(); ?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">Article</li>
        </ol>
    </nav>
    <!-- Affichage de l'article -->
    <section id="post">
        <div class="card shadow">
            <div class="card-header bg-dark text-light">
                <h2 class="mt-2 mb-3"><?= $post->title() ?></h2>
                <p class="my-0">
                    <em>Créé le <?= $post->creation_date("special_format") ?> par <a class="text-blue" 
                        href="user-<?= $post->user_id() ?>"><?= $post->login() ?></a> 
                        (Modifié le <?= $post->update_date("special_format") ?>)</em>
                    <a href="#comments" class="badge badge-blue ml-2 my-1 py-1" data-toggle="tooltip"
                        data-placement="bottom" title="Voir les commentaires">Commentaires 
                        <span class="badge badge-light"><?= $nbItems ?></span></a>
                </p>
                <?php
                if (isset($_SESSION["userID"]) && $_SESSION["userID"]== $post->user_id()) {
                ?>
                <a class="text-blue a-edit-post m-1" href="edit-post-<?= $post->id() ?>" 
                    data-toggle="tooltip" data-placement="bottom" title="Modifier l'article">
                    <span class="far fa-edit"></span> Modifier</a>
                <?php 
                } 
                ?>
            </div>
            <div id="post-content" class="card-body text-body">
                <?= $post->content("html_format") ?>
            </div>
        </div>
        <?php 
        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$post->user_id()) {
        ?>
        <a class="text-blue" href="edit-post-<?= $post_id ?>"><span class="far fa-edit"></span>Modifier l'article</a>
        <?php 
        } 
        ?>
    </section>

    <!-- Formulaire d'ajout d'un commentaire -->
    <section id="form-comment" class="mt-4">

        <?php $session->flash(); // Message en session flash ?>

        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-6">
                <h3 class="mb-4">Nouveau commentaire</h3>
                <div class="row">
                    <div class="col-md-12">
                        <form action="post-<?= $post_id ?>#form-comment" method="post" class="px-3">
                            <?php 
                            if (!isset($_SESSION["userID"])) {
                            ?>
                            <div class="row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <input type="text" name="name" id="name" class="col-md-8 form-control mb-4 shadow-sm"
                                    value="">
                            </div>
                            <?php
                            }
                            ?>
                            <div class="form-group row">
                                <label for="content" class="sr-only">Contenu du message</label>
                                <textarea name="content" class="col-md-12 form-control shadow-sm" id="content"
                                    rows="4"></textarea>
                            </div>
                            <div class="form-group row float-right">
                                <input type="submit" value="Envoyer" name="save_comment" id="save_comment"
                                    class="btn btn-blue shadow">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Affiche les commentaires -->
    <section id="comments">
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-6 mt-2">

                <h3 class="mb-4">Commentaires</h3>
                <?php 
                if (!isset($comments)) {
                    echo "Aucun commentaire.";
                } else {
                    $pagination->view(TRUE, TRUE); // Ajoute la barre de pagination
                    // Récupère les messages
                    foreach ($comments as $comment) {
                    ?>
                <div id="comment-<?= $comment->id() ?>" class="comment card shadow">
                    <div class="card-body">
                    <?php 
                    if (!empty($comment->login())) {
                        $user_login = $comment->login();
                    } else {
                        if (!empty($comment->user_name())) {
                            $user_login = $comment->user_name();
                        } else {
                            $user_login = "Anonyme";
                        }
                    }
                    ?>
                        <p><strong><?= $user_login ?></strong>, le <?= $comment->creation_date("special_format") ?>
                            <?php if ($comment->update_date("") != $comment->creation_date("")) { echo "(Modifié le " . $comment->update_date("special_format") . ")"; } ?>
                        </p>
                        <div class="comment-content position relative"><?= nl2br($comment->content()) ?>
                            <span class="comment-fade-out d-none"></span>
                        </div>
                        <?php
                        if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$comment->user_id()) {
                        ?>
                        <div>
                            <a href="post-<?= isset($post_id) ? $post_id : "" ?>-comment-<?= $comment->id() ?>-delete#form-comment"
                                onclick="if(window.confirm('Voulez-vous vraiment supprimer ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                <span class="fas fa-times text-danger" data-toggle="tooltip" data-placement="bottom" title="Supprimer le commentaire"></span>
                            </a>
                        </div>
                        <?php
                        } else {
                            if($comment->status()==2) {
                        ?>
                        <div class="report-comment"><span class="fas fa-flag text-danger"></span></div>
                        <?php
                            } else {
                        ?>
                        <div class="report-comment">
                            <a href="post-<?= isset($post_id) ? $post_id : "" ?>-comment-<?= $comment->id() ?>-report#form-comment"
                                onclick="if(window.confirm('Voulez-vous vraiment signaler ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                <span class="far fa-flag text-warning" data-toggle="tooltip" data-placement="bottom" title="Signaler le commentaire"></span>
                            </a>
                        </div>
                        <?php
                            }
                        }
                        ?>
                        <?php
                        if (isset($_SESSION["userID"]) && $_SESSION["userID"] == $comment->user_id()) {
                        ?>
                        <div class="edit-comment mt-3">
                            <a href="#comment-<?= $comment->id() ?>"><span class="far fa-edit text-blue">
                                    Modifier</span></a>
                        </div>
                        <?php
                        }
                        ?>
                        <div id="form-edit-comment-<?= $comment->id() ?>" class="form-edit-comment d-none">
                            <form
                                action="post-<?= $post_id ?>-comment-<?= $comment->id() ?>#form-comment"
                                method="post">
                                <div class="form-group">
                                    <label for="comment-content-<?= $comment->id() ?>" class="sr-only">Contenu du message</label>
                                    <textarea name="comment-content-<?= $comment->id() ?>" class="form-control shadow-sm" id="comment-content-<?= $comment->id() ?>"
                                        rows="4"><?= $comment->content() ?></textarea>
                                </div>
                                <div class="form-group float-right">
                                    <input type="submit" value="Envoyer" name="editComment"
                                        id="edit-<?= $comment->id() ?>" class="btn btn-blue shadow">
                                    <button value="Annuler" id="cancel_edit-comment-<?= $comment->id() ?>"
                                        class="cancel-edit-comment btn btn-secondary shadow">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                    }
                    $pagination->view(FALSE, TRUE); // Ajoute la barre de pagination
                }
                ?>
            </div>
        </div>
    </section>
</div>

<?php $script ="<script> comments = new Comments() </script>"; ?>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
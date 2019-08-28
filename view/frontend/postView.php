<?php  $title = $_SESSION["settings"]->blog_name() . " - " .  $this->_post->title(); ?>

<?php ob_start(); ?>

<div class="container">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent">
            <li class="breadcrumb-item"><a href="blog" class="text-blue">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">Article</li>
        </ol>
    </nav>
    
    <!-- Affichage de l'article -->
    <section id="post">
        <div class="card shadow">
            <div class="card-header bg-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "dark" ?> text-<?= $_SESSION["settings"]->style_blog() == "light" ? "dark" : "light" ?>">
                <h2 class="mt-2 mb-3"><?=  $this->_post->title() ?></h2>
                <p class="my-0">
                    <em>Publié le <?=  $this->_post->publication_date("datetime_special") ?> par <a class="text-blue" 
                        href="user-<?=  $this->_post->user_id() ?>"><?=  $this->_post->login() ?></a> 
                        (Modifié le <?=  $this->_post->update_date("datetime_special") ?>)</em>
                    <a href="#comments" class="badge badge-blue ml-2 my-1 py-1" data-toggle="tooltip"
                        data-placement="bottom" title="Voir les commentaires">Commentaires 
                        <span class="badge badge-light"><?= $nbItems ?></span></a>
                </p>
                <?php
                if (isset($_SESSION["user"]) && ($_SESSION["user"]["role"] <= 2 || $_SESSION["user"]["id"] ==  $this->_post->user_id())) {
                ?>
                <a class="text-blue a-edit-post m-1" href="edit-post-<?=  $this->_post->id() ?>" 
                    data-toggle="tooltip" data-placement="bottom" title="Modifier l'article">
                    <span class="far fa-edit"></span></a>
                <?php 
                } 
                ?>
            </div>
            <div id="post-content" class="card-body text-body">
                <?=  $this->_post->content("html_format") ?>
            </div>
        </div>
        <?php 
        if (isset($_SESSION["user"]) && ($_SESSION["user"]["role"] <= 2 || $_SESSION["user"]["id"] ==  $this->_post->user_id())) {
        ?>
        <a class="ml-2 text-blue" href="edit-post-<?= $this->_post->id() ?>"><span class="far fa-edit"></span> Modifier l'article</a>
        <?php 
        } 
        ?>
    </section>

    <!-- Formulaire d'ajout d'un commentaire -->
    <section id="form-comment" class="mt-4">
        <?= $this->_session->flash() ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-6">
                <h3 class="mb-4">Nouveau commentaire</h3>
                <div class="row">
                    <div class="col-md-12">
                        <form action="post-<?= $this->_post->id() ?>#form-comment" method="post" class="px-3">
                            <?php 
                            if (!isset($_SESSION["user"]["id"])) {
                            ?>
                            <div class="row">
                                <label for="name" class="col-md-4 col-form-label">Nom</label>
                                <input type="text" name="name" id="name" class="col-md-8 form-control mb-4 shadow-sm">
                            </div>
                            <?php
                            }
                            ?>
                            <div class="form-group row">
                                <label for="content" class="sr-only">Contenu du message</label>
                                <textarea name="content" id="content" class="col-md-12 form-control shadow-sm" rows="4" 
                                    required><?= isset($_POST["content"]) ? htmlspecialchars($_POST["content"]) : "" ?></textarea>
                            </div>
                            <div class="form-group row float-right">
                                <input type="submit" value="Envoyer" name="save-comment" id="save-comment" class="btn btn-blue shadow">
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
                if (!isset( $this->_comments)) {
                    echo "Aucun commentaire.";
                } else {
                    $this->_pagination->view(TRUE, TRUE); // Ajoute la barre de pagination
                    // Récupère les messages
                    foreach ( $this->_comments as $comment) {
                        // Détermine le nom de l'utilisateur à faire apparaître
                        if (!empty($comment->login())) {
                            $userLogin = $comment->login();
                        } elseif (!empty($comment->user_name())) {
                            $userLogin = $comment->user_name();
                        } else {
                            $userLogin = "Anonyme";
                        }
                    ?>
                <div id="comment-<?= $comment->id() ?>" class="comment card shadow">
                    <div class="card-body">
                        <p class="mb-2"><strong><?= $userLogin ?></strong>, le <?= $comment->creation_date("datetime_special") ?>
                            <?php if ($comment->update_date("datetime_fr") != $comment->creation_date("datetime_fr")) { echo "(Modifié le " . $comment->update_date("datetime_special") . ")"; } ?>
                        </p>
                        <hr class="my-2">
                        <div id="comment-content-<?= $comment->id() ?>" class="comment-content position relative"><?= nl2br($comment->content()) ?>
                            <span id="comment-fadeout-<?= $comment->id() ?>" class="comment-fadeout d-none"></span>
                        </div>
                        <?php
                        if (isset($_SESSION["user"]) && ($_SESSION["user"]["role"] == 1 || ($_SESSION["user"]["id"] == $comment->user_id()))) {
                        ?>
                        <div>
                            <a href="post-<?= $this->_post->id() ?>-comment-<?= $comment->id() ?>-delete#form-comment"
                                onclick="if(window.confirm('Voulez-vous vraiment supprimer ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                <span class="fas fa-times text-danger" data-toggle="tooltip" data-placement="bottom" title="Supprimer le commentaire"></span>
                            </a>
                        </div>
                        <?php
                        } elseif ($comment->status() == 3) { ?>
                        <div class="report-comment"><span class="fas fa-flag text-danger" data-toggle="tooltip" data-placement="bottom" title="Le commentaire a été signalé"></span></div>
                        <?php
                        } else { ?>
                        <div class="report-comment">
                            <a href="post-<?= $this->_post->id() ?>-comment-<?= $comment->id() ?>-report#form-comment"
                                onclick="if(window.confirm('Voulez-vous vraiment signaler ce commentaire ?', 'Demande de confirmation')){return true;}else{return false;}">
                                <span class="far fa-flag text-warning" data-toggle="tooltip" data-placement="bottom" title="Signaler le commentaire"></span>
                            </a>
                        </div>
                        <?php
                        }
                        if (isset($_SESSION["user"]) && $_SESSION["user"]["id"] == $comment->user_id()) {
                        ?>
                        <div class="edit-comment mt-3">
                            <a id="comment-edit-<?= $comment->id() ?>" href="#comment-<?= $comment->id() ?>"><span class="far fa-edit text-blue"> Modifier</span></a>
                        </div>
                        <?php
                        }
                        ?>
                        <div id="comment-form-<?= $comment->id() ?>" class="comment-form d-none">
                            <form action="post-<?= $this->_post->id() ?>-comment-<?= $comment->id() ?>#form-comment" method="post">
                                <div class="form-group">
                                    <label for="comment-form-content-<?= $comment->id() ?>" class="sr-only">Contenu du message</label>
                                    <textarea name="comment-form-content-<?= $comment->id() ?>" class="form-control shadow-sm" id="comment-form-content-<?= $comment->id() ?>"
                                        rows="4"><?= $comment->content() ?></textarea>
                                </div>
                                <div class="form-group float-right">
                                    <input type="submit" value="Envoyer" name="edit-comment" id="comment-form-edit-<?= $comment->id() ?>" class="btn btn-blue shadow">
                                    <button value="Annuler" id="comment-form-cancel-<?= $comment->id() ?>" class="comment-form-cancel btn btn-secondary shadow">Annuler</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } $this->_pagination->view(FALSE, TRUE); } ?>
            </div>
        </div>
    </section>
</div>

<?php $script ="<script> comments = new Comments() </script>"; ?>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
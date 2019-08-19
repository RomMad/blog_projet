<?php  $title = $_SESSION["blog_name"]; ?>

<?php ob_start(); ?>

<div class="container">

    <div id="blog" class="row">

        <div class="col-md-12">
        <?php 
        // Vérifie si l'utilisateur a les droits pour écrire un article
        if (isset($_SESSION["user"]["role"]) && $_SESSION["user"]["role"]<5) {
        ?>
            <div class="mt-4 mb-4">
                <a class="text-blue" href="edit-post"><span class="far fa-file"></span> Rédiger un
                    nouvel article</a>
            </div>
        <?php
        }
        // Affiche les résultats si recherche
        if (!empty($_GET["search"])) {
            echo "<p> " . $nbItems . " résultat(s).</p>";
        }    

        $session->flash(); // Message en session flash

        $pagination->view(TRUE, TRUE); // Ajoute la barre de pagination

        ?>
            <div class="row">

            <?php
            if ($nbItems) {
                foreach ($posts as $post) {
                ?>
                <div class="col-md-<?=  isset($settings) ? 12 / $settings->posts_by_row() : 12 ?> mb-4">
                    <div class="card h-100 mb-0 shadow">
                        <div class="card-header bg-dark text-light">
                            <a class="text-blue" href="post-<?= $post->id() ?>">
                                <h3 class="mt-1"><?= $post->title() ?></h3>
                            </a>
                            <em>Créé le <?= $post->creation_date("special_format") ?> par <a class="text-blue"
                                    href="user-<?= $post->user_id() ?>"><?= $post->user_login() ?></a></em>
                            <?php if (isset($_SESSION["user"]) && ($_SESSION["user"]["role"] <= 2 || $_SESSION["user"]["id"] == $post->user_id())) { ?>
                            <a class="text-blue a-edit-post m-1"
                                href="edit-post-<?= $post->id() ?>"><span class="far fa-edit" 
                                data-toggle="tooltip" data-placement="bottom" title="Modifier l'article">
                                </span></a>
                            <?php } ?>
                        </div>
                        <div class="card-body text-body">
                            <div class="post_content"><?= $post->content("raw_format") ?>
                                <?php if (strlen($post->content("raw_format")) > 200) { ?>
                                <!-- Si le contenu est > à 1200 caractères, affiche le bouton 'Continuer la lecture' et ajoute un effet fade out -->
                                <span class="post-fadeout"></span>
                            </div>
                            <div>
                                <a href="post-<?= $post->id() ?>"
                                    class="btn btn-outline-blue mt-2">Continuer la lecture
                                    <span class="fas fa-angle-right"></span>
                                </a>
                                <?php
                            }
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                }
            }
            ?>

            </div>

            <?php $pagination->view(FALSE, TRUE); ?>

            <div class="mt-4 mb-4">
                <a class="text-blue" href="edit-post"><span class="far fa-file"></span> Rédiger un nouvel article</a>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
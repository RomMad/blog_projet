<?php $title = "Jean Forteroche | Le blog"; ?>

<?php ob_start(); ?>

<div class="container">

    <section id="blog" class="row">

        <div class="col-md-12">

        <?php 
        // Vérifie si l'utilisateur a les droits pour écrire un article
        if (isset($_SESSION["userRole"]) && $_SESSION["userRole"]<5) {
        ?> 
            <div class="mt-4 mb-4">
                <a class="text-blue" href="index.php?action=editPost"><span class="far fa-file"></span> Rédiger un nouvel article</a>
            </div>
        <?php
        }
        // Affiche les résultats si recherche
        if (!empty($_GET["search"])) {
            echo "<p> " . $nbItems . " résultat(s).</p>";
        }    

        $session->flash(); // Message en session flash

        $pagination->view(); // Ajoute la barre de pagination

        ?>
        <div class="row">

            <?php
            if ($nbItems) {
                foreach ($posts as $post) {
                ?>
                <div class="col-md-<?=  12 / $settings->posts_by_row() ?>">
                    <div class="card shadow">
                        <div class="card-header bg-dark text-light">
                            <a class="text-blue" href="index.php?action=post&id=<?= $post->id() ?>">
                                <h3 class="mt-1"><?= $post->title() ?></h3>
                            </a>
                            <em>Créé le <?= $post->creation_date("special_format") ?> par <a class="text-blue" href="index.php?action=user&id=<?= $post->user_id() ?>"><?= $post->user_login() ?></a></em>
                            <?php if (isset($_SESSION["userID"]) && $_SESSION["userID"]==$post->user_id()) { ?>
                                <a class="text-blue a-edit-post m-1" href="index.php?action=editPost&id=<?= $post->id() ?>"><span class="far fa-edit"></span> Modifier</a>
                            <?php } ?>
                        </div>
                        <div class="card-body text-body">
                            <div class="post_content"><?= $post->content("raw_format") ?>
                            <?php if (strlen($post->content("raw_format")) > 200) { ?> <!-- Si le contenu est > à 1200 caractères, affiche le bouton 'Continuer la lecture' et ajoute un effet fade out -->
                                <span class="post-fade-out"></span>
                            </div>
                            <div>
                                <a href="index.php?action=post&id=<?= $post->id() ?>" class="btn btn-outline-blue mt-2">Continuer la lecture 
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

        <?php $pagination->view() ?> <!-- Ajoute la barre de pagination -->

        <div class="mt-4 mb-4">
            <a class="text-blue" href="index.php?action=editPost"><span class="far fa-file"></span> Rédiger un nouvel article</a>
        </div>

        </section>

</div>

<?php $content = ob_get_clean(); ?>

<?php require "view/template.php"; ?>
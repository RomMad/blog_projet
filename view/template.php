<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bibliothèque Bootstrap-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- Bibliothèque Fontawesome-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css"
        integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
    <link rel="stylesheet" href="/blog_projet/public/css/style.css" />
    <link rel="icon" href="/blog_projet/public/images/logo-<?= $_SESSION["settings"]->style_blog() == "light" ? "white" : "blue" ?>.ico" />
    <title><?= $title ?></title>
    <meta property="og:title" content="<?= $title ?>" />
    <meta property="og:type" content="blog" />
    <meta property="og:url" content="https://leblog.romain-mad.fr" />
    <meta property="og:image" content="/blog_projet/public/images/logo-<?= $_SESSION["settings"]->style_blog() == "light" ? "white" : "blue" ?>.ico" />
    <meta name="description" content="Le blog de Jean Forteroche." />

    <!-- Scripts TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/002hakdccklcsi43zdvvm0ug6hp1f83dx6abvrs923nlf6ay/tinymce/5/tinymce.min.js">
    </script>
    <script>
        tinymce.init({
            selector: "textarea#post_content",
            height: 500,
            language_url: "vendor/tinymce/languages/fr_FR.js",
            language: "fr_FR",
            menubar: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor textcolor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            content_css: [
                '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
                '//www.tiny.cloud/css/codepen.min.css'
            ]
        });
    </script>
</head>

<body>

    <div id="loader">
        <span class="fas fa-circle-notch"></span>
    </div>

    <main class="min-vh-100">
        <header id="header">
            <nav class="navbar navbar-expand-lg navbar-dark bg-<?= $_SESSION["settings"]->style_blog() == "light" ? "blue" : "dark" ?> mb-3 py-3 shadow">
                <a class="navbar-brand mr-0 text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "blue" ?> overflow-hidden"
                    href="/blog_projet/blog"><img id="logo_blog" src="/blog_projet/public/images/logo-<?= $_SESSION["settings"]->style_blog() == "light" ? "white" : "blue" ?>.ico" alt="logo du blog"> <?= isset($_SESSION["settings"]) ? $_SESSION["settings"]->blog_name() : "Le blog" ?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div id="navbarSupportedContent" class="collapse navbar-collapse ml-2">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "light" ?>" href="/blog_projet/blog"><span class="fas fa-home"></span> Accueil</a>
                        </li>
                        <?php 
                            
                        if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] < 5) {
                        ?>
                        <li class="nav-item">
                            <a class="nav-link text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "light" ?>" href="edit-post">Créer un article</a>
                        </li>
                        <?php 
                        }

                        if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] <= 4) {
                        ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "light" ?>" href="settings" id="navbarDropdown"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Administration</a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] == 1) { ?> 
                                <a class="dropdown-item" href="settings">Paramètres généraux</a>
                                <div class="dropdown-divider"></div>
                            <?php }
                            if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] <= 4) { ?> 
                                <a class="dropdown-item" href="posts">Gestion des articles</a>
                            <?php } ?>
                            <?php if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] <= 2) { ?> 
                                <a class="dropdown-item" href="comments">Gestion des commentaires</a>
                            <?php }
                            if (isset($_SESSION["user"]) && $_SESSION["user"]["role"] == 1) { ?> 
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="users">Gestion des utilisateurs</a>
                                <a class="dropdown-item" href="new-user">Ajouter un utilisateur</a>
                            <?php } ?>
                            </div>
                        </li>
                        <?php 
                        }
                        ?>
                    </ul>
                    <form action="blog" method="get" class="form-inline my-2 my-lg-0 mr-3">
                        <label for="search" class="sr-only col-form-label">Recherche</label>
                        <input name="search" id="search" type="search" class="form-control" placeholder="Recherche" aria-label="Search"
                            value="<?= isset($_SESSION["filter_search"]) ? htmlspecialchars($_SESSION["filter_search"]) : "" ?>">
                        <button id="send-search" class="btn btn-outline-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "blue" ?> my-2 ml-1 px-2 py-1" type="submit">
                            <span class="fas fa-search"></span></button>
                    </form>

                    <div class="text-light">
                    <?php 
                    if (isset($_SESSION["user"])) {
                    ?>
                        <a class="text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "blue" ?> font-weight-bold" href="profil" data-toggle="popover"
                            data-trigger="hover" data-placement="bottom" data-html="true"
                            title="<?= htmlspecialchars($_SESSION["user"]["surname"]) ?> <?= htmlspecialchars($_SESSION["user"]["name"]) ?>"
                            data-content="Dernière connexion : <br /><?= $_SESSION["user"]["lastConnection"] ?><br /> Profil : <?= $_SESSION["user"]["profil"] ?>">
                            <span class="fas fa-user"></span> <?= $_SESSION["user"]["login"] ?>
                        </a>
                        <br />
                        <a class="text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "blue" ?>" href="disconnection">Se déconnecter</a>
                        <?php 
                        } else {
                        ?>
                        <a class="text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "blue" ?>" href="connection">Se connecter</a>
                        <br />
                        <a class="text-<?= $_SESSION["settings"]->style_blog() == "light" ? "light" : "blue" ?>" href="inscription">S'inscrire</a>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </nav>
        </header>

        <?= $content ?>

        <a id="scroll-top" class="scroll-top" href="#header"><span class="fa fa-chevron-up"></span></a>
        
    </main>

    <footer class="bg-dark p-4 text-light shadow">
        <p>Ce site web est un blog réalisé dans le cadre d'une formation de développeur Web.</p>
        <p>© Romain MADELAINE | <a href="https://romain-mad.fr" target="_blank" class="text-blue">romain-mad.fr</a></p>
    </footer>

    <script src="public/js/jquery.js"></script>
    <script src="public/js/scroll_to.js"></script>
    <script src="public/js/app.js"></script>
    <script src="public/js/seePassword.js"></script>
    <script src="public/js/comments.js"></script>
    <script src="public/js/selectAllCheckboxes.js"></script>
    <?= isset($script) ? $script : "" ?>
    <!-- Les fichiers Javascript pour Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>

</body>

</html>
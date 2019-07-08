<?php 
    session_start();

    include("connection_bdd.php");

    var_dump($_POST);
    // Vérifie si informations dans variable POST
    if (!empty($_POST)) {
        $user_login = htmlspecialchars($_POST['user_login']);
        $user_email = htmlspecialchars($_POST['user_email']);
        $user_pass_hash = password_hash(htmlspecialchars($_POST['user_pass']), PASSWORD_DEFAULT); // Hachage du mot de passe
        // Insert les données dans la table users
        $req = $bdd->prepare('INSERT INTO users(user_login, user_email, user_pass) VALUES(:user_login, :user_email, :user_pass)');
        $req->execute(array(
            'user_login' => $user_login,
            'user_email' => $user_email,
            'user_pass' => $user_pass_hash,
            ));
            // Récupère l'ID de l'utilisateur
            $req = $bdd->prepare('SELECT ID FROM users WHERE user_login = ?');
            $req->execute(array($user_login));
            $data = $req->fetch();
            // Ajoute les infos de l'utilisateurs dans la Session
            $_SESSION['ID'] = $data['ID'];
            $_SESSION['user_login'] = $user_login;
            $statusInscription = "Inscription réussie.";
            ?> 
            <meta http-equiv="refresh" content="2;url=index.php"/>
            <?php 
    };

?>

<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.php"); ?>

    <div class="container">

        <section id="inscription" class="row">

            <div class="col-sm-10 col-md-8 col-lg-6 mx-auto">

            <?= isset($statusInscription) ? $statusInscription : '' ?>
                <br /> <br />
                <form action="inscription.php" method="post" class="col-md-12 card shadow">
                    <div class="form-group row">
                        <h3 class="h4 card-header col-md-12 h2 bg-light text-dark">Inscription</h3>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <div class="row">
                                <label for="user_login" class="col-md-4 col-form-label">Login</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_login" id="user_login" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_email" class="col-md-4 col-form-label">Email</label>
                                <div class="col-md-8">
                                    <input type="text" name="user_email" id="user_email" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="nom" class="col-md-4 col-form-label">Nom</label>
                                <div class="col-md-8">
                                    <input type="text" name="nom" id="nom" class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="prenom" class="col-md-4 col-form-label">Prénom</label>
                                <div class="col-md-8">
                                    <input type="text" name="prenom" id="prenom" class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_pass" class="col-md-4 col-form-label">Mot de passe</label>
                                <div class="col-md-8">
                                    <input type="password" name="user_pass" id="user_pass" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="row">
                                <label for="user_pass-confirm" class="col-md-4 col-form-label">Confirmation mot de
                                    passe</label>
                                <div class="col-md-8">
                                    <input type="password" name="user_pass-confirm" id="user_pass-confirm" 
                                        class="form-control"><br />
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="float-right">
                                        <input type="submit" value="Valider" id="validation" class="btn btn-info shadow">
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
<?php 
    session_start();

    include("connection_bdd.php");
    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION)) {
        header('Location: connection.php');
    };

    var_dump($_GET);
        if (!empty($_GET)) {
        $req = $bdd->prepare('SELECT ID, user_login, user_name, user_surname, user_email, user_status, DATE_FORMAT(user_birthday, \'%d/%m/%Y %H:%i\') AS user_birthday_fr 
        FROM users
        WHERE ID =?');
        
        $req->execute(array($_SESSION['ID']));
        $data = $req->fetch();

        $user_login = $data['user_login'];
        $user_name = $data['user_name'];
        $user_surname = $data['user_surname'];
        $user_birthday = $data['user_birthday_fr'];
        $user_email = $data['user_email'];
        $user_status = $data['user_status'];
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
            <form action="profil.php" method="post" class="col-md-12 card shadow">
                <div class="form-group row">
                    <h2 class="card-header col-md-12 h2 bg-light text-dark">Profil</h2>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="row">
                            <label for="user_login" class="col-md-4 col-form-label">Login</label>
                            <div class="col-md-8">
                                <input type="text" name="user_login" id="user_login" 
                                    class="form-control" value="<?= isset($user_login) ? $user_login : '' ?>"><br />
                            </div>
                        </div>
                        <div class="row">
                            <label for="user_email" class="col-md-4 col-form-label">Email</label>
                            <div class="col-md-8">
                                <input type="text" name="user_email" id="user_email" 
                                    class="form-control" value="<?= isset($user_email) ? $user_email : '' ?>"><br />
                            </div>
                        </div>
                        <div class="row">
                            <label for="nom" class="col-md-4 col-form-label">Nom</label>
                            <div class="col-md-8">
                                <input type="text" name="nom" id="nom" class="form-control" value="<?= isset($user_name) ? $user_name : '' ?>"><br />
                            </div>
                        </div>
                        <div class="row">
                            <label for="prenom" class="col-md-4 col-form-label">Prénom</label>
                            <div class="col-md-8">
                                <input type="text" name="prenom" id="prenom" class="form-control" value="<?= isset($user_surname) ? $user_surname : '' ?>"><br />
                            </div>
                        </div>
                        <div class="row">
                            <label for="user_pass" class="col-md-4 col-form-label">Modification du mot de passe</label>
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
                        <!-- Les boutons de validation et d'annulation -->
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="float-right">
                                    <input type="submit" value="Valider" id="validation"
                                        class="btn btn-info shadow">
                                    <button id="buttonCancel" class="btn btn-secondary shadow">Annuler</button>
                                </div>
                            </div>
                        </div>
                        <!-- Les informations au clic sur le bouton de validation -->
                        <div class="row">
                            <div id="alert-control" class="col-md-12">
                                <p id="alert-control-para" role="alert"></p>
                            </div>
                        </div>
                    </div>
            </form>
        </div>

<?php  
    if (isset($statusInscription)) {
        echo $statusInscription;
    };
?>

</section>

    </div>

    <?php include("scripts.html"); ?>

</html
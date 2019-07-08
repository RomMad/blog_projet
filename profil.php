<?php 
    session_start();

    include("connection_bdd.php");
    // Redirige vers la page de connexion si non connecté
    if (empty($_SESSION)) {
        header('Location: connection.php');
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
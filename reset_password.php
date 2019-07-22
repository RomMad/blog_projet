<?php 

session_start();

require("connection_bdd.php");

var_dump($_GET);
if (isset($_GET["token"])) {
    $token = htmlspecialchars($_GET["token"]);
    $validation = true;
    $msgProfil = "";
    $typeAlert = "danger";

    // Vérifie si le token est correct
    $req = $bdd->prepare("SELECT ID, reset_date FROM reset_passwords WHERE token = ?");
    $req->execute(array($token));
    $dataResetPassword = $req->fetch();

    if (!$dataResetPassword) {
        $msgProfil = $msgProfil . "<li>Le lien de réinitialisation est incorrect.</li>";
        $validation = false;
    };

    $dateNow = new DateTime("now");
    $dataResetPassword =  new DateTime($dataResetPassword["reset_date"]);
    var_dump($dateNow);
    var_dump($dataResetPassword);
    // $interval = date_diff($dateNow, $dataResetPassword);
    // echo $interval->format('%R%m minutes');
    $interval = (date_timestamp_get($dateNow)+(60*60*2)-date_timestamp_get($dataResetPassword))/60;
    echo $interval;

    if ($interval>15) {
        $msgProfil = $msgProfil . "<li>Le lien de réinitialisation est périmé.</li>";
        $validation = false;
    };

    var_dump($_POST);
    // Vérifie si information dans variable POST
    if (!empty($_POST)) {
    
        if (isset($_GET["token"])) {
            $token = htmlspecialchars($_GET["token"]);
            $new_pass = htmlspecialchars($_POST["new_pass"]);
            $new_pass_confirm = htmlspecialchars($_POST["new_pass_confirm"]);
           
            // Vérifie si le nouveau mot de passe est valide (minimum 6 caratères, 1 lettre minuscule, 1 lettre majuscule, 1 chiffre)
            if (!preg_match("#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$#", $new_pass)) {
                $msgProfil = $msgProfil . "<li>Le nouveau mot de passe n'est pas valide.</li>";
                $validation = false;
            };
            // Vérifie si la confirmation du mot de passe est identique
            if ($new_pass!=$new_pass_confirm) {
                $msgProfil = $msgProfil . "<li>Le mot de passe et la confirmation sont différents.</li>";
                $validation = false;
            };
            // Met à jour le mot de passe si validation est vraie
            if ($validation) {        
            $new_pass_hash = password_hash($new_pass, PASSWORD_DEFAULT); // Hachage du mot de passe
            $req = $bdd->prepare("UPDATE users SET pass = :new_pass WHERE ID = :ID");                
            $req->execute(array(
                "new_pass" => $new_pass_hash,
                "ID" => $_SESSION["user_ID"]
                )); 
    
            $msgProfil = "Le mot de passe est mis à jour.";
            $typeAlert = "success";
            };
        };
    
        $_SESSION["flash"] = array(
            "msg" => $msgProfil,
            "type" =>  $typeAlert
        );

    };

} else {
    header("Location: connection.php");
};

?>

<!DOCTYPE html>
<html lang="fr">
<?php require("head.html"); ?>

<body>

    <?php require("header.php"); ?>

    <div class="container">
        <section id="reset-password" class="row">
                <form action="reset_password.php?token=<?= isset($token) ? $token : "" ?>" method="post" class="form-signin col-xs-8 col-sm-6 col-md-4 mx-auto text-center mt-4 mb-4">
                    <h1 class="h3 mb-4 font-weight-normal">Réinitialisation du mot de passe</h1>
                    <label for="email" class="sr-only">Email</label>
                    <input type="text" name="email" id="email" class="form-control mb-4" placeholder="Email">
                    <label for="new_pass" class="sr-only">Mot de passe</label>
                    <div>
                        <input type="password" name="new_pass" id="new_pass" class="form-control mb-2" placeholder="Nouveau mot de passe">
                        <span class="fas fa-eye"></span>
                    </div>
                    <div>
                        <input type="password" name="new_pass_confirm" id="new_pass_confirm" class="form-control mb-4" placeholder="Confirmation du mot de passe">
                        <span class="fas fa-eye"></span>
                    </div>
                    <input type="submit" value="Envoyer" id="submit" class="btn btn-lg btn-info btn-block mb-4 shadow">

                    <?php include("msg_session_flash.php") ?>
                </form>
            </div>
        </section>
    </div>

    <?php include("footer.php"); ?>

    <?php include("scripts.html"); ?>

</body>

</html>
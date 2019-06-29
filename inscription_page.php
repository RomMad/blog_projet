<!DOCTYPE html>
<html lang="fr">
<?php include("head.html"); ?>

<body>

    <?php include("header.html"); ?>

    <div class="container">

        <section id="inscription" class="row">

            <?php include("inscription_form.html"); ?>

            <?php  
                if (isset($statusInscription)) {
                    echo $statusInscription;
                };
            ?>
            
        </section>

    </div>

    <?php include("scripts.html"); ?>

</html
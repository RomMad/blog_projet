// Système pour afficher l'intégralité du commentaire et système de modification du commentaire
let commentsElt = document.getElementById("comments"); // la section avec tous les commentaires
let nbCommentsElt = commentsElt.getElementsByClassName("comment").length; // compte le nombre de commentaires
let commentsElt2 = commentsElt.getElementsByClassName("comment");

// Boucle pour chaque commentaire
for (let i = 0; i < nbCommentsElt; i++) {
    let rectCardBodyElt = commentsElt2[i].getBoundingClientRect();
    let commentContentElt = commentsElt2[i].querySelector(".card-body .comment-content");
    let commentFadeOutElt = commentsElt2[i].querySelector(".card-body .comment-fade-out");
    let aEditCommentElt = commentsElt2[i].querySelector(".card .edit-comment");
    let formEditCommentElt = commentsElt2[i].querySelector(".comment .form-edit-comment");
    let cancelEditCommentElt = commentsElt2[i].querySelector(".comment .form-edit-comment .cancel-edit-comment");

    // commentContentElt.style.transition = "max-height 0.5s ease";

    // Masque le contenu du commentaire quand celui dépasse les 200px de hauteur
    if (rectCardBodyElt.height > 150) {
        commentContentElt.style.maxHeight = "150px";
        commentContentElt.style.overflow = "hidden";
        commentContentElt.className = "comment-content";
        commentFadeOutElt.className = "comment-fade-out d-block";
        commentContentElt.style.cursor = "pointer";
    };
    // Affiche la totalité de commentaire au clic le contenu du commentaire
    commentContentElt.addEventListener("click", function () {
        if (commentContentElt.style.maxHeight === "2000px") {
            commentContentElt.style.maxHeight = "150px";
            commentContentElt.style.overflow = "hidden";
            commentContentElt.className = "comment-content";
            commentFadeOutElt.className = "comment-fade-out d-block";
        } else {
            commentContentElt.style.maxHeight = "2000px";
            commentContentElt.style.overflow = "";
            commentContentElt.className = "comment-content";
            commentFadeOutElt.className = "comment-fade-out d-none";

        };
    });
    // Affiche le formulaire de commentaire au clic sur le lien "Modifier"
    if (aEditCommentElt) {
        aEditCommentElt.addEventListener("click", function () {
            commentContentElt.className = "comment-content";
            commentContentElt.className = "d-none";
            aEditCommentElt.className = "d-none";
            formEditCommentElt.className = "form-edit-comment d-block";
        });
    };
    // MAsque le formulaire de commentaire au clic sur le bouton "Annuler"
    if (cancelEditCommentElt) {
        cancelEditCommentElt.addEventListener("click", function (e) {
            e.preventDefault();
            formEditCommentElt.className = "form-edit-comment d-none";
            commentContentElt.className = "comment-content";
            aEditCommentElt.className = "edit-comment mt-2";
        });
    };

};
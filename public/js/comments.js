class Comments {
    constructor() {
        this.commentsElt = document.getElementById("comments"); // la section avec tous les commentaires
        this.nbCommentsElt = this.commentsElt.getElementsByClassName("comment").length; // compte le nombre de commentaires
        this.commentsElt2 = document.getElementsByClassName("comment");
        this.commentsContentElt = document.getElementsByClassName("comment-content");
        this.init();
    }

    init() {
        // Boucle pour chaque commentaire
        for (let i = 0; i < this.nbCommentsElt; i++) {
            this.rectCommentContentElt = this.commentsContentElt[i].getBoundingClientRect();
            this.commentContentElt = this.commentsElt2[i].querySelector(".comment-content");
            this.commentFadeOutElt = this.commentsElt2[i].querySelector(".comment-fade-out");
            this.aEditCommentElt = this.commentsElt2[i].querySelector(".edit-comment");
            this.formEditCommentElt = this.commentsElt2[i].querySelector(".form-edit-comment");
            this.cancelEditCommentElt = this.commentsElt2[i].querySelector(".form-edit-comment .cancel-edit-comment");
            // commentContentElt.style.transition = "max-height 0.5s ease";
            this.commentContentElt.id = "comment-content-" + (i + 1);
            this.commentFadeOutElt.id = "comment-fadeOut-" + (i + 1);

            if (this.aEditCommentElt) {
                this.aEditCommentElt.id = "comment-btn-edit-" + (i + 1);
                this.aEditCommentElt.addEventListener("click", this.edit.bind(this, i + 1));

            }
            this.cancelEditCommentElt.id = "comment-btn-cancel-" + (i + 1);
            this.formEditCommentElt.id = "comment-form-" + (i + 1);


            // Masque le contenu du commentaire quand celui dépasse les 200px de hauteur
            if (this.rectCommentContentElt.height > 125) {
                this.commentContentElt.style.maxHeight = "125px";
                this.commentContentElt.style.overflow = "hidden";
                this.commentContentElt.className = "comment-content";
                this.commentFadeOutElt.className = "comment-fade-out d-block";
                this.commentContentElt.style.cursor = "pointer";
            }

            // Affiche la totalité de commentaire au clic le contenu du commentaire
            this.commentContentElt.addEventListener("click", this.reduce.bind(this, i + 1));
            this.cancelEditCommentElt.addEventListener("click", function (e) {
                e.preventDefault();
                this.cancel(i + 1);
            }.bind(this));
        }
    }

    // Affiche ou réduit le commentaire au clic le contenu du commentaire
    reduce(id) {
        let commentContentElt = document.getElementById("comment-content-" + id);
        let commentFadeOutElt = document.getElementById("comment-fadeOut-" + id);
        if (commentContentElt.style.maxHeight === "2000px") {
            commentContentElt.style.maxHeight = "100px";
            commentContentElt.style.overflow = "hidden";
            commentContentElt.className = "comment-content";
            commentFadeOutElt.className = "comment-fade-out d-block";
        } else {
            commentContentElt.style.maxHeight = "2000px";
            commentContentElt.style.overflow = "";
            commentContentElt.className = "comment-content";
            commentFadeOutElt.className = "comment-fade-out d-none";
        }
    }

    // Affiche le formulaire de commentaire au clic sur le lien "Modifier"
    edit(id) {
        let commentContentElt = document.getElementById("comment-content-" + id);
        let aEditCommentElt = document.getElementById("comment-btn-edit-" + id);
        let formEditCommentElt = document.getElementById("comment-form-" + id);
        commentContentElt.className = "comment-content";
        commentContentElt.className = "d-none";
        aEditCommentElt.className = "d-none";
        formEditCommentElt.className = "form-edit-comment d-block";

    }

    // Masque le formulaire de commentaire au clic sur le bouton "Annuler"
    cancel(id) {
        let commentContentElt = document.getElementById("comment-content-" + id);
        let aEditCommentElt = document.getElementById("comment-btn-edit-" + id);
        let formEditCommentElt = document.getElementById("comment-form-" + id);
        formEditCommentElt.className = "form-edit-comment d-none";
        commentContentElt.className = "comment-content";
        aEditCommentElt.className = "edit-comment mt-2";
    }

}
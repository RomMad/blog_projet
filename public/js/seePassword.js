class SeePassword {
    constructor() {
        this.init();
    }

    init() {
        // Script pour afficher le mot de passe
        this.passElt = document.getElementById("pass");
        if (this.passElt) {
            this.showPasswordElt = document.getElementById("showPassword");
            // Affiche du mot de passe au clic sur l'oeil
            this.showPasswordElt.addEventListener("mousedown", function () {
                this.passElt.type = "text";
            }.bind(this));
            // Masque le mot de passe au relachement de la souris
            document.addEventListener("mouseup", function () {
                this.passElt.type = "password";
            }.bind(this));
        }
        // Script pour afficher le mot de passe de confirmation
        this.passConfirmElt = document.getElementById("pass_confirm");
        if (this.passConfirmElt) {
            this.showConfirmPasswordElt = document.getElementById("showConfirmPassword");
            // Affiche du mot de passe au clic sur l'oeil
            this.showConfirmPasswordElt.addEventListener("mousedown", function () {
                this.passConfirmElt.type = "text";
            }.bind(this));
            // Masque le mot de passe au relachement de la souris
            document.addEventListener("mouseup", function () {
                this.passConfirmElt.type = "password";
            }.bind(this));
        }
    }
}
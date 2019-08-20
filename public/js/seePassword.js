// Class pour démasquer le mot de passe
class SeePassword {
    constructor() {
        this.passwordGroupElts = document.querySelectorAll(".password-group");
        this.init();
    }

    init() {
        this.passwordGroupElts.forEach(function (passwordGroupElt) {
            let passwordElt = passwordGroupElt.querySelector(".password");
            let showPasswordElt = passwordGroupElt.querySelector(".show-password");
            // Affiche du mot de passe au clic sur l'oeil
            showPasswordElt.addEventListener("mousedown", function () {
                passwordElt.type = "text";
            }.bind(this));
            // Masque le mot de passe au relachement de la souris
            document.addEventListener("mouseup", function () {
                passwordElt.type = "password";
            }.bind(this));
        }.bind(this));
    }
}
// Script pour afficher le mot de passe de confirmation

passConfirmElt = document.getElementById("pass_confirm");
showConfirmPasswordElt = document.getElementById("showConfirmPassword");
// Affiche du mot de passe au clic sur l'oeil
showConfirmPasswordElt.addEventListener("mousedown", function () {
    passConfirmElt.type = "text";
});
// Masque le mot de passe au relachement de la souris
document.addEventListener("mouseup", function () {
    passConfirmElt.type = "password";
});
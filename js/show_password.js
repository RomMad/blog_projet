// Script pour afficher le mot de passe

passElt = document.getElementById("pass");
showPasswordElt = document.getElementById("showPassword");
// Affiche du mot de passe au clic sur l'oeil
showPasswordElt.addEventListener("mousedown", function () {
    passElt.type = "text";
});
// Masque le mot de passe au relachement de la souris
document.addEventListener("mouseup", function () {
    passElt.type = "password";
});
passElt = document.getElementById("pass");
iconEyeElt = document.getElementById("icon-eye");
// Affiche du mot de passe au clic sur l'oeil
iconEyeElt.addEventListener("mousedown", function () {
    passElt.type = "text";
});
// Masque le mot de passe au relachement de la souris
document.addEventListener("mouseup", function () {
    passElt.type = "password";
});
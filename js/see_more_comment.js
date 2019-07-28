let commentsElt = document.getElementById("comments"); // la section avec tous les commentaires
let nbCommentsElt = commentsElt.getElementsByClassName("comment").length; // compte le nombre de commentaires
let commentsElt2 = commentsElt.getElementsByClassName("comment");

for (let i = 0; i < nbCommentsElt; i++) {
    let cardBodyElt = commentsElt2[i].querySelector(".card-body");
    let rectCardBodyElt = commentsElt2[i].getBoundingClientRect();

    if (rectCardBodyElt.height > 200) {
        cardBodyElt.style.maxHeight = "200px";
        cardBodyElt.style.overflow = "hidden";
        cardBodyElt.className = "card-body commentFadeOut";
        cardBodyElt.style.cursor = "pointer";
    };

    cardBodyElt.addEventListener("click", function () {
        cardBodyElt.style.transition = "all 0.5s ease";
        if (cardBodyElt.style.maxHeight === "2000px") {
            cardBodyElt.style.maxHeight = "200px";
            cardBodyElt.style.overflow = "hidden";
            cardBodyElt.className = "card-body commentFadeOut";
        } else {
            cardBodyElt.style.maxHeight = "2000px";
            cardBodyElt.style.overflow = "";
            cardBodyElt.className = "card-body";
        };
    });
};
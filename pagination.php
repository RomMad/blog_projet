<?php 
Class Pagination {

    private $_linkNbDisplayed,
            $_linkPagination,
            $_anchor,
            $_nbPages,
            $_currentPage;

    function __construct ($nbItems, $nbDisplayed, $linkNbDisplayed, $linkPagination, $anchor) {
        $this->_nbPages = ceil($nbItems / $nbDisplayed);
        $this->_nbDisplayed = $nbDisplayed;
        $this->_linkNbDisplayed = $linkNbDisplayed;
        $this->_linkPagination = $linkPagination;
        $this->_anchor = $anchor;

        if (!empty($GET["page"])) {
            $this->_currentPage = $_GET["page"];
        }
    }

    // Adaptation de la pagination en fonction du nombre de pages et du positionnement
    public function initialPosition() {

    }
    
}

// Vérification si informations dans variable POST
if (!empty($_POST)) {
    $nbDisplayed = htmlspecialchars($_POST["nbDisplayed"]);
    setcookie("pagination[nbDisplayed" . $typeItem . "]", $nbDisplayed, time() + 365*24*3600, null, null, false, false);
} elseif (!empty($_COOKIE["pagination"]["nbDisplayed" . $typeItem])) {
    $nbDisplayed = $_COOKIE["pagination"]["nbDisplayed" . $typeItem];
} else {
    $nbDisplayed = 10;
}

// Vérification si informations dans variable GET
if (!empty($_GET["page"])) {
    $currentPage = htmlspecialchars($_GET["page"]);
    // Calcul le nombre de pages par rapport aux nombre d'articles
    $maxLimit = $currentPage*$nbDisplayed;
    $minLimit = $maxLimit-$nbDisplayed;
} else  {
    $currentPage = 1;
    $minLimit = 0;
    $maxLimit = $nbDisplayed;
}
$nbPages = ceil($nbItems / $nbDisplayed);

// Adaptation de la pagination en fonction du nombre de pages et du positionnement
if ($currentPage == 1) {
    $pageLink_1 = $currentPage;
    $pageLink_2 = $currentPage + 1;
    $pageLink_3 = $currentPage + 2;  
    $prevPage = 1;
    $prevPageLink = "disabled";
    $prevPageColorLink = "";  
    $activepageLink_1 = "active disabled";
} else {
    $pageLink_1 = $currentPage - 1;
    $pageLink_2 = $currentPage;
    $pageLink_3 = $currentPage + 1;
    $prevPage = $currentPage - 1;
    $prevPageLink = "";
    $prevPageColorLink = "text-blue";
    $activepageLink_1 = ""; 
}
if ($currentPage == $nbPages && $currentPage > 2) {
    $pageLink_1 = $currentPage - 2;
    $pageLink_2 = $currentPage - 1;
    $pageLink_3 = $currentPage;
}
// Mise en forme bouton Lien 2
if ($currentPage >= 2 && ($currentPage != $nbPages || $nbPages < 3)) {
    $activepageLink_2 = "active disabled";
} else {
    $activepageLink_2 = "";
}
// Mise en forme bouton Lien 3
if ($currentPage >= $nbPages) {
    $activepageLink_3 = "active disabled"; 
} else {
    $activepageLink_3 = "";
}
// Mise en forme bouton "Suivant"
if ($currentPage != $nbPages) {
    $nextPage = $currentPage + 1;
    $nextPageLink = "";
    $nextPageColorLink = "text-blue";
} else {
    $nextPage = $currentPage;
    $nextPageLink = "disabled";
    $nextPageColorLink = "";
}
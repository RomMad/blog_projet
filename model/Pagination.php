<?php 
Class Pagination {

    protected   $_typeItem,
                $_nbItems,
                $_linkNbDisplayed,
                $_linkPagination,
                $_anchorPagination,
                $_nbPages,
                $_currentPage,
                $_pageLink_1,
                $_pageLink_2,
                $_pageLink_3,
                $_prevPageLink,
                $_nextPageLink,
                $_activepageLink_1,
                $_activepageLink_2,
                $_activepageLink_3;

    public  $_nbLimit,
            $_nbDisplayed;
            
    function __construct($typeItem, $nbItems, $linkNbDisplayed, $linkPagination, $anchorPagination) {
        $this->_typeItem = $typeItem;
        $this->_nbItems = $nbItems;
        $this->_linkNbDisplayed = $linkNbDisplayed;
        $this->_linkPagination = $linkPagination;
        $this->_anchorPagination = $anchorPagination;

        $this->init();
    }

    // Adaptation de la pagination en fonction du nombre de pages et du positionnement
    public function init() {
        // Vérification si informations dans variable POST
        if (!empty($_POST["nbDisplayed"])) {
            $this->_nbDisplayed = htmlspecialchars($_POST["nbDisplayed"]);
            setcookie("pagination[nbDisplayed_" . $this->_typeItem . "]", $this->_nbDisplayed, time() + 365*24*3600, null, null, false, false);
        } elseif (!empty($_COOKIE["pagination"]["nbDisplayed_" . $this->_typeItem])) {
            $this->_nbDisplayed = $_COOKIE["pagination"]["nbDisplayed_" . $this->_typeItem];

        } else {
            $this->_nbDisplayed = 20;
        }

        // Vérification si informations dans variable GET
        if (!empty($_GET["page"])) {
            $this->_currentPage = htmlspecialchars($_GET["page"]);
            // Calcul le nombre de pages par rapport aux nombre d'articles
            $this->_nbLimit = ($this->_currentPage * $this->_nbDisplayed) - $this->_nbDisplayed;
        } else  {
            $this->_currentPage = 1;
            $this->_nbLimit = 0;
        }
        $this->_nbPages = ceil($this->_nbItems / $this->_nbDisplayed);

        // Adaptation de la pagination en fonction du nombre de pages et du positionnement
        if ($this->_currentPage == 1) {
            $this->_pageLink_1 = $this->_currentPage;
            $this->_pageLink_2 = $this->_currentPage + 1;
            $this->_pageLink_3 = $this->_currentPage + 2;  
            $this->_prevPage = 1;
            $this->_prevPageLink = "disabled";
            $this->_prevPageColorLink = "";  
            $this->_activepageLink_1 = "active disabled";
        } else {
            $this->_pageLink_1 = $this->_currentPage - 1;
            $this->_pageLink_2 = $this->_currentPage;
            $this->_pageLink_3 = $this->_currentPage + 1;
            $this->_prevPage = $this->_currentPage - 1;
            $this->_prevPageLink = "";
            $this->_prevPageColorLink = "text-blue";
            $this->_activepageLink_1 = ""; 
        }
        if ($this->_currentPage == $this->_nbPages && $this->_currentPage > 2) {
            $this->_pageLink_1 = $this->_currentPage - 2;
            $this->_pageLink_2 = $this->_currentPage - 1;
            $this->_pageLink_3 = $this->_currentPage;
        }
        // Mise en forme bouton Lien 2
        if ($this->_currentPage >= 2 && ($this->_currentPage != $this->_nbPages || $this->_nbPages < 3)) {
            $this->_activepageLink_2 = "active disabled";
        } else {
            $this->_activepageLink_2 = "";
        }
        // Mise en forme bouton Lien 3
        if ($this->_currentPage >= $this->_nbPages) {
            $this->_activepageLink_3 = "active disabled"; 
        } else {
            $this->_activepageLink_3 = "";
        }
        // Mise en forme bouton "Suivant"
        if ($this->_currentPage != $this->_nbPages) {
            $this->_nextPage = $this->_currentPage + 1;
            $this->_nextPageLink = "";
            $this->_nextPageColorLink = "text-blue";
        } else {
            $this->_nextPage = $this->_currentPage;
            $this->_nextPageLink = "disabled";
            $this->_nextPageColorLink = "";
        }
    }
    
    // Affiche la barre de pagination
    public function view() {

        ?><div class="row"><?php
        // Si le nombre d'élements est supérieur à 5, affiche la liste déroulante avec le nombre d'éléments à afficher
        if ($this->_nbItems > 5) {
            $this->selectNumber();
        }
        // Si le nombre de pages à afficher est supérieur à 1, affiche les liens de paginations
        if ($this->_nbPages > 1) {
            $this->selectpage();
        }
        ?></div><?php
    }
    // Affiche la liste déroulante avec le nombre d'éléments à afficher
    protected function selectNumber() {
        ?>
        <div class="col-md-6 my-2">
            <form action="<?= $this->_linkNbDisplayed ?>" method="post" class="form-inline">
                <label class="col-form-label mr-1" for="nbDisplayed">Affichés</label>
                <select name="nbDisplayed" id="nbDisplayed" class="custom-select form-control-sm mr-1 shadow-sm" >
                    <option value="5" <?= $this->_nbDisplayed  == 5 ? "selected" : "" ?> >5</option>
                    <option value="10" <?= $this->_nbDisplayed == 10 ? "selected" : "" ?> >10</option>
                    <option value="15" <?= $this->_nbDisplayed == 15 ? "selected" : "" ?> >15</option>
                    <option value="20" <?= $this->_nbDisplayed == 20 ? "selected" : "" ?> >20</option>
                    <option value="50" <?= $this->_nbDisplayed == 50 ? "selected" : "" ?> >50</option>
                </select>
                <input type="submit" id="pagination" class="btn btn-blue form-control-sm pt-1 px-lg-3 px-md-2 shadow-sm" value="OK">
                                                        
            </form>
        </div>
        <?php
    }
    // Affiche les liens de paginations
    protected function selectPage () {
        ?>
        <div class="col-md-6 my-2">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item <?= $this->_prevPageLink ?> shadow-sm">
                    <a class="page-link <?= $this->_prevPageColorLink ?> font-weight-bold"href="<?= $this->_linkPagination ?>page-<?= $this->_prevPage . $this->_anchorPagination ?>" tabindex="-1" aria-disabled="true"><span aria-hidden="true">&laquo;</span></a>
                    </li>
                    <?php 
                    if ($this->_currentPage > 2 && $this->_nbPages > 3) {
                    ?>
                        <li class="page-item"><a class="page-link text-blue p-2 px-2 shadow-sm" href="<?= $this->_linkPagination ?>page-1<?= $this->_anchorPagination ?>">1...</a></li>
                    <?php                                                               
                    }
                    ?>
                    <li class="page-item <?= $this->_activepageLink_1 ?> shadow-sm"><a class="page-link text-blue" href="<?= $this->_linkPagination ?>page-<?= $this->_pageLink_1 . $this->_anchorPagination ?>"><?= $this->_pageLink_1 ?></a></li>
                    <li class="page-item <?= $this->_activepageLink_2 ?> shadow-sm"><a class="page-link text-blue" href="<?= $this->_linkPagination ?>page-<?= $this->_pageLink_2 . $this->_anchorPagination ?>"><?= $this->_pageLink_2 ?></a></li>

                    <?php 
                    if ($this->_nbPages > 2) {
                    ?>
                        <li class="page-item <?= $this->_activepageLink_3 ?> shadow-sm"><a class="page-link text-blue" href="<?= $this->_linkPagination ?>page-<?= $this->_pageLink_3 . $this->_anchorPagination ?>"><?= $this->_pageLink_3 ?></a></li>
                    <?php 
                    }
                    ?>

                    <?php 
                    if ($this->_currentPage < $this->_nbPages -1 && $this->_nbPages > 3) {
                    ?>
                        <li class="page-item shadow-sm"><a class="page-link px-2 text-blue" href="<?= $this->_linkPagination ?>page-<?= $this->_nbPages . $this->_anchorPagination ?>">...<?= $this->_nbPages ?></a></li>
                    <?php 
                    }
                    ?>
                        <li class="page-item <?= $this->_nextPageLink ?> shadow-sm">
                    <a class="page-link <?= $this->_nextPageColorLink ?> font-weight-bold"" href="<?= $this->_linkPagination ?>page-<?= $this->_nextPage . $this->_anchorPagination ?>"><span aria-hidden="true">&raquo;</span></a>
                    </li>
                </ul>
            </nav>
        </div> 
        <?php
    }   
}
<div class="row">
<?php 
// Si le nombre d'élements est supérieur à 5, affiche la liste déroulante avec le nombre d'éléments à afficher
if ($this->_nbItems > 5) 
{ ?>
    <!-- Affiche la liste déroulante avec le nombre d'éléments à afficher -->
    <div class="col-md-6 mt-1 mb-3">
        <form action="<?= $this->_linkNbDisplayed ?>" method="post" class="form-inline">
            <label class="col-form-label mr-1" for="nbDisplayed">Affichés</label>
            <select name="nbDisplayed" id="nbDisplayed-<?= $this->_idSelectNumber ?>" class="custom-select form-control-sm mr-1 shadow-sm" >
                <option value="5" <?= $this->_nbDisplayed == 5 ? "selected" : "" ?> >5</option>
                <option value="10" <?= $this->_nbDisplayed == 10 ? "selected" : "" ?> >10</option>
                <option value="15" <?= $this->_nbDisplayed == 15 ? "selected" : "" ?> >15</option>
                <option value="20" <?= $this->_nbDisplayed == 20 ? "selected" : "" ?> >20</option>
                <option value="50" <?= $this->_nbDisplayed == 50 ? "selected" : "" ?> >50</option>
            </select>
            <input type="submit" id="pagination-<?= $this->_idSelectNumber ?>" class="btn btn-blue form-control-sm pt-1 px-lg-3 px-md-2 shadow-sm" value="OK">
        </form>
    </div>

<?php 
}
// Si le nombre de pages à afficher est supérieur à 1, affiche les liens de paginations
if ($this->_nbPages > 1) 
{ ?>
    <!-- Affiche les liens de paginations  -->
    <div class="col-md-6 mt-1 mb-3">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end mb-0">
                <!-- Lien la page précédente -->
                <li class="page-item <?= $this->_currentPage == 1 ? "disabled" : "" ?> shadow-sm">
                    <a class="page-link <?= $this->_currentPage == 1 ? "" : "text-blue" ?> font-weight-bold" 
                        href="<?= $this->_link ?>page-<?= ($this->_currentPage - 1) . $this->_anchor ?>" 
                        tabindex="-1" aria-disabled="true"><span aria-hidden="true">&laquo;</span></a>
                </li> 
                <?php if ($this->_currentPage > 2 && $this->_nbPages > 3) { ?>
                <!-- Lien vers la première page -->
                <li class="page-item"><a class="page-link text-blue p-2 px-2 shadow-sm" 
                    href="<?= $this->_link ?>page-1<?= $this->_anchor ?>">1...</a></li>
                <?php }
                // Boucle pour afficher la pagination
                for ($i = $this->_firstPage; $i <= $this->_nbPages && $i < $this->_lastPage ; $i++) {
                ?> 
                <li class="page-item <?= $i == $this->_currentPage ? "active disabled" : "" ?> shadow-sm">
                    <a class="page-link text-blue" href="<?= $this->_link ?>page-<?= $i . $this->_anchor ?>"><?= $i ?></a></li>
                <?php 
                }
                if ($this->_currentPage < $this->_nbPages - 1 && $this->_nbPages > 3) { ?>
                <!-- Lien vers la dernière page -->
                <li class="page-item shadow-sm"><a class="page-link px-2 text-blue" 
                    href="<?= $this->_link ?>page-<?= $this->_nbPages . $this->_anchor ?>">...<?= $this->_nbPages ?></a></li>
                <?php } ?>
                <!-- Lien vers la page suivante -->
                <li class="page-item <?= $this->_currentPage != $this->_nbPages ? "" : "disabled" ?> shadow-sm">
                    <a class="page-link <?= $this->_currentPage != $this->_nbPages ? "text-blue" : "" ?> font-weight-bold" 
                        href="<?= $this->_link ?>page-<?= ($this->_currentPage + 1) . $this->_anchor ?>">
                        <span aria-hidden="true">&raquo;</span></a>
                </li>
            </ul>
        </nav>
    </div> 
</div>
<?php 
}
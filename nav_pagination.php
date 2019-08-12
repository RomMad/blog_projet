<?php 
if ($nbItems>0) {
?>
    <div class="row">

        <div class="col-sm-5 my-1">
            <form action="<?= $linkNbDisplayed ?>" method="post" class="form-inline">
                <label class="col-form-label mr-1" for="nbDisplayed">Affichés</label>
                <select name="nbDisplayed" id="nbDisplayed" class="custom-select form-control-sm mr-1 shadow-sm" >
                    <option value="5" <?= $nbDisplayed  == 5 ? "selected" : "" ?> >5</option>
                    <option value="10" <?= $nbDisplayed == 10 ? "selected" : "" ?> >10</option>
                    <option value="15" <?= $nbDisplayed == 15 ? "selected" : "" ?> >15</option>
                    <option value="20" <?= $nbDisplayed == 20 ? "selected" : "" ?> >20</option>
                    <option value="50" <?= $nbDisplayed == 50 ? "selected" : "" ?> >50</option>
                </select>
                <input type="submit" id="pagination" class="btn btn-blue form-control-smcpt-1 px-lg-3 px-md-2 shadow-sm" value="OK">
                                                        
            </form>
        </div>

        <div class="col-sm-7 my-1">
        <?php 
            if ($nbPages > 1) {
            ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end">
                        <li class="page-item <?= $prevPageLink ?> shadow-sm">
                        <a class="page-link <?= $prevPageColorLink ?> font-weight-bold"href="<?= $linkPagination ?>page=<?= $prevPage . $anchorPagination ?>" tabindex="-1" aria-disabled="true"><span aria-hidden="true">&laquo;</span></a>
                        </li>
                        <?php 
                            if ($currentPage > 2 && $nbPages > 3) {
                        ?>
                                <li class="page-item"><a class="page-link text-blue px-2 shadow-sm" href="<?= $linkPagination ?>page=1<?= $anchorPagination ?>">1...</a></li>
                        <?php                                                               
                            }
                        ?>
                        <li class="page-item <?= $activepageLink_1 ?> shadow-sm"><a class="page-link text-blue" href="<?= $linkPagination ?>page=<?= $pageLink_1 . $anchorPagination ?>"><?= $pageLink_1 ?></a></li>
                        <li class="page-item <?= $activepageLink_2 ?> shadow-sm"><a class="page-link text-blue" href="<?= $linkPagination ?>page=<?= $pageLink_2 . $anchorPagination ?>"><?= $pageLink_2 ?></a></li>

                        <?php 
                            if ($nbPages > 2) {
                        ?>
                                <li class="page-item <?= $activepageLink_3 ?> shadow-sm"><a class="page-link text-blue" href="<?= $linkPagination ?>page=<?= $pageLink_3 . $anchorPagination ?>"><?= $pageLink_3 ?></a></li>
                        <?php 
                            }
                        ?>

                        <?php 
                            if ($currentPage < $nbPages -1 && $nbPages > 3) {
                        ?>
                                <li class="page-item shadow-sm"><a class="page-link px-2 text-blue" href="<?= $linkPagination ?>page=<?= $nbPages . $anchorPagination ?>">...<?= $nbPages ?></a></li>
                        <?php 
                            }
                        ?>
                            <li class="page-item <?= $nextPageLink ?> shadow-sm">
                        <a class="page-link <?= $nextPageColorLink ?> font-weight-bold"" href="<?= $linkPagination ?>page=<?= $nextPage . $anchorPagination ?>"><span aria-hidden="true">&raquo;</span></a>
                        </li>
                    </ul>
                </nav>

            <?php 
            }
            ?>
        </div>
    </div>
    
<?php
}
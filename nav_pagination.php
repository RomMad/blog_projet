<div class="row">

    <div class="col-md-6">
        <form action="<?= $linkNbDisplayed ?>" method="post" class="form-inline">
            <label class="mr-2 col-form-label-sm" for="nbDisplayed">Nb affichés</label>
            <select name="nbDisplayed" id="nbDisplayed" class="custom-select mr-sm-2 form-control-sm" >
                <option value="5" <?= $nbDisplayed==5 ? "selected" : "" ?> >5</option>
                <option value="10" <?= $nbDisplayed==10 ? "selected" : "" ?> >10</option>
                <option value="15" <?= $nbDisplayed==15 ? "selected" : "" ?> >15</option>
                <option value="20" <?= $nbDisplayed==20 ? "selected" : "" ?> >20</option>
            </select>
            <button type="submit" class="btn btn-info form-control-sm">OK</button>
        </form>
    </div>

    <div class="col-md-6">
    <?php 
        if ($nbPages>1) {
        ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= $prevPageLink ?>">
                    <a class="page-link <?= $prevPageColorLink ?> font-weight-bold"href="<?= $linkPagination ?>?page=<?= $prevPage ?><?= $ancrePagination ?>" tabindex="-1" aria-disabled="true"><</a>
                    </li>
                    <?php 
                        if ($page>2 && $nbPages>3) {
                    ?>
                            <li class="page-item"><a class="page-link text-info" href="<?= $linkPagination ?>?page=1"<?= $ancrePagination ?>>1...</a></li>
                    <?php 
                        };
                    ?>
                    <li class="page-item <?= $activepageLink_1 ?>"><a class="page-link text-info" href="<?= $linkPagination ?>?page=<?= $pageLink_1 ?><?= $ancrePagination ?>"><?= $pageLink_1 ?></a></li>
                    <li class="page-item <?= $activepageLink_2 ?>"><a class="page-link text-info" href="<?= $linkPagination ?>?page=<?= $pageLink_2 ?><?= $ancrePagination ?>"><?= $pageLink_2 ?></a></li>

                    <?php 
                        if ($nbPages>2) {
                    ?>
                            <li class="page-item <?= $activepageLink_3 ?>"><a class="page-link text-info" href="<?= $linkPagination ?>?page=<?= $pageLink_3 ?><?= $ancrePagination ?>"><?= $pageLink_3 ?></a></li>
                    <?php 
                        };
                    ?>

                    <?php 
                        if ($page<$nbPages-1 && $nbPages>3) {
                    ?>
                            <li class="page-item"><a class="page-link text-info" href="<?= $linkPagination ?>?page=<?= $nbPages ?><?= $ancrePagination ?>">...<?= $nbPages ?></a></li>
                    <?php 
                        };
                    ?>
                        <li class="page-item <?= $nextPageLink ?>">
                    <a class="page-link <?= $nextPageColorLink ?> font-weight-bold"" href="<?= $linkPagination ?>?page=<?= $nextPage ?><?= $ancrePagination ?>">></a>
                    </li>
                </ul>
            </nav>

        <?php 
        };
        ?>
    </div>
</div>
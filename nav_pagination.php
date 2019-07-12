<?php 
    if ($nbPages>1) {
?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-end">
                <li class="page-item <?= $prevPageLink ?>">
                <a class="page-link <?= $prevPageColorLink ?>"href="<?= $link ?>?page=<?= $prevPage ?><?= $ancre ?>" tabindex="-1" aria-disabled="true">Précédent</a>
                </li>
                <?php 
                    if ($page>2) {
                ?>
                        <li class="page-item"><a class="page-link text-info" href="<?= $link ?>?page=1"<?= $ancre ?>>1...</a></li>
                <?php 
                    };
                ?>
                <li class="page-item <?= $activepageLink_1 ?>"><a class="page-link text-info" href="<?= $link ?>?page=<?= $pageLink_1 ?><?= $ancre ?>"><?= $pageLink_1 ?></a></li>
                <li class="page-item <?= $activepageLink_2 ?>"><a class="page-link text-info" href="<?= $link ?>?page=<?= $pageLink_2 ?><?= $ancre ?>"><?= $pageLink_2 ?></a></li>
                <li class="page-item <?= $activepageLink_3 ?>"><a class="page-link text-info" href="<?= $link ?>?page=<?= $pageLink_3 ?><?= $ancre ?>"><?= $pageLink_3 ?></a></li>
                <?php 
                    if ($page<$nbPages-1) {
                ?>
                        <li class="page-item"><a class="page-link text-info" href="<?= $link ?>?page=<?= $nbPages ?><?= $ancre ?>">...<?= $nbPages ?></a></li>
                <?php 
                    };
                ?>
                    <li class="page-item <?= $nextPageLink ?>">
                <a class="page-link <?= $nextPageColorLink ?>"" href="<?= $link ?>?page=<?= $nextPage ?><?= $ancre ?>">Suivant</a>
                </li>
            </ul>
        </nav>

<?php 
    };
?>
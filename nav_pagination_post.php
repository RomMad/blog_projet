<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end">
        <li class="page-item <?= $prevPageLink ?>">
        <a class="page-link <?= $prevPageColorLink ?>"href="post.php?page=<?= $prevPage ?>#comments"" tabindex="-1" aria-disabled="true">Précédent</a>
        </li>
        <?php 
            if ($page>2) {
        ?>
                <li class="page-item"><a class="page-link text-info" href="post.php?page=1#comments">1 ...</a></li>
        <?php 
            };
        ?>

        <li class="page-item <?= $activepageLink_1 ?>"><a class="page-link text-info" href="post.php?page=<?= $pageLink_1 ?>#comments"><?= $pageLink_1 ?></a></li>

        <?php 
            if ($nbPages>=2) {
        ?>
                <li class="page-item <?= $activepageLink_2 ?>"><a class="page-link text-info" href="post.php?page=<?= $pageLink_2 ?>#comments"><?= $pageLink_2 ?></a></li>
        <?php 
            };
        ?>
        <?php 
            if ($page>=3) {
        ?>
                <li class="page-item <?= $activepageLink_3 ?>"><a class="page-link text-info" href="post.php?page=<?= $pageLink_3 ?>#comments"><?= $pageLink_3 ?></a></li>
        <?php 
            };
        ?>
        <?php 
            if ($page<$nbPages-1) {
        ?>
                <li class="page-item"><a class="page-link text-info" href="post.php?page=<?= $nbPages ?>#comments">... <?= $nbPages ?></a></li>
        <?php 
            };
        ?>
            <li class="page-item <?= $nextPageLink ?>">
        <a class="page-link <?= $nextPageColorLink ?>"" href="post.php?page=<?= $nextPage ?>#comments">Suivant</a>
        </li>
    </ul>
</nav>
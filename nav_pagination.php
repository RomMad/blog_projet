<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end">
        <li class="page-item <?= $prevPageLink ?>">
        <a class="page-link <?= $prevPageColorLink ?>"href="blog.php?page=<?= $prevPage ?>" tabindex="-1" aria-disabled="true">Précédent</a>
        </li>
        <?php 
            if ($page>2) {
        ?>
                <li class="page-item"><a class="page-link text-info" href="blog.php?page=1">1 ...</a></li>
        <?php 
            };
        ?>
        <li class="page-item <?= $activepageLink_1 ?>"><a class="page-link text-info" href="blog.php?page=<?= $pageLink_1 ?>"><?= $pageLink_1 ?></a></li>
        <li class="page-item <?= $activepageLink_2 ?>"><a class="page-link text-info" href="blog.php?page=<?= $pageLink_2 ?>"><?= $pageLink_2 ?></a></li>
        <li class="page-item <?= $activepageLink_3 ?>"><a class="page-link text-info" href="blog.php?page=<?= $pageLink_3 ?>"><?= $pageLink_3 ?></a></li>
        <?php 
            if ($page<$nbPages-1) {
        ?>
                <li class="page-item"><a class="page-link text-info" href="blog.php?page=<?= $nbPages ?>">... <?= $nbPages ?></a></li>
        <?php 
            };
        ?>
            <li class="page-item <?= $nextPageLink ?>">
        <a class="page-link <?= $nextPageColorLink ?>"" href="blog.php?page=<?= $nextPage ?>">Suivant</a>
        </li>
    </ul>
</nav>
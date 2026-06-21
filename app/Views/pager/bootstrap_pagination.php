<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-end mb-0">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a class="page-link rounded-start"
                    href="<?= $pager->getFirst() . (strpos($pager->getFirst(), '?') !== false ? '&' : '?') . 'active_tab=' . (strpos($_SERVER['REQUEST_URI'], 'page_log=') !== false ? 'aktivitas' : 'riwayat-shift') ?>"
                    aria-label="First">
                    <span aria-hidden="true">&laquo;&laquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link"
                    href="<?= $pager->getPrevious() . (strpos($pager->getPrevious(), '?') !== false ? '&' : '?') . 'active_tab=' . (strpos($_SERVER['REQUEST_URI'], 'page_log=') !== false ? 'aktivitas' : 'riwayat-shift') ?>"
                    aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <?php
                // Tentukan tab berdasarkan parameter URL Pager
                $activeTab = 'data-user';
                if (strpos($link['uri'], 'page_log=') !== false) {
                    $activeTab = 'aktivitas';
                } elseif (strpos($link['uri'], 'page_shift=') !== false || strpos($link['uri'], 'page_shift_group=') !== false) {
                    $activeTab = 'riwayat-shift';
                }
                ?>
                <a class="page-link" href="<?= $link['uri'] . '&active_tab=' . $activeTab ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a class="page-link"
                    href="<?= $pager->getNext() . (strpos($pager->getNext(), '?') !== false ? '&' : '?') . 'active_tab=' . (strpos($_SERVER['REQUEST_URI'], 'page_log=') !== false ? 'aktivitas' : 'riwayat-shift') ?>"
                    aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link rounded-end"
                    href="<?= $pager->getLast() . (strpos($pager->getLast(), '?') !== false ? '&' : '?') . 'active_tab=' . (strpos($_SERVER['REQUEST_URI'], 'page_log=') !== false ? 'aktivitas' : 'riwayat-shift') ?>"
                    aria-label="Last">
                    <span aria-hidden="true">&raquo;&raquo;</span>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>
<?php
extract($pager);
$pages = [];
$activePage = (int)$activePage;
$displayLimit = (int)$displayLimit;
$firstPageOfPreviousChunk = 1;

if(isset($pagesArrayChunks)) {
    $pages = $pagesArrayChunks[$pageSetKey];
}

$inLastPage = $activePage === (int)$totalPages;
$inFirstPage = $activePage === 1;
$inLastChunk = $pageSetKey === count($pagesArrayChunks) - 1;
$inFirstChunk = $pageSetKey === 0;
if(($pageSetKey - 1) > -1) {
    $firstPageOfPreviousChunk = min($pagesArrayChunks[$pageSetKey - 1]);
}
?>
<ul>
    <li class="prev <?php echo ($inFirstPage) ? 'disabled' : ''; ?>">
        <a class="" href="#">« Previous</a>
    </li>

    <?php if(!$inFirstChunk && !$inFirstPage) { ?>
        <li class="numbers gigya-page">
            <a href="<?php echo $pagerURL; ?>/1" data-page="1">1</a>
        </li>
    <?php }?>

    <?php if($activePage > $displayLimit) { ?>
        <li class="ccm-pagination-ellipses gigya-page">
            <a href="<?php echo $pagerURL . '/' . $firstPageOfPreviousChunk; ?>">...</a>
        </li>
    <?php }?>

    <?php
    foreach ($pages as $page) {
        $page = (int)$page;
        ?>
        <li class="numbers <?php echo ($page === $activePage) ? 'disabled' : ''?> gigya-page">
            <a href="<?php echo $pagerURL . '/' . $page; ?>" data-page="<?php echo $page; ?>"><?php echo $page; ?></a>
        </li>
    <?php } ?>

    <?php if(!$inLastPage && !$inLastChunk) { ?>
        <li class="ccm-pagination-ellipses gigya-page">
            <a href="<?php echo $pagerURL . '/' . (max($pages) + 1); ?>">...</a>
        </li>
    <?php }?>

    <?php if($totalCount > 0 && (!$inLastPage && !$inLastChunk)) { ?>
        <li class="numbers <?php echo ($inLastPage) ? 'disabled' : ''?> gigya-page">
            <a href="<?php echo $pagerURL . '/' . $totalPages; ?>" data-page="<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
        </li>
    <?php } ?>

    <li class="next <?php echo ($inLastPage) ? 'disabled' : ''; ?>">
        <a class="" href="#">Next »</a>
    </li>
</ul>

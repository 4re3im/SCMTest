<?php
$total = intval($total);
$page = intval($page);
$limit = intval($limit);

$totalPages = ceil($total/$limit);
if ($total < $limit) {
    $totalPages = 1;
}
$totalSets = round($totalPages / $limit, 0, PHP_ROUND_HALF_UP);

$initial = 0;
$terminal = 0;

if ($totalPages < $limit) {
    $initial = 1;
    $terminal = $totalPages;
} else {
    for ($i = 1; $i <= $totalSets; $i++) {
        if ($page <= $i * $limit) {
            $terminal = intval($i * $limit);
            $initial = ($terminal - $limit) + 1;
            break;
        }
    }
}
?>
<ul>
    <li class="prev <?php echo $page === 1 ? 'disabled' : ''; ?>">
        <a class="" href="#">« Previous</a>
    </li>

    <?php if($page > $limit) { ?>
        <li class="numbers">
            <a href="#" data-page="1">1</a>
        </li>
    <?php }?>

    <?php if ($initial > $limit) { ?>
        <li class="ccm-pagination-ellipses numbers">
            <a href="#" data-page="<?php echo $initial - 1; ?>">...</a>
        </li>
    <?php }?>

    <?php
    for ($j = $initial; $j <= $terminal; $j++) { ?>
        <li class="numbers <?php echo $page === $j ? 'disabled active' : ''; ?>">
            <a href="" data-page="<?php echo $j; ?>"><?php echo $j; ?></a>
        </li>
    <?php } ?>

    <?php if ($totalPages > $terminal) { ?>
        <li class="ccm-pagination-ellipses numbers">
            <a href="#" data-page="<?php echo $terminal + 1; ?>">...</a>
        </li>

        <li class="numbers">
            <a href="#" data-page="<?php echo $totalPages; ?>">
                <?php echo $totalPages; ?>
            </a>
        </li>
    <?php } ?>


    <li class="next <?php echo $page === $totalPages ? 'disabled' : ''?>">
        <a class="" href="#">Next »</a>
    </li>
</ul>
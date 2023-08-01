<?php
$pkgHandle = 'institution_management';
?>
<div class="ccm-list-wrapper">
    <?php if ($totalCount === 0) { ?>
        <p>There are no institutions found related to your search.</p>
    <?php } else { ?>
        <table class="ccm-results-list" id="app-institutions-list">
            <thead>
            <tr>
                <?php
                foreach ($headers as $header) {
                    echo '<th>' . $header . '</th>';
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            echo Loader::packageElement(
                'search/table_body',
                $pkgHandle,
                ['headers' => $headers, 'results' => $results]
            )
            ?>
            </tbody>
        </table>
    <?php }?>
</div>
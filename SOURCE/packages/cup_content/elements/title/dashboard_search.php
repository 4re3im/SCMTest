
<?php if (count($titles) > 0): ?>

    <table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
        <thead>
            <tr>
                <th>Thumbnail</th>
                <th width="200" class="<?php echo $titleList->getSearchResultsClass('name') ?>">
                    <a href="<?php echo $titleList->getSortByURL('name', 'asc') ?>"
                       onclick="return sortColumn(this);"><?php echo t('Name') ?>
                    </a>
                </th>
                <th class="<?php echo $titleList->getSearchResultsClass('prettyUrl') ?>">
                    <a href="<?php echo $titleList->getSortByURL('prettyUrl', 'asc') ?>"
                       onclick="return sortColumn(this);"><?php echo t('Pretty Url') ?>
                    </a>
                </th>
                <th class="<?php echo $titleList->getSearchResultsClass('modifiedAt') ?>">
                    <a href="<?php echo $titleList->getSortByURL('modifiedAt', 'asc') ?>"
                       onclick="return sortColumn(this);"><?php echo t('Modified At') ?>
                    </a>
                </th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($titles as $idx => $title): ?>
                <tr ref="<?php echo $title->id; ?>">
                    <td><img src="<?php echo $title->getImageURL(60); ?>"/></td>
                    <td><?php echo $title->name; ?></td>
                    <td><?php echo $title->prettyUrl; ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($title->modifiedAt)); ?></td>
                    <td>
                        <a href="<?php echo View::url('/dashboard/cup_content/titles/show', $title->id) .
                            '?keywords=' . $_REQUEST['keywords'] . '&isbn=' . $_REQUEST['isbn']; ?>">VIEW</a>
                        &nbsp;&nbsp;
                        <a href="javascript:deleteItem(<?php echo $title->id; ?>);">DELETE</a><br />
                        <a href="<?php echo View::url('/go_product_editor/' . $title->id) ?>">Edit Go contents</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <style>
        .custom_pagination .pager span{
            margin:0px 5px;
        }
    </style>
    <div class="custom_pagination">
        <?php if ($pagination->getTotalPages() > 1): ?>
            <?php
            $pagination->jsFunctionCall = 'gotoPage';
            ?>
            <div style="float:right" class="pager">
                <?php echo $pagination->getPrevious('Previous'); ?> &nbsp;|&nbsp;
                <?php echo $pagination->getPages(); ?> &nbsp;|&nbsp;
                <?php echo $pagination->getNext('Next'); ?>
            </div>
        <?PHP endif; ?>
        Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> |
        Total Results: <?php echo $pagination->result_count; ?>
    </div>

<?php else: ?>
    <div id="ccm-list-none"><?php echo t('No titles found.') ?></div>
<?php endif; ?>
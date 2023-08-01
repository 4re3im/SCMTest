<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php if(count($series) > 0):?>
    
    <table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
        <thead>
            <tr>
                <th>Image</th>
                <th width="200" class="<?php echo $seriesList->getSearchResultsClass('name')?>"><a href="<?php echo $seriesList->getSortByURL('name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Name')?></a></th>
                <th class="<?php echo $seriesList->getSearchResultsClass('prettyUrl')?>"><a href="<?php echo $seriesList->getSortByURL('prettyUrl', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Pretty Url')?></a></th>
                <th class="<?php echo $seriesList->getSearchResultsClass('modifiedAt')?>"><a href="<?php echo $seriesList->getSortByURL('modifiedAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Modified At')?></a></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($series as $idx=>$each_series):?>
                <tr ref="<?php echo $each_series->id;?>">
                    <td><img src="<?php echo $each_series->getImageURL(60);?>"/></td>
                    <td><?php echo $each_series->name;?></td>
                    <td><?php echo $each_series->prettyUrl;?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($each_series->modifiedAt));?></td>
                    <td>
                        <a href="<?php echo View::url('/dashboard/cup_content/series/edit', $each_series->id);?>">VIEW</a>
                        <br />
                        <a href="javascript:deleteItem(<?php echo $each_series->id;?>);">DELETE</a>
                        <br />
                        <?php // SB-385 added by jbernardez 20191029 ?>
                        <a href="<?php echo View::url('/go_series_editor', $each_series->id);?>">Edit Series<br />Content</a>
                    </td>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
    
    <style>
        .custom_pagination .pager span{
            margin:0px 5px;
        }
    </style>
    <div class="custom_pagination">
        <?php if($pagination->getTotalPages() > 1):?>
            <?php
                $pagination->jsFunctionCall = 'gotoPage';
            ?>
            <div style="float:right" class="pager">
                <?php echo $pagination->getPrevious('Previous');?> &nbsp;|&nbsp; <?php echo $pagination->getPages();?> &nbsp;|&nbsp; <?php echo $pagination->getNext('Next');?>
            </div>
        <?php endif;?>
        Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> | Total Results: <?php echo $pagination->result_count; ?>
    </div>

<?php else:?>
    <div id="ccm-list-none"><?php echo t('No series found.')?></div>
<?php endif;?>
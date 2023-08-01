
<?php if(count($subjects) > 0):?>
    
    <table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
        <thead>
            <tr>
                <th width="200" class="<?php echo $subjectList->getSearchResultsClass('name')?>"><a href="<?php echo $subjectList->getSortByURL('name', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Name')?></a></th>
                <th class="<?php echo $subjectList->getSearchResultsClass('isPrimary')?>"><a href="<?php echo $subjectList->getSortByURL('isPrimary', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('isPrimary')?></a></th>
                <th class="<?php echo $subjectList->getSearchResultsClass('isSecondary')?>"><a href="<?php echo $subjectList->getSortByURL('isSecondary', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('isSecondary')?></a></th>
                <?php // SB-399 added by jbernardez 20191112 ?>
                <th class="<?php echo $subjectList->getSearchResultsClass('isEnabled')?>"><a href="<?php echo $subjectList->getSortByURL('isEnabled', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('isEnabled')?></a></th>
                <th class="<?php echo $subjectList->getSearchResultsClass('prettyUrl')?>"><a href="<?php echo $subjectList->getSortByURL('prettyUrl', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Pretty Url')?></a></th>
                <th class="<?php echo $subjectList->getSearchResultsClass('modifiedAt')?>"><a href="<?php echo $subjectList->getSortByURL('modifiedAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Modified At')?></a></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($subjects as $idx=>$subject):?>
                <tr ref="<?php echo $subject->id;?>">
                    <td><?php echo $subject->name;?></td>
                    <td>
                        <?php if($subject->isPrimary):?>
                            YES
                        <?php endif;?>
                    </td>
                    <td>
                        <?php if($subject->isSecondary):?>
                            YES
                        <?php endif;?>
                    </td>
                    <?php // SB-399 added by jbernardez 20191112 ?>
                    <td>
                        <?php if ($subject->isEnabled):?>
                            YES
                        <?php endif;?>
                    </td>
                    <td><?php echo $subject->prettyUrl;?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($subject->modifiedAt));?></td>
                    <td>
                        <a href="<?php echo View::url('/dashboard/cup_content/subjects/edit', $subject->id);?>">EDIT</a>
                        &nbsp;&nbsp;
                        <a href="javascript:deleteItem(<?php echo $subject->id;?>);">DELETE</a>
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
        <?PHP endif;?>
        Page <?php echo $pagination->getRequestedPage(); ?> of <?php echo $pagination->getTotalPages(); ?> | Total Results: <?php echo $pagination->result_count; ?>
    </div>

<?php else:?>
    <div id="ccm-list-none"><?php echo t('No subjects found.')?></div>
<?php endif;?>
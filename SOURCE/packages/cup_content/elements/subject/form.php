<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 
?>
<?php echo $form->hidden('id', @$entry['id']); ?>

<div class="span16">

    <div class="clearfix">
        <?php echo $form->label('name', t('Name') . '   <span class="ccm-required">*</span>')?>
        <div class="input">
            <?php echo $form->text('name', @$entry['name'], array('class' => "span6"))?>
        </div>
    </div>
    
    <div class="clearfix">
        <?php echo $form->label('description', t('Description') . '')?>
        <div class="input">
            <?php echo $form->textarea('description', @$entry['description'], array('class' => "span6"))?>
        </div>
    </div>
    
    <div class="clearfix">
        <?php echo $form->label('Department', t('Department') . '')?>
        <div class="input">
            <?php echo $form->checkbox('isPrimary', 1, @$entry['isPrimary'])?> Primary <br />
            <?php echo $form->checkbox('isSecondary', 1, @$entry['isSecondary'])?> Secondary
        </div>
    </div>

    <?php // SB-398 added by jbernardez 20191112 ?>
    <div class="clearfix">
        <?php echo $form->label('Enabled', t('Enabled') . '')?>
        <div class="input">
            <?php echo $form->checkbox('isEnabled', 1, @$entry['isEnabled'])?> &nbsp;
        </div>
    </div>
    
    <div class="clearfix">
        <?php echo $form->label('region', t('Region') . '')?>
        <div class="input">
            <?php 
                $region_options = array(
                    'ALL' => 'All',
                    'AU'  => 'Australia',
                    'NZ'  => 'New Zealand'
                );
            ?>
            <?php echo $form->select('region', $region_options, @$entry['region'])?>
        </div>
    </div>
</div>

<div class="clearfix"></div>
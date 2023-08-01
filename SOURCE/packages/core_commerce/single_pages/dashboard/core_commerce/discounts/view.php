<?php  if (is_object($type) && ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add')) { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Discount'), false, 'span12 offset2', false)?>
<form method="post" action="<?php echo $this->action('add')?>" id="ccm-core-commerce-discount-form">
<div class="ccm-pane-body">
	<?php  Loader::packageElement("discount/type_form_required", 'core_commerce', array('type' => $type)); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/discounts')?>" class="btn"><?php echo t('Cancel')?></a>
	<input type="submit" class="btn primary ccm-button-right" value="<?php echo t('Add')?>" />
</div>
</form>	

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php  } else if (isset($discount)) { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Discount'), false, 'span12 offset2', false)?>

<form method="post" action="<?php echo $this->action('edit')?>" id="ccm-core-commerce-discount-form">
<div class="ccm-pane-body">
<?php  Loader::packageElement("discount/type_form_required", 'core_commerce', array('type' => $type, 'discount' => $discount)); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/discounts')?>" class="btn"><?php echo t('Cancel')?></a>
	<input type="submit" class="btn primary ccm-button-right" value="<?php echo t('Save')?>" />
</div>
</form>	

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>


<?php  } else if ($this->controller->getTask() == 'manage_discount_types' || $this->controller->getTask() == 'discount_type_added') { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Manage Discount Types'))?>


	<?php  if (count($types) == 0) { ?>
		<p><?php echo t('There are no discount types installed.')?></p>
	<?php  } else { ?>
	
	<table class="grid-list zebra-striped" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<th><?php echo t('Handle')?></td>
		<th><?php echo t('Name')?></td>
	</tr>
	<?php  foreach($types as $st) { ?>
		<tr>
			<td><?php echo $st->getDiscountTypeHandle()?></td>
			<td><?php echo $st->getDiscountTypeName()?></td>
		</tr>
	<?php  } ?>
	</table>
	<?php  } ?>
	
	<h3><span><?php echo t('Custom Discount Types')?></span></h3>
	<?php  $types = CoreCommercePendingDiscountType::getList(); ?>
	<?php  if (count($types) == 0) { ?>
		<?php echo t('There are no available discount types awaiting installation.')?>
	<?php  } else { ?>

		<ul id="ccm-block-type-list">
			<?php  foreach($types as $at) { ?>
				<li class="ccm-block-type ccm-block-type-available">
					<form id="discount_type_install_form_<?php echo $at->getDiscountTypeHandle()?>" style="margin: 0px" method="post" action="<?php echo $this->action('add_discount_type')?>">
						<?php 
						print $form->hidden("discountTypeHandle", $at->getDiscountTypeHandle());
						?>
						<p class="ccm-block-type-inner"><?php echo $ih->submit(t("Install"), 'submit', 'right', 'small')?><?php echo $at->getDiscountTypeName()?></p>
					</form>
				</li>
			<?php  } ?>
		</ul>
	<?php  } ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>
	
<?php  } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Discounts'), false, false, false)?>

<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
<a href="<?php echo $this->action('manage_discount_types')?>" id="ccm-list-view-customize-top"><span class="ccm-menu-icon ccm-icon-properties"></span><?php echo t('Manage Discount Types')?></a>
</div>
</div>


<div class="ccm-pane-body ccm-pane-body-footer">

<?php  if (count($discounts) == 0) { ?>
	<?php echo t('No discounts defined.')?>
<?php  } else { ?>

<table border="0" cellspacing="1" cellpadding="0" class="zebra-striped">
<tr>
	<th><?php echo t('Name')?></th>
	<th><?php echo t('Type')?></th>
	<th><?php echo t('Valid From')?></th>
	<th><?php echo t('Valid To')?></th>
	<th><?php echo t('Code')?></th>
	<th><?php echo t('Enabled?')?></th>
</tr>


	<?php 
	foreach($discounts as $d) { ?>
	<tr id="discountID_<?php echo $d->getDiscountID()?>">
		<td><a href="<?php echo $this->url('/dashboard/core_commerce/discounts', 'edit', $d->getDiscountID())?>"><?php echo $d->getDiscountName()?></a></td>
		<td><?php echo $d->getDiscountType()->getDiscountTypeName()?></td>
		<td><?php echo $date->getLocalDateTime($d->getDiscountStart())?></td>
		<td><?php echo $date->getLocalDateTime($d->getDiscountEnd())?></td>
		<td><?php echo $d->getDiscountCode()?></td>
		<td><?php echo  ($d->isDiscountEnabled()) ? t('Yes') : t('No') ?></td>
	</tr>
	
	<?php  } ?>

</table>

<?php  } ?>

<h3><?php echo t('Choose Discount Type')?></h3>

<form method="get" action="<?php echo $this->action('select_type')?>" id="ccm-core-commerce-discount-type-form">

<?php echo $form->select('discountTypeID', $seltypes)?>

<?php echo $form->submit('submit', t('Go'))?>

</form>
</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>

<?php  } ?>
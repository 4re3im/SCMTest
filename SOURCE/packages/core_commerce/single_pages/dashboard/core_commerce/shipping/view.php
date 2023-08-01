<?php  if (isset($type)) { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Shipping Type'), false, 'span16', false)?>

<form method="post" action="<?php echo $this->action('save')?>" id="ccm-core-commerce-shipping-type-form">
<div class="ccm-pane-body">
	<?php  Loader::packageElement("shipping/type_form_required", 'core_commerce', array('type' => $type)); ?>
</div>
<div class="ccm-pane-footer">
	<a href="<?php echo $this->url('/dashboard/core_commerce/shipping')?>" class="btn"><?php echo t('Back to Shipping')?></a>
	<?php echo $ih->submit(t('Save'), 'ccm-core-commerce-shipping-type-form', 'right', 'primary')?>
</div>
</form>	

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php  } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Shipping Types'))?>

<?php  if (count($types) == 0) { ?>
	<p><?php echo t('There are no shipping types installed.')?></p>
<?php  } else { ?>

<table class="grid-list zebra-striped" border="0" cellspacing="1" cellpadding="0">
<tr>
	<th><?php echo t('Name')?></th>
	<th><?php echo t('Handle')?></th>
	<th><?php echo t('Enabled')?></th>
</tr>
<?php  foreach($types as $st) { ?>
	<tr>
		<td><a href="<?php echo $this->url('/dashboard/core_commerce/shipping', 'edit_type', $st->getShippingTypeID())?>"><?php echo $st->getShippingTypeName()?></a></td>
		<td><?php echo $st->getShippingTypeHandle()?></td>
		<td><?php echo $st->isShippingTypeEnabled() ? t('Yes') : t('No')?></td>
	</tr>
<?php  } ?>
</table>
<?php  } ?>

<h3><?php echo t('Custom Shipping Types')?></h3>
<?php  $types = CoreCommercePendingShippingType::getList(); ?>
<?php  if (count($types) == 0) { ?>
	<?php echo t('There are no available shipping types awaiting installation.')?>
<?php  } else { ?>

	<ul id="ccm-block-type-list">
		<?php  foreach($types as $at) { ?>
			<li class="ccm-block-type ccm-block-type-available">
				<form id="shipping_type_install_form_<?php echo $at->getShippingTypeHandle()?>" style="margin: 0px" method="post" action="<?php echo $this->action('add_shipping_type')?>">
					<?php 
					print $form->hidden("shippingTypeHandle", $at->getShippingTypeHandle());
					?>
					<p class="ccm-block-type-inner"><?php echo $ih->submit(t("Install"), 'submit', 'right', 'small')?><?php echo $at->getShippingTypeName()?></p>
				</form>
			</li>
		<?php  } ?>
	</ul>

<?php  } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>

<?php  } ?>
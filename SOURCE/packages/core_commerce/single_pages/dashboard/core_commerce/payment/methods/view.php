<?php  if (isset($method)) { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Payment Method'), false, 'span12 offset2', false)?>
<form method="post" action="<?php echo $this->action('save')?>" id="ccm-core-commerce-payment-method-form">
<div class="ccm-pane-body">
	<?php  Loader::packageElement("payment/method_form_required", 'core_commerce', array('method' => $method)); ?>
</div>
<div class="ccm-pane-footer">
<?php 
			$valt = Loader::helper('validation/token');
			$ih = Loader::helper('concrete/interface');
			$delConfirmJS = t('Delete this payment method?');
			?>
			<script type="text/javascript">
			deleteMethod = function() {
				var url = "<?php echo $this->url('/dashboard/core_commerce/payment/methods', 'delete_method', $method->getPaymentMethodID(), $valt->generate('delete_method'))?>";
				if (confirm('<?php echo $delConfirmJS?>')) {
					location.href = url;				
				}
			}
			</script>
	<a href="<?php echo $this->action('view')?>" class="btn"><?php echo t('Back to Payment Methods')?></a>
	<input type="submit" class="primary ccm-button-v2-right btn" value="<?php echo t('Save')?>" />
   <?php  print $ih->button_js(t('Delete'), "deleteMethod()", 'right', 'error');?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

<?php  } else { ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Payment Methods'))?>

	<?php  if (count($methods) == 0) { ?>
		<p><?php echo t('There are no payment methods installed.')?></p>
	<?php  } else { 
	
	$mls = Loader::helper('multilingual','core_commerce');
	?>
	
	<table class="grid-list zebra-striped" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<th><?php echo t('Name')?></td>
		<th><?php echo t('Handle')?></td>
		<?php  if($mls->isEnabled()) { ?>
				<th><?php echo t('Language')?></td>
		<?php  } ?>
		<th><?php echo t('Enabled')?></td>
	</tr>
	<?php 
	foreach($methods as $st) { ?>
         <tr>
            <td><a href="<?php echo $this->url('/dashboard/core_commerce/payment/methods', 'edit_method', $st->getPaymentMethodID())?>"><?php echo $st->getPaymentMethodName()?></a></td>
            <td><?php echo $st->getPaymentMethodHandle()?></td>
            <?php  if($mls->isEnabled()) { ?>
               <td><?php  echo $st->getPaymentMethodLanguageIcon();?></td>
            <?php  } ?>
            <td><?php echo $st->isPaymentMethodEnabled() ? t('Yes') : t('No')?></td>
         </tr>
   <?php  } ?>
	</table>
	<?php  } ?>
	
<h3><?php echo t('Custom Payment Methods')?></h3>

<?php  $methods = CoreCommercePendingPaymentMethod::getList(); ?>
<?php  if (count($methods) == 0) { ?>
	<?php echo t('There are no available payment methods awaiting installation.')?>
<?php  } else { ?>

	<ul id="ccm-block-type-list">
		<?php  foreach($methods as $at) { ?>
			<li class="ccm-block-type ccm-block-type-available">
				<form id="payment_method_install_form_<?php echo $at->getPaymentMethodHandle()?>" style="margin: 0px" method="post" action="<?php echo $this->action('add_payment_method')?>">
					<?php 
					print $form->hidden("paymentMethodHandle", $at->getPaymentMethodHandle());
					?>
					<p class="ccm-block-type-inner"><?php echo $ih->submit(t("Install"), 'submit', 'right', 'small')?><?php echo $at->getPaymentMethodName()?></p>
				</form>
			</li>
		<?php  } ?>
	</ul>
<?php  } ?>

<?php  if ($missing_defaults) { ?>
   <h3><?php echo t('Restore Defaults')?></h3>
   <p><?php echo t('A default payment method was deleted.  Click here to restore default methods if desired.');?></p>
   <form id="payment_method_restore_defaults" style="margin: 0px" method="post" action="<?php echo $this->action('restore_defaults')?>">
   <div class="clearfix">
   <?php echo $ih->submit(t("Restore"), 'submit', 'left', 'small')?>
   </div>
   </form>
<?php  }?>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>

<?php  } ?>

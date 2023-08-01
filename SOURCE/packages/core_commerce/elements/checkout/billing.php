<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<form method="post" action="<?php echo $action?>">
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="25%"><label for="oEmail"><?php echo t('Email Address')?> <span class="ccm-required">*</span></label><?php echo $form->text('oEmail', $o->getOrderEmail())?></td>
		<td width="25%">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_first_name');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
		</td>
		<td width="25%">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_last_name');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
		</td>
		<td width="25%">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_phone');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<?php 
			$ak = CoreCommerceOrderAttributeKey::getByHandle('billing_address');
			echo $form_attribute->display($ak, $ak->isOrderAttributeKeyRequired());
			?>
			</td>
	</tr>
	<?php 
	$set = AttributeSet::getByHandle('core_commerce_order_billing');
	if (is_object($set)) { 
		$keys = $set->getAttributeKeys();
		foreach($keys as $ak) {
			if (!in_array($ak->getAttributeKeyHandle(), $akHandles)) { ?>	
				<tr>
					<td colspan="4"><?php echo $form_attribute->display($ak->getAttributeKeyHandle(), $ak->isOrderAttributeKeyRequired())?></td>
				</tr>
			<?php  }
		}
	} ?>
	</table>
	
	<!-- sparkmill -->
	<div>
		<span class="ccm-required">*Required field</span>
	</div>
	
	<style>
		.subscription_terms{
			padding-top: 8px;
		}
		
		.subscription_terms .terms{
			font-family: 'PT Sans',sans-serif;
			padding-top: 5px;
			padding-bottom: 5px;
			/* font-style: italic; */
			font-size: 13px;
			color: #333333;
		}
		
		.subscription_terms .label{
			color: #333333;
			font-family: PT Sans;
			font-size: 12px;
			font-weight: bold;
		}
		
		.subscription_terms .field span{
			vertical-align:middle;
		}
	</style>
	
	<div class="subscription_terms">
		<div class="terms">
			We do not share, use or store any information except for the purposes of processing and delivering your order.
		</div>
		<!--
		<div class="terms">
			Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send you additional product information. Cambridge University Press and Cambridge HOTmaths respect your privacy and will not pass your details on to any third party, in accordance with our privacy policy. This policy also contains information about how to access and seek correction to your personal data, or to complain about a breach of Australian Privacy Principles.
		</div>
		
		<div class="label">If you do not wish to receive further information please tick below: </div>
		<div class="field">
			<span><input type="checkbox" name="no_email" value="1"/></span> I do not wish to receive promotional material by email
		</div>
		<div class="field">
			<span><input type="checkbox" name="no_post" value="1"/></span> I do not wish to receive promotional material by regular post
		</div>
		<div class="label">At any time in the future you may opt out by sending us an email with UNSUBSCRIBE in the heading.</div>
		-->
	</div>
	
	<?php  
	$u = new User();
	if($u->isRegistered()) { ?>
	<div class="ccm-core-commerce-profile-address-save">
		<label><?php echo t('Save Info For Future Purchases')?><?php  echo $form->checkbox('save_profile',1)?></label>
	</div>
	<?php  } ?>
	<div class="ccm-core-commerce-cart-buttons">
	<?php echo $this->controller->getCheckoutNextStepButton()?>
	<?php echo $this->controller->getCheckoutPreviousStepButton()?>
	</div>
	<div class="ccm-spacer"></div>
</form>


<!-- sparkmill -->
<script>
	jQuery('document').ready(function(){
		setTimeout(function() {
			<?php if($_SESSION['DEFAULT_LOCALE'] == 'en_NZ'):?>
				if(jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select').val() != 'NZ'){
					jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select').val('NZ');
					jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select').trigger('change');
					
					jQuery('.ccm-attribute-address-line.ccm-attribute-address-state-province input').val('New Zealand');
				}
				jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select option[value!="NZ"]').remove();
			<?php else:?>
				if(jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select').val() != 'AU'){
					jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select').val('AU');
					jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select').trigger('change', function(){
						jQuery('.ccm-attribute-address-line.ccm-attribute-address-state-province select option[value="AAT"]').remove();
					});
				}
				jQuery('.ccm-attribute-address-line.ccm-attribute-address-country select option[value!="AU"]').remove();
				jQuery('.ccm-attribute-address-line.ccm-attribute-address-state-province select option[value="AAT"]').remove();
			<?php endif;?>
			
			
		}, 1000);
	});
</script>
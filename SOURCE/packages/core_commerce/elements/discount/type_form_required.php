<?php  
$form = Loader::helper('form'); 
$ih = Loader::helper("concrete/interface");
$dt = Loader::helper('form/date_time');
$date = Loader::helper('date');

$valt = Loader::helper('validation/token');
$discountHandle = '';
$discountName = '';
$discountIsEnabled = 0;
$discountStart = '';
$discountEnd = '';
$discountCode = '';


if (is_object($discount)) {
	$discountHandle = $discount->getDiscountHandle();
	$discountName = $discount->getDiscountName();
	$discountIsEnabled = $discount->isDiscountEnabled();
	$discountStart = $date->getLocalDateTime($discount->getDiscountStart());
	$discountEnd = $date->getLocalDateTime($discount->getDiscountEnd());
	$discountCode = $discount->getDiscountCode();
	print $form->hidden('discountID', $discount->getDiscountID());
} else if ($this->controller->isPost()) {
	$discountStart = $dt->translate('discountStart');
	$discountEnd = $dt->translate('discountEnd');
}
?>

<?php  if (is_object($discount)) { ?>
	<?php 
	$valt = Loader::helper('validation/token');
	$ih = Loader::helper('concrete/interface');
	$delConfirmJS = t('Are you sure you want to remove this discount?');
	?>
	<script type="text/javascript">
	deleteDiscount = function() {
		if (confirm('<?php echo $delConfirmJS?>')) { 
			location.href = "<?php echo $this->url('/dashboard/core_commerce/discounts', 'delete', $discount->getDiscountID(), $valt->generate('delete_discount'))?>";				
		}
	}
	</script>
	<?php  print $ih->button_js(t('Delete Discount'), "deleteDiscount()", 'right', 'error');?>

<?php  } ?>

<fieldset>
<legend><?php echo t('Details')?></legend>
<div class="clearfix">
<label><?php echo t('Type')?></label>
<div class="input">
	<select disabled="disabled"><option><?php echo $type->getDiscountTypeName()?></option></select>
</div>
</div>

<style type="text/css">
input.ccm-activate-date-time {margin-right: 8px !important}
table {margin-bottom: 0px !important;}
</style>

<div class="clearfix">
	<?php echo $form->label('discountIsEnabled', t('Status'))?>
	<div class="input">
		<?php echo $form->select('discountIsEnabled', array(
			'1' => t('Enabled'), 
			'0' => t('Disabled')
		), $discountIsEnabled, array('class' => 'span4'));?>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('discountHandle', t('Handle'))?>
	<div class="input">
		<?php echo $form->text('discountHandle', $discountHandle, array('class' => 'span4'))?>
		<span class="help-inline"><?php echo t('Required - No spaces, all lowercase')?></span>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('discountName', t('Name'))?>
	<div class="input">
		<?php echo $form->text('discountName', $discountName, array('class' => 'span4'))?>
		<span class="help-inline"><?php echo t('Displayed in cart at checkout')?></span>
	</div>
</div>


<div class="clearfix">
	<?php echo $form->label('discountCode', t('Coupon Code'))?>
	<div class="input">
		<?php echo $form->text('discountCode', $discountCode, array('class' => 'span4'))?>
		<span class="help-inline"><?php echo t('Entered by shopper')?></span>
	</div>
</div>
</fieldset>

<fieldset>
<legend><?php echo t('Availability')?></legend>
<div class="clearfix">
	<?php echo $form->label('discountStart_activate', t('Start Date'))?>
	<div class="input">
		<?php echo  $dt->datetime('discountStart', $discountStart, true);?>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('discountEnd_activate', t('End Date'))?>
	<div class="input">
		<?php echo  $dt->datetime('discountEnd', $discountEnd, true);?>
	</div>
</div>

<div class="clearfix">
<label><?php echo t('Users/Groups')?></label>
<div class="input">
		<?php 
			$discountUsers = array();
			$discountGroups = array();
			if ($this->controller->isPost()) { 
				if (is_array($_POST['discountFilterGroups'])) {
					foreach($_POST['discountFilterGroups'] as $gID) {
						$g = Group::getByID($gID);
						if (is_object($g)) {
							$discountGroups[] = $g;
						}
					}
				}
				if (is_array($_POST['discountFilterUsers'])) {
					foreach($_POST['discountFilterUsers'] as $uID) {
						$ui = UserInfo::getByID($uID);
						if (is_object($ui)) {
							$discountUsers[] = $ui;
						}
					}
				}
			} else if (is_object($discount)) {
				$discountUsers = $discount->getDiscountUsers();
				$discountGroups = $discount->getDiscountGroups();
			}
		?>
		<table id="ccm-core-commerce-discount-users-groups" class="ccm-results-list">
		<thead>
		<tr>
			<th><div style="width: 16px">&nbsp;</div></th>
			<th width="100%"><?php echo t('Name')?></th>
			<th style="padding: 9px 10px 9px 10px"><a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/user_group_selector?include_core_groups=1" id="ug-selector" dialog-modal="false" dialog-width="90%" dialog-title="<?php echo t('Choose User/Group')?>"  dialog-height="70%"" ><img src="<?php echo ASSETS_URL_IMAGES?>/icons/add.png" width="16" height="16" /></a></th>
		</tr>
		</thead>
		<tbody>
		<tr id="ccm-core-commerce-discount-users-groups-none" <?php  if (count($discountUsers) > 0 || count($discountGroups) > 0) { ?>style="display: none" <?php  } ?>>
			<td colspan="3"><?php echo t('None selected (Leave blank to offer discount to everyone)')?></td>
		</tr>
		<?php  foreach($discountGroups as $g) { 
			$gID = $g->getGroupID();
			$gName = $g->getGroupName();
			?>
			<tr><td><img src="<?php echo ASSETS_URL_IMAGES?>/icons/group.png" width="16" height="16" /></td><td><?php echo $gName?><input type="hidden" name="discountFilterGroups[]" value="<?php echo $gID?>" /></td><td><a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveDiscountUserGroup(this)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td></tr>
		<?php  } ?>
		<?php  foreach($discountUsers as $ui) { 
			$uID = $ui->getUserID();
			$uName = $ui->getUserName();
			?>
			<tr><td><img src="<?php echo DIR_REL?>/packages/core_commerce/images/icons/user.png" width="16" height="16" /></td><td><?php echo $uName?><input type="hidden" name="discountFilterUsers[]" value="<?php echo $uID?>" /></td><td><a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveDiscountUserGroup(this)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td></tr>
		<?php  } ?>
		</tbody>
		</table>
	</div>
</div>
</fieldset>

<?php echo $form->hidden('discountTypeID', $type->getDiscountTypeID())?>
<?php echo $valt->output('add_or_update_discount')?>
<?php  $type->render('type_form', $discount); ?>

<script type="text/javascript">
$(function() {
	$("#ug-selector").dialog();

	ccm_triggerSelectGroup = function(gID, gName) {
		$("#ccm-core-commerce-discount-users-groups-none").hide();
		var html = '<tr><td><img src="<?php echo ASSETS_URL_IMAGES?>/icons/group.png" width="16" height="16" /></td><td>' + gName + '<input type="hidden" name="discountFilterGroups[]" value="' + gID + '" /></td><td><a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveDiscountUserGroup(this)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td></tr>';
		$('#ccm-core-commerce-discount-users-groups tbody').append(html);
	}
	
	ccm_triggerSelectUser = function(uID, uName) {
		$("#ccm-core-commerce-discount-users-groups-none").hide();
		var html = '<tr><td><img src="<?php echo DIR_REL?>/packages/core_commerce/images/icons/user.png" width="16" height="16" /></td><td>' + uName + '<input type="hidden" name="discountFilterUsers[]" value="' + uID + '" /></td><td><a href="javascript:void(0)" onclick="ccm_coreCommerceRemoveDiscountUserGroup(this)"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td></tr>';
		$('#ccm-core-commerce-discount-users-groups tbody').append(html);
	}

});


ccm_coreCommerceRemoveDiscountUserGroup = function(obj) {
	var tbody = $('#ccm-core-commerce-discount-users-groups tbody');
	$(obj).parent().parent().remove();
	var cnt = tbody.find('tr').length;
	if (cnt == 1) {		
		tbody.find('tr').show();
	}	

}
</script>

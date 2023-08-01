<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('View Inspection Copy Order'), false)?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<style>
	td.field_name{
		text-align: right;
		vertical-align: top;
		font-size: 12px;
		padding-right: 5px;
		padding-bottom: 4px;
	}
	
	td.field_value{
		vertical-align: top;
		font-size: 12px;
		font-weight: bold;
		padding-right: 5px;
		padding-bottom: 4px;
	}
	
	td.gap{
		width: 10px;
	}
	
	td.section_title{
		text-align: center;
		letter-spacing: 1px;
		padding-top: 15px;
		font-size: 18px;
		font-weight: bold;
	}
	
	
	table.order_items{
		
	}
	
	table.order_items th{
		padding: 0px 8px 5px 8px;
		font-size: 12px;
		font-weight: bold;
	}
	
	table.order_items td{
		padding: 0px 8px 3px 8px;
		font-size: 12px;
	}
</style>


<div class="ccm-pane-body">
	<div class="">
		<table>
			<tr>
				<td class="field_name">Order ID</td>
				<td class="field_value"><?php echo $orderObj->id;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">Status</td>
				<td class="field_value"><?php echo $orderObj->status;?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>

			<tr>
				<td class="field_name">School Order Number</td>
				<td class="field_value"><?php echo $orderObj->school_order_number;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">Email</td>
				<td class="field_value"><?php echo $orderObj->email;?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			
			<tr>
				<td class="field_name">Title</td>
				<td class="field_value"><?php echo $orderObj->title;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">First Name</td>
				<td class="field_value"><?php echo $orderObj->first_name;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">Last Name</td>
				<td class="field_value"><?php echo $orderObj->last_name;?></td>
			</tr>
			
			<tr>
				<td class="field_name">Position</td>
				<td class="field_value"><?php echo $orderObj->position;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">School / Campus</td>
				<td class="field_value"><?php echo $orderObj->school_campus;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">Postcode</td>
				<td class="field_value"><?php echo $orderObj->school_postcode;?></td>
			</tr>
			
			<tr>
				<td class="field_name">Phone</td>
				<td class="field_value"><?php echo $orderObj->phone;?></td>
				<td class="gap">&nbsp;</td>
				<td class="field_name">Add to mailling list</td>
				<td class="field_value"><?php if($orderObj->add_to_mailling_list){echo "Yes";}else{echo "No";};?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		
			<tr>
				<td class="section_title" colspan="11">Shipping Address</td>
			</tr>
		
			<tr>
				<td class="field_name">Address</td>
				<td  class="field_value" colspan="7">
					<?php echo $orderObj->shipping_address_line_1;?><br/>
					<?php echo $orderObj->shipping_address_line_2;?><br/>
				</td>
			</tr>
			<tr>
				<td class="field_name">City</td>
				<td class="field_value"><?php echo $orderObj->shipping_address_city;?></td>
				<td>&nbsp;</td>
				<td class="field_name">State</td>
				<td class="field_value"><?php echo $orderObj->shipping_address_state;?></td>
				<td>&nbsp;</td>
				<td class="field_name">Postcode</td>
				<td class="field_value"><?php echo $orderObj->shipping_address_postcode;?></td>
			</tr>
			</tr>
				<td class="field_name">Country</td>
				<td class="field_value"><?php echo $orderObj->shipping_address_country;?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		
		
		
		<div style="width:100%;height: 40px;"></div>
		<?php $entryAssoc = $orderObj->getAssoc();?>
		<div>
			<div style="font-size:18px; font-weight: bold;padding-bottom:10px;">Order Items</div>
			<table class="order_items">
				<tr>
					<th>ISBN</th>
					<th>Product Name</th>
				</tr>
				<?php foreach($entryAssoc['items'] as $ids => $each_item_record):?>
				<tr>
					<td><?php echo $each_item_record['isbn'];?></td>
					<td><?php echo $each_item_record['product_name'];?></td>
				</tr>
				<?php endforeach;?>
			</table>
		</div>
	</div>
	
	<div style="width:100%;height: 40px;"></div>
	<div>
		<a href="<?php echo $this->url('/dashboard/cup_content/inspection_copy_orders');?>" class="btn primary">Back</a>
	</div>
</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

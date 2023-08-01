<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 
	$ch = Loader::helper('cup_content_html', 'cup_content');
	$uh = Loader::helper('url');
	Loader::helper('tools', 'cup_content');
	
	$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
	$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));
	
?>

<?php //echo $ch->renderSimpleHeading();?>
<?php Loader::element('frontend/simple_heading', array(), 'cup_content');?>
<div class="cup-content-master-frame">

	<div class="left-sidebar-content-frame">
		<?php Loader::element('frontend/inspection_copy/subject_sidebar', array('criteria'=>$criteria), 'cup_content');?>
	</div>
	
	<div class="main-content-frame with-left-sidebar">
		<div class="h30_spacer"></div>
		<div class="content-page-title">INSPECTION COPY REQUEST</div>
		<div style="width:100%;height:5px;"></div>
		<div class="spacer_bar blue"></div>
		<div style="width:100%;height:5px;"></div>
		
		<div class="inspection_action_frame">
			<?php $selected_count = count($_SESSION['inspection_copy']['order_list']);?>
			<div class="selected_inspection_title_number_frame">
				<span id="selected_inspection_title_number"><?php echo $selected_count;?></span> Titles selected
			</div>
			<?php if($selected_count > 0):?>
				<div class="inspection_order_btn_frame"><a href="<?php echo $this->url('/education/inspection_copy/order');?>"><span>Place order</span></a></div>
			<?php else:?>
				<div class="inspection_order_btn_frame hidden"><a href="<?php echo $this->url('/education/inspection_copy/order');?>"><span>Place order</span></a></div>
			<?php endif;?>
		</div>
		<div class="heading_message">
			<p>Titles that may be adopted for class use are available on inspection in print format to teachers who wish to examine them.</p>
			<p>This Inspection copy request service is available in Australia and New Zealand.</p>
			<p>Please use the order form below or contact Customer Service on 03 8671 1400. You must provide your school address for delivery.</p>
			<p>After 30 days, inspection copies may be: purchased at the published price or returned in good condition at your school’s expense to Cambridge University Press.</p>
			<p><a href="javascript:void(0);" id="view_inspection_terms">View all Inspection Copy terms and conditions.</a></p>
			<p>Select one or more titles and click place order.</p>
		</div>
		<div class="cup-result-frame" id="result_content_frame">
			<?php Loader::element('frontend/inspection_result', array('list'=>$list, 'criteria'=>$criteria), 'cup_content');?>
		</div>
		<div>
			<div class="inspection_action_frame">
				<?php $selected_count = count($_SESSION['inspection_copy']['order_list']);?>
				<div class="selected_inspection_title_number_frame">
					<span id="selected_inspection_title_number"><?php echo $selected_count;?></span> Titles selected
				</div>
				<?php if($selected_count > 0):?>
					<div class="inspection_order_btn_frame"><a href="<?php echo $this->url('/education/inspection_copy/order');?>"><span>Place order</span></a></div>
				<?php else:?>
					<div class="inspection_order_btn_frame hidden"><a href="<?php echo $this->url('/education/inspection_copy/order');?>"><span>Place order</span></a></div>
				<?php endif;?>
			</div>
			
			<div style="clear:both;width:1px;height:30px;"></div>
		</div>
	</div>
	<div style="clear:both;width:1px;height:0px;"></div>
</div>

<script>
	function gotoPage(dom, pageNumber){
		var ref = jQuery(dom).attr('href');
		if(ref.indexOf("ajax=yes") == -1){
			ref = ref+'&ajax=yes';
		}
		//alert(ref);
		jQuery('#result_content_frame').addLoadingMask();
		jQuery.get(ref, 
			function(html_data){
				jQuery('#result_content_frame').html(html_data);
				jQuery('#result_content_frame').removeLoadingMask();
			}
		);
		return false;
	}
	
	function sortColumn(dom){
		//return true;
		var ref = jQuery(dom).attr('href');
		if(ref.indexOf("ajax=yes") == -1){
			ref = ref+'&ajax=yes';
		}
		
		//alert(ref);
		jQuery('#result_content_frame').addLoadingMask();
		jQuery.get(ref, 
			function(html_data){
				jQuery('#result_content_frame').html(html_data);
				jQuery('#result_content_frame').removeLoadingMask();
			}
		);
		return false;
	}
</script>



<div class="hidden">
	<div class="inspection_terms_frame">
		<div class="spacer"></div>
		<div class="title">INSPECTION COPY TERMS AND CONDITIONS</div>
		<div class="section">
			<div class="item">
				<h5>Can anyone get an inspection copy?</h5>
				<p>The Inspection Copy service is only available within Australia and New Zealand, and is only available to School Coordinators and University lecturers for the purpose of analysing the book for possible adoption. Special conditions apply for any inspection requests. Please read the guidelines below.</p>
			</div>
			
			<div class="item">
				<h5>Can I request any title?</h5>
				<p>Not all titles are available on inspection in Australia or New Zealand. Any titles flagged ‘Firm Sale Only’ are not available through this service in your region. </p>
				<p>These titles will not have an inspection copy option in the Shopping Cart screen. Select Australia or New Zealand in the CHOOSE YOUR REGION drop down menu above to show titles available on inspection in your region in the shopping cart.</p>
			</div>
			
			<div class="item">
				<h5>Inspection copy guidelines</h5>
				<p>Inspection copies should be ordered using an official school order number or a University department purchase order. Any request for inspections must have full teacher/professor name, department, delivery address (preferably not a Post Office Box address) and phone number.</p>
				<p>Cambridge pays freight on delivery of all inspection copies made to the customer. Customers may inspect the goods free for 30 days from the date of the invoice. At the end of the 30-day inspection period, customers may:</p>
			</div>
		</div>
		
		<div class="section">
			<div class="item">
				<p><strong>(a) BUY THE INSPECTION COPY</strong> - by keeping the inspection copy and sending a copy of the remittance advice (quoting reference number) and payment to Cambridge. Payment must be in Australian dollars and can be made either by cheque, money order, Bankcard, MasterCard or Visa. If paying by credit card, card number, expiry date, cardholder’s name, signature and telephone number must be provided.</p>
				</div>
			
			<div class="item">
				<p><strong>(b) ORDER FURTHER COPIES</strong> - by sending a new order together with a copy of the original invoice. If 20 or more copies are ordered or if the title is booklisted, the customer may keep the inspection copy free of charge.</p>
			</div>
			
			<div class="item">
				<p><strong>(c) BUY THE INSPECTION COPY</strong> - by returning the original invoice and inspection copy within 120 days from the original invoice date. Return freight is at the customer’s expense. The returned copy must be in “mint” condition, or it will be returned to the customer at the customer’s expense.</p>
			</div>
			
			<div class="item">
					<p>In Australia, the original invoice and inspection copy must be returned to Cambridge's Warehouse Returns Department, 477 Williamstown Road, Port Melbourne, VIC, 3165.</p>
					<p>In New Zealand, the original invoice and inspection copy must be returned to Cambridge University Press C/ o DHL Global Forwarding, 18 Verissimo Drive, Auckland International Airport, New Zealand.</p>
			</div>
			
			<div class="item">
					<p>Monthly correspondence will be sent to the Customer for any outstanding invoice.</p>
			</div>
			
			<div class="item">
				<p>Where books are not returned within 120 days from the original invoice date, a new invoice will be raised on the customer's account. Normal trading terms will then apply</p>
			</div>
		</div>
		<div class="spacer"></div>
	</div>
</div>

<script>
	jQuery('a#view_inspection_terms').click(function(){
		jQuery.colorbox({
			html: jQuery('.inspection_terms_frame').clone(),
			width: 870,
			height: 600,
			});
	});
</script>
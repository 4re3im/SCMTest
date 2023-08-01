<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Order Listing'), false);?>
<?php Loader::packageElement('alert_message_header', 'cup_content'); ?>

<?php
	$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', 'cup_content'));
	$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', 'cup_content'));

    // will over define by local c5 jquery-ui.css
    //$this->addHeaderItem($html->css("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"));
    $this->addHeaderItem($html->javascript("jquery.ui.js"));

    $this->addHeaderItem($html->javascript('jquery-ui-timepicker-addon.js', 'cup_content'));
    $this->addHeaderItem($html->javascript('jquery-ui-sliderAccess.js', 'cup_content'));
    $this->addHeaderItem($html->css('jquery-ui-timepicker-addon.css', 'cup_content'));

	$orders = $list->getPage();
	$pagination = $list->getPagination();

    echo $html->css("http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css");
?>
<?php if(count($orders) > 0):?>
	<p>
		<form method="get">
			<?php 
				$q_orderID = "";
				$q_invoiceID = "";
				$q_email = "";
				$q_vistaID = "";
                $q_datetime_start = "";
                $q_datetime_end = "";

				if(isset($_GET['q_orderID']) && strlen(trim($_GET['q_orderID']))>0){
					$q_orderID = trim($_GET['q_orderID']);
				}
				
				if(isset($_GET['q_invoiceID']) && strlen(trim($_GET['q_invoiceID']))>0){
					$q_invoiceID = trim($_GET['q_invoiceID']);
				}
				
				if(isset($_GET['q_email']) && strlen(trim($_GET['q_email']))>0){
					$q_email = trim($_GET['q_email']);
				}
				
				if(isset($_GET['q_vistaID']) && strlen(trim($_GET['q_vistaID']))>0){
					$q_vistaID = trim($_GET['q_vistaID']);
				}

                if(isset($_GET['q_datetime_start']) && strlen(trim($_GET['q_datetime_start']))>0){
                    $q_datetime_start = trim($_GET['q_datetime_start']);
                }

                if(isset($_GET['q_datetime_end']) && strlen(trim($_GET['q_datetime_end']))>0){
                    $q_datetime_end = trim($_GET['q_datetime_end']);
                }
			?>
			<!--
			InvoiceID: <input type="text" name="q_invoiceID" value="<?php echo str_replace('"', '/"', $q_invoiceID);?>"/>
			<span style="padding:0px 5px;">&nbsp;</span>
			-->
			OrderID: <input type="text" name="q_orderID" value="<?php echo str_replace('"', '/"', $q_orderID);?>"/>
			<span style="padding:0px 5px;">&nbsp;</span>
			Email: <input type="text" name="q_email" value="<?php echo str_replace('"', '/"', $q_email);?>"/>
			<span style="padding:0px 5px;">&nbsp;</span>
			Vista Order ID: <input type="text" name="q_vistaID" value="<?php echo str_replace('"', '/"', $q_vistaID);?>"/>
			<span style="padding:0px 5px;">&nbsp;</span>
            <br/>
            Start Date Time: <input type="text" name="q_datetime_start" class="datetime_picker" value="<?php echo str_replace('"', '/"', $q_datetime_start);?>">
            <span style="padding:0px 5px;">&nbsp;</span>
            End Date Time:  <input type="text" name="q_datetime_end" class="datetime_picker" value="<?php echo str_replace('"', '/"', $q_datetime_end);?>">
			<div style="text-align:right"><input type="submit" value="Search"/></div>
			
		</form>
	</p>
	
	
	<table border="0" cellspacing="0" cellpadding="0" id="ccm-product-list" class="ccm-results-list">
		<thead>
			<tr>
				<th class="<?php echo $list->getSearchResultsClass('orderID')?>"><a href="<?php echo $list->getSortByURL('orderID', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Order ID')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('email')?>"><a href="<?php echo $list->getSortByURL('email', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Email')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('status')?>"><a href="<?php echo $list->getSortByURL('status', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Status')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('vistaOrderID')?>"><a href="<?php echo $list->getSortByURL('vistaOrderID', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Vista Order ID (AU)')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('vistaOrderID_nz')?>"><a href="<?php echo $list->getSortByURL('vistaOrderID_nz', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Vista Order ID (NZ)')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('vistaOrderID_digital')?>"><a href="<?php echo $list->getSortByURL('vistaOrderID_digital', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Vista Order ID (D)')?></a></th>
				<th class="<?php echo $list->getSearchResultsClass('modifiedAt')?>"><a href="<?php echo $list->getSortByURL('modifiedAt', 'asc')?>" onclick="return sortColumn(this);"><?php echo t('Modified At')?></a></th>
			
			</tr>
		</thead>
		<tbody>
			<?php foreach($orders as $idx=>$order):?>
				<tr ref="<?php echo $order->orderID;?>">
					<td><a href="<?php echo $this->url('/dashboard/core_commerce/orders/search/detail', $order->orderID);?>"><?php echo $order->orderID;?></a></td>
					<td><a href="javascript:sendInvoice(<?php echo $order->orderID;?>)"><?php echo $order->email;?></a></td>
					<td><?php echo $order->status;?></td>
					<td>
						<a href="javascript:show_response('<?php echo $order->vistaOrderID;?>');"><?php echo $order->vistaOrderID;?></a>
						<div style="display: none" id="response_<?php echo $order->vistaOrderID;?>"><textarea><?php echo $order->response;?></textarea></div>
					</td>
					<td>
						<a href="javascript:show_response('<?php echo $order->vistaOrderID_nz;?>');"><?php echo $order->vistaOrderID_nz;?></a>
						<div style="display: none" id="response_<?php echo $order->vistaOrderID_nz;?>"><textarea><?php echo $order->response_nz;?></textarea></div>
					</td>
					<td>
						<a href="javascript:show_response('<?php echo $order->vistaOrderID_digital;?>');"><?php echo $order->vistaOrderID_digital;?></a>
						<div style="display: none" id="response_<?php echo $order->vistaOrderID_digital;?>"><textarea><?php echo $order->response_digital;?></textarea></div>
					</td>
					<td><?php echo date('Y-m-d H:i:s', strtotime($order->modifiedAt));?></td>
					
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
	<div id="ccm-list-none"><?php echo t('No Orders found.')?></div>
<?php endif;?>


<div style="clear:both"></div>
<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>



<script>
    function sendInvoice(order_id){
        var action_url = "<?php echo rtrim($this->url('/dashboard/cup_content/config/send_invoice'), "/");?>";
        action_url = action_url+"/"+order_id;

        $.fn.dialog.open({
            title: 'Resend Invoice',
            href: action_url,
            width: '300px',
            modal: true,
            height: '300px'
        });
    }

	function show_response(orderId){
		//alert(jQuery('#response_'+orderId).html();
		//return;
		jQuery.colorbox({html:'<div class="popup-content" style="padding:25px 0px;margin:0px 10px;"></div>', width:700, height:600}, function(){
			jQuery('div.popup-content').empty();
			jQuery('div.popup-content').append(jQuery('#response_'+orderId+' textarea').clone());
			jQuery('div.popup-content').find('textarea').css({width:'99%', height:500, resize:'none'});
		});
	}


    jQuery( ".datetime_picker" ).datetimepicker({
        changeMonth: true,
        changeYear: true,
        showSecond: true,
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss'
    });
</script>
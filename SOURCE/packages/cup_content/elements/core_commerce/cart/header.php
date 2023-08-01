
<?php if($step == '4'):?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>CONFIRMATION</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first step"><div class="text">1.Shopping Cart</div></div>
			<div class="item step"><div class="text">2.Your Details</div></div>
			<div class="item step"><div class="text">3.Payment</div></div>
			<div class="item step"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php elseif($step == '3'):?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>PAYMENT DETAILS</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first step"><div class="text">1.Shopping Cart</div></div>
			<div class="item step"><div class="text">2.Your Details</div></div>
			<div class="item step"><div class="text">3.Payment</div></div>
			<div class="item"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php elseif($step == '3a'):?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>PAYMENT METHOD</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first step"><div class="text">1.Shopping Cart</div></div>
			<div class="item step"><div class="text">2.Your Details</div></div>
			<div class="item step"><div class="text">3.Payment</div></div>
			<div class="item"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php elseif($step == '2' || $step == '2a' ):?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>YOUR BILLING INFORMATION</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first step"><div class="text">1.Shopping Cart</div></div>
			<div class="item step"><div class="text">2.Your Details</div></div>
			<div class="item"><div class="text">3.Payment</div></div>
			<div class="item"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php elseif($step == '2b' ):?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>YOUR DELIVERY INFORMATION</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first step"><div class="text">1.Shopping Cart</div></div>
			<div class="item step"><div class="text">2.Your Details</div></div>
			<div class="item"><div class="text">3.Payment</div></div>
			<div class="item"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php elseif($step == '1'):?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>SHOPPING CART</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first step"><div class="text">1.Shopping Cart</div></div>
			<div class="item"><div class="text">2.Your Details</div></div>
			<div class="item"><div class="text">3.Payment</div></div>
			<div class="item"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php else:?>
	<div class="cup_core_commerce_cart_header">
		<div class="title">
			<div class="spacer1"></div>
			<h1>Shopping Cart</h1>
			<div class="spacer2"></div>
		</div>
		<div class="breadcrumb">
			<div class="clr spacer"></div>
			<div class="item first"><div class="text">1.Shopping Cart</div></div>
			<div class="item"><div class="text">2.Your Details</div></div>
			<div class="item"><div class="text">3.Payment</div></div>
			<div class="item"><div class="text">4.Confirmation</div></div>
			<div class="clr spacer"></div>
		</div>
	</div>
<?php endif;?>

<?php if(isset($_GET['e']) && strlen($_GET['e']) > 0):?>
	<?php $es = base64_decode($_GET['e']);?>
	<?php $es = json_decode($es);?>
	<?php if($es && is_array($es) && count($es) > 0):?>
		<div class="cup_core_commerce_cart_errors">
			<ul class="ccm-error">
				<?php foreach($es as $e):?>
				<li><?php echo $e;?></li>
				<?php endforeach;?>
			</ul>
		</div>
	<?php endif;?>
<?php endif;?>
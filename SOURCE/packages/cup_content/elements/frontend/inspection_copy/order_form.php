<?php
	$entry = $order->getAssoc();
?>
<div class="inspection_copy_order_form">
	<form method="post">
	<div class="section">
		<div class="item">
			<div class="field_name">SCHOOL ORDER NUMBER</div>
			<div class="field_value">
				<input type="text" name="school_order_number"/>
			</div>
		</div>
		
		<div class="item">
			<?php $title_options = array(
								'Mr' => 'Mr',
								'Ms' => 'Ms',
								'Mrs' => 'Mrs',
								'Dr' => 'Dr',
								'Prof' => 'Prof',
								'Father' => 'Father',
								'Brother' => 'Brother',
								'Sister' => 'Sister',
								'Mother' => 'Mother'
			);?>
			<div class="field_name">TITLE
				<select name="title">
					<?php foreach($title_options as $key => $value):?>
						<?php if(strcmp($value, @$entry['title']) == 0):?>
							<option value="<?php echo $value;?>" selected="selected"><?php echo $key;?></option>
						<?php else:?>
							<option value="<?php echo $value;?>"><?php echo $key;?></option>
						<?php endif;?>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">FIRST NAME <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="first_name" value="<?php echo str_replace('"', '\"', @$entry['first_name']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">LAST NAME <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="last_name" value="<?php echo str_replace('"', '\"', @$entry['last_name']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">POSITION <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="position" value="<?php echo str_replace('"', '\"', @$entry['position']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">SCHOOL / CAMPUS <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="school_campus" value="<?php echo str_replace('"', '\"', @$entry['school_campus']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">CAMPUS POSTCODE <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="school_postcode" value="<?php echo str_replace('"', '\"', @$entry['school_campus']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">PHONE NUMBER <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="phone" value="<?php echo str_replace('"', '\"', @$entry['phone']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">EMAIL <span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="email" value="<?php echo str_replace('"', '\"', @$entry['email']);?>"/>
			</div>
		</div>
		
		<div class="item">
			<div class="terms">
			Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send you additional product information. Cambridge University Press and Cambridge HOTmaths respect your privacy and will not pass your details on to any third party, in accordance with our privacy policy. This policy also contains information about how to access and seek correction to your personal data, or to complain about a breach of Australian Privacy Principles.
			</div>
			<div class="field_name">
			If you do not wish to receive further information please tick below: 
			</div>
			<div class="field">
				<span class="inline_option_item">
					<input type="checkbox" name="no_email" value="1"/>
				</span>
				I do not wish to receive promotional material by email
			</div>
			
			<div class="field">
				<span class="inline_option_item">
					<input type="checkbox" name="no_post" value="1"/>
				</span>
				I do not wish to receive promotional material by regular post
			</div>
			<div class="fieldname">
				At any time in the future you may opt out by sending us an email with UNSUBSCRIBE in the heading.
			</div>
		</div>
		<!--
		<div class="item">
			<div class="field_name">Add to mailing list <span class="required">*</span></div>
			<div class="field_value">
				<?php if(@$entry['add_to_mailling_list'] == 1):?>
					<span><input type="radio" value="1" name="add_to_mailling_list" checked="checked"/> Yes </span>
				<?php else:?>
					<span><input type="radio" value="1" name="add_to_mailling_list"/> Yes </span>
				<?php endif;?>
				
				<?php if(@$entry['add_to_mailling_list'] == 0):?>
					<span><input type="radio" value="0" name="add_to_mailling_list" checked="checked"/> No </span>
				<?php else:?>
					<span><input type="radio" value="0" name="add_to_mailling_list"/> No </span>
				<?php endif;?>
			</div>
		</div>
		-->
		<div class="item">
			<div class="field_name">
				<input type="checkbox" name="agreed_terms_and_conditions" value="yes"/>
				<a id="view_inspection_terms" href="#">Agreed to terms and conditions</a> <span class="required">*</span>
			</div>
		</div>
		
		<div class="item">
			<div class="field_name"><span class="required">*Required field</span></div>
		</div>
	</div>
	
	
	<div class="section">
		<div class="section_title">
			SHIPPING DETAIL
		</div>
		
		<div class="item">
			<div class="field_name">Address Line 1<span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="shipping_address_line_1" value="<?php echo str_replace('"', '\"', @$entry['shipping_address_line_1']);?>"/> 
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">Address Line 2</div>
			<div class="field_value">
				<input type="text" name="shipping_address_line_2" value="<?php echo str_replace('"', '\"', @$entry['shipping_address_line_2']);?>"/> 
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">CITY<span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="shipping_address_city" value="<?php echo str_replace('"', '\"', @$entry['shipping_address_city']);?>"/> 
			</div>
		</div>
		
		
		
		<?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0):?>
			<?php $state_options = array(
								'... please select ...' => '',
								'ACT' => 'ACT',
								'NSW' => 'NSW',
								'VIC' => 'VIC',
								'QLD' => 'QLD',
								'TAS' => 'TAS',
								'NT' => 'NT',
								'SA' => 'SA',
								'WA' => 'WA'
			);?>
		<div class="item">
			<div class="field_name">STATE<span class="required">*</span></div>
			<div class="field_value">
				<select name="shipping_address_state">
					<?php foreach($state_options as $key => $value):?>
						<?php if(strcmp($value, @$entry['shipping_address_state']) == 0):?>
							<option value="<?php echo $value;?>" selected="selected"><?php echo $key;?></option>
						<?php else:?>
							<option value="<?php echo $value;?>"><?php echo $key;?></option>
						<?php endif;?>
					<?php endforeach;?>
				</select>
			</div>
		</div>
		<?php endif;?>
		
		<div class="item">
			<div class="field_name">POSTCODE<span class="required">*</span></div>
			<div class="field_value">
				<input type="text" name="shipping_address_postcode" value="<?php echo str_replace('"', '\"', @$entry['shipping_address_postcode']);?>"/> 
			</div>
		</div>
		
		<div class="item">
			<div class="field_name">COUNTRY</div>
			<div class="field_value">
				<?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_AU') == 0):?>
					Australia
				<?php else:?>
					New Zealand
				<?php endif;?>
			</div>
		</div>
	</div>
	
	<div style="clear:both; height: 0px; width: 0px;"></div>
	
	<div class="action_frame">
		<input type="submit" value="Submit Order"/> 
	</div>
	
	<div style="clear:both; height: 50px; width: 1px;"></div>
	</form>
</div>






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

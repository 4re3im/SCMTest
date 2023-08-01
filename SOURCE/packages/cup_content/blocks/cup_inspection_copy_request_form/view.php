<div class="btCupInspectionCopyRequestForm">
	<div class="inner">
		<div class="errors_frame">
			<div class="close_btn"></div>
			<div class="content">
<!--				"FIRST NAME" is required"<br/>
				"FIRST NAME" is required"<br/>
				"FIRST NAME" is required"<br/>-->
			</div>
		</div>
		<div class="success_frame">
			<span>Thank You</span><br/>
			Your Inspection Copy Request Has been Successfully Submitted.                         
		</div>
		<div class="heading_msg">
			<?php echo "<h3>" . nl2br($title) ."</h3>";?>
		</div>
		<div class="form_content">
			<?php
				$action_url = Loader::helper('concrete/urls')->getToolsURL('process_inspection_copy_request_form', 'cup_content');
                                //$action_url = $this->getBlockURL().'/tools/process_form';                                  
                                //$action_url = $this->getBlockURL().'/process_inspection_copy_request_form.php'; 
			?>
                    
<!--                        <form action="<?php //echo $this->action('inspection_copy_request')?>" method="post" id="inspection_copy_request_form" class="ajax-form">-->
                        <form action="<?php echo $action_url; ?>" method="post" id="inspection_copy_request_form" class="ajax-form">
				<div class="form_item">
					<label>TITLE</label>
					<select name="title">
						<option value="Mr">Mr</option>
						<option value="Ms">Ms</option>
						<option value="Mrs">Mrs</option>
						<option value="Dr">Dr</option>
						<option value="Prof">Prof</option>
						<option value="Father">Father</option>
						<option value="Brother">Brother</option>
						<option value="Sister">Sister</option>
						<option value="Mother">Mother</option>
					</select>
				</div>
				
				<div class="form_item">
					<label>FIRST NAME<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="first_name">
					</div>
				</div>
				
				<div class="form_item">
					<label>LAST NAME<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="last_name">
					</div>
				</div>
				
				<div class="form_item">
					<label>POSITION<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="position">
					</div>
				</div>
				
				<div class="form_item">
					<label>SCHOOL/CAMPUS<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="school_campus">
					</div>
				</div>
				
				<div class="form_item">
					<label>CAMPUS POSTCODE<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="campus_postcode" class="short">
					</div>
				</div>
				
				<div class="form_item">
					<label>PHONE NUMBER<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="phone">
					</div>
				</div>
				
				<div class="form_item">
					<label>EMAIL<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="email">
					</div>
				</div>
				
				<div class="form_item">
					<label>YOUR QUERY<span class="required">*</span></label>
					<div class="field">
						<textarea name="query"></textarea>
					</div>
				</div>
				
                                <div class="spacer"></div>
                            
				<div class="form_item">
					<label>Add to mailing list<span class="required">*</span></label>
					<div class="field">
						<span class="inline_option_item">
							<input type="radio" name="mailing_list" value="1">Yes
						</span>
						
						<span class="inline_option_item">
							<input type="radio" name="mailing_list" value="0">No
						</span>
					</div>
				</div>
				
                                <div class="spacer"></div>
                                
                                <div class="form_item">
					<label>Agreed to Terms and Condition<span class="required">*</span></label>
					<div class="field">
						<input type="checkbox" name="terms_and_condition" value="1">Yes
					</div>
				</div>
                                
                                <div class="spacer"></div>
                                
                                <h5>SHIPPING DETAIL</h5>
                                
                                <div class="form_item">
					<label>Address Line 1<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="address_line_1">
					</div>
				</div>
				
				<div class="form_item">
					<label>Address Line 2</label>
					<div class="field">
						<input type="text" name="address_line_2">
					</div>
				</div>
				
				<div class="form_item">
					<label>CITY<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="city">
					</div>
				</div>
                                
                                <div class="form_item">
					<label>STATE</label>
					<select name="state">
						<option value="ACT">ACT</option>
						<option value="NSW">NSW</option>
						<option value="VIC">VIC</option>
						<option value="QLD">QLD</option>
						<option value="TAS">TAS</option>
						<option value="NT">NT</option>
						<option value="SA">SA</option>
						<option value="WA">WA</option>						
					</select>
				</div>
                                
                                <div class="form_item">
					<label>POSTCODE<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="postcode" class="short">
					</div>
				</div>
                                
                                   <div class="form_item">
					<label>COUNTRY</label>
					<select name="country">
						<option value="Australia">Australia</option>
						<option value="New_Zealand">New Zealand</option>												
					</select>
				</div>
                                
                                <?php
                                    //Title of Product Requested
                                ?>
                                
				<div class="form_item">
					<input type="submit" name="submit"  value="Send enquiry"/><span id="loading"></span>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	jQuery('#inspection_copy_request_form.ajax-form').submit(function(){
            
		var action_url = jQuery(this).attr('action');
		var submit_type = jQuery(this).attr('method');
		if(typeof(submit_type) === 'undefined' || submit_type === false){
			submit_type = 'GET';
		}
		
		jQuery('.btCupInspectionCopyRequestForm .form_content input[type="submit"]').hide();
		jQuery('.btCupInspectionCopyRequestForm .form_content span#loading').show();
		                
                jQuery.ajax({
			type: submit_type,
			url: action_url, 
			data: jQuery(this).serialize(),
			success: function(html_data){
				json = jQuery.parseJSON(html_data);
				if(json.result == 'error'){                                    
					jQuery('.btCupInspectionCopyRequestForm .errors_frame .content').html(json.error);
					jQuery('.btCupInspectionCopyRequestForm .errors_frame').slideDown(300);					
					jQuery('.btCupInspectionCopyRequestForm .form_content input[type="submit"]').show();
					jQuery('.btCupInspectionCopyRequestForm .form_content span#loading').hide();                                            
				}else if(json.result == 'success'){                                        
					jQuery('.btCupInspectionCopyRequestForm .errors_frame').fadeOut(300);
					jQuery('.btCupInspectionCopyRequestForm .heading_msg').fadeOut(300);
					jQuery('.btCupInspectionCopyRequestForm .form_content').fadeOut(300, function(){
						jQuery('.btCupInspectionCopyRequestForm .success_frame').fadeIn(300);
					});
				}
			}
		});
		
		return false;
	});
	
	jQuery('.btCupInspectionCopyRequestForm .errors_frame div.close_btn').click(function(){
		jQuery('.btCupInspectionCopyRequestForm .errors_frame').slideUp(300);
	});
</script>


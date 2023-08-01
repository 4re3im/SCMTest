<div class="btCupContactForm">
	<div class="inner">
		<div class="errors_frame">
			<div class="close_btn"></div>
			<div class="content">
			</div>
		</div>
		<div class="success_frame">
			<span>Thank You</span><br/>
			Your enquiry Has been Successfully Submitted.
		</div>
		<div class="heading_msg">
			<?php echo nl2br($title);?>
		</div>
		<div class="form_content">
			<?php
				$action_url = Loader::helper('concrete/urls')->getToolsURL('process_contact_us_form', 'cup_content');
				//$action_url = $this->getBlockURL().'/tools/process_form';
			?>
			<form action="<?php echo $action_url;?>" method="post" id="contact_form" class="ajax-form">
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
					<label>SCHOOL/CAMPUS</label>
					<div class="field">
						<input type="text" name="school_campus">
					</div>
				</div>
				
				<div class="form_item">
					<label>POSTCODE<span class="required">*</span></label>
					<div class="field">
						<input type="text" name="postcode" class="short">
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
				
				<div class="form_item">
					<div class="terms">
					Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send you additional product information. Cambridge University Press and Cambridge HOTmaths respect your privacy and will not pass your details on to any third party, in accordance with our privacy policy. This policy also contains information about how to access and seek correction to your personal data, or to complain about a breach of Australian Privacy Principles.
					</div>
					<label>
					If you do not wish to receive further information please tick below: 
					</label>
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
					<label>
						At any time in the future you may opt out by sending us an email with UNSUBSCRIBE in the heading.
					</label>
				</div>
				<!--
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
				-->
				
				<div class="form_item">
					<label><span class="required">*Required field</span></label>
				</div>
				
				<div class="spacer"></div>
				<div class="form_item">
					<input type="submit" value="Send enquiry"/><span id="loading"></span>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	jQuery('#contact_form.ajax-form').submit(function(){
		var action_url = jQuery(this).attr('action');
		var submit_type = jQuery(this).attr('method');
		if(typeof(submit_type) === 'undefined' || submit_type === false){
			submit_type = 'GET';
		}
		
		jQuery('.btCupContactForm .form_content input[type="submit"]').hide();
		jQuery('.btCupContactForm .form_content span#loading').show();
	

var data = jQuery(this).serialize();
console.log(data);
	
		jQuery.ajax({
			type: submit_type,
			url: action_url, 
			data: jQuery(this).serialize(),
			success: function(html_data){
				json = jQuery.parseJSON(html_data);
console.log(html_data);
				if(json.result == 'error'){
					jQuery('.btCupContactForm .errors_frame .content').html(json.error);
					jQuery('.btCupContactForm .errors_frame').slideDown(300);
					
					jQuery('.btCupContactForm .form_content input[type="submit"]').show();
					jQuery('.btCupContactForm .form_content span#loading').hide();
				}else if(json.result == 'success'){
					jQuery('.btCupContactForm .errors_frame').fadeOut(300);
					jQuery('.btCupContactForm .heading_msg').fadeOut(300);
					jQuery('.btCupContactForm .form_content').fadeOut(300, function(){
						jQuery('.btCupContactForm .success_frame').fadeIn(300);
					});
				}
			}
		});
		
		return false;
	});
	
	jQuery('.btCupContactForm .errors_frame div.close_btn').click(function(){
		jQuery('.btCupContactForm .errors_frame').slideUp(300);
	});
</script>
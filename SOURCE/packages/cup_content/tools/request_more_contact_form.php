<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
$u = new User();

if(isset($_POST['email'])){
	
	$field_query = $_POST['query'];
	$field_source = array();
	if(isset($_POST['source'])){
		$field_source = $_POST['source'];
	}
	$field_title = $_POST['title'];
	$field_first_name = $_POST['first_name'];
	$field_last_name = $_POST['last_name'];
	$field_position = $_POST['position'];
	$field_school_campus = $_POST['school_campus'];
	$field_postcode = $_POST['postcode'];
	$field_phone = $_POST['phone'];
	$field_email = $_POST['email'];
	
	$product_title = $_POST['product_title'];
	$product_isbn = $_POST['product_isbn'];
	
	$receive_email = 1;
	$receive_post = 1;
	
	if(isset($_POST['no_email']) && $_POST['no_email']==1){
		$receive_email = 0;
	}
	
	if(isset($_POST['no_post']) && $_POST['no_post']==1){
		$receive_post = 0;
	}
	
	$result = array('result'=>'failed', 'error'=>'unknown error');
	$errors = array();
	
	if(strlen($field_query) < 1){
		$errors[] = "\"Query\" is required";
	}
	
	/*
	if(count($field_source) < 1){
		$errors[] = "\"Where did you hear about us?\" is required";
	}
	*/
	
	if(count($field_first_name) < 1){
		$errors[] = "\"First Name\" is required";
	}
	
	if(strlen($field_last_name) < 1){
		$errors[] = "\"Last Name\" is required";
	}
	
	if(strlen($field_position) < 1){
		$errors[] = "\"Position\" is required";
	}
	
	/*	Change to not required as reuested on 2013-04-01
	if(strlen($field_school_campus) < 1){
		$errors[] = "\"School / Campus\" is required";
	}
	*/
	
	if(strlen($field_postcode) < 1){
		$errors[] = "\"Postcode\" is required";
	}
	
	if(strlen($field_phone) < 1){
		$errors[] = "\"Phone number\" is required";
	}
	
	if(strlen($field_email) < 1){
		$errors[] = "\"Email\" is required";
	}elseif(!filter_var($field_email, FILTER_VALIDATE_EMAIL)){
		$errors[] = "\"Email\" invalid, please check.";
	}
	
	if(count($errors) > 0){
		$result = array('result'=>'failed', 'error'=>$errors);
	}else{
		$pkg  = Package::getByHandle('cup_content');
		$email_to = $pkg->config('CONTACT_FORM_EMAIL_RECEIVER');
		$email_from = $pkg->config('FROM_EMAIL_ADDRESS');
	
		$mh = Loader::helper('mail');
		$mh->replyto($field_email);
		$mh->to($email_to);
		$mh->from($email_from);
		
		$mh->setSubject('Enquiry - '.$product_title);
		
		ob_start();?>
<html>
<body>
	<style>
		.field_section .terms{
			padding-top: 5px;
			padding-bottom: 5px;
			font-style: italic;
			font-size: 12px;
		}
		
		.field_attr{
			color: #444;
			vertical-align: top;
			padding-right: 8px;
			text-align: right;
		}
		
		.field_val{
			vertical-align: top;
		}
	</style>
		<table>
			<tr>
				<td width="1%" class="field_attr">From</td>
				<td colspan="3" width="99%" class="field_val"><?php echo $field_title;?> <?php echo $field_first_name;?> <?php echo $field_last_name;?></td>
			</tr>
			<tr>
				<td width="1%" class="field_attr">Email</td>
				<td width="69%" class="field_val"><?php echo $field_email?></td>
				<td width="1%" class="field_attr">Phone</td>
				<td width="29%" class="field_val"><?php echo $field_phone?></td>
			</tr>
			<tr>
				<td width="1%" class="field_attr">School/Campus</td>
				<td width="69%" class="field_val"><?php echo $field_school_campus?></td>
				<td width="1%" class="field_attr">Postcode</td>
				<td width="29%" class="field_val"><?php echo $field_postcode?></td>
			</tr>
			<tr>
				<td width="1%" class="field_attr">Lead Sources</td>
				<td colspan="3" width="69%" class="field_val"><?php echo implode(', ', $field_source);?></td>
			</tr>
			<tr>
				<td width="1%" class="field_attr">Product Title</td>
				<td width="69%" class="field_val"><?php echo $product_title?></td>
				<td width="1%" class="field_attr">ISBN</td>
				<td width="29%" class="field_val"><?php echo $product_isbn?></td>
			</tr>
			<tr>
				<td width="1%" class="field_attr">Email Subscription</td>
				<td width="69%" class="field_val"><?php echo $receive_email?"Yes":"No";?></td>
				<td width="1%" class="field_attr">Post Subscription</td>
				<td width="29%" class="field_val"><?php echo $receive_post?"Yes":"No";?></td>
			</tr>
			<tr>
				<td width="1%" class="field_attr">Query</td>
				<td colspan="3" width="69%" class="field_val"><?php echo nl2br(htmlentities($field_query));?></td>
			</tr>
		</table>
</body>
</html>
<?php 	$htmlBody = ob_get_clean();
		$mh->setBodyHTML($htmlBody);
		$mh->sendMail();
		$result = array('result'=>'success');
		
	}
	
	echo json_encode($result);
	
	
	exit();
}



$uh = Loader::helper('concrete/urls');
$contact_form_link = $uh->getToolsURL('request_more_contact_form', 'cup_content');


$title = "";
$isbn = "";

if ($_POST['title'] != '') {
	$title = $_POST['title'];
}

$selected_values = array();
if(isset($_POST['isbn'])) {
	$isbn = $_POST['isbn'];
}


$html = Loader::helper('html');
echo $html->css('pop_up_contact_form.css', 'cup_content');
?>

<div id="pop-up-frame">
	<div class="error-msg-frame">
		<div class="close_btn"></div>
		<div style="width:1px;height:30px"></div>
		<div class="errors">
			
		</div>
		<div style="width:1px;height:15px"></div>
	</div>
	
	<form action="<?php echo $contact_form_link;?>" method="post" id="contact_form">
	<div class="form_section">
		<div style="width: 100%; height: 10px"></div>
		<div class="contact_info">
			<h2>Contact Customer Service:</h2>
			<p>
				Phone: +61 3 8671 1400<br/>
				Email: <a href="mailto:enquiries@cambridge.edu.au"><strong>enquiries@cambridge.edu.au</strong></a><br/>
				Australia Freephone: 1800 005 210<br/>
				New Zealand Freephone: 0800 023 520
			</p>
		</div>

		<div style="width: 1px;height:6px;"></div>
		<div class="teachers_fill_out_form_title">Teachers fill out form:</div>
		<div style="width: 1px;height:6px;"></div>
		<div style="">
			This is a form for Educators, you will need to provide your school contact details to complete the request.
		</div>
		<div style="width: 1px;height:6px;"></div>
		<div class="title_name"><?php echo $title;?></div>
		<input type="hidden" name="product_title" value="<?php echo str_replace('"', '\"', $title);?>"/>
		<input type="hidden" name="product_isbn" value="<?php echo str_replace('"', '\"', $isbn);?>"/>
		
		<div style="width: 1px;height:10px;"></div>
		<div class="field_section">
			<div class="field_name">YOUR QUERY <span class="required">*</span></div>
			<div class="field_attr">
				<textarea name="query"></textarea>
			</div>
		</div>
		
		<div style="width: 100%; height: 15px"></div>
		
		<div class="field_section">
			<div class="field_name">Where did you hear about us?</div>
			<div class="field_attr">
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Advertisement"> Advertisement
				</div>	
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="School Display"> School Display
				</div>	
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Conference"> Conference
				</div>	
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Telemarketing"> Telemarketing
				</div>	
				
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="External Referral"> External Referral
				</div>
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Web"> Web
				</div>
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Mailing"> Mailing
				</div>
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Social Media"> Social Media
				</div>
				
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Sales Call"> Sales Call
				</div>
		
				<div class="checkbox_item">
					<input type="checkbox" name="source[]" value="Other"> Other
				</div>
				<div style="clear:both;width0px;height:0px;"></div>
			</div>
		</div>
	</div>
	<div class="form_section">
		<div style="width: 100%; height: 30px"></div>
		<div class="field_section">
			<div class="field_name">TITLE <span class="required">*</span>
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
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">FIRST NAME<span class="required">*</span></div>
			<div class="field_attr">
				<input type="text" name="first_name"/>
			</div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">LAST NAME<span class="required">*</span></div>
			<div class="field_attr">
				<input type="text" name="last_name"/>
			</div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">POSITION<span class="required">*</span></div>
			<div class="field_attr">
				<input type="text" name="position"/>
			</div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">SCHOOL / CAMPUS</div>
			<div class="field_attr">
				<input type="text" name="school_campus"/>
			</div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">POSTCODE<span class="required">*</span></div>
			<div class="field_attr">
				<input type="text" name="postcode"/>
			</div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">PHONE NUMBER<span class="required">*</span></div>
			<div class="field_attr">
				<input type="text" name="phone"/>
			</div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name">EMAIL<span class="required">*</span></div>
			<div class="field_attr">
				<input type="text" name="email"/>
			</div>
		</div>
		
		<div style="width: 100%; height: 15px"></div>
		
		
		
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="field_name"><span class="required">*Required field</span></div>
		</div>
		<div style="width: 100%; height: 15px"></div>
		<!--
		<div class="field_section end">
			<div style="float: right">
				<input type="submit" name="submit" value="Send enquiry"/><span class="loader"></span>
			</div>
		</div>
		-->
	</div>
	<div style="clear:both; width:0px; height: 0px;"></div>
	
	<div class="form_section" style="width: 580px;">
		<div style="width: 100%; height: 15px"></div>
		<div class="field_section">
			<div class="terms">
				Cambridge University Press and its affiliate, Cambridge HOTmaths, may occasionally send you additional product information. Cambridge University Press and Cambridge HOTmaths respect your privacy and will not pass your details on to any third party, in accordance with our privacy policy. This policy also contains information about how to access and seek correction to your personal data, or to complain about a breach of Australian Privacy Principles.
			</div>
			<div class="field_name">
				If you do not wish to receive further information please tick below: 
			</div>
			<div class="field_attr">
				<span class="inline_option_item">
					<input type="checkbox" name="no_email" value="1"/>
				</span>
				I do not wish to receive promotional material by email
			</div>
			<div class="field_attr">
				<span class="inline_option_item">
					<input type="checkbox" name="no_post" value="1"/>
				</span>
				I do not wish to receive promotional material by regular post
			</div>
			<div class="field_name">
				At any time in the future you may opt out by sending us an email with UNSUBSCRIBE in the heading.
			</div>
		</div>
		<div style="clear:both; width:1px; height: 15px;"></div>
		<div style="clear:both; width:0px; height: 0px;"></div>
		<div class="field_section end" style="width:580px;">
			<div style="float: right">
				<input type="submit" name="submit" value="Send enquiry"/><span class="loader"></span>
			</div>
		</div>
		<div style="clear:both; width:0px; height: 0px;"></div>
	</div>
	
	</form>
</div>


<script>
	jQuery('#pop-up-frame .error-msg-frame .close_btn').click(function(){
		jQuery('#pop-up-frame .error-msg-frame').hide();
	});

	jQuery('#contact_form').submit(function(){
		var form_data = jQuery(this).serialize();
		//alert(form_data);
		//alert(JSON.stringify(form_data));
		
		var selected_data = new Array();
		jQuery('#popup-selected-area .popup-selection-item').each(function(){
			selected_data.push(jQuery(this).html());
		});
		/*
		var submit_data = {
								'keywords': jQuery(this).find('input[name="keywords"]').val(),
								'list-only': 'yes',
								'selected_values': selected_data
							};
		*/
		var action_url = jQuery(this).attr('action');
		var submit_type = jQuery(this).attr('method');
		
		jQuery('.form_section input[type="submit"]').hide();
		jQuery('.form_section span.loader').show();
		
		//jQuery('#popup-selection-area').addLoadingMask();
		jQuery.ajax({
			type: submit_type,
			url: action_url, 
			data: form_data, //jQuery(this).serialize(),
			success: function(raw){
				var json = jQuery.parseJSON(raw);

				if(json.result == "failed"){
					var msg_area = jQuery('#pop-up-frame .error-msg-frame .errors');
					msg_area.empty();
					
					var error_msg = "";
					for(var i = 0; i < json.error.length; i++){
						error_msg += json.error[i]+"<br/>";
					}
					
					msg_area.html(error_msg);
					jQuery('#pop-up-frame .error-msg-frame').slideDown('slow');
					
					jQuery('.form_section input[type="submit"]').show();
					jQuery('.form_section span.loader').hide();
				}else if(json.result == "success"){
					var content = jQuery('#pop-up-frame');
					content.html('	<div style="text-align:center"> \
										<div style="width:1px;height:60px"></div> \
										<div style="font-size: 18px; line-height: 28px; font-weight: bold; color: #000000;">Thank You!<br/>Your enquiry has been successfully sent.</div> \
									</div> \
								');
				}
			}
		});
		
		return false;
	});
</script>
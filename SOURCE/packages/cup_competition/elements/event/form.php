<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?> 
<?php  
$form = Loader::helper('form');

$v = View::getInstance();
$html = Loader::helper('html');
$th = Loader::helper('concrete/urls'); 

$this->addHeaderItem($html->javascript('tiny_mce/tiny_mce.js'));

$this->addHeaderItem($html->css("jquery.ui.css"));
$this->addHeaderItem($html->javascript("jquery.js"));
$this->addHeaderItem($html->javascript('jquery.ui.js'));
$this->addHeaderItem($html->javascript('jquery-ui-timepicker-addon.js', 'cup_competition'));
$this->addHeaderItem($html->javascript('jquery-ui-sliderAccess.js', 'cup_competition'));
$this->addHeaderItem($html->css('jquery-ui-timepicker-addon.css', 'cup_competition'));

?>

<?php echo $form->hidden('id', @$entry['id']); ?>

<div class="span16">
	<div class="clearfix">
		<?php echo $form->label('name', t('Name') . '	<span class="ccm-required">*</span>')?>
		<div class="input">
			<?php echo $form->text('name', @$entry['name'], array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('category', t('Category'))?>
		<div class="input">
			<?php echo $form->select('category', array('HSC'=>'HSC', 'VCE'=>'VCE'), @$entry['category'],array('class' => "span6"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('type', t('Competition Type'))?>
		<div class="input">
			<?php //echo $form->select('type', 
					//			array('Photo'=>'Photo', 
					//				'Q&A'=>'Q&A'), 
					//			@$entry['type'], array('class' => "span6"))?>
			<?php $options = array('Photo'=>'Photo', 
									'Q&A'=>'Q&A');?>
			<select name="type" class="span6">
				<?php foreach($options as $key=>$value):?>
					<?php if(strcmp($key, @$entry['type']) == 0):?>
						<option value="<?php echo $key;?>" selected="selected"><?php echo $value;?></option>
					<?php else:?>
						<option value="<?php echo $key;?>"><?php echo $value;?></option>
					<?php endif;?>
				<?php endforeach;?>
			</select>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('start_time', t('Start Time'))?>
		<div class="input">
			<?php echo $form->text('start_time', 
								@$entry['start_time'], array('class' => "span6 datetime_field"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('end_time', t('End Time'))?>
		<div class="input">
			<?php echo $form->text('end_time', 
								@$entry['end_time'], array('class' => "span6 datetime_field"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('max_submission', t('Maximum Submission'))?>
		<div class="input">
			<?php echo $form->select('max_submission', 
								array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8), 
								@$entry['max_submission'], array('class' => "span6"))?>
		</div>
	</div>
	

	<div class="clearfix">
		<?php echo $form->label('homepage_content', t('Homepage Content'))?>
		<div class="input">
			<?php  Loader::element('editor_init'); ?>
			<?php  Loader::element('editor_config'); ?>
			<?php echo $form->textarea('homepage_content', @$entry['homepage_content'], array('class' => "ccm-advanced-editor"))?>
		</div>
	</div>
	
	<div class="clearfix">
		<?php echo $form->label('terms_and_conditions_content', t('Terms and Conditions Content'))?>
		<div class="input">
			<?php echo $form->textarea('terms_and_conditions_content', @$entry['terms_and_conditions_content'], array('class' => "ccm-advanced-editor"))?>
		</div>
	</div>
	
	<div style="background: #98ddff;">
		<div class="clearfix" style="margin: 0px 10px">
			<?php //echo $form->label('application_questions', t('Application Questions'))?>
			<div style="font-size:14px; font-weight: bold; line-height: 32px;">
				Form Builder (Step 1)
			</div>
			<div>
				<div class="form_builder" id="form_builder">
					<div class="form_content">
						<table style="width:100%">
							<thead>
								<tr>
									<th style="width:25%; padding: 5px; text-align: left">Field Name</th>
									<th style="width:15%; padding: 5px; text-align: left">Type</th>
									<th style="width:15%; padding: 5px; text-align: left">Required</th>
									<th style="width:45%; padding: 5px; text-align: left">Options</th>
									<th style="width:15%; padding: 5px; text-align: left">Remove</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$form_config = array();
								if(isset($entry['form_config']) && is_array($entry['form_config'])){
									$form_config = $entry['form_config'];
								}
								foreach($form_config as $each_field):
									$tmp_field_name = $each_field['field_name'];
									$tmp_field_type = $each_field['field_type'];
									$tmp_field_required = $each_field['field_required'];
									$tmp_field_config = $each_field['field_config'];
								?>
								
								<tr>
									<td style="padding: 5px;" valign="top">
										<input type="text" name="form_config_web[field_name][]" value="<?php echo str_replace('"', '\"', $tmp_field_name);?>"/>
									</td>
									<td style="padding: 5px;" valign="top">
										<?php $options = array('text'=>'text', 'select'=>'select', 'checkbox'=>'checkbox', 'textarea'=>'textarea');?>
										<select name="form_config_web[field_type][]" class="span2">
											<?php foreach($options as $key=>$value):?>
												<?php if(strcmp($key, $tmp_field_type) == 0):?>
													<option value="<?php echo $key;?>" selected="selected"><?php echo $value;?></option>
												<?php else:?>
													<option value="<?php echo $key;?>"><?php echo $value;?></option>
												<?php endif;?>
											<?php endforeach;?>
										</select>
									</td>
									<td style="padding: 5px;" valign="top">
										<select name="form_config_web[field_required][]" class="span2">
											<?php if($tmp_field_required):?>
												<option value="0">No</option>
												<option value="1" selected="selected">Yes</option>
											<?php else:?>
												<option value="0" selected="selected">No</option>
												<option value="1">Yes</option>
											<?php endif;?>
										</select>
									</td>
									<td style="padding: 5px;" valign="top">
										<textarea name="form_config_web[field_config][]"><?php echo htmlspecialchars($tmp_field_config);?></textarea>
									</td>
									<td style="padding: 5px;" >
										<a id="remove_field" href="javascript:void(0);">REMOVE</a>
									</td>
								</tr>
								
								<?php endforeach;?>
							</tbody>
						</table>
						<a href="javascript:add_field()">Add Field</a>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
	<div style="width:100%;height:20px"></div>
	
	<div style="background:#c6aaff;">
		<div class="clearfix qa_question_frame" style="margin: 0px 10px">
			<div style="width:100%;height:20px"></div>
			<!--
			<div class="input">
				<?php echo $form->textarea('qa_question', @$entry['qa_question'], array('class' => "span6"))?>
			</div>
			-->
			<div style="font-size:14px; font-weight: bold; line-height: 32px;">
				Q&A Form Builder (Step 2)
			</div>
			<div>
				<div class="form_builder" id="form_builder2">
					<div class="form_content">
						<table style="width:100%">
							<thead>
								<tr>
									<th style="width:25%; padding: 5px; text-align: left">Field Name</th>
									<th style="width:15%; padding: 5px; text-align: left">Type</th>
									<th style="width:15%; padding: 5px; text-align: left">Required</th>
									<th style="width:45%; padding: 5px; text-align: left">Options</th>
									<th style="width:15%; padding: 5px; text-align: left">Remove</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$form_config = array();
								if(isset($entry['qa_question']) && is_array($entry['qa_question'])){
									$form_config = $entry['qa_question'];
								}
								foreach($form_config as $each_field):
									$tmp_field_name = $each_field['field_name'];
									$tmp_field_type = $each_field['field_type'];
									$tmp_field_required = $each_field['field_required'];
									$tmp_field_config = $each_field['field_config'];
								?>
								
								<tr>
									<td style="padding: 5px;" valign="top">
										<input type="text" name="form_qa[field_name][]" value="<?php echo str_replace('"', '\"', $tmp_field_name);?>"/>
									</td>
									<td style="padding: 5px;" valign="top">
										<?php $options = array('text'=>'text', 'select'=>'select', 'checkbox'=>'checkbox', 'textarea'=>'textarea');?>
										<select name="form_qa[field_type][]" class="span2">
											<?php foreach($options as $key=>$value):?>
												<?php if(strcmp($key, $tmp_field_type) == 0):?>
													<option value="<?php echo $key;?>" selected="selected"><?php echo $value;?></option>
												<?php else:?>
													<option value="<?php echo $key;?>"><?php echo $value;?></option>
												<?php endif;?>
											<?php endforeach;?>
										</select>
									</td>
									<td style="padding: 5px;" valign="top">
										<select name="form_qa[field_required][]" class="span2">
											<?php if($tmp_field_required):?>
												<option value="0">No</option>
												<option value="1" selected="selected">Yes</option>
											<?php else:?>
												<option value="0" selected="selected">No</option>
												<option value="1">Yes</option>
											<?php endif;?>
										</select>
									</td>
									<td style="padding: 5px;" valign="top">
										<textarea name="form_qa[field_config][]"><?php echo htmlspecialchars($tmp_field_config);?></textarea>
									</td>
									<td style="padding: 5px;" >
										<a id="remove_field" href="javascript:void(0);">REMOVE</a>
									</td>
								</tr>
								
								<?php endforeach;?>
							</tbody>
						</table>
						<a href="javascript:add_field2()">Add Field</a>
					</div>
				</div>
			</div>
			<div style="width:100%;height:20px"></div>
		</div>
	</div>
	
	
	
	
	
	<div style="width:100%;height:20px"></div>
	<div style="background: #ffaa66; color: #333333">
		<div style="margin: 0px 10px;padding: 10px 0px;">
			<strong>Email Content Replacement tag:</strong> <br/><br/>
			%%FIRST_NAME%% %%LAST_NAME%% %%EMAIL%% <br/><br/>
			%%EVENT_NAME%% %%EVENT_START_DATE%% %%EVENT_END_DATE%% %%EVENT_START_DATETIME%% %%EVENT_END_DATETIME%% 
		</div>
	</div>
	
	<div style="background: #ffcc99;padding:0px 10px;">
		<div style="width:100%;height:20px"></div>
		<div class="clearfix">
			<?php echo $form->label('CONFIRMATION_EMAIL_SUBJECT', t('Confirmation Email Subject'))?>
			<div class="input">
				<?php echo $form->text('config[CONFIRMATION_EMAIL_SUBJECT]', @$entry['config']['CONFIRMATION_EMAIL_SUBJECT'], array('class' => "span4"))?>
			</div>
		</div>
		
		<div class="clearfix">
			<?php echo $form->label('CONFIRMATION_EMAIL_BODY', t('Confirmation Email Body'))?>
			<div class="input">
				<?php echo $form->textarea('config[CONFIRMATION_EMAIL_BODY]', @$entry['config']['CONFIRMATION_EMAIL_BODY'], array('class' => "ccm-advanced-editor"))?>
			</div>
		</div>
		
		
		
		<div class="clearfix">
			<?php echo $form->label('APPROVAL_EMAIL_SUBJECT', t('Approval Email Subject'))?>
			<div class="input">
				<?php echo $form->text('config[APPROVAL_EMAIL_SUBJECT]', @$entry['config']['APPROVAL_EMAIL_SUBJECT'], array('class' => "span4"))?>
			</div>
		</div>
		
		<div class="clearfix">
			<?php echo $form->label('APPROVAL_EMAIL_BODY', t('Approval Email Body'))?>
			<div class="input">
				<?php echo $form->textarea('config[APPROVAL_EMAIL_BODY]', @$entry['config']['APPROVAL_EMAIL_BODY'], array('class' => "ccm-advanced-editor"))?>
			</div>
		</div>
		
		
		<div class="clearfix">
			<?php echo $form->label('REJECTION_EMAIL_SUBJECT', t('Rejection Email Subject'))?>
			<div class="input">
				<?php echo $form->text('config[REJECTION_EMAIL_SUBJECT]', @$entry['config']['REJECTION_EMAIL_SUBJECT'], array('class' => "span4"))?>
			</div>
		</div>
		
		<div class="clearfix">
			<?php echo $form->label('REJECTION_EMAIL_BODY', t('Rejection Email Body'))?>
			<div class="input">
				<?php echo $form->textarea('config[REJECTION_EMAIL_BODY]', @$entry['config']['REJECTION_EMAIL_BODY'], array('class' => "ccm-advanced-editor"))?>
			</div>
		</div>
	</div>
	
</div>

<div class="clearfix"></div>

<script>
	jQuery('.datetime_field').datetimepicker({dateFormat: "yy-mm-dd", timeFormat: 'HH:mm:ss'});

	
	var remove_field = function(){
		var table_tr = jQuery(this).parent().parent();
		table_tr.remove();
		
	}
	
	var add_field = function(){
		var form_builder_block = jQuery('div.form_builder#form_builder');
		var table = form_builder_block.find('table');
		var table_body = table.find('tbody');
		
		var template = '<tr>'
							+'<td valign="top" style="padding: 5px"><input type="text" name="form_config_web[field_name][]"/></td>'
							+'<td valign="top" style="padding: 5px">'
								+'<select name="form_config_web[field_type][]" class="span2">'
									+'<option value="text">text</option>'
									+'<option value="select">select</option>'
									+'<option value="checkbox">checkbox</option>'
									+'<option value="textarea">textarea</option>'
								+'</select>'
							+'</td>'
							+'<td style="padding: 5px;" valign="top">'
									+'<select name="form_config_web[field_required][]" class="span2">'
											+'<option value="0">No</option>'
											+'<option value="1">Yes</option>'
									+'</select>'
							+'</td>'
							+'<td style="padding: 5px;" valign="top">'
								+'<textarea name="form_config_web[field_config][]"></textarea>'
							+'</td>'
							+'<td style="padding: 5px;">'
								+'<a id="remove_field" href="javascript:void(0);">REMOVE</a>'
							+'</td>'
						+'</tr>';
		var row_obj = jQuery(template);
		row_obj.find('a#remove_field').click(remove_field);
						
		table_body.append(row_obj);
	}
	
	
	var add_field2 = function(){
		var form_builder_block = jQuery('div.form_builder#form_builder2');
		var table = form_builder_block.find('table');
		var table_body = table.find('tbody');
		
		var template = '<tr>'
							+'<td valign="top" style="padding: 5px"><input type="text" name="form_qa[field_name][]"/></td>'
							+'<td valign="top" style="padding: 5px">'
								+'<select name="form_qa[field_type][]" class="span2">'
									+'<option value="text">text</option>'
									+'<option value="select">select</option>'
									+'<option value="checkbox">checkbox</option>'
									+'<option value="textarea">textarea</option>'
								+'</select>'
							+'</td>'
							+'<td style="padding: 5px;" valign="top">'
									+'<select name="form_qa[field_required][]" class="span2">'
											+'<option value="0">No</option>'
											+'<option value="1">Yes</option>'
									+'</select>'
							+'</td>'
							+'<td style="padding: 5px;" valign="top">'
								+'<textarea name="form_qa[field_config][]"></textarea>'
							+'</td>'
							+'<td style="padding: 5px;">'
								+'<a id="remove_field" href="javascript:void(0);">REMOVE</a>'
							+'</td>'
						+'</tr>';
		var row_obj = jQuery(template);
		row_obj.find('a#remove_field').click(remove_field);
						
		table_body.append(row_obj);
	}
	
	jQuery('div.form_builder#form_builder a#remove_field').click(remove_field);
	jQuery('div.form_builder#form_builder a#remove_field2').click(remove_field);
	
	
	var switch_type = function(){
		var type = jQuery('select[name="type"]').val();
		if(type == 'Photo'){
			jQuery('div.qa_question_frame').slideUp();
		}else if(type == 'Q&A'){
			jQuery('div.qa_question_frame').slideDown();
		}
	}
	
	jQuery('select[name="type"]').change(switch_type);
	switch_type();
</script>



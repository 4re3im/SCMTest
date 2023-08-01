<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$hash = 'md5';
?>
<?php if(strcmp($eventObj->type, 'Photo') == 0):?>
	<div class="cup_competition_page_section_title">
		<strong>Step 2</strong> - Your photo entry
	</div>
	<div style="width:100%;height: 20px;"></div>

	<div class="cup_competition_page_content">

		<div class="cup_competition_question_item">
			<div class="label">Upload your file:<span class="required">*</span></div>
			<div class="field">
				<input type="file" name="image"/>
			</div>
			<div class="note">
				Initial submissions should be less than 4 MB. Winners will be asked to supply high resolution<br/>
images for publication ( 300dpi, 20cm on the shortest side ) and your original work.
			</div>
		</div>

		<div class="cup_competition_question_item">
			<div class="label">IMAGE CAPTION<span class="required">*</span></div>
			<div class="field">
				<input type="text" name="image_caption" value="<?php echo str_replace('"', '\"', @$entryObj->image_caption);?>"/>
			</div>
		</div>
		
		<div class="cup_competition_question_item">
			<div class="label">IMAGE DESCRIPTION<span class="required">*</span></div>
			<div class="field">
				<textarea name="image_description"><?php echo @$entryObj->image_caption;?></textarea>
			</div>
		</div>
		<div style="width:100%;height: 20px;"></div>
	</div>
<?php else:?>
	<div class="cup_competition_page_section_title">
		<strong>Step 2</strong> - Your Q&A entry
	</div>
	<div style="width:100%;height: 20px;"></div>

	<!--
	<div class="cup_competition_page_content">
		<div class="cup_competition_question_item">
			<div class="field">
				<strong><?php echo @$eventObj->qa_question;?></strong>
			</div>
		</div>
	
		<div class="cup_competition_question_item">
			<div class="label">Answer<span class="required">*</span></div>
			<div class="field">
				<textarea name="qa_answer"><?php echo @$entryObj->qa_answer;?></textarea>
			</div>
		</div>
		<div style="width:100%;height: 20px;"></div>
	</div>
	
	-->
	
	<div class="cup_competition_page_content">
	<?php if(is_array($eventObj->qa_question)):
		foreach($eventObj->qa_question as $field):?>
		<?php 
			$ct++;
			if(strcmp($field['field_type'], 'text') == 0){
				$ct++;
			}elseif(strcmp($field['field_type'], 'textarea') == 0){
				$ct = $ct + 3;
			}elseif(strcmp($field['field_type'], 'select') == 0){
				$ct++;
			}elseif(strcmp($field['field_type'], 'checkbox') == 0){
				$ct = $ct + count(explode("\n", $field['field_config']));
			}
			
			$field_name = $field['field_name'];
			if($hash){
				$field_name = hash($hash, $field_name);
			}
			$field_name = 'fields_qa['.$field_name.']';
			
			$field_value = "";
			if(isset($entryObj->qa_answer[$field['field_name']])){
				$field_value = $entryObj->qa_answer[$field['field_name']];
			}
		?>
		
		<?php if(false): //$ct > intVal($style_line_ct/2) && !$is_split):?>
			</div>
			<div style="float: left; margin-left: 50px;">
			<?php $is_split = true;?>
		<?php endif;?>
		
		<div class="cup_competition_question_item">
			<div class="label">
				<?php echo $field['field_name'];?>
				<?php if(isset($field['field_required']) && $field['field_required']):?><span class="required">*</span><?php endif;?>
			</div>
			<div class="field">
				<?php if(strcmp($field['field_type'], 'text') == 0):?>
					<input type="text" name="<?php echo $field_name;?>" value="<?php echo str_replace('"', '\"', @$field_value);?>"/>
				<?php elseif(strcmp($field['field_type'], 'textarea') == 0):?>
					<textarea name="<?php echo $field_name;?>"/><?php echo @$field_value;?></textarea>
				<?php elseif(strcmp($field['field_type'], 'select') == 0):?>
					<select name="<?php echo $field_name;?>">
						<option value="">please select ...</option>
						<?php foreach(explode("\n", $field['field_config']) as $value):?>
							<?php $value = trim($value);?>
							<?php if(strcmp($value, $field_value) == 0):?>
								<option value="<?php echo str_replace('"', '\"', $value);?>" selected="selected"><?php echo $value;?></option>
							<?php else:?>
								<option value="<?php echo str_replace('"', '\"', $value);?>"><?php echo $value;?></option>
							<?php endif;?>
						<?php endforeach;?>
					</select>
				<?php elseif(strcmp($field['field_type'], 'checkbox') == 0):?>
					
					<?php foreach(explode("\n", $field['field_config']) as $value):?>
						<?php $value = trim($value);?>
						<div class="checkbox_item">
							<?php if(is_array($field_value) && in_array(trim($value),  $field_value)):?>
							<input type="checkbox" name="<?php echo $field_name;?>[]" value="<?php echo str_replace('"', '\"', $value);?>" checked="checked"/>
							<?php else:?>
							<input type="checkbox" name="<?php echo $field_name;?>[]" value="<?php echo str_replace('"', '\"', $value);?>"/>
							<?php endif;?>
							<!--<div class="value">--><?php echo $value;?><!--</div>-->
						</div>
					<?php endforeach;?>
				<?php endif;?>
			</div>
		</div>
		
		

	<?php endforeach;
	endif;?>
	</div>
	
	
<?php endif;?>

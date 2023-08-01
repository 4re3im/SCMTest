<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$hash = 'md5';

//print_r($eventObj->form_config);
$style_line_ct = 6;
if(is_array($eventObj->form_config)){
	foreach($eventObj->form_config as $field){
		$style_line_ct++;
		if(strcmp($field['field_type'], 'text') == 0){
			$style_line_ct++;
		}elseif(strcmp($field['field_type'], 'textarea') == 0){
			$style_line_ct = $style_line_ct + 3;
		}elseif(strcmp($field['field_type'], 'select') == 0){
			$style_line_ct++;
		}elseif(strcmp($field['field_type'], 'checkbox') == 0){
			$style_line_ct = $style_line_ct + count(explode("\n", $field['field_config']));
		}
	}
}

//echo "<div>{$style_line_ct}</div>";

$is_split = false;

$ct = 6;
?>

<div class="cup_competition_page_section_title">
	<strong>Step 1</strong> - Your details
</div>
<div style="width:100%;height: 20px;"></div>

<div class="cup_competition_page_content">

	<div style="float: left">
		
		<div class="cup_competition_question_item">
			<div class="label">FIRST NAME<span class="required">*</span></div>
			<div class="field">
				<input type="text" name="first_name" value="<?php echo str_replace('"', '\"', $entryObj->first_name);?>"/>
			</div>
		</div>
		
		<div class="cup_competition_question_item">
			<div class="label">LAST NAME<span class="required">*</span></div>
			<div class="field">
				<input type="text" name="last_name" value="<?php echo str_replace('"', '\"', $entryObj->last_name);?>"/>
			</div>
		</div>
		
		<div class="cup_competition_question_item">
			<div class="label">EMAIL<span class="required">*</span></div>
			<div class="field">
				<input type="text" name="email" value="<?php echo str_replace('"', '\"', $entryObj->email);?>"/>
			</div>
		</div>
		
	<?php if(is_array($eventObj->form_config)):
		foreach($eventObj->form_config as $field):?>
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
			$field_name = 'fields['.$field_name.']';
			
			$field_value = "";
			if(isset($entryObj->question_data[$field['field_name']])){
				$field_value = $entryObj->question_data[$field['field_name']];
			}
		?>
		
		<?php if($ct > intVal($style_line_ct/2) && !$is_split):?>
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
					<textarea name="<?php echo $field_name;?>"/><?php echo @$field_value;?>"</textarea>
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
	<div style="clear: both; width:1px height: 1px;"></div>
</div>

<pre>
<?php //print_r($entryObj->getAssoc());?>
</pre>
<?php $entry = $entryObj->getAssoc();
	$eventObj = $entryObj->getEventObject();
	$eventAssoc = $eventObj->getAssoc();
?>

<div class="cup-competition-entry-view left">
	<div class="field-attribute">Email:</div>
	<div class="field-value"><?php echo $entry['email'];?></div>
	<div class="clr"></div>
</div>

<div class="clr"></div>

<div class="cup-competition-entry-view left">
	<div class="field-attribute">First Name:</div>
	<div class="field-value"><?php echo $entry['first_name'];?></div>
	<div class="clr"></div>
</div>

<div class="cup-competition-entry-view left">
	<div class="field-attribute">Last Name:</div>
	<div class="field-value"><?php echo $entry['last_name'];?></div>
	<div class="clr"></div>
</div>

<div class="clr"></div>

<div style="width:100%;height:20px"></div>
	<?php 
	if(is_array($eventObj->form_config)):
	foreach($eventObj->form_config as $each_field):?>
		<?php $fieldname = $each_field['field_name'];?>
		<?php $fieldvalue = "";
			if(isset($entry['question_data'][$fieldname])){
				$fieldvalue = $entry['question_data'][$fieldname];
			}
		?>
		<div class="cup-competition-form-entry-view">
			<div class="field_name"><?php echo $fieldname;?></div>
			<div class="field_value">
				<?php if(is_array($fieldvalue)):?>
					<ul>
						<?php foreach($fieldvalue as $each_val):?>
						<li><?php echo $each_val;?></li>
						<?php endforeach;?>
					</ul>
				<?php elseif(strlen($fieldvalue) < 1):?>
					<i>... skip answer ...</i>
				<?php else:?>
					<?php echo $fieldvalue;?>
				<?php endif;?>
			</div>
		</div>
	<?php endforeach;
	endif;?>
<div style="width:100%;height:20px"></div>

<?php if(strcmp($eventAssoc['type'], 'Photo') == 0):?>
	<div style="width:100%; height: 30px"></div>
	<div class="cup-competition-dashboard-form-section-title"> Photo </div>
	<div style="margin: 0px 20px">
		<img src="<?php echo $entryObj->getImageUrl(500);?>"/>
		<table>
			<tr>
				<td valign="top" style="padding-top: 5px;"><strong>Caption</strong></td>
				<td valign="top" style="padding-left: 10px; padding-top: 5px;"><?php echo $entryObj->image_caption;?></td>
			</tr>
			<tr>
				<td valign="top" style="padding-top: 5px;"><strong>Description</strong></td>
				<td valign="top" style="padding-left: 10px; padding-top: 5px"><?php echo $entryObj->image_description;?></td>
			</tr>
		</table>
	</div>
<?php else:?>
	<div style="width:100%; height: 30px"></div>
	<div class="cup-competition-dashboard-form-section-title"> Q&A </div>
	<div style="margin: 0px 20px">
		<?php 
		if(is_array($eventObj->qa_question)):
		foreach($eventObj->qa_question as $each_field):?>
			<?php $fieldname = $each_field['field_name'];?>
			<?php $fieldvalue = "";
				if(isset($entry['qa_answer'][$fieldname])){
					$fieldvalue = $entry['qa_answer'][$fieldname];
				}
			?>
			<div class="cup-competition-form-entry-view">
				<div class="field_name"><?php echo $fieldname;?></div>
				<div class="field_value">
					<?php if(is_array($fieldvalue)):?>
						<ul>
							<?php foreach($fieldvalue as $each_val):?>
							<li><?php echo $each_val;?></li>
							<?php endforeach;?>
						</ul>
					<?php elseif(strlen($fieldvalue) < 1):?>
						<i>... skip answer ...</i>
					<?php else:?>
						<?php echo $fieldvalue;?>
					<?php endif;?>
				</div>
			</div>
		<?php endforeach;
		endif;?>
	</div>
<?php endif;?>


<div style="width:100%;height:20px"></div>
<div class="cup-competition-entry-view left">
	<div class="field-attribute">Status:</div>
	<div class="field-value">
		<?php if(strcmp($entry['status'], 'pending') == 0):?>
			<select name="status">
				<option value="pending">Pending</option>
				<option value="approved">Approve</option>
				<option value="rejected">Reject</option>
			</select>
		<?php else:?>
			<?php echo $entry['status'];?>
		<?php endif;?>
	</div>
	<div class="clr"></div>
</div>

<div class="clr"></div>

<div class="cup-competition-entry-view left">
	<div class="field-attribute">Note:</div>
	<div class="field-value">
		<textarea name="note" style="width:400px;height: 80px;"><?php echo $entry['note']?></textarea>
	</div>
	<div class="clr"></div>
</div>

<div class="clr"></div>



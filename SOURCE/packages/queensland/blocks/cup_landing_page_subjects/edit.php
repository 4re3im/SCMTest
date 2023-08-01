<?php
	defined('C5_EXECUTE') or die(_("Access Denied."));

	$form = Loader::helper('form');
	$al = Loader::helper('concrete/asset_library');
	
	Loader::element('editor_init');
	Loader::element('editor_config');

?>
<script>
	window.tinymce.dom.Event.domLoaded = true;
</script>
<style>
table.landing_subjects_content{
	margin: 0 20px;
	width: 1200px;
}

table.landing_subjects_content thead th{
	background: #CCCCCC;
	color: #333333;
	text-align: center;
}

table.landing_subjects_content span#btnAddSubjectRow{
	padding: 3px 5px;
	background: #333333;
	color: #CCCCCC;
	font-weight: bold;
	cursor:pointer;
}

table.landing_subjects_content .btn-mv-up{
	background: #AAAAAA;
	color: #666666;
	padding: 3px 4px;
	cursor: pointer;
}

table.landing_subjects_content .btn-mv-dn{
	background: #AAAAAA;
	color: #666666;
	padding: 3px 4px;
	margin: 0px 5px;
	cursor: pointer;
}

table.landing_subjects_content .btn-remove{
	background: #AAAAAA;
	color: #666666;
	padding: 3px 4px;
	cursor: pointer;
}

table.landing_subjects_content thead th.col-name{
	width: 80px;
}

table.landing_subjects_content thead th.col-image{
	width: 120px;
}

table.landing_subjects_content thead th.col-txt{
	width: 120px;
}

table.landing_subjects_content thead th.col-txt-info{
	width: 250px;
}

table.landing_subjects_content thead th.col-html{
	width: 200px;
}

table.landing_subjects_content thead th.col-html-info{
	width: 250px
}

table.landing_subjects_content thead th.action{
	width: 120px;
}

table.landing_subjects_content td input,
table.landing_subjects_content td textarea{
	width: 90%;
}

table.landing_subjects_content td textarea{
	min-height: 80px;
}

table.landing_subjects_content tbody.entry > tr:nth-child(odd){ 
	background-color:#eee; 
}

table.landing_subjects_content tbody.entry > tr:nth-child(even){ 
	background-color:#ccc; 
}
</style>

<?php
	$bg_color_opts = array(
			"#d84b2d" => "Orange 1",
			"#f3ab58" => "Orange 2",
			"#ec733c" => "Orange 3",
			"#e8582e" => "Orange 4",
			"#ef9049" => "Orange 5"
		);
?>

<table class="landing_subjects_content">
	<thead>
		<tr>
			<th class="col-name">Name</th>
			<th class="col-image">Image</th>
			<th class="col-txt">Instructional Text</th>
			<th class="col-txt-info">Info (Wiki text)</th>
			<th class="col-html">Video Embed Code</th>
			<th class="col-html-info">Video Info (Wiki text)</th>
			<th class="col-background-color">Background</th>
			<th class="col-action">Action</th>
		</tr>
	<thead>
	<tbody class="entry">
	<?php foreach($config as $key => $each):?>
	<tr>
		<td class="col-name">
			<?php echo $form->text("config[".$key."][name]", $each["name"]);?>
		</td>
		<td class="col-image">
			<?php
			$file = null;
			if($each["image"]){
				$file =  File::getByID($each["image"]);
			}
			echo $al->image('image'.$key, "config[".$key."][image]", t('Pick an image.'), $file);
			?>
		</td>
		<td class="col-txt"><?php echo $form->text("config[".$key."][text]", $each["text"]);?></td>
		<td class="col-txt-info"><?php echo $form->textarea("config[".$key."][info]", $each["info"], array('class'=>"jqte"));?></td>
		<td class="col-html"><?php echo $form->textarea("config[".$key."][html]", $each["html"]);?></td>
		<td class="col-html-info"><?php echo $form->textarea("config[".$key."][html_info]", $each["html_info"], array('class'=>"jqte"));?></td>
		<td class="col-bg-color"><?php echo $form->select("config[".$key."][background_hex]", $bg_color_opts, $each["background_hex"] );?></td>
		<td class="col-action">
			<span class="btn-mv-up">UP</span>
			<span class="btn-mv-dn">DN</span>
			<span class="btn-remove">Rm</span>
		</td>
	</tr>
	<?php endforeach;?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4"><span id="btnAddSubjectRow">Add Subject</span></td>
		</tr>
	</tfoot>
</table>

<div id="file_selector_template" style="display:none;">
	<?php echo $al->image('xxxkeyxxx', 'aaakeyaaa', t('Pick an image.'));?>
<div>


<script>
jQuery('textarea.jqte').jqte();

jQuery('table.landing_subjects_content span.btn-mv-up').click(function(){
	var current_row = jQuery(this).parent().parent();
	var prev_row = current_row.prev();
	if(prev_row.length > 0)
	current_row.detach().insertBefore(prev_row);
});

jQuery('table.landing_subjects_content span.btn-mv-dn').click(function(){
	var current_row = jQuery(this).parent().parent();
	var next_row = current_row.next();
	if(next_row.length > 0)
	current_row.detach().insertAfter(next_row);
});

jQuery('table.landing_subjects_content span.btn-remove').click(function(){
	jQuery(this).parent().parent().remove();
});

jQuery('table.landing_subjects_content span#btnAddSubjectRow').click(function(){
	var d = new Date();
	var k = d.getTime();
	var name_field = jQuery('<input type="text" name="config['+k+'][name]">');
	//var image_field = jQuery('<input type="text" name="config\\['+k+'\\]\\[image]">');
	
	var edid = "editor-"+k;
	
	var html = jQuery('#file_selector_template').html();
	
	html = html.replace(/aaakeyaaa/g, "config["+k+"][image]");
	html = html.replace(/xxxkeyxxx/g, "image"+k);
	var image_field = jQuery(html);
	
	var txt_field = jQuery('<textarea name="config['+k+'][text]"></textarea>');
	var info_field = jQuery('<textarea name="config['+k+'][info]"></textarea>');
	info_field.addClass(edid);
	
	var html_field = jQuery('<textarea name="config['+k+'][html]"></textarea>');
	var htmlinfo_field = jQuery('<textarea name="config['+k+'][html_info]"></textarea>');
	htmlinfo_field.addClass(edid);
	
	var background_field = jQuery('<select name="config['+k+'][background_hex]"></select>');
	var bg_color_1 = jQuery('<option value="#d84b2d">Orange 1</option>');
	var bg_color_2 = jQuery('<option value="#f3ab58">Orange 2</option>');
	var bg_color_3 = jQuery('<option value="#ec733c">Orange 3</option>');
	var bg_color_4 = jQuery('<option value="#e8582e">Orange 4</option>');
	var bg_color_5 = jQuery('<option value="#ef9049">Orange 5</option>');
	background_field.append(bg_color_1);
	background_field.append(bg_color_2);
	background_field.append(bg_color_3);
	background_field.append(bg_color_4);
	background_field.append(bg_color_5);
	background_field.addClass(edid);
	
	
	var action_move_up = jQuery('<span class="btn-mv-up">UP</span>');
	var action_move_dn = jQuery('<span class="btn-mv-dn">DN</span>');
	var action_remove = jQuery('<span class="btn-remove">Rm</span>');
	
	action_move_up.click(function(){
		var current_row = jQuery(this).parent().parent();
		var prev_row = current_row.prev();
		if(prev_row.length > 0)
		current_row.detach().insertBefore(prev_row);
	});
	
	action_move_dn.click(function(){
		var current_row = jQuery(this).parent().parent();
		var next_row = current_row.next();
		if(next_row.length > 0)
		current_row.detach().insertAfter(next_row);
	});
	
	action_remove.click(function(){
		jQuery(this).parent().parent().remove();
	});
	
	
	var name_col = jQuery("<td></td>");
	name_col.append(name_field);
	
	
	var image_col = jQuery("<td></td>");
	image_col.append(image_field);
	
	
	var txt_col = jQuery("<td></td>");
	txt_col.append(txt_field);
	
	var info_col = jQuery("<td></td>");
	info_col.append(info_field);
	
	var html_col = jQuery("<td></td>");
	html_col.append(html_field);
	
	
	var htmlinfo_col = jQuery("<td></td>");
	htmlinfo_col.append(htmlinfo_field);
	
	var bgcolor_col = jQuery("<td></td>");
	bgcolor_col.append(background_field);
	
	

	var action_col = jQuery("<td></th>");
	action_col.append(action_move_up);
	action_col.append(action_move_dn);
	action_col.append(action_remove);
	
	
	var row = jQuery('<tr></tr>');
	row.append(name_col);
	row.append(image_col);
	row.append(txt_col);
	row.append(info_col);
	row.append(html_col);
	row.append(htmlinfo_col);	
	row.append(bgcolor_col);	
	row.append(action_col);
	
	jQuery("table.landing_subjects_content tbody.entry").append(row);
	
	info_field.jqte();
	htmlinfo_field.jqte();
	
});
</script>
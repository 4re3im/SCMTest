<?php
	$ch = Loader::helper('cup_content_html', 'cup_content');

    Loader::model('title/model', 'cup_content');
?>
<?php if(count($sample_pages) > 0):?>
    <?php
        $titleObj = CupContentTitle::fetchByID($sample_pages[0]->titleID);
    ?>

	<ul>
		<?php foreach($sample_pages as $each):?>
			<?php $file_url = $ch->url('/titles/downloadSamplePage/'.$titleObj->isbn13.'/'.$each->id.'/'.$each->filename);
                 $file_url = rtrim($file_url, "/");
            ?>
			<li><a target="_blank" href="<?php echo $file_url;?>" onClick="_gaq.push(['_trackEvent', 'download', 'Sample Pages', '<?php echo $file_url;?>']);"><?php echo $each->description;?> - <?php echo $ch->formatRawSize($each->filesize);?></a></li>
		<?php endforeach;?>
	</ul>
<?php else:?>
	<div style="font-size: 14px; color: #333333;">No sample pages available</div>
<?php endif;?>
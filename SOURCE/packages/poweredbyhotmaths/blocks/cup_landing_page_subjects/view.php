<?php
	require_once("wiky.inc.php");
	require_once("lib/DefaultParserBackend.php");
	require_once("lib/WikitextParser.php");
	
	WikitextParser::init();
	
	$wiky=new wiky;

?>

<div class="cup-landing-subjects">
	<div style="height: 20px;width:100%"></div>
<?php $ct = 0;?>
<?php foreach($config as $key => $each):?>
	<?php $ct++;?>
	<?php if($ct % 2 == 0):?>
	<div class="subject right" style="background:<?php echo $each["background_hex"];?>" bgc="<?php echo $each["background_hex"];?>">
	<?php else:?>
	<div class="subject left" style="background:<?php echo $each["background_hex"];?>" bgc="<?php echo $each["background_hex"];?>">
	<?php endif;?>
		<div class="frame">
			
			<div class="logo">
				<?php 
				if($each["image"]){
					$file =  File::getByID($each["image"]);
					if($file):
						$fv = $file->getApprovedVersion(); ?>
					<img src="<?php echo $fv->getDownloadURL();?>">
				
					<?php endif;
				}?>
			</div>
			<div class="info">
				<div class="pad"></div>
				<div class="content">
					<?php
						echo $each["info"];
					?>
				</div>
				<div class="pad"></div>
			</div>
			<div class="video">
				<div class="content"><?php echo $each["html"];?></div>
				<div class="desc">
					<?php
						echo $each["html_info"];
					?>
				</div>
			</div>
			<div class="banner">
				<h3><?php echo $each["name"];?></h3>
				<div class="txt">
					<?php echo $each["text"];?>
				</div>
			</div>
			<div class="action">
				<?php if(strlen(trim($each["info"])) > 0):?>
				<a class="info_tog" href="javascript: void(0);"><i class="info"></i></a>
				<?php endif;?>
				
				<?php if(strlen(trim($each["html"])) > 0):?>
				<a class="htmlinfo_tog" href="javascript: void(0);"><i class="video"></i></a>
				<?php endif;?>
			</div>
		</div>
	</div>
<?php endforeach;?>
	<div style="clear:both;height:1px;width:100%"></div>
	<div style="height: 110px;width:100%"></div>
</div>

<script>
jQuery(".cup-landing-subjects .subject").each(function(){
	var h3 = jQuery(this).find(".banner h3");
	jQuery(this).find(".banner").css({height: 30+h3.height()+"px"});
});

jQuery(".cup-landing-subjects .subject").hover(
	function(){
		jQuery(this).animate({
          backgroundColor: "#da4922"
		});
		
		jQuery(this).find(".banner").animate({height:"120px"});
		jQuery(this).find('h3').animate({color:"#da4922"});
	}, 
	function(){
		jQuery(this).animate({
          backgroundColor: jQuery(this).attr("bgc")
		});
		var h3 = jQuery(this).find(".banner h3")
		//jQuery(this).find(".banner").animate({height:"60px"});
		jQuery(this).find(".banner").animate({height: 30+h3.height()+"px"});
		jQuery(this).find('h3').animate({color:"#FFFFFF"});
		
	});
	

jQuery(".cup-landing-subjects .subject .action a i.info").click(function(){
	var m = jQuery(this).parent().parent().parent();
	if(m.find(".action a i.video").hasClass("active")){
		m.find("div.video").hide();
		m.find(".action a i.video").removeClass("active");
		
		var iframe = m.find("div.video iframe")[0].contentWindow;
		iframe.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');	
	}
	
	if(jQuery(this).hasClass("active")){
		m.find("div.info").hide();
		jQuery(this).removeClass("active");
	}else{
		m.find("div.info").show();
		jQuery(this).addClass("active");
	}
})


jQuery(".cup-landing-subjects .subject .action a i.video").click(function(){
	var m = jQuery(this).parent().parent().parent();
	if(m.find(".action a i.info").hasClass("active")){
		m.find("div.info").hide();
		m.find(".action a i.info").removeClass("active");
	}
	
	if(jQuery(this).hasClass("active")){
		m.find("div.video").css({display:"none"});
		jQuery(this).removeClass("active");
		var iframe = m.find(".video iframe")[0].contentWindow;
		iframe.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');	
	}else{
		m.find("div.video").css({display:"block"});
		jQuery(this).addClass("active");
	}
})

jQuery(".cup-landing-subjects .subject div.video iframe").css({width:"100%", height:"100%"});
</script>
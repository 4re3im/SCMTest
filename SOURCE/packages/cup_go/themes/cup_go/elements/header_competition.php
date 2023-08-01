	<div id="global-nav">
            
        
		<ul id="nav">
			<?php
				$cup_logo_url = BASE_URL.DIR_REL.'/themes/cup/images/cup/logo.jpg';
			?>
			<li class="logo"><a href="http://www.cambridge.org"><img alt="Cambridge University Press logo" src="<?php echo $cup_logo_url;?>"></a></li>

								<li><a href="http://www.cambridge.org/aus/browse/academic.asp?site_locale=en_AU&amp;prefCountry=AU">Academic</a></li>
								<li><a href="http://journals.cambridge.org/?site_locale=en_AU&amp;prefCountry=AU">Journals</a></li>
								<li><a href="http://www.cambridge.org/au/elt/?site_locale=en_AU&amp;prefCountry=AU">Cambridge English</a></li>
								<li><a href="http://www.cambridge.edu.au/education/?site_locale=en_AU&amp;prefCountry=AU">Education</a></li>
								<li><a href="http://www.cambridge.org/bibles/?site_locale=en_AU&amp;prefCountry=AU">Bibles</a></li>
			
			<li><a href="http://www.cambridge.org/digital-products/">Digital Products</a></li>
			<li class="drop"><a href="http://www.cambridge.org/about-us/">About Us&nbsp;&nbsp;</a>
				<ul>
					<li><a href="http://www.cambridge.org/about-us/who-we-are/press-syndicate/">Governance</a></li>
					<li><a href="http://www.cambridge.org/about-us/what-we-do/cambridge-conference-facilities">Conference Venues</a></li>
					<li><a href="http://www.cambridge.org/about-us/rights-permissions/">Rights &amp; Permissions</a></li>
					<li><a href="http://www.cambridge.org/about-us/contact-us/">Contact Us</a></li>
				</ul>
			</li>
			<li><a href="http://www.cambridge.org/about-us/careers/">Careers</a></li>
		</ul>
	</div>
	<div class="frame_header">
		<?php $this->inc('elements/header_heading_content.php');?>
	</div>
	<div class="frame_body">
	<?php $backgroundAttribute = $c->getAttribute('background');
		$content_background_style = "";
		if ($backgroundAttribute) {
			$backgroundFile = $backgroundAttribute->getRelativePath();
			$content_background_style = "background:url('{$backgroundFile}')";
		}
	?>
		<div class="frame_content" style="<?php echo $content_background_style;?>">
			<div class="cup-menu-frame">
				<div class="green_bar"></div>
				<div class="master-frame">
					<?php if(isset($category) && strcmp(strtoupper($category), 'HSC') == 0):?>
						<a href="<?php echo View::url("/checkpoints/hsc");?>">
							<div class="btn home">CHECKPOINTS HOME</div>
						</a>
					<?php else:?>
						<a href="<?php echo View::url("/checkpoints/vce");?>">
							<div class="btn home">CHECKPOINTS HOME</div>
						</a>
					<?php endif;?>
					
					<?php if(isset($category) && strcmp(strtoupper($category), 'HSC') == 0):?>
						<a href="<?php echo View::url("/checkpoints/hsc/booksellers");?>">
							<div class="btn ">FIND A BOOKSELLER</div>
						</a>
					<?php endif;?>
					
					<?php if(isset($category) && strcmp(strtoupper($category), 'HSC') == 0):?>
						<a href="<?php echo View::url("/checkpoints/hsc/study_guides");?>">
							<div class="btn ">BUY ONLINE</div>
						</a>
					<?php else:?>
						<a href="<?php echo View::url("/checkpoints/vce/study_guides");?>">
							<div class="btn ">BUY ONLINE</div>
						</a>
					<?php endif;?>
					
					<?php if(isset($category) && strcmp(strtoupper($category), 'HSC') == 0):?>
						<a href="https://www.facebook.com/pages/Cambridge-Checkpoints-HSC/195493853801140">
							<div class="btn facebook">&nbsp;</div>
						</a>
					<?php else:?>
						<a href="https://www.facebook.com/pages/Cambridge-Checkpoints-VCE/130600457006469">
							<div class="btn facebook">&nbsp;</div>
						</a>
					<?php endif;?>
					<div class="clr empty"></div>
				</div>
			</div>
			<div class="clr empty"></div>
			<div class="cup-banner <?php echo strtolower($category);?>"></div>
		



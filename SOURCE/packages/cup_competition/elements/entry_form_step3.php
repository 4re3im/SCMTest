<div class="cup_competition_page_section_title">
	<strong>Step 3</strong> - Terms and conditions
</div>
<div style="width:100%;height: 20px;"></div>

<div class="cup_competition_page_content">
	<div class="cup_competition_question_item">
		<div class="label"></div>
		<div class="field">
			<div class="checkbox_item">
				<?php if(isset($_POST['agree_terms_and_conditions'])):?>
				<input type="checkbox" name="agree_terms_and_conditions" value="Yes" checked="checked"/>
				<?php else:?>
				<input type="checkbox" name="agree_terms_and_conditions" value="Yes"/>
				<?php endif;?>
				<a target="_blank" href="<?php echo $this->url('/competition/terms_and_conditions/'.strtoupper($eventObj->category));?>">Agree to terms and conditions</a><span class="required">*</span>
			</div>
		</div>
	</div>
	
	<div style="width:100%;height: 20px;"></div>
	<div class="cup_competition_question_item">
		<?php
			$captcha = Loader::helper('validation/captcha');
		?>
		<div class="label"></div>
		<div class="field">
				<?php $captcha->display();?>
				<input type="text" name="ccmCaptchaCode" style="width: 200px;"/>
		</div>
	</div>
	<div style="width:100%;height: 20px;"></div>
</div>

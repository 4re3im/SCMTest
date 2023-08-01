<?php  defined('C5_EXECUTE') or die("Access Denied."); ?>

   <div id="footer" class="container-fluid">
      <div class="container">
         <ul class="noPadding">
            <li><a href="https://twitter.com/Cambridge_AusEd" class="twitter icon hvr-bounce-in" target="_blank">Twitter</a></li>
            <li><a href="https://www.facebook.com/CambridgeUniversityPressEducationAustralia?ref=hl" class="facebook icon hvr-bounce-in" target="_blank">Facebook</a></li>
            <li><a href="https://plus.google.com/107441058984269920723/posts" class="googlePlus icon hvr-bounce-in" target="_blank">Google Plus</a></li>
            <li><a href="https://www.youtube.com/user/CUPANZEducation" class="youtube icon hvr-bounce-in" target="_blank">Youtube</a></li>
         </ul>
         <p>
            <span href="#" class="edjin icon"></span>
         </p>
      </div>
      <div class="container-fluid">
         <ul class="small noPadding">
            <li><a href="<?php echo $this->url('/dynamicenglish/privacy-policy'); ?>">Privacy Policy</a></li>
            <li><a href="<?php echo $this->url('/dynamicenglish/terms'); ?>">Terms of use</a></li>
         </ul>
      </div>
   </div>


   <a href="#logo" class="cd-top">Top</a>


<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   <script src="<?php //echo $this->getThemePath(); ?>/js/bootstrap.min.js"></script>
   <script src="<?php //echo $this->getThemePath(); ?>/js/custom.js"></script>
 -->


</div>

<?php  Loader::element('footer_required'); ?>
</body>
</html>
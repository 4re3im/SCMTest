<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
?>
<?php $this->inc('elements/header.php'); ?>
<style type="text/css">
<?php if($c->isEditMode()){ ?>
.navbar-wrapper {position: inherit;margin-bottom: 5px;}
<?php }else{ ?>
.navbar-wrapper {position: relative;}
<?php } ?>
</style>

    <!-- Carousel
    ================================================== -->
    <div id="myCarousel" class="carousel slide">
      <div class="carousel-inner">
      	<?php 
		$a = new Area('Banner');
		$a->setBlockLimit(1);
		$a->display($c);
		?>
		<?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
        <div class="item active">
          <img src="<?php echo $this->getStyleSheet('assets/img/examples/slide-01.jpg')?>" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>Example headline.</h1>
              <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <a class="btn btn-large btn-primary" href="#">Sign up today</a>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="<?php echo $this->getStyleSheet('assets/img/examples/slide-02.jpg')?>" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>Another example headline.</h1>
              <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <a class="btn btn-large btn-primary" href="#">Learn more</a>
            </div>
          </div>
        </div>
        <div class="item">
          <img src="<?php echo $this->getStyleSheet('assets/img/examples/slide-03.jpg')?>" alt="">
          <div class="container">
            <div class="carousel-caption">
              <h1>One more for good measure.</h1>
              <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <a class="btn btn-large btn-primary" href="#">Browse gallery</a>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
    </div><!-- /.carousel -->



    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing">

      <!-- Three columns of text below the carousel -->
      <div class="row highlight">
        <div class="span4">
	        <?php 
			$a = new Area('highlight1');
			$a->display($c);
			?>
          <?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
          <img class="img-circle" src="http://placehold.it/140x140">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna, vel scelerisque nisl consectetur et.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <?php } ?>
        </div><!-- /.span4 -->
        <div class="span4">
           <?php 
			$a = new Area('highlight2');
			$a->display($c);
			?>
          <?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
          <img class="img-circle" src="http://placehold.it/140x140">
          <h2>Heading</h2>
          <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <?php } ?>
        </div><!-- /.span4 -->
        <div class="span4">
           <?php 
			$a = new Area('highlight3');
			$a->display($c);
			?>
          <?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
          <img class="img-circle" src="http://placehold.it/140x140">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <?php } ?>
        </div><!-- /.span4 -->
      </div><!-- /.row -->


      <br/><br/>


      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
      	<?php 
			$a = new Area('Hero');
			$a->display($c);
		?>
	    <?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
        <h1>Hello, world!</h1>
        <p>This is a template for a simple marketing or informational website. It includes a large callout called the hero unit and three supporting pieces of content. Use it as a starting point to create something more unique.</p>
        <p><a class="btn btn-primary btn-large">Learn more &raquo;</a></p>
        <?php } ?>
      </div>

      <!-- Example row of columns -->
      <div class="row">
        <div class="span6">
          <?php 
			$a = new Area('Feature Left');
			$a->display($c);
		  ?>
		  <?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
          <h2>Heading</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <?php } ?>
        </div>
        <div class="span6">
          <?php 
			$a = new Area('Feature Right');
			$a->display($c);
		  ?>
		  <?php if(!$c->isEditMode() && $a->getTotalBlocksInArea() < 1){ ?>
          <h2>Heading</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
          <?php } ?>
       </div>
      </div>

      <!-- /END THE FEATURETTES -->

<?php $this->inc('elements/footer.php'); ?>
<?php
	Loader::model('title_downloadable_file/model', 'cup_content');
	$ch = Loader::helper('cup_content_html', 'cup_content');
	
	$hasRealPrice = false;
	
	$isDigitalProduct = false;
	
	if($titleObject->hasAccessCode() || $titleObject->hasDownloadableFile){
		$isDigitalProduct = true;
	}
	
?>
<?php if(!in_array(strtoupper($titleObject->availability), array("NDR"))):?>

<?php $product = $titleObject->getCurrentLocateProduct();?>

<?php if($product && $product->isProductEnabled()):?>
	<?php if($product->prPrice > 0.001):?>
		<?php $hasRealPrice = true; ?>
		<div class="price-tag">
			<?php if(isset($display_quantity) && $display_quantity == true):?>
				Price: &nbsp;
			<?php endif;?>
			<?php $ch->currency();?>
			<?php Loader::packageElement('product/price', 'core_commerce', array('product'=>$product)); ?>
		
		</div>
	<?php endif;?>
	<?php if(in_array(strtoupper($titleObject->availability), array('NP???' /*this code does not exist - always skip*/))):?>
		<div class="price-action forthcoming">Forthcoming</div>
	<?php else:?>
		<?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0):	//New Zealand?>
			<?php
				$stock_quantity = $product->getProductQuantity();
				
				$titleObject = CupContentTitle::fetchByProductId($product->getProductID());
				$auProduct = $titleObject->getAuProduct();
				if($auProduct->getProductID()){
					$stock_quantity += $auProduct->getProductQuantity();
				}
			?>
			<?php if($titleObject->hasAccessCode() && in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ', 'TOS', 'UCC', 'NP', 'NPE', 'NPX', 'NPZ', 'NYP'))): //digital product?>
				<?php if(in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ'))):?>
					<div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('TOS', 'UCC'))):?>
					<div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
					<div class="price-action forthcoming">Forthcoming</div>
				<?php endif;?>
			<?php elseif($titleObject->hasAccessCode() && $product->isSoldOut() ):?>
					<div class="price-action contactcs">Contact&nbsp;Customer&nbsp;Service</div>
			<?php elseif($titleObject->hasDownloadableFile 
					&& in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ', 'TOS', 'UCC', 'NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
				<?php if(in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ'))):?>
					<div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('TOS', 'UCC'))):?>
					<div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
					<div class="price-action forthcoming">Forthcoming</div>
				<?php else:?>
					<div class="price-action contactcs">Contact&nbsp;Customer&nbsp;Service</div>
				<?php endif;?>
			<?php elseif($product->isSoldOut()): 	//physical product?>
				<?php if(in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ'))):?>
					<div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('POS', 'UCC'))):?>
					<div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('NP', 'NPX', 'NPZ', 'NYP'))):?>
					<div class="price-action forthcoming">Forthcoming</div>
				<?php else:?>
					<div class="price-action contactcs">Contact&nbsp;Customer&nbsp;Service</div>
				<?php endif;?>
			<?php else:?>
				<div class="price-action">
					<?php
						$args['product'] = $product;
                        $args['id'] = $product->getProductID() . '-' . $subject->id;
                        // Nick Ingarsia, 27/10/14, pass in cID, if this is called via AJAX
                        if (isset($cID)) {
    						$args['cID'] = $cID;
                        }
						if(isset($display_quantity) && $display_quantity == true){
							$args['displayQuantity'] = true;
						}
						Loader::packageElement('product/display_simple', 'cup_content', $args);
					?>
				</div>
			<?php endif;?>
		<?php else: //Australia?>
			<?php if($titleObject->hasAccessCode() && in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ', 'TOS', 'UCC', 'NP', 'NPE', 'NPX', 'NPZ', 'NYP'))): //digital product?>
				<?php if(in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ'))):?>
					<div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('TOS', 'UCC'))):?>
					<div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
					<div class="price-action forthcoming">Forthcoming</div>
				<?php endif;?>
			<?php elseif($titleObject->hasAccessCode() && $product->isSoldOut() ):?>
					<div class="price-action contactcs">Contact&nbsp;Customer&nbsp;Service</div>
			<?php elseif($titleObject->hasDownloadableFile 
					&& in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ', 'TOS', 'UCC', 'NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
				<?php if(in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ'))):?>
					<div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('TOS', 'UCC'))):?>
					<div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
					<div class="price-action forthcoming">Forthcoming</div>
				<?php else:?>
					<div class="price-action contactcs">Contact&nbsp;Customer&nbsp;Service</div>
				<?php endif;?>
			<?php elseif($product->isSoldOut()): 	//physical product?>
				<?php if(in_array(strtoupper($titleObject->availability), array('IPX', 'IPZ'))):?>
					<div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('POS', 'UCC'))):?>
					<div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
				<?php elseif(in_array(strtoupper($titleObject->availability), array('NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
                    <?php $circa_price = $titleObject->getCurrentLocateCircaPrice();?>
                    <?php if($circa_price && in_array(strtoupper($titleObject->availability), array('NP', 'NPX'))):?>
                        <?php if(!$hasRealPrice):?>
						<div class="circa-price">
                            Circa Price:
                            <span class="circa-price-value">$<?php echo $circa_price;?></span>
                        </div>
						<?php endif;?>
                    <?php endif;?>
					<div class="price-action forthcoming">Forthcoming</div>
				<?php else:?>
					<div class="price-action contactcs">Contact&nbsp;Customer&nbsp;Service</div>
				<?php endif;?>
			<?php else:?>
				<div class="price-action">
					<?php
						$args['product'] = $product;
						$args['id'] = $product->getProductID() . '-' . $subject->id;
                        // Nick Ingarsia, 27/10/14, pass in cID, if this is called via AJAX
                        if (isset($cID)) {
                            $args['cID'] = $cID;
                        }
						if(isset($display_quantity) && $display_quantity == true){
							$args['displayQuantity'] = true;
						}
						Loader::packageElement('product/display_simple', 'cup_content', $args);
					?>
				</div>
			<?php endif;?>
		<?php endif;?>
	<?php endif;?>
<?php else:?>
    <?php $circa_price = $titleObject->getCurrentLocateCircaPrice();?>
    <?php if($product && in_array(strtoupper($titleObject->availability), array('NP', 'NPE', 'NPX', 'NPZ', 'NYP'))):?>
        <?php if(in_array(strtoupper($titleObject->availability), array('NP', 'NPX'))):?>
            <?php if(strcmp($_SESSION['DEFAULT_LOCALE'], 'en_NZ') == 0):	//New Zealand ?>
                <?php $product = $titleObject->getNzProduct();?>
                <?php if($product && $product->prPrice > 0.001):?>
                    <div class="sale-price">
                        <?php echo $product->getProductPrice();?>
                    </div>
                <?php elseif($product && $circa_price > 0.001):?>
                     <div class="circa-price">
                         Circa Price:
                         <span class="circa-price-value">$<?php echo $circa_price;?></span>
                     </div>
                <?php endif;?>
            <?php else: //Australia ?>
                <?php $product = $titleObject->getAuProduct();?>
                <?php if($product && $product->prPrice > 0.001):?>
					<div class="price-tag">
						<div class="sale-price">
							Price: $<?php echo $product->getProductPrice();?>
						</div>
					</div>
                <?php elseif($product && $circa_price > 0.001):?>
                    <div class="circa-price">
                        Circa Price:
                        <span class="circa-price-value">$<?php echo $circa_price;?></span>
                    </div>
                <?php endif;?>
            <?php endif;?>
        <?php endif;?>
        <div class="price-action forthcoming">Forthcoming</div>
    <?php elseif(in_array(strtoupper($titleObject->availability), array("IPX", "IPZ"))):?>
        <div class="price-action contactcs">Out&nbsp;of&nbsp;print</div>
    <?php elseif(in_array(strtoupper($titleObject->availability), array('POS', 'UCC'))):?>
	    <div class="price-action contactcs">Temporarily&nbsp;unavailable</div>
    <?php else:?>
	    <div class="price-action unavailable">Contact&nbsp;Customer&nbsp;Service</div>
    <?php endif;?>
<?php endif;?>
<div style="clear:both;width:1px;height:0px;"></div>
<?php endif;?>
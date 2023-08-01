<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

// Check if we have a valid series id and page id
$seriesID = !empty($_GET['seriesID']) ? intval($_GET['seriesID']) : null;
// Default the cID to 1, if none passed in.
$cID = !empty($_GET['cID']) ? intval($_GET['cID']) : 1;

if (empty($cID) || empty($seriesID))
{
    die();
}

Loader::model('series/model', 'cup_content');
Loader::helper('tools', 'cup_content');
$ch = Loader::helper('cup_content_html', 'cup_content');

$series = new CupContentSeries($seriesID);
$titles = $series->getTitleObjects();
$size = count($titles);

?>


<?php
    foreach($titles as $idx => $each_title):
        $className = "title-item";
        if($idx + 1 == $size){
            $className = "title-item end";
        }
?>
        <div class="<?php echo $className;?>">
            <div class="spacer-padding"></div>
                <div class="image-frame">
                    <div class="relative_frame">
                        <img src="<?php echo $each_title->getImageURL(90);?>"/>
                        <?php if($each_title->new_product_flag):?>
                        <div class="cup-new-product-90"></div>
                        <?php endif;?>
                    </div>
                </div>
                
                <div class="info-frame">
                    <div class="title-info"><a href="<?php echo $each_title->getUrl();?>"><?php echo $each_title->name;?></a></div>
                    <div class="author-info">
                        <?php $ch->printAuthors($each_title, ' / ');?>
                    </div>
                    <div class="description-info">
                        <?php echo $each_title->shortDescription;?>
                    </div>
                </div>
                
                <div class="action-info">
                    <div class="price-info">
                        <?php Loader::packageElement(
                            'page_component/title_price',
                            'cup_content',
                                array(
                                    'titleObject' => $each_title,
                                    'cID' => $cID
                                )
                            );
                            ?>
                    </div>
                    <div class="isbn-info">
                        ISBN <?php echo $each_title->isbn13;?>
                    </div>
                    <div class="format-info">
                        INCLUDED COMPONENTS
                        <div class="formats_frame">
                        <?php $ch->renderFormats($each_title->formats);?>
                        </div>
                    </div>
                </div>
                <div style="clear:both;width:1px;height:0px;"></div>
                
            <div class="spacer-padding"></div>
        </div>
<?php endforeach;?>

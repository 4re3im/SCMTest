<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$uh = Loader::helper('concrete/urls');
$slider_config_url = $uh->getToolsURL('slider/hsc_config', 'cup_competition');

$category = 'hsc';

if ($eventObj && strcmp($eventObj->category, 'HSC') == 0) {
    $slider_config_url = $uh->getToolsURL('slider/hsc_config', 'cup_competition');
} elseif ($eventObj && strcmp($eventObj->category, 'VCE') == 0) {
    $slider_config_url = $uh->getToolsURL('slider/vce_config', 'cup_competition');
    $category = 'vce';
}

strtolower($eventObj->category);
?>

<div class="cup-competition-slider">
    <div class="slider_frame">
        <div class="image_holder">
        </div>

        <div class="image_frame">
            <div class="transition_frame">
                <div class="position_one"></div>
                <div class="position_two"></div>
            </div>
        </div>

        <div class="loading_indicator"></div>

        <div class="description_frame">
            <div class="indication_frame">
                <div style="clear:both; width:0px; height:0px;"></div>
            </div>
            <div class="info_frame">
                <div class="content_area">

                </div>
            </div>
            <div style="clear:both; width:0px; height:0px;"></div>
        </div>
    </div>

    <div class="menu-btn-frame">
        <div class="btns">
            <?php
            $links = array(
                'HSC' => $this->url('/education/competition/hsc'),
                'VCE' => $this->url('/education/competition/vce'),
            );
            ?>
            <?php if ($eventObj && strcmp($eventObj->category, 'HSC') == 0): ?> 
                <span class="active">HSC</span>
            <?php else: ?>
                <a href="<?php echo $links['HSC']; ?>">HSC</a>
            <?php endif; ?>	
            /
            <?php if ($eventObj && strcmp($eventObj->category, 'VCE') == 0): ?> 
                <span class="active">VCE</span>
            <?php else: ?>
                <a href="<?php echo $links['VCE']; ?>">VCE</a>
<?php endif; ?>	
        </div>
    </div>
</div>

<script>
    cup_slider_start('<?php echo $slider_config_url; ?>');
</script>

<div class="cup-competition-content-menu">
    <div class="gap"></div>
    <a href="<?php echo $this->url('/education/competition/' . $category); ?>"><div class="item home active">COMPETITION</div></a>
    <a href="<?php echo $this->url('/education/competition/entry_form/' . $category); ?>"><div class="item">ENTRY FORM</div></a>
    <a href="<?php echo $this->url('/education/competition/terms_and_conditions/' . $category); ?>"><div class="item">TERMS & CONDITIONS</div></a>
    <?php if ($eventObj && strcmp($eventObj->type, 'Photo') == 0): ?>
        <a href="<?php echo $this->url('/education/competition/gallery/' . $category); ?>"><div class="item">PHOTO GALLERY</div></a>
<?php endif; ?>
    <div style="clear:both;height:0px;height:0px;"></div>
</div>
<div style="clear:both;height:0px;height:0px;"></div>


<?php if ($eventObj): ?>
    <div><?php echo $eventObj->homepage_content; ?></div>
<?php endif; ?>

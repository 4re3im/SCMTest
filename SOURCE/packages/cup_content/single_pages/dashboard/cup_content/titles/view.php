<?php

defined('C5_EXECUTE') || die(_("Access Denied."));

$html = Loader::helper('html');
const PACKAGE_NAME = 'cup_content';
$this->addHeaderItem($html->css("jquery.ui.css"));
$this->addHeaderItem($html->javascript("jquery.ui.js"));
$this->addHeaderItem($html->css('wform.css', PACKAGE_NAME));
$this->addHeaderItem($html->javascript('colorbox/jquery.colorbox-min.js', PACKAGE_NAME));
$this->addHeaderItem($html->css('../js/colorbox/colorbox.css', PACKAGE_NAME));
$this->addHeaderItem($html->javascript('jquery.wspecial.js', PACKAGE_NAME));

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('View Title'), false);
Loader::packageElement('alert_message_header', PACKAGE_NAME);

const GREEN_YES = '<span style="color:GREEN;">YES</span>';
const RED_NO = '<span style="color:RED;">NO</span>';

?>

<div>
    <a href="<?php echo $this->url('/dashboard/cup_content/titles/search/');
        echo '?keywords=' . $_REQUEST['keywords'].'&isbn='. $_REQUEST['isbn'];?>"
       class="btn primary" style="float:right; margin-left: 10px;">
            <?php echo t('Back To Titles')?>
    </a>

    <a href="<?php echo $this->url('/dashboard/cup_content/titles/edit', $title->id) ?>"
       class="btn">
        <?php echo t('Edit Title')?>
    </a>

    <a href="<?php echo $this->url('/dashboard/cup_content/titles/sample_page_list', $title->id) ?>"
       class="btn">
        <?php echo t('Edit Sample Pages')?>
    </a>

    <!-- ANZGO-3109 Modified by Shane Camus 02/23/2018 -->
    <a href="<?php echo $this->url('/go_product_editor/' . $title->id) ?>" class="btn" style="margin-left: 10px;">
        <?php echo t('Edit Go Contents') ?>
    </a>
</div>

<div class="ccm-pane-body">
    <table>
        <tr>
            <td style="padding:3px 10px;">Image</td>
            <td>
                <img src="<?php echo BASE_URL . $title->getImageURL(180); ?>"/>
                <?php if ($title->hasImage()): ?>
                    <a href="<?php echo $this->url('/dashboard/cup_content/titles/deleteImage', $title->id) ?>">
                        Delete Image
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Is Enabled:</td>
            <td>
                <?php echo $title->isEnabled ? GREEN_YES : RED_NO; ?>
            </td>
        </tr>
        <!-- ANZGO-1708 -->
        <tr>
            <td style="padding:3px 10px;">Is Go Product:</td>
            <td>
                <?php echo $title->isGoProduct ? GREEN_YES : RED_NO; ?>
            </td>
        </tr>
        <!-- // ANZUAT-90 -->
        <tr>
            <td style="padding:3px 10px;">Show Buy Button:</td>
            <td>
                <?php echo $title->showBuyNow ? GREEN_YES : RED_NO; ?>
            </td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">ISBN:</td>
            <td><?php echo $title->isbn13; ?></td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Title:</td>
            <td><?php echo $title->displayName; ?></td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Subtitle:</td>
            <td><?php echo $title->displaySubtitle; ?></td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Edition:</td>
            <td><?php echo $title->edition; ?></td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Publish Date:</td>
            <td><?php echo date('d/m/Y', $title->publishDate); ?></td>
        </tr><tr>
            <td style="padding:3px 10px;">Divisions:</td>
            <td>
                <?php echo ($title->divisions) ? implode(", ", $title->divisions) :  ''; ?>
            </td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Formats:</td>
            <td>
                <?php echo ($title->formats) ? implode(", ", $title->formats) :  ''; ?>
            </td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Year Levels:</td>
            <td>
                <?php echo ($title->yearLevels) ? implode(", ", $title->yearLevels) : ''; ?>
            </td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Regions:</td>
            <td>
                <?php echo ($title->regions) ? implode(", ", $title->regions) : ''; ?>
            </td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Feature:</td>
            <td><?php echo $title->feature; ?></td>
        </tr>
        <tr>
            <td style="padding:3px 10px;">Tagline:</td>
            <td><?php echo $title->tagline; ?></td>
        </tr>
    </table>
</div>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false) ?>

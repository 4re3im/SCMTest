<?php

/**
 * Header for Education Theme
 */

?>
<div id="global-nav">
    <ul id="nav">
        <li class="logo">
            <a href="http://www.cambridge.org">
                <!--ANZGO-3508 Modified by John Renzo S. Sunico, 01/29/2018 -->
                <img alt="Cambridge University Press logo"
                     src="<?php echo $this->getThemePath() . '/images/cup/logo.jpg' ?>">
            </a>
        </li>
        <li><a href="http://www.cambridge.org/aus/browse/academic.asp?site_locale=en_AU&amp;prefCountry=AU">Academic</a>
        </li>
        <li><a href="http://journals.cambridge.org/?site_locale=en_AU&amp;prefCountry=AU">Journals</a></li>
        <li><a href="http://www.cambridge.org/au/elt/?site_locale=en_AU&amp;prefCountry=AU">Cambridge English</a></li>
        <li><a href="http://www.cambridge.edu.au/education/?site_locale=en_AU&amp;prefCountry=AU">Education</a></li>
        <li><a href="http://www.cambridge.org/bibles/?site_locale=en_AU&amp;prefCountry=AU">Bibles</a></li>

        <li><a href="http://www.cambridge.org/digital-products/">Digital Products</a></li>
        <li class="drop"><a href="http://www.cambridge.org/about-us/">About Us&nbsp;&nbsp;</a>
            <ul>
                <li><a href="http://www.cambridge.org/about-us/who-we-are/press-syndicate/">Governance</a></li>
                <li><a href="http://www.cambridge.org/about-us/what-we-do/cambridge-conference-facilities">Conference
                        Venues</a></li>
                <li><a href="http://www.cambridge.org/about-us/rights-permissions/">Rights &amp; Permissions</a></li>
                <li><a href="http://www.cambridge.org/about-us/contact-us/">Contact Us</a></li>
            </ul>
        </li>
        <li><a href="http://www.cambridge.org/about-us/careers/">Careers</a></li>
    </ul>
</div>
<div class="frame_header">
    <?php $this->inc('elements/header_heading_content.php'); ?>
</div>
<div style="clear:both; height:0px;"></div>
<div class="frame_body">
    <?php $backgroundAttribute = $c->getAttribute('background');
    $contentBackgroundStyle = "";
    if ($backgroundAttribute) {
        $backgroundFile = $backgroundAttribute->getRelativePath();
        $contentBackgroundStyle = "background:url('{$backgroundFile}')";
    }
    ?>
    <div class="frame_content" style="<?php echo $contentBackgroundStyle; ?>">
        <div class="page_heading_background">
            <div class="cup-menu-frame">
                <div class="yellow_bar"></div>
                <div class="master-frame">
                    <?php // SB-246 added by mabrigos 20190905 ?>
                    <a href="<?php echo $this->url('education/'); ?>">
                        <div class="btn_home"></div>
                    </a>
                    <a href="<?php echo $this->url('education/cart'); ?>">
                        <div class="btn_shopping_cart">Shopping Cart</div>
                    </a>
                    <!-- <div class="btn_my_account">MY ACCOUNT</div> -->
                    <a href="<?php echo $this->url('education/about/how-order'); ?>">
                        <div class="btn_how_to_order">HOW TO ORDER</div>
                    </a>
                    <a href="<?php echo $this->url('education/booksellers'); ?>">
                        <div class="btn_find_a_book_seller">FIND A BOOKSELLER</div>
                    </a>
                    <a href="<?php echo $this->url('education/about/contact-us'); ?>">
                        <div class="btn_contact_us">CONTACT US</div>
                    </a>
                    <div class="clr empty"></div>
                </div>
            </div>
            <div class="clr empty"></div>
            <!--ANZGO-3508 Modified by Shane Camus, 01/31/2018 -->
            <a href="/education">
                <div class="cup-banner"></div>
            </a>
        </div>




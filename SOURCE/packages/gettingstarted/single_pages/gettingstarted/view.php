<?php
/**
 * ANZGO-3553 Added by Jeszy Tanada 10/23/2017
 * Getting Started page view
 */
defined('C5_EXECUTE') || die(_("Access Denied"));
$v = View::getInstance();
?>
    <div class="page-wrap">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/gettingstarted">
                        <img alt="Cambridge" src="/packages/go_theme/elements/svgs/activate_logo.svg">
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="main-nav">
                    <ul class="nav navbar-nav navbar-right">
                        <!-- ANZGO-3682 Added by Maryjes Tanada, 04/18/2018 added /activate link -->
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="/activate/">
                                Activate Code
                            </a>
                        </li>
                        <li><a class="hvr-underline-from-center hvr-bounce-to-top" href="/codecheck/">
                                Code Check
                            </a>
                        </li>
                        <li>
                            <!--ANZGO-3881 modified by mtanada 20181017-->
                            <a class="hvr-underline-from-center hvr-bounce-to-top"
                               href="https://cambridgehelp.zendesk.com" target="_blank">
                                Support
                            </a>
                        </li>
                        <li>
                            <!--ANZGO-3919 modified by mtanada 20181120-->
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="/go/contact/">
                                Contact Us
                            </a>
                        </li>
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top" href="../go">
                                Cambridge GO
                            </a>
                        </li>
                        <li>
                            <a class="hvr-underline-from-center hvr-bounce-to-top"
                               href="https://cambridge.edu.au/education/">
                                Store
                            </a>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>

        <div id="main" class="container-fluid">
            <div class="container holds-the-iframe">
                <?php $aw = new Area('Welcome'); $aw->display($c); ?>
                <?php $as = new Area('Getting Started Video'); $as->display($c); ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php // SB-527 modified by jbernardez 20200320 ?>
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- page-wrap -->

    <footer class="footer">
        <div class="footer-container">
            <div class="container-fluid footer-logo">
                <?php // SB-436 modified by jbernardez 20200121 ?>
                &nbsp;
            </div>

            <div class="container-fluid footer-links">
                <ul>
                    <?php // SB-436 modified by jbernardez 20200121 ?>
                    <li><a href="#"><?php echo date('Y'); ?> Cambridge University Press</a></li>
                    <li><a href="#">ABN 28 508 204 178</a></li>
                    <li><a href="#">Privacy Statement</a></li>
                    <li><a href="#">Terms of Use</a></li>
                </ul>
            </div>
        </div>
    </footer>

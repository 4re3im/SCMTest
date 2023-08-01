</div>
</div>
<!-- page-wrap -->
<footer class="footer">
    <div class="footer-container">
        <div class="container-fluid footer-logo">
            <ul>
                <li>
                    <a href="#"><img src="" alt=""> </a>
                </li>
                <li>
                    <a href="#"><img src="" alt=""></a>
                </li>
                <li>
                    <a href="#"><img src="" alt=""></a>
                </li>
                <li>
                    <a href="#"><img src="" alt=""></a>
                </li>
                <li>
                    <a href="#"><img src="" alt=""></a>
                </li>
            </ul>
        </div>
        <div class="container-fluid footer-links">
            <ul>
                <li><a href="#"><?php echo date("Y"); ?> Cambridge University
                        Press</a></li>
                <li><a href="#">ABN 28 508 204 178</a></li>
                <?php /* ANZGO-3253 modified by jbernardez 20170920 */ ?>
                <li><a href="https://www.cambridge.edu.au/go/privacy/">Privacy
                        Statement</a></li>
                <li><a href="https://www.cambridge.edu.au/go/terms">Terms of
                        Use</a></li>
            </ul>
        </div>
    </div>
</footer>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="<?php echo $this->getThemePath(); ?>/js/bootstrap.min.js"></script>
<script src="<?php echo $this->getThemePath(); ?>/js/custom.js?v=11"></script> <!-- ANZGO-3527 Modified by Jeszy, ANZGO-3556 modified by jbernardez  -->
<?php Loader::element('footer_required'); ?>
</body>
</html>

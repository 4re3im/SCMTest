<?php
// Tagged for deletion as this is not being used, modified for uniformity
global $u;
if ($u->isRegistered()) {
    $myresources_url = "http://tng.cambridge.edu.au/go/myresources";
} else {
    $myresources_url = "http://tng.cambridge.edu.au/go/login";
}
?>
<div class="container-fluid container-bg-1">
    <div class="row">
<br />
        <div class="col-lg-6 col-lg-offset-3 text-center">
            <h1 class="front-head abril-heavy">GO - your online gateway</h1>
            <p>Digital resources and support material for schools</p>
            <br />
            <p>How to access your resources edited</p>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
                    <div class="panel-group go-accordion" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel panel-default go-panel" style="border-top: 1px solid black;">
                            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                                 aria-labelledby="headingOne">
                                <div class="panel-body">
                                    <div class="row text-center">
                                        <div class="col-lg-12">
                                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <h1 class="abril">
                                                <svg class="svg-lg">
                                                <use xlink:href="#icon-login"></use>
                                                </svg>
                                                <span class="svg-text front-head">Login</span>
                                            </h1>
                                            <p>Log in to your existing Cambridge GO user account or Create a new user account.</p>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <a href="http://tng.cambridge.edu.au/go/login/" class="btn btn-success btn-block go-btn front-ajax-btn" id="front-login">LOGIN</a>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <a href="http://tng.cambridge.edu.au/go/signup/teacher" class="btn btn-info btn-block btn-black-text" id="front-signup-teacher">Create teacher account</a>
                                                </div>
                                            </div>
                                            <br />
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <a href="http://tng.cambridge.edu.au/go/signup/student" class="btn btn-info btn-block btn-black-text" id="front-signup-student">Create student account</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <div class="row text-center">
                                        <div class="col-lg-12">
                                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <h1 class="abril">
                                                <svg class="svg-lg">
                                                <use xlink:href="#icon-activate"></use>
                                                </svg>
                                                <span class="svg-text front-head">Activate</span>
                                            </h1>
                                            <p>Activate Cambridge GO resources by entering the unique 16-character access code found in the front of your textbook.</p>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <a href="http://tng.cambridge.edu.au/go/activate/" class="btn btn-success btn-block go-btn front-ajax-btn" id="front-activate1">ACTIVATE</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div id="collapseTwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo">
                                <div class="panel-body">
                                    <div class="row text-center">
                                        <div class="col-lg-12">
                                            <span class="glyphicon glyphicon-triangle-bottom"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                                            <h1 class="abril">
                                                <svg class="svg-lg">
                                                <use xlink:href="#icon-my_resources"></use>
                                                </svg>
                                                <span class="svg-text front-head">GO!</span>
                                            </h1>
                                            <p>Activate Cambridge GO resources by entering the unique 16-character access code found in the front of your textbook.</p>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <a href="<?php echo $myresources_url; ?>" class="btn btn-success btn-block go-btn" id="front-resources">MY RESOURCES</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- .panel-group -->
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-lg-6 col-lg-offset-3 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
            <br />
            <br />
            <p>Still having trouble? Visit our <a href="https://cambridgehelp.zendesk.com" id="front-support">
                    support page</a> or <a href="http://tng.cambridge.edu.au/go/contact" class="front-ajax-btn"
                    id="front-contact">contact us</a>.</p>
        </div>
    </div>
</div>
<?php
defined('C5_EXECUTE') or die(_("Access Denied"));
Loader::library('authentication/open_id');
$form = Loader::helper('form');

if(isset($error)) {
    $log_error = $error->getList();
}
?>
<div class="container">
    <div class="row text-center">
        <br />
        <div class="col-lg-2 col-lg-offset-5 col-md-2 col-md-offset-5 col-sm-2 col-sm-offset-5">
            <svg class="svg-lg">
            <use xlink:href="#icon-login"></use>
            </svg>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-lg-2 col-lg-offset-5 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
            <h1>Login</h1>
        </div>
    </div>
    <form method="post" action="<?php echo $this->url("/go/login","do_login"); ?>" class="go-form">
        <?php if(isset($error)) { ?>
        <div class="row text-center">
            <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
                <div class="alert alert-danger">
                    <?php
                    foreach ($log_error as $e) {
                        echo $e;
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row text-center">
            <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
                <input type="text" name="uName" id="uName" value="<?php echo (isset($uName)) ? $uName : ''; ?>" class="form-control go-input" placeholder="Username" />
            </div>
        </div>
        <br />
        <div class="row text-center">
            <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
                <input type="password" name="uPassword" class="form-control go-input" placeholder="Password" />
            </div>
        </div>
        <br />
        
        
        <div class="row text-center">
            <div class="col-lg-2 col-lg-offset-5 col-md-2 col-md-offset-5 col-sm-2 col-sm-offset-5 col-xs-4 col-xs-offset-4">
                <input type="submit" class="btn btn-success btn-lg btn-block go-btn" value="LOGIN" />
            </div>
        </div>
    </form>
    <br />
    <div class="row text-center">
        <div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-8 col-xs-offset-2">
            <p>Don't have an account yet?</p>
            <a href="#" class="btn btn-info btn-block">Create student account</a>
            <br />
            <a href="#" class="btn btn-info btn-block">Create teacher account</a>
        </div>
    </div>
    <br />
</div>
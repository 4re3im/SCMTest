<?php
if(isset($error)) {
	$log_error = $error->getList();
}
?>
<div class="container" id="sso-login-container">
	<div class="row">
		<div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Login</h3>
				</div>
				<div class="panel-body">
					<?php if(isset($error)) { ?>
					<div class="row">
						<div class="col-lg-12">
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
					<form action="<?php echo $this->url('sso/login/do_login'); ?>" method="POST">
						<div class="form-group">
							<label for="user-email">Email address</label>
							<input type="email" class="form-control" id="user-email" name="uName" placeholder="Email">
						</div>
						<div class="form-group">
							<label for="user-pass">Password</label>
							<input type="password" class="form-control" id="user-pass" name="uPassword" placeholder="Password">
						</div>
						<input type="hidden" name="uMaintainLogin" value="1" />
						<input type="hidden" name="redirectURL" value="<?php echo $url ?>" />
						<button type="submit" class="btn btn-default pull-right">Submit</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

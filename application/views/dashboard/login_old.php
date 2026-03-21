<!DOCTYPE html>
<html dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo base_url().'assets/images/MYVEGIZ LOGO 1st -01-cut.png';?>">
    <title>Admin - My Vegiz</title>
    <!-- Custom CSS -->
    <link href="<?php echo base_url().'assets/admin/dist/css/style.min.css';?>" rel="stylesheet">
	
	<style>
		.btn-myve{
			background-image: linear-gradient(to right,#588002d1 , #9EC746);
			border-color: #9EC746;
			color: whitesmoke;
		}
	</style>
</head>

	<body>
		<div class="main-wrapper">
			<!-- ============================================================== -->
			<!-- Preloader - style you can find in spinners.css -->
			<!-- ============================================================== -->
			<div class="preloader">
				<div class="lds-ripple">
					<div class="lds-pos"></div>
					<div class="lds-pos"></div>
				</div>
			</div>
			<!-- ============================================================== -->
			<!-- Preloader - style you can find in spinners.css -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Login box.scss -->
			<!-- ============================================================== -->
			<div class="auth-wrapper d-flex no-block justify-content-center align-items-center" style="background: linear-gradient(rgba(0,0,0, 0.7), rgba(0, 0, 0, .8)),url(<?php echo base_url().'assets/admin/assets/images/big/myvegiz_login-min.jpg';?>) ;background-position: center; background-repeat: no-repeat;background-size: cover;;">
				<div class="auth-box">
					<div id="loginform">
						<div class="logo">
							<span class="db"><img src="<?php echo base_url().'assets/images/MYVEGIZ LOGO 3rd-01-cut.png';?>" alt="logo" style="width:250px"/></span>
							<h5 class="font-medium m-b-20">Sign In to Admin</h5>
						</div>
						<!-- Form --> 
						<div class="row">
							<div class="col-12">
								<form class="form-horizontal m-t-20" id="loginform"  action="<?php echo base_url() . 'index.php/authentication/user_login_process' ?>" name="s" method="post">
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
										</div>
										<input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
									</div>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
										</div>
										<input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
									</div>
									<div class="form-group row">
										<div class="col-md-12">
											<div class="custom-control custom-checkbox">
												<input type="checkbox" class="custom-control-input" id="customCheck1">
												<label class="custom-control-label" for="customCheck1">Remember me</label>
												<a href="javascript:void(0)" id="to-recover" class="text-dark float-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a>
											</div>
										</div>
									</div>
									<div class="form-group text-center">
										<div class="col-xs-12 p-b-20">
											<button class="btn btn-block btn-lg btn-myve" type="submit" name="s">Log In</button>
										</div>
									</div>
									<div class="form-group text-center">
										<div class="col-xs-12">
										<?php
											if (isset($logout_message)) {
												echo "<div class='text-success'>";
												echo $logout_message;
												echo "</div>";
											}
										 ?>
										<?php
											if (isset($error_message)) {
											echo "<div class='text-danger'>";
											echo $error_message; 
												echo "</div>";
											}
										?>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div id="recoverform">
						<div class="logo">
							<span class="db"><img src="<?php echo base_url().'assets/admin/assets/images/logo-icon.png';?>" alt="logo" /></span>
							<h5 class="font-medium m-b-20">Recover Password</h5>
							<span>Enter your Email and instructions will be sent to you!</span>
						</div>
						<div class="row m-t-20">
							<!-- Form -->
							<form class="col-12" action="http://themedesigner.in/demo/wrappixel/admin-template/xtreme/html/ltr/index.html">
								<!-- email -->
								<div class="form-group row">
									<div class="col-12">
										<input class="form-control form-control-lg" type="email" required="" placeholder="Username">
									</div>
								</div>
								<!-- pwd -->
								<div class="row m-t-20">
									<div class="col-12">
										<button class="btn btn-block btn-lg btn-danger" type="submit" name="action">Reset</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- ============================================================== -->
			<!-- Login box.scss -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Page wrapper scss in scafholding.scss -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Page wrapper scss in scafholding.scss -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Right Sidebar -->
			<!-- ============================================================== -->
			<!-- ============================================================== -->
			<!-- Right Sidebar -->
			<!-- ============================================================== -->
		</div>
		<!-- ============================================================== -->
		<!-- All Required js -->
		<!-- ============================================================== -->
		<script src="<?php echo base_url().'assets/admin/assets/libs/jquery/dist/jquery.min.js';?>"></script>
		<!-- Bootstrap tether Core JavaScript -->
		<script src="<?php echo base_url().'assets/admin/assets/libs/popper.js/dist/umd/popper.min.js';?>"></script>
		<script src="<?php echo base_url().'assets/admin/assets/libs/bootstrap/dist/js/bootstrap.min.js'?>"></script>
		<!-- ============================================================== -->
		<!-- This page plugin js -->
		<!-- ============================================================== -->
		<script>
		$('[data-toggle="tooltip"]').tooltip();
		$(".preloader").fadeOut();
		// ============================================================== 
		// Login and Recover Password 
		// ============================================================== 
		$('#to-recover').on("click", function() {
			$("#loginform").slideUp();
			$("#recoverform").fadeIn();
		});
		//For Store Credentials
			$(function () {
			  $('#username').change(function () {
				$('#customCheck1').removeAttr('checked');
			  });
			  $('#password').change(function () {
				$('#customCheck1').removeAttr('checked');
			  });
			  
			  if (localStorage.chkbox && localStorage.chkbox != '') {
				  $('#customCheck1').attr('checked', 'checked');
				  $('#username').val(localStorage.username);
				  $('#password').val(localStorage.pass);
			  } else {
				  $('#customCheck1').removeAttr('checked');
				  $('#username').val('');
				  $('#password').val('');
			  }

			  $('#customCheck1').click(function () {
				  if ($('#customCheck1').is(':checked')) {
					  // save username and password
					  localStorage.username = $('#username').val();
					  localStorage.pass = $('#password').val();
					  localStorage.chkbox = $('#customCheck1').val();
				  } else {
					  localStorage.username = '';
					  localStorage.pass = '';
					  localStorage.chkbox = '';
				  }
			  });
			});
		</script>
		
	</body>
</html>
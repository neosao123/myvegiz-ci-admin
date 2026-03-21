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
    <title>Reset Password - My Vegiz</title>
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
							<h5 class="font-medium m-b-20">Reset your password here...</h5>
						</div>
						<!-- Form --> 
						<div class="row">
						    <div class="col-12 mb-3 text-center" id="error">
						    </div>
							<div class="col-12">
								<form class="form-horizontal m-t-20" id="resetForm"  action="" name="s" method="post">
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon1"><i class="ti-pencil"></i></span>
										</div>
										<input type="hidden" class="form-control form-control-lg" id="code" name="code" value="<?=$code?>">
										<input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="New Password" aria-label="New Password" aria-describedby="basic-addon1" required>
									</div>
									<div class="input-group mb-3">
										<div class="input-group-prepend">
											<span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
										</div>
										<input type="password" class="form-control form-control-lg" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" aria-label="Password" aria-describedby="basic-addon1" required>
									</div> 
									<div class="form-group text-center">
										<div class="col-xs-12 p-b-20">
											<button class="btn btn-block btn-lg btn-myve" type="button" name="s" >Submit</button>
										</div>
									</div> 
								</form>
							</div>
						</div>
					</div> 
				</div>
			</div> 
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
    		var base = "<?=base_url()?>";
    		 
    		//For Store Credentials 
    		$("body").on("click",".btn-myve",function(){
    		   var password = $("#password").val().trim();
    		   var confirmPassword = $("#confirmPassword").val().trim();
    		   var code = $("#code").val().trim();
    		   if(password.length>5 && confirmPassword.length>5){
    		       $(".btn-myve").attr('disabled',true);
    		       $.ajax({
    		           url:base+'Resetpassword/resetDeliveryBoyPassword',
    		           type:'post',
    		           data:{'code':code,'password':password,'confirmPassword':confirmPassword},
    		           success:function(response){
    		               var  result = JSON.parse(response);
    		               if(result.status){
    		                    $(".btn-myve").attr('disabled',true);
    		                    $("#error").html('<span class="text-success">'+result.message+'</span>');
    		                    setTimeout(function(){window.open(base,"_self")},3000);
    		               } else {
    		                    $(".btn-myve").attr('disabled',false);
    		                    $("#password").val("");
    		                    $("#confirmPassword").val("");
    		                    $("#error").html('<span class="text-danger">'+result.message+'</span>');
    		                    return false;
    		               }
    		           }
    		       })
    		   }
    		})
		</script> 
	</body>
</html>
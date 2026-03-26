<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
	<style>
		.select2-container--classic .select2-selection--single,
		.select2-container--default .select2-selection--multiple,
		.select2-container--default .select2-selection--single,
		.select2-container--default .select2-selection--single .select2-selection__arrow,
		.select2-container--default .select2-selection--single .select2-selection__rendered {
			border-color: rgba(0, 0, 0, 0.25);
			height: auto;
		}

		.select2-container--default .select2-selection--multiple .select2-selection__choice {
			background: #588002
		}

		.select2-container--default .select2-results__option--highlighted[aria-selected],
		.select2-container--default .select2-results__option[aria-selected=true] {
			background: #96bf3fbd
		}

		.select2-container--default .select2-selection--multiple .select2-selection__choice>span {
			color: white !important;
			forn-weight: bold
		}
	</style>
	<div class="page-wrapper">

		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">User Master</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Create User</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div> 
		<div class="container-fluid col-md-6"> 
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Create User</h4>
					<hr />
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url() . 'Usermaster/save'; ?>" enctype="multipart/form-data" novalidate>

						<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message)) {
							echo $error_message;
						}
						echo "</div>";
						?>

						<div class="form-row">
							<div class="col-md-12 mb-3">
								<label for="empCode ">Name : <b style="color:red">*</b></label>
								<input type="text" class="form-control" name="name" id="name" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>

						<?php
						echo "<div class='text-danger text-center' id='errormessage'>";
						if (isset($errormessage)) {
							echo $errormessage;
						}
						//echo validation_errors();
						echo "</div>";
						?>

						<div class="form-row">
							<div class="col-md-12 mb-3">

								<label for="userName">User Name : <b style="color:red">*</b></label>

								<input type="text" id="userName" name="userName" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>

							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12 mb-3">

								<label for="userEmail"> Email :</label>

								<input type="email" id="userEmail" name="userEmail" class="form-control" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$">

								<div class="invalid-feedback">
									Invalid Email ID!
								</div>
								<div id="errorEmail" style="color:#e66060;"></div>
                               
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-6 mb-3">

								<label for="password">User Password : <b style="color:red">*</b></label>

								<input type="password" id="password" name="password" class="form-control" required>

								<div class="invalid-feedback">
									Required Field!
								</div>

							</div>
							<div class="col-md-6 mb-3">
								<label for="confirmPassword">Confirm Password : <b style="color:red">*</b></label>
								<input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
								<div class="invalid" id="conPassword" style="color:red"> </div>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-12 mb-3">

								<label for="mobilenumber"> Mobile Number : <b style="color:red">*</b></label>

								<input type="text" id="mobilenumber" name="mobilenumber" class="form-control" pattern="[789][0-9]{9}" required>

								<div class="invalid-feedback">
									Required Field!
								</div>
								<div id="errorMobi" style="color:#e66060;"></div>

							</div>
						</div>

						<div class="form-row">
							<div class="col-md-12 mb-3">
								
								<label for="userRole">User Role : <b style="color:red">*</b></label>

								<select id="userRole" name="userRole" class="form-control" required>
									<option value="" selected> Select Option</option>
									 <?php foreach ($userRole->result() as $user) {
                                        ?>
                                        <option value="<?= $user->code?>"><?= $user->roleName?></option>
                                        <?php
                                      }
                                    ?>
								</select>

								<div class="invalid-feedback">
									Required Field!
								</div>

							</div>
						</div> 
						<div class="form-row" id="dlbaddr" style="display:none">
							<div class="col-md-12 mb-3">
								<label for="place">City : <b style="color:red">*</b></label>
								<div class="controls">
									<select id="cityCode" name="cityCode" class="form-control">
										<option value="">Select City</option>
										<?php
										if (isset($city)) {
											foreach ($city->result() as $c) {
												echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
											}
										}
										?>
									</select>
								</div>
								<span class="text-danger" id="showErr">
									
								</span>
							</div> 
							<div class="col-md-12 mb-3" >						
                                <label for="place">Delivery For : <b style="color:red">*</b></label>	
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="foodDelivery" name="deliveryType" value="food" class="custom-control-input" checked>
                                    <label class="custom-control-label" for="foodDelivery">Food AND Vegetable/Grocery Orders</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="slotDelivery" name="deliveryType" value="slot" class="custom-control-input">
                                    <label class="custom-control-label" for="slotDelivery" >Slot Orders</label>
                                </div> 
                            </div> 
			
							<div class="col-md-6">
								<div class="form-group">
									<input type="hidden" class="form-control" id="dayMonday" name="day" placeholder="Day" value="monday">
									<input type="text"  class="form-control pickatime" id="fromTime" name="fromTime" placeholder="From Time">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<input type="text"  class="form-control pickatime" id="toTime" name="toTime" placeholder="To Time">
								</div>
							</div> 
						</div> 
						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="profilePhoto">Profile Photo : </label>
								<input type="file" id="profilePhoto" name="profilePhoto" class="form-control" accept="image/*">
							</div>
						</div> 
						<div class="form-group">
							<div class="custom-control custom-checkbox mr-sm-2">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive">
									<label class="custom-control-label" for="isActive">Active</label>
								</div>
							</div>
						</div> 
						<div class="text-xs-right"> 
							<button type="submit" id="submit" name='s' class="btn btn-info" onclick="page_isPostBack=true;">Submit</button>
							<button type="Reset" class="btn btn-reset">Reset</button> 
						</div> 
					</form>
				</div>
			</div>
		</div>
	</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script> 
	$('.pickatime').timepicker({
		timeFormat: 'h:mm p',
		interval: 30, 
		dynamic: true,
		dropdown: true,
		scrollbar: true,
	});
</script>	
	<script>
		var previous;
		$('document').ready(function() {
			$("#userName").maxlength({
				max: 30
			});
			$("#userRole").children('option[value="ADM"]').hide(); 
			var userCount = "<?= $userCount ?>";
			if(userCount>=20){
				$("#userRole").children('option[value="DLB"]').hide(); 
			}
			$("#userRole").change(function() {
				var role = $("#userRole").val();
				if (role == 'DLB') {
					$("#dlbaddr").show(); 
					$("#showErr").text("Required field!");
					$("#cityCode").attr('required',true);
				} else {
					$("#dlbaddr").hide(); 
					$("#showErr").text("");
					$("#cityCode").removeAttr('required');
				}
			});
		}); // End Ready
		// Page Leave Yes / No
		var page_isPostBack = false;
		function windowOnBeforeUnload() {
			if (page_isPostBack == true)
				return; // Let the page unload

			if (window.event)
				window.event.returnValue = 'Are you sure?';
			else
				return 'Are you sure?';
		}
		window.onbeforeunload = windowOnBeforeUnload;
		// End Page Leave Yes / No
		setTimeout(function() {
			$('#error_message').hide('fast');
		}, 4000);
		
		$('#confirmPassword').focus(function() {
			$('#confirmPassword').change(function() { 
				var confirmPassword = $(this).val();
				var password = $('#password').val();
				if (confirmPassword != password) {
					$('#conPassword').html("Confirm Password Doesn't Matches to New Password");
					e.preventDefault();
				}
				setTimeout(function() {
					$('#conPassword').hide('fast');
				}, 1000);

			});
		}); 
		
		 $('#mobilenumber').on('change', function(e) {
				let mobile  = $(this).val();
				if (mobile != "") {
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url() . 'Usermaster/duplicateMobileNumber' ?>',
						data: {
							'mobile': mobile,
						},
						dataType: "JSON",
						success: function(response){
							if(response.status == true)
							{
								$('#submit').prop('disabled', true);
								$("#errorMobi").show();
								$("#errorMobi").text(response.message);
							}
							else
							{
								$('#submit').prop('disabled', false);
								$("#errorMobi").hide();
							}
						}
					});
				}
			});
			
			 $('#userEmail').on('change', function(e) {
	           
				let email  = $(this).val();
				if (email != "") {
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url() . 'Usermaster/duplicateEmail' ?>',
						data: {
							'email': email,
						},
						dataType: "JSON",
						success: function(response) {
							
							if(response.status == true)
							{
								$('#submit').prop('disabled', true);
								$("#errorEmail").show();
								$("#errorEmail").text(response.message);
							}
							else
							{
								$('#submit').prop('disabled', false);
								$("#errorEmail").hide();
							}
						}
					});
				}
			});
  
		// Example starter JavaScript for disabling form submissions if there are invalid fields
		(function() {
			'use strict';
			window.addEventListener('load', function() {
				// Fetch all the forms we want to apply custom Bootstrap validation styles to
				var forms = document.getElementsByClassName('needs-validation');
				// Loop over them and prevent submission
				var validation = Array.prototype.filter.call(forms, function(form) {
					form.addEventListener('submit', function(event) {
						if (form.checkValidity() === false) {
							event.preventDefault();
							event.stopPropagation();
						}
						form.classList.add('was-validated');
					}, false);
				});
			}, false);
		})();
	</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<div class="page-wrapper">			
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title">User Master</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Change In User </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Change In User</h4>
                <hr/>  
				
                <form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url() . 'Usermaster/update'; ?>" novalidate>
                    <?php
                    if (isset($error_message)) {
                        echo "<div class='text-danger text-center' id='error_message'>".$error_message."</div>";
                    } 
				    
	
                    foreach ($query->result() as $row) {
						 
                    ?>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <input type="text" id="code" readonly name="code" class="form-control" value="<?php echo $row->code ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="empCode ">Name : <b style="color:red">*</b></label>
                               <input type="text" class="form-control" name="name" id="name" required value="<?= $row->name?>">
                                <div class="invalid-feedback">
                                    Required Field!
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="uname" name="uname" class="form-control" value="<?php echo $row->username ?>" >
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="userName">User Name : <b style="color:red">*</b></label>
                                <input type="text" id="userName" name="userName" class="form-control" value="<?php echo $row->username ?>" required>
                                <span style="color:red" class="userError">User Name Already  Exist!!</span>
                                <div class="invalid-feedback">
                                    Required Field!
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="userEmail"> Email :</label>
                                <input type="email" id="userEmail" name="userEmail" class="form-control" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?php echo $row->userEmail ?>">
                                <div class="invalid-feedback">
                                    Invalid Email ID!
                                </div>
								<div id="errorEmail" style="color:#e66060;"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="oldPassword">Old Password :</label>
                                <input type="password" id="oldPassword" name="oldPassword"  class="form-control">
                                <span style="color:red" class="passError">Password Doesn't Match</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password">New Password : </label>
                                <input type="password" id="password" name="password" class="form-control">
                                <span style="color:red" id="errorid"></span>
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
                                <script type="text/javascript">
                                    var userRole = "<?php echo $row->role ?>";
                                    $("#userRole").val(userRole);
                                </script>
                                <div class="invalid-feedback">
                                    Required Field!
                                </div>
                            </div>    
                        </div>
						<div class="form-row">
							<div class="col-md-12 mb-3">

								<label for="mobilenumber"> Mobile Number : <b style="color:red">*</b></label>

								<input type="text" id="mobilenumber" name="mobilenumber" class="form-control" value="<?php echo $row->mobile; ?>" pattern="[789][0-9]{9}" required>

								<div class="invalid-feedback">
									Required Field!
								</div>
                                <div id="errorMobi" style="color:#e66060;"></div>
							</div>
						</div>
                        <div class="row" id="dlbaddr" style="display:<?= $row->role == 'DLB' ? 'block':'none'?>">
                            <div class="col-md-12 mb-3" >						
                                <label for="place">City : <b style="color:red">*</b></label>							
                                <div class="controls">
                                    <select  id="cityCode" name="cityCode" class="form-control" <?= $row->role == 'DLB' ? 'required':''?>>
                                        <option value="">Select City</option>
                                        <?php
                                        if (isset($city)) {
                                            foreach ($city->result() as $c) {
                                                $selected = $c->code == $row->cityCode ? "selected" : "";
                                                echo '<option value="' . $c->code . '" ' . $selected . '>' . $c->cityName . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
							 
                            <div class="col-md-12 mb-3" >						
                                <label for="place">Delivery For : <b style="color:red">*</b></label>	
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="foodDelivery" name="deliveryType" value="food" class="custom-control-input" <?=$row->deliveryType == "food" ? 'checked':''?>>
                                    <label class="custom-control-label" for="foodDelivery">Food AND Vegetable/Grocery Orders</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="slotDelivery" name="deliveryType" value="slot" class="custom-control-input" <?=$row->deliveryType == "slot" ? 'checked':''?>>
                                    <label class="custom-control-label" for="slotDelivery" >Slot Orders</label>
                                </div> 
                            </div> 
							<?php $startTime=$endTime='';
							if($row->availableStartTime!="" && $row->availableStartTime !=NULL){
								$startTime = date('h:i A',strtotime($row->availableStartTime));
							}
							if($row->availableEndTime!="" && $row->availableStartTime !=NULL){
								$endTime = date('h:i A',strtotime($row->availableEndTime));
								
							}
							
							?>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<input type="hidden" class="form-control" id="dayMonday" name="day" placeholder="Day" value="monday">
										<input type="text"  class="form-control pickatime" id="fromTime" name="fromTime" placeholder="From Time" value="<?= $startTime ?>">
									</div>
								</div>
								<div class="col-md-6 mb-3">
									<div class="form-group">
										<input type="text"  class="form-control pickatime" id="toTime" name="toTime" placeholder="To Time" value="<?= $endTime ?>">
									</div>
								</div>
							</div>
                        </div>  
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label for="profilePhoto">Profile Photo : </label>
                                <input type="file" id="profilePhoto" name="profilePhoto" class="form-control" accept="image/*">
                            </div>  
                        </div>
                        <div class="row el-element-overlay">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="el-card-item">
                                        <div class="el-card-avatar el-overlay-1"> 
                                            <img src="<?php
                                            if (!empty($row->profilePhoto)) {
                                                echo base_url() . 'uploads/profilePhoto/' . $row->profilePhoto;
                                            } else {
                                                echo base_url() . 'assets/admin/assets/images/users/1.jpg';
                                            }
                                            ?>" alt="Profile File"/>
                                            <div class="el-overlay">
                                                <ul class="list-style-none el-info">
                                                    <li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo $row->profilePhoto != "" ? base_url() . 'uploads/profilePhoto/' . $row->profilePhoto : base_url() . 'assets/admin/assets/images/users/1.jpg'; ?>" target="_blank"><i class="icon-magnifier"></i></a></li>
                                                    <li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('<?= $row->code; ?>', ' <?= $row->profilePhoto; ?>')"><i class="icon-trash"></i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="el-card-content">
                                            <h4 class="m-b-0">Profile Photo</h4> <!-- <span class="text-muted">Graphics Designer</span> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						<div class="" style="display: flex;">		
                        <div class="form-group">
                            <div class="custom-control custom-checkbox mr-sm-2">
                                <div class="custom-control custom-checkbox">
                                    <?php
                                    if ($row->isActive == "1") {
                                        echo "<input id='isActive' type='checkbox'value='1' class='custom-control-input'  name='isActive' checked='checked'>";
                                    } else {
                                        echo "<input id='isActive' type='checkbox'value='1' class='custom-control-input'name='isActive'>";
                                    }
                                    ?>
                                    <label class="custom-control-label" for="isActive">Active</label>
                                </div>
                            </div>
                        </div>
						<!--Added on 13-3-2021 by Ani-->
						 <?php
                         if ($row->role == "DLB") 
						 {?>
						 <div class="form-group">
                            <div class="custom-control custom-checkbox mr-sm-2">
                                <div class="custom-control custom-checkbox">
                                    <?php
                                    if ($row->loginStatus == "1") {
                                        echo "<input id='isOnline' type='checkbox' value='1' class='custom-control-input'  name='isOnline' checked='checked'>";
                                    } else {
                                        echo "<input id='isOnline' type='checkbox' value='1' class='custom-control-input'name='isOnline'>";
                                    }
                                    ?>
                                    <label class="custom-control-label" for="isOnline">Online/Offline</label>
                                </div>
                            </div>
                        </div>
						<?php }?>
						<!--Added on 13-3-2021 by Ani end-->
                        <?php
                    }
                    ?>  
					</div>
                    <div class="text-xs-right">
                        <button type="submit" name='s' id="submit" class="btn btn-success" onclick="page_isPostBack = true;">Submit</button>
                        <a href="<?php echo base_url() . 'Usermaster/listRecords'; ?>"><button type="button" class="btn btn-reset">Back</button></a>
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
	function deleteButton(code, name) {
		$.ajax({
			url: base_path + 'Usermaster/deleteUserImage',
			method: "POST",
			data: {
				'code': code,
				'name': name
			},
			datatype: "text",
			success: function(data) {
				location.reload();

			}
		});
	}

	setTimeout(function() {
		$('#errormessage').hide('fast');
	}, 6000);

	$('document').ready(function() {
		
		var uRole = $("#userRole").val();
		
		if (uRole == 'DLB') {
			$("#dlbaddr").show();
		} else {
			$("#dlbaddr").hide();
		} 
        $("#userRole").children('option[value="ADM"]').hide();
		var userCount = "<?= $userCount ?>";
		if(userCount>=20){
			$("#userRole").children('option[value="DLB"]').hide(); 
		}
		//onchange userrole
		$("#userRole").change(function() {
            var role = $("#userRole").val();
            if (role == 'DLB') {
                $("#dlbaddr").show(); 
                $("#cityCode").attr('required',true);
            } else {
                $("#dlbaddr").hide(); 
                $("#cityCode").removeAttr('required'); 
            }
        });

		$("#userName").maxlength({
			max: 30
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
	//////////////////// ////password section //////////////////////////////////////////
	$('.passError').hide();
	$(".userError").hide();
	$("#errorid").hide();
	function msgPass() {
		$('.passError').hide();
	}
	function errorId() {
		$('#errorid').hide();
	}
	$("#oldPassword").change(function() {
		var password = $(this).val();
		var code = $("#code").val();
		debugger;
		$.ajax({
			url: "<?php echo site_url('Usermaster/checkPassword'); ?>",
			method: "GET",
			data: {
				'password': password,
				"code": code
			},
			datatype: "text",
			success: function(data) {
				console.log(data);
				if (data == 'true') {
					$('#oldPassword').val("");
					$(".passError").show();
					setTimeout(msgPass, 4000); 
				}
			}
		});
	});
	$("#password").change(function() {
		var oldPassword = $("#oldPassword").val();
		var password = $(this).val();
		if (oldPassword == '') {
			$('#errorid').show();
			$("#password").val('');
			$('#errorid').html("Input Old Password First");
			setTimeout(errorId, 4000);
		}
		if (oldPassword == password) {
			$('#errorid').show();
			$('#errorid').html("Old and New Password  Should Not Be Same");
			$("#password").val('');
			setTimeout(errorId, 4000);
		}
	});
	
	
		 $('#mobilenumber').on('change', function(e) {
	           
				var mobile  = $(this).val();
				var code = $("#code").val();
				if (mobile != "") {
					$.ajax({
						type: 'get',
						url: '<?php echo base_url() . 'Usermaster/duplicateMobileForEdit' ?>',
						data: {
							'mobile': mobile,'code':code
						},
						dataType: "JSON",
						success: function(response) {
							 
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
	           
				var email  = $(this).val();
				var code = $("#code").val();
				if (email != "") {
					$.ajax({
						type: 'get',
						url: '<?php echo base_url() . 'Usermaster/duplicateEmailForEdit' ?>',
						data: {
							'email': email,'code':code
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
	

	function msg() {
		$('.userError').hide();
	}
	$("#userName").change(function() {
		var userName = $(this).val();
		var uname = $('#uname').val();

		$.ajax({
			url: "<?php echo site_url('Usermaster/checkUserName'); ?>",
			method: "GET",
			data: {
				'userName': userName
			},
			datatype: "text",
			success: function(data) {
				console.log(data);
				if (data == 'true') {
					if (userName == uname) {
						$('#userName').val(uname);
					} else {
						$('#userName').val(uname);
						$(".userError").show();
						setTimeout(msg, 4000);
					}

				}
			}
		});
	}); //change
	//////userSection////////////////////////////
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
	/* for online/offline check added by ani on 13-3-2021 */ 
	$("#isOnline").change(function() { 
		var chckStatus = $(this).val();
		var code = $("#code").val();
		var userRole = $("#userRole").val();
		var isActive = $("#isActive").val();
		
		if($("#isOnline").prop('checked') == false)
		{
		if(userRole=="DLB")
		{
			$.ajax({
				url: "<?php echo site_url('Usermaster/checkDeliveryBoyOrders'); ?>",
				method: "GET",
				data: {
					"code": code,
					"userRole":userRole
				},
				datatype: "text",
				success: function(data) {
					console.log(data);
					
					if (data == 'true') {
						swal({
        								title: "Are you sure?",
        								text: "The Delivery Boy has Orders, Please Transfer Orders.",
        								type: "warning",
        								button:"Ok",
										showCancelButton: !0,
										cancelButtonText: "Cancel!"
        							}, function(e) {
        								//$('#isOnline').attr('checked', false); 
										$('#isOnline').prop('checked',true);
										$('#isActive').prop('checked',true);
        								});
        							
        							
        						
        				
					}
					
				}
			});
		}
	    }
		else
		{
			
			if($("#isOnline").prop('checked') == true && $("#isActive").prop('checked') == false)
		    {
			                         swal({
        								title: "Are you sure?",
        								text: "First Activate Delivery Boy.",
        								type: "warning",
        								button:"Ok",
										showCancelButton: !0,
										cancelButtonText: "Cancel!"
        							}, function(e) {
        								//$('#isOnline').attr('checked', false); 
										$('#isOnline').prop('checked',false);
										//$('#isActive').prop('checked',true);
        								});
		   }
		  
		}
	});
	
	$("#isActive").change(function() { 
		var chckStatus = $(this).val();
		var code = $("#code").val();
		var userRole = $("#userRole").val();
		var isActive = $("#isActive").val();
		
		if($("#isActive").prop('checked') == false)
		{
		if(userRole=="DLB")
		{
			$.ajax({
				url: "<?php echo site_url('Usermaster/checkDeliveryBoyOrders'); ?>",
				method: "GET",
				data: {
					"code": code,
					"userRole":userRole
				},
				datatype: "text",
				success: function(data) {
					console.log(data);
					
					if (data == 'true') {
						swal({
        								title: "Are you sure?",
        								text: "The Delivery Boy has Orders, Please Transfer Orders.",
        								type: "warning",
        								button:"Ok",
										showCancelButton: !0,
										cancelButtonText: "Cancel!"
        							}, function(e) {
        								//$('#isOnline').attr('checked', false); 
										$('#isOnline').prop('checked',true);
										$('#isActive').prop('checked',true);
        								});
        							
        							
        						
        				
					}else
					{
						$('#isOnline').prop('checked',false);
						
					}
					
				}
			});
		}
	    }
	
	});
	
	/* for online/offline check added by ani on 13-3-2021 end*/
</script>

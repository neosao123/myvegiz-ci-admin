<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Update Profile</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Update Profile</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
		
	<div class="container-fluid">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Your Profile</h4>
					<h6 class="card-subtitle">Detailed Information, Password Update etc...</h6>
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item"> <a class="nav-link active show" data-toggle="tab" href="#info" role="tab" aria-selected="true"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down"> Vendor Info</span></a> </li>
						<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#pswd" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down"> Password Change</span></a> </li>
						<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#bnk" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down"> Bank Details</span></a> </li>
						<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#doc" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down"> Document Details</span></a> </li>
						<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#setting" role="tab" aria-selected="false"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down"> Packaging & GST</span></a> </li>
					</ul>
					<!-- Tab panes -->
					<div class="tab-content tabcontent-border">
						<div class="tab-pane active show" id="info" role="tabpanel">
							<div class="p-20">
								<h4 class="card-title">Profile Details</h4>
								<hr/>
								<?php foreach($query->result() as $row){  ?> 
									<div class="form-row">
										<?php if($row->entityImage!=""){ ?>
											<div class="col-md-4 mb-3 text-center">
												<label for="entityImage"> Entity Image :</label>
												<div class="controls mt-1">
													<img src="<?php echo myvegiz_base.'/uploads/vendor/'.$row->code.'/'.$row->entityImage;?>" id="entityImageShow" alt="Entity Image" height="120" width="120">
												</div>
											</div>
										<?php } ?> 
									</div>
									<div class="form-row">
										
										<div class="col-md-4 mb-3">
											<label for="firstName">First Name :</label>
											<input type="text" id="firstName" value="<?=$row->firstName?>" class="form-control-line" required readonly>
										</div>
										
										<div class="col-md-4 mb-3">
											<label for="middleName">Middle Name : </label>
											<input type="text" id="middleName"  value="<?=$row->middleName?>" class="form-control-line" readonly>
										</div>
										
										<div class="col-md-4 mb-3">
											<label for="lastName">Last Name :</label>
											<input type="text" id="lastName" value="<?=$row->lastName?>" class="form-control-line" readonly required>
										</div>
									</div>
									
									<div class="form-row">
										<div class="col-md-12 mb-3">
											<label for="entityName">Entity Name : </label>
											<input type="text" id="entityName" value="<?=$row->entityName?>" class="form-control-line" readonly  required>
										</div>
									</div>
									
									<div class="form-row">
										<div class="col-md-12 mb-3">
											<label for="address">Address : </label>
											<input type="text" id="address"  value="<?=$row->address?>" class="form-control-line" readonly>
										</div>
									</div>
									
									<div class="form-row">
										<div class="col-md-4 mb-3">
											<label for="ownerContact1">Owner Contact :</label>
											<input type="text" id="ownerContact1"  value="<?=$row->ownerContact?>" class="form-control-line" required readonly>
										</div>
										
										<div class="col-md-4 mb-3">
											<label for="entityContact">Entity Contact : </label>
											<input type="text" id="entityContact"  value="<?=$row->entityContact?>" class="form-control-line" readonly>
										</div>
										
										<div class="col-md-4 mb-3">
											<label for="entitycategoryCode">Entity Category :</label>
											<input type="text" value="<?=$entitycategory->result_array()[0]['entityCategoryName']?>" class="form-control-line" readonly required>
										</div>
										 
										<div class="col-md-4 mb-3">
											<label for="entitycategoryCode">Profile Active Status :</label>
											<?php 
												if($row->isActive == "1"){
													 
													echo "<span class='label label-sm label-success'>Active</span>";
													}else{ 
													echo "<span class='label label-sm label-warning'>Inactive</span>";
												}
											?>
										</div>										
									</div> 
									
									<div class="form-row">
										<div class="col-md-6 mb-3">
											<label for="latitude"> Latitude : </label>
											<input type="text" id="latitude" name="latitude" value="<?=$row->latitude?>" class="form-control-line" readonly>
										</div>
										
										<div class="col-md-6 mb-3">
											<label for="longitude"> Longitude :</label>
											<input type="text" id="longitude" name="longitude" value="<?=$row->longitude?>" class="form-control-line" readonly>
										</div> 
									</div>
									
									<div class="form-row">
										<div class="col-md-12 mb-3">
											<label for="cuisineCode"> Type of cuisines served :</label></br>					            
											<?php
												if($cuisineslines)
												{
													foreach($cuisineslines->result_array() as $c)
													{
														echo '<span class="btn btn-success btn-sm mr-2">'.$c['cuisineName'].'</span>';
													}
												}
											?>
										</div>
									</div>
									
								<?php }?>
							</div>
						</div>
						<div class="tab-pane p-20" id="pswd" role="tabpanel">
							<div class="p-20">
								<h4 class="card-title">Update Credentials</h4>
								<hr/>						
								<form class="needs-validation" method="post" id="passwordForm" enctype="multipart/form-data" novalidate>
									<?php
										echo "<div class='text-danger text-center' id='error_message'>";
										if (isset($error_message))
										{
											echo '<b>'.$error_message.'</b>';
										}
										echo "</div>";
										if($query) 
										{ 
											foreach($query->result() as $row)
											{  
									?>  
									<div class="form-row"> 
										<input type="hidden" id="code" name="code" value="<?=$row->code?>" class="form-control" readonly> 
										<div class="col-md-6 mb-3">
											<label for="ownerContact">Owner Contact :<b style="color:red">*</b></label>
											<input type="number" id="ownerContact" name="ownerContact" class="form-control" value="<?=$row->ownerContact?>" required>
											<div class="invalid-feedback">
												Required Field!
											</div>
										</div>
									</div>	
									<div class="form-row">
										<div class="col-md-6 mb-3">
											<label for="password">Password : <b style="color:red">*</b></label>
											<input type="password" id="password" name="password" class="form-control" value="" required>
											<div class="invalid-feedback">
												Required Field!
											</div>
										</div>
										<div class="col-md-6 mb-3">
											<label for="confirmPassword">Confirm Password: <b style="color:red">*</b></label>
											<input type="password" id="confirmPassword" name="confirmPassword" class="form-control" value="" required>
											<div class="invalid-feedback">
												Required Field! 
											</div>
										</div>
									</div>	 
									<?php 
											} 
										}
									?>
									<div class="text-xs-right">
										<button type="submit" class="btn btn-success" id="updatePassword">Submit</button> 
									</div>
								</form>
							</div>
						</div>
						<div class="tab-pane p-20" id="bnk" role="tabpanel">
							<div class="p-20">
								<h4 class="card-title">Bank Details</h4>
								<hr/>
								<?php 
									foreach($query->result() as $row){  
									if($row->bankDetails!=""){
										$bankDetails = json_decode($row->bankDetails);
								?> 
									<div class="form-row">
										<div class="col-md-6 mb-3">
											<label for="beneficiaryName"> Beneficiary Name : </label>
											<input type="text" id="beneficiaryName" name="beneficiaryName" value="<?=$bankDetails->beneficiaryName?>" class="form-control-line" readonly required>
										</div>
										
										<div class="col-md-6 mb-3">
											<label for="bankName"> Name of Bank :</label>
											<input type="text" id="bankName" name="bankName" value="<?=$bankDetails->bankName?>" class="form-control-line" readonly>
										</div>
										
										<div class="col-md-6 mb-3">
											<label for="accountNumber"> Account Number :</label>
											<input type="text" id="accountNumber" name="accountNumber" value="<?=$bankDetails->accountNumber?>" class="form-control-line" readonly>
										</div>
										
										<div class="col-md-6 mb-3">
											<label for="ifscCode"> IFSC Code :</label>
											<input type="text" id="ifscCode" name="ifscCode" value="<?=$bankDetails->ifscCode?>" class="form-control-line" readonly>
										</div>
									</div>
								<?php 
									} else {
								?>
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<span class="text-danger">No Bank Details were uploaded...</span> 
									</div>
								</div>
								<?php
									}
								}
								?>
							</div>
						</div>
						<div class="tab-pane p-20" id="doc" role="tabpanel">
							<div class="p-20">
								<h4 class="card-title">Uploaded Document Details</h4>
								<hr/>
								<?php foreach($query->result() as $row){  ?> 
									<div class="form-row">
										<div class="col-md-6 mb-3">
											<label for="fssaiNumber"> FSSAI Number : </label>
											<input type="text" id="fssaiNumber" name="fssaiNumber" value="<?=$row->fssaiNumber?>" class="form-control-line" readonly required>
										</div>
										
										<div class="col-md-6 mb-3">
											<label for="gstNumber"> GST Number :</label>
											<input type="text" id="gstNumber" name="gstNumber" value="<?=$row->gstNumber?>" class="form-control-line" readonly>
										</div>
									</div>
									<div class="form-row"> 
										<?php if($row->fssaiImage!=""){ ?>
											<div class="col-md-4 mb-3 text-center">
												<label for="fssaiImage"> FSSAI Image :</label>
												<div class="controls">
													<img src="<?php echo myvegiz_base.'/uploads/vendor/'.$row->code.'/'.$row->fssaiImage;?>" id="fssaiImageShow" alt="FSSAI Image" height="120" width="120">
												</div>
											</div>
										<?php } ?>
										<?php if($row->gstImage!=""){ ?>
											<div class="col-md-4 mb-3 text-center">
												<label for="gstImage"> GST Image :</label>
												<div class="controls">
													<img src="<?php echo myvegiz_base.'/uploads/vendor/'.$row->code.'/'.$row->gstImage;?>" id="gstImageShow" alt="GST Image" height="120" width="120">
												</div>
											</div>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
						</div>
						<div class="tab-pane p-20" id="setting" role="tabpanel">
							<div class="p-20">
								<h4 class="card-title">Configuration Settings</h4>
								<hr/>  
								<?php foreach($query->result() as $row) {  ?> 
									<div class="row">
										<div class="col-md-7 mb-3">
											<label>GST Applicable Type : </label>
											<div class="custom-control custom-radio">
												<input type="radio" id="gstApplicableNo" readonly name="gstApplicable" value="NO" <?=$row->gstApplicable=="NO"?'checked':''?> class="custom-control-input">
												<label class="custom-control-label" for="gstApplicableNo">No (Not Applicable)</label>
											</div>	
											<div class="custom-control custom-radio">
												<input type="radio" id="gstApplicableYes" readonly name="gstApplicable" value="YES" <?=$row->gstApplicable=="YES"?'checked':''?> class="custom-control-input">
												<label class="custom-control-label" for="gstApplicableYes">Yes (Applicable)</label>
											</div>											
										</div>
										<div class="col-md-5 mb-3">
											<label for="gstPercent">GST (%)</label>
											<input type="text" id="gstPercent" maxlength="3" name="gstPercent" class="form-control-line" readonly value="<?=$row->gstPercent<0 || $row->gstPercent==null  ? 0 .' %':$row->gstPercent. ' %'?>">
										</div>
									</div>
									<form class="needs-validation" method="post" id="configForm" enctype="multipart/form-data" novalidate>
										<div class="row"> 
											<input name="configVendorCode" value="<?=$row->code?>" readonly type="hidden">
											<div class="col-md-7 mb-3">
												<label>Delivery Packaging Type : <b style="color:red">*</b></label>
												<div class="custom-control custom-radio">
													<input type="radio" id="productPacking" name="packagingType" value="PRODUCT" <?=$row->packagingType=="PRODUCT"?'checked':''?> class="custom-control-input">
													<label class="custom-control-label" for="productPacking">Product Wise</label>
												</div>
												<div class="custom-control custom-radio">
													<input type="radio" id="cartPacking" name="packagingType" value="CART" <?=$row->packagingType=="CART"?'checked':''?> class="custom-control-input">
													<label class="custom-control-label" for="cartPacking">Cart Wise</label>
												</div>
											</div>
											<div class="col-md-5 mb-3" id="cartPack" style="display:<?=$row->packagingType=="CART" ? 'block': 'none' ?>">
												<label for="cartPackagingPrice">Delivery Packing Price</label>
												<input type="number" id="cartPackagingPrice" maxlength="3" name="cartPackagingPrice" class="form-control" <?=$row->packagingType=="CART"? 'required':''?> value="<?=$row->cartPackagingPrice?>">
												<div class="invalid-feedback">
													Required Field!
												</div>
											</div>											
										</div> 
										<div class="row"> 
											<div class="text-xs-right">
												<button type="submit" class="btn btn-success" id="updateConfig">Submit</button> 
											</div>
										</div>
									</form>
								<?php 
									}
								?> 
							</div>
						</div>
					</div> 
				</div>
			</div>
		</div>
	</div>	
<script>
	$("body").on("change","input[name=packagingType]",function(e){ 
		if($(this).is(":checked")){
			var thisVal = $(this).val();
			if(thisVal=="CART"){
				$("#cartPack").show();
				$("#cartPackagingPrice").attr("required",true);
			} else {
				$("#cartPack").hide();
				$("#cartPackagingPrice").removeAttr("required");
				$("#cartPackagingPrice").val(0);
			}
		} 
	});
	
	$(document).ready(function () {
		$("#passwordForm").on("submit", function(e) {
			var fd = new FormData();
			var other_data = $('form').serializeArray();
			$.each(other_data,function(key,input){
				fd.append(input.name,input.value);
			}); 
			$.ajax({
				type: "POST",
				url: base_path+"index.php/MyProfile/update",
				enctype: 'multipart/form-data',
				contentType: false,
				processData: false,
				data: fd,
				beforeSend: function() {
					$('#submit').prop('disabled', true);
				},
				success: function (data) {
					var obj=JSON.parse(data);
					var status = obj.status; 
					if(status)
					{  
						toastr.success(obj.message, 'Password', { "progressBar": true });    
						location.reload();
					}					 
					else
					{
						toastr.error(obj.message, 'Password', { "progressBar": true });
						location.reload();
					}
				}
			});
			e.preventDefault();
		});
		$("#configForm").on("submit", function(e) {
			var fd = new FormData();
			var other_data = $('form').serializeArray();
			$.each(other_data,function(key,input){
				fd.append(input.name,input.value);
			}); 
			$.ajax({
				type: "POST",
				url: base_path+"index.php/MyProfile/configUpdate",
				enctype: 'multipart/form-data',
				contentType: false,
				processData: false,
				data: fd,
				beforeSend: function() {
					$('#updateConfig').prop('disabled', true);
				},
				success: function (data) {
					var obj=JSON.parse(data);
					var status = obj.status;					 
					if(status)
					{  
						toastr.success(obj.message, 'Configuration', { "progressBar": true });    
						location.reload();
					}
					else
					{
						toastr.error(obj.message, 'Configuration', { "progressBar": true });
						location.reload();
					}
					$('#updateConfig').prop('disabled', false);
				}
			});
			e.preventDefault();
		});
	});
	 
</script>
<style>
#myMap {
   height: 300px;
   width: 680px;
}
.select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--single .select2-selection__arrow, .select2-container--default .select2-selection--single .select2-selection__rendered {
	border-color: rgba(0,0,0,0.25);
	border-radius:0px;
    height: 35px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #444;
    line-height: 35px;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
background:#588002
}
.select2-container--default .select2-results__option--highlighted[aria-selected],.select2-container--default .select2-results__option[aria-selected=true]  {background:#96bf3fbd}
.select2-container--default .select2-selection--multiple .select2-selection__choice > span { color:white!important;forn-weight:bold}

.el-element-overlay .el-card-item .el-overlay-1 {
    width: 150px;
    /* overflow: hidden; */
    position: relative;
    text-align: center;
    cursor: default;
    height: 150px;
}


.el-element-overlay .el-card-item .el-overlay {
    width: inherit;
    height: inherit;
    position: absolute;
    /* overflow: hidden; */
    top: 0;
    left: 0;
    opacity: 0;
    background-color: rgba(0,0,0,.7);
    -webkit-transition: all .4s ease-in-out;
    transition: all .4s ease-in-out;
    /* margin-top: 20px; */
}


.el-element-overlay .el-card-item .el-overlay-1 img {
    /* display: block; */
    position: relative;
    -webkit-transition: all .4s linear;
    transition: all .4s linear;
    width: 120px;
    height: 120px;
    margin-top: 16px;
}

</style>
	<div class="page-wrapper"> 
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Vendor Item</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Vendor Item</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid col-md-8">
		
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Update Vendor Item</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url().'index.php/Food/Vendoritem/update';?>" novalidate>
						<?php
							echo "<div class='text-danger text-center' id='error_message'>";
							if (isset($error_message))
							{
								echo '<b>'.$error_message.'</b>';
							}
							echo "</div>";
							if($query)
							{
								foreach($query->result_array() as $r)
								{
						?>						 
						<div class="form-row">
							<div class="col-md-7 mb-3">
								<input value="<?= $r['code']?>" id="code" name="code" readonly hidden>
								<label for="itemName">Item Name :<b style="color:red">*</b></label>
								<input type="text" id="itemName" name="itemName" value="<?= $r['itemName']?>" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
							<div class="col-md-5 mb-3">
								<label for="vendorCode">Vendor:<b style="color:red">*</b></label>
								<select id="vendorCode" name="vendorCode" class="form-control js-example-responsive" required style="width:100%">
									<option value="">Select Vendor</option>
									<?php 
									if($vendormaster){
										foreach($vendormaster->result() as $curren)
										{	
											$selected = $r['vendorCode']==$curren->code ? "selected" : "";
											echo'<option value="'.$curren->code.'" '.$selected.'>'.$curren->entityName.'</option>';
										}
									}
									?>
								</select>	
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
						</div>

						<div class="form-row">
							<div class="col-md-4 mb-3">
								<label for="salePrice"> Sale Price : <b style="color:red">*</b></label>
								<input type="number" id="salePrice" value="<?=$r['salePrice']?>" name="salePrice" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>  
							<div class="col-md-4 mb-3">
								<label for="itemPackagingPrice"> Packing Charges : <b style="color:red">*</b></label>
								<input type="number" id="itemPackagingPrice" value="<?=$r['itemPackagingPrice']?>" name="itemPackagingPrice" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
							<div class="col-md-4 mb-3">
								<label for="maxOrderQty"> Maximum Order Quantity : <b style="color:red">*</b></label>
								<input type="number" id="maxOrderQty" value="<?=$r['maxOrderQty']?>" name="maxOrderQty" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
							<div class="col-md-4 mb-3">
								<label for="itemImage"> Item Image : </label>
								<input type="file" id="itemImage" name="itemImage" class="form-control" onchange="document.getElementById('itemImageShow').src = window.URL.createObjectURL(this.files[0])">
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							
							<div class="col-md-4 mb-3">
								<label for="cuisineType"> Cuisine Type : <b style="color:red">*</b></label>
								<select id="cuisineType" name="cuisineType" class="form-control" required>
									<option value="">Select Cuisine</option>
									<option value="veg" <?= $r['cuisineType']== 'veg' ? "selected" : "" ?>>Veg</option>
									<option value="nonveg" <?= $r['cuisineType']== 'nonveg' ? "selected" : "" ?>>Non - Veg</option>
								</select>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>
						
						<div class="form-row">
							<?php 
							if($r['itemPhoto']!="") 
							{
							?>
								<!--<input type="file" id="itemImage" name="itemImage" class="form-control" style="display:none;">-->
								<div class="col-md-3 el-element-overlay">
									<div class="card">
										<div class="el-card-item">
											<div class="el-card-avatar el-overlay-1"> <img id="itemImageShow" src="<?php echo base_url().'partner/uploads/'.$r['vendorCode'].'/vendoritem/'.$r['itemPhoto'];?>" alt="category File">
												<div class="el-overlay">
													<ul class="list-style-none el-info">
														<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo base_url().'partner/uploads/'.$r['vendorCode'].'/vendoritem/'.$r['itemPhoto'];?>" target="_blank"><i class="icon-magnifier"></i></a></li>
														<!--<li class="el-item"><a class="btn default btn-outline el-link" onclick="buttonClick('categoryImage')"><i class="icon-pencil"></i></a></li>-->
														<li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('<?= $r['code']; ?>','<?= $r['vendorCode']; ?>','<?= $r['itemPhoto']; ?>')"><i class="icon-trash"></i></a></li>
													</ul>
												</div>
											</div>
											<div class="el-card-content">
												<h4 class="m-b-0">Item Image</h4> 
											</div>
										</div>
									</div>
								</div>  
								<span id="photoError" class="text-danger"></span>
							<?php
							} 
							/*else 
							{
							?> 
								<div class="col-md-12 mb-3">
									<label for="itemImage"> Item Image :</label>
									<div class="input-group"> 
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="itemImage" name="itemImage">
											<label class="custom-file-label" for="itemImage">Choose file</label>
										</div>
									</div> 
									<span>Please upload images of 640 X 960 (width x height) size.</span>
									<div class="invalid-feedback">
										Required Field!
									</div>
									<span id="photoError" class="text-danger"></span>
								</div>
							<?php
							
							}*/
							?>
						</div>
						
						<div class="form-row">
					 		<div class="col-md-12 mb-3">
								<label for="address">Item Description :</label>
								<textarea type="text" id="itemDescription" name="itemDescription" class="form-control"><?= $r['itemDescription']?></textarea>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
						</div>
						
						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="menuCategoryCode">Menu Category:<b style="color:red">*</b></label>
								<select id="menuCategoryCode" name="menuCategoryCode" class="form-control" required>
									<option value="">Select Menu Category</option>
									<?php 
									if($menucategory)
									{
										foreach($menucategory->result() as $ra)
										{
											$selected = $r['menuCategoryCode']==$ra->code ? "selected" : "";
											echo'<option value="'.$ra->code.'" '.$selected.'>'.$ra->menuCategoryName.'</option>';
										}
									}
									?>
								</select>	
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
							<div class="col-md-6 mb-3">
								<label for="menuSubCategoryCode">Menu Sub Category:</label>
								<select id="menuSubCategoryCode" name="menuSubCategoryCode" class="form-control">
									<option value="">Select Menu Category</option>
									<?php 
									if($menusubcategory)
									{
										foreach($menusubcategory->result() as $ms)
										{
											$selected = $r['menuSubCategoryCode']==$ms->code ? "selected" : "";
											echo'<option value="'.$ms->code.'" '.$selected.'>'.$ms->menuSubCategoryName.'</option>';
										}
									}
									?>
								</select>									 
							</div> 
						</div> 
						<div class="row">
							<div class="form-group col-md-4">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox"> 
										<input type="checkbox" value="1" class="custom-control-input" <?= $r['itemActiveStatus']==1 ? "checked" : "";?> id="itemActiveStatus" name="itemActiveStatus">
										<label class="custom-control-label" for="itemActiveStatus">Item Status</label>
									</div>
								</div>
							</div>
							<div class="form-group col-md-3">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox"> 
										<input type="checkbox" value="1" class="custom-control-input" <?= $r['isAdminApproved']==1 ? "checked" : "";?> id="isAdminApproved" name="isAdminApproved">
										<label class="custom-control-label" for="isAdminApproved">Approved</label>
									</div>
								</div>
							</div>
							<div class="form-group col-md-3">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox"> 
										<input type="checkbox" value="1" class="custom-control-input" <?= $r['isActive']==1 ? "checked" : "";?> id="isActive" name="isActive">
										<label class="custom-control-label" for="isActive">Active</label>
									</div>
								</div>
							</div>
						</div>
						<div class="text-xs-right">							
							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
							<button type="Reset" class="btn btn-reset">Reset</button>
						</div>
						<?php
								}
							}
						?>
					</form>
				</div>
			</div>
		</div>	
	</div>

<script>
	$('input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
		// Disable keyboard scrolling
		$('input[type=number]').on('keydown',function(e) {
		var key = e.charCode || e.keyCode;
		// Disable Up and Down Arrows on Keyboard
		if(key == 38 || key == 40 ) {
			e.preventDefault();
		} 
		else 
		{
			return;
		}
	});
	function buttonClick(value){
		// alert(value);
		if(value == 'categoryImage')
		{
			$("#itemImage").click();
		}
	}
	
	function deleteButton(codeItem,code,value) {
		// debugger;
		$.ajax({
			url: base_path + 'index.php/Food/Vendoritem/deleteImage',
			method: "POST",
			data: {
				'value': value,
				'code': code,
				'codeItem': codeItem,
			},
			datatype: "text",
			success: function(data) {
				// console.log(data);
				// return false;
				if (data = "true") {
					location.reload();
				} else {
					alert('not deleted');
				}
			}
		});
	}
	
	$("body").on("change","#itemImage",function(e){ 
		$("#photoError").empty();
		var file =  $('#itemImage')[0].files[0];
		fileType= file.type.split('/');
		if(fileType[0]=='image')
		{
			var filename = $(this).val();
			var lastIndex = filename.lastIndexOf("\\");
			if (lastIndex >= 0) {
				filename = filename.substring(lastIndex + 1);
			}
			$(this).next("label").text(filename); 
		} else {
			$(this).val("");
			$("#photoError").html("Please upload an image (jpeg,jpg,png,bmp)!");
			return false;
		} 
	});
	
	$(document).ready(function(e){
	   $("#vendorCode").select2();
	})
	
	$("body").on("change","#menuCategoryCode",function(e){
		var this_code = $(this).val().trim();
		if (this_code!=undefined || this_code!="")
		{ 
			$.ajax({
				url:base_path+'index.php/Food/Vendoritem/getsubCategoryItems',
				data:{'menuCategoryCode':this_code},
				type:'get',
				success:function(response)
				{
					if(response!=undefined|| response!="")
					{
						$("#menuSubCategoryCode").empty();
						$("#menuSubCategoryCode").append(response);
					}
				}	
			}) ;
		}
	});
	
	var page_isPostBack = false;
	function windowOnBeforeUnload()
	{
		if ( page_isPostBack == true )
			return; // Let the page unload
		if ( window.event )
			window.event.returnValue = 'Are you sure?'; 
		else
			return 'Are you sure?'; 
	}
	window.onbeforeunload = windowOnBeforeUnload;
		// End Page Leave Yes / No
	setTimeout(function(){$('#error_message').hide('fast');},8000);
 
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
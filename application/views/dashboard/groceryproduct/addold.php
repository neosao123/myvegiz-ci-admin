<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Grocery Product</h4>
				<div class="d-flex align-items-center" <nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active" aria-current="page">Create Grocery Product</li>
					</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid col-md-8">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title"> Create Grocery Product</h4>
				<hr />
				<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url() . 'index.php/Groceryproduct/save'; ?>" enctype="multipart/form-data" novalidate>
					<div class="form-row">
						
						<div class="col-md-6 mb-3">
							<label for="productCategory">Product Category : <b style="color:red">*</b></label>
							<div class="controls">
								<select class="custom-select form-control" id="productCategory" name="productCategory" required>
									<option value="" readonly>Select Option..</option>
									<?php if($category){ foreach ($category->result() as $cat) {
										echo '<option value="' . $cat->categorySName . '" id="'.$cat->code.'">' . $cat->categoryName . '</option>';
									} } ?>
								</select>
							</div>
						</div>
						<div class="col-md-6 mb-3">
					        <label for="productSubCategory">Product Sub Category : <b style="color:red">*</b></label>
							<div class="controls">
								<select class="custom-select form-control" id="productSubCategory" name="productSubCategory" required>
									<option value="" readonly>Select Sub Category</option> 
								</select>
							</div>
					   </div>
					</div>
					<div class="form-row">
					   
					   <div class="col-md-12 mb-3">
							<label for="productName">Product Name : <b style="color:red">*</b></label>
							<input type="text" id="productName" name="productName" class="form-control" required>
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>

					<div class="form-row">
						<div class="col-md-2 mb-3"></div>
						<div class="col-md-12 mb-3">
							<label for="productDescription">Product Description : </label>
							<div class="controls">
								<textarea class="form-row" id="summernote" name="productDescription">
								</textarea>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="minimumSellingQuantity"> Minimum Selling Quantity :</label>
							<input type="number" id="minimumSellingQuantity" name="minimumSellingQuantity" value="0" class="form-control">
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="productRegularPrice">Product Regular Price : </label>
							<input type="number" id="productRegularPrice" name="productRegularPrice" value="0" class="form-control">
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="minStock">Product Minimum Stock: </label>
							<input type="number" id="minStock" name="minStock" class="form-control">
						</div>
						<div class="col-md-6 mb-3">
							<label for="productUom">Product UOM : </label>
							<select id="productUom" name="productUom" class="form-control">
								<option value="" readonly>Select Option..</option>
								<?php foreach ($uommaster->result() as $uom) {
									echo '<option value="' . $uom->uomSName . '">' . $uom->uomName . '</option>';
								} ?>
							</select>
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<label for="productBenefit">Product Benefits: </label>	
					<div class="form-row">	
						<div class="col-md-11 mb-3">								
							<input type="text" id="productBenefit" name="productBenefit[]" class="form-control">
						</div>
						<div class="col-md-1 mb-3">
							<button class="btn btn-success" type="button" onclick="benefits_fields();"><i class="fa fa-plus"></i></button>   
						</div> 
					</div>
					<div id="benefits_fields"></div>
					<label>Product Selling Price: </label>	
					<div class="form-row mb-3">							
						<div class="col-md-4">
							<select class="form-control cityCode" required name="cityCode[]" id="cityCode0">
								<option value="">Select City</option>
								<?php
									if($citymaster)
									{
										foreach($citymaster->result_array() as $r){
											echo '<option value="'.$r['code'].'">'.$r['cityName'].'</option>';
										}
									}
								?>
							</select>
						</div>
						<div class="col-md-4">
							<input type="number" id="sellingPrice0" required name="sellingPrice[]" class="form-control sellingPrice" placeholder="Selling Price (0.00)">	 
						</div>
						<div class="col-md-3">
							<select class="form-control" required id="productStatus0" name="productStatus[]">
								<option>Select Status</option>
								<option value="AVL">Available</option>
								<option value="OOS">Out Off Stock</option>
							</select>
						</div>
						<div class="col-md-1">								 
							<button class="btn btn-success" type="button" onclick="price_fields();"><i class="fa fa-plus"></i></button>   
						</div> 
					</div>
					<div id="price_fields"></div>
					<div class="form-row">
						<div class="col-md-8 mb-3">
							<label for="images">Add Product Images: </label>
							<div class="email-repeater form-group">
								<div data-repeater-list="repeater-group">
									<div data-repeater-item class="row m-b-15">
										<div class="col-md-10">
											<div class="custom-file">
												<input type="file" class="custom-file-input" id="customFile" name="images[]" multiple>
												<label class="custom-file-label" for="customFile">Choose file</label>
											</div>
										</div>
										<div class="col-md-2">
											<button data-repeater-delete="" class="btn btn-danger waves-effect waves-light" type="button"><i class="ti-close"></i>
											</button>
										</div>
									</div>
								</div>
								<button type="button" data-repeater-create="" class="btn btn-info waves-effect waves-light">Add More Images
								</button>
							</div>
							<span>Please upload images of 400 X 400 (width x height) size.</span>
						</div>
					</div>
					<div class="form-group">					 
						<div class="custom-control custom-checkbox">
							<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive">
							<label class="custom-control-label" for="isActive">Active</label>
						</div>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" value="1" class="custom-control-input" id="isPopular" name="isPopular">
							<label class="custom-control-label" for="isPopular">Popular?</label>
						</div> 
					</div>
					<?php 
						if($tags->num_rows()>0){ ?>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-2 mb-1">
								<h6> Tags:</h6>
							</div>
						</div>
						<div class="row" style="margin-left:35px;">
							
							<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" class="custom-control-input" id="tagSection" checked name="tagCode" value="1">
									<label class="custom-control-label"  for="tagSection"><b>No Tag</b></label>
								</div>
							</div>
							<?php 
								foreach($tags->result_array() as $tag){
							?>
							<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" class="custom-control-input" id="tagSection<?= $tag['code']?>" name="tagCode" value="<?= $tag['code']?>">
									<label class="custom-control-label" style="color:<?= $tag['tagColor']?>" for="tagSection<?= $tag['code']?>"><b><?= $tag['tagTitle']?></b></label>
									
								</div>
								<!--<div class="box" style="background-color:<?= $tag['tagColor']?>"></div>-->
							</div>
							<?php }?>
						</div>
					</div>
						<?php }?>
					<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message)) {
							echo $error_message;
						}
						echo "</div>";
					?>
					<div class="text-xs-right">
						<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
						<button type="reset" class="btn btn-reset">Reset</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	$("body").delegate(".sellingPrice","change",function() {
		var productRegularPrice = $("#productRegularPrice").val();
		var productSellingPrice = $(this).val();
		if (parseInt(productRegularPrice) < parseInt(productSellingPrice)) {
			alert('Regular Price must be higher than the Selling Price');
			$(this).val('');
		}
	});
	var path = base_path + "product/uploadFile/";
	$(".dropzone").dropzone({
		url: path,
		paramName: 'file',
		success: function(file, response) {
		}
	});
	$('document').ready(function() {
		// $("#dzid").dropzone({ url: "/file/post" });
		$("#productName").maxlength({
			max: 50
		});
		$("#productDescription").maxlength({
			max: 256
		});
		$('.btn-reset').click(function() {
			$('#summernote').summernote('reset');
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
	var room = 1;
	var room2 = 1;
	function benefits_fields(count, flag) {
		if (flag == 'edit') {
			if (room == 1) {
				room = count;
			}
		}
		var objTo = document.getElementById('benefits_fields')
		var divtest = document.createElement("div");
		divtest.setAttribute("class", "form-group removeclass" + room);
		var rdiv = 'removeclass' + room;			 
		divtest.innerHTML = '<div class="form-row"><div class="col-md-11"> <div class="form-group"> <input type="text" id="productBenefit' + room + '" name="productBenefit[]" class="form-control" > </div> </div> <div class="col-md-1"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_benifits_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div>';
		objTo.appendChild(divtest)
		room++;
	}

	function price_fields(count, flag) {
		if (flag == 'edit') {
			if (room2 == 1) {
				room2 = count;
			}
		}
		var objTo = document.getElementById('price_fields')
		var divtest = document.createElement("div");
		divtest.setAttribute("class", "form-group removeclassP" + room2);
		var citydata =  $("#cityCode0").html();
		var rdiv = 'removeclassP' + room;			 
		if (flag == 'edit') {
			divtest.innerHTML = '<div class="form-row"><div class="col-md-4"><select class="form-control cityCode" id="cityCode'+room2+'" name="cityCode[]" required>'+citydata+'</select></div><div class="col-md-4"><input class="form-control sellingPrice" name="sellingPrice[]" id="sellingPrice'+room2+'" placeholder="selling Price (0.00)"></div><div class="col-md-3"><select class="form-control" id="productStatus'+room2+'" name="productStatus[]" required><option value="">Select Status</option><option value="AVL">Available</option><option value="OOS">Out Off Stock</option></select></div><div class="col-md-1"><button class="btn btn-danger" onclick="remove_price_fields(' + room2 + ');"><i class="fa fa-trash"></i></button></div></div>';
		} else {
			divtest.innerHTML = '<div class="form-row"><div class="col-md-4"><select class="form-control cityCode" id="cityCode'+room2+'" name="cityCode[]" required>'+citydata+'</select></div><div class="col-md-4"><input class="form-control sellingPrice" name="sellingPrice[]" id="sellingPrice'+room2+'" placeholder="selling Price (0.00)"></div><div class="col-md-3"><select class="form-control" id="productStatus'+room2+'" name="productStatus[]" required><option value="">Select Status</option><option value="AVL">Available</option><option value="OOS">Out Off Stock</option></select></div><div class="col-md-1"><button class="btn btn-danger" onclick="remove_price_fields(' + room2 + ');"><i class="fa fa-trash"></i></button></div></div>';
		}
		objTo.appendChild(divtest);
		room2++;
		
	}
	$("body").delegate(".cityCode","change",function(){
		if(room2>1){				 
			var city_code=$(this).val();
			var thisid = $(this).attr('id');
			var counter=room2;
			var duplicate="";
			if(city_code!=""){
				$(".cityCode").each(function(){
					if($(this).attr('id')!=thisid){
						var prevRecord=$(this).val();	
						if(prevRecord==city_code) {
							$("#"+thisid).val("");
							swal('City','This City is Selected Already!','success');
						}
					}
				});
			}
		}
	});
	
	$("body").delegate('#productCategory',"change",function() { 
		debugger;
		var productCategory = $(this).find('option:selected').attr('id');
		if(productCategory!=""){
			$.ajax({
				url: base_path + 'Groceryproduct/getSubCategoryList',
				type: "POST",
				data: {
					'productCategory': productCategory
				},
				success: function(response) {
					if(response!=""){
						$('#productSubCategory').html(response);
						$("#productSubCategory").attr('readonly',false);
					} else {
						var opt = '<option value="">Select Sub Category</option>'
						$('#productSubCategory').html(opt);
						$("#productSubCategory").attr('readonly',true);
					}
				}
			});
		} else {
			var opt = '<option value="">Select Sub Category</option>'
			$('#productSubCategory').html(opt);
			$("#productSubCategory").attr('readonly',true);
		}
	});
	
	
	function remove_benifits_fields(rid, flag, code) {
		$('.removeclass' + rid).remove(); 
	}
	function remove_price_fields(rid, flag, code) {
		$('.removeclassP' + rid).remove(); 
	}
</script>
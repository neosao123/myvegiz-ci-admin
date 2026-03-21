<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Product</h4>
				<div class="d-flex align-items-center" <nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active" aria-current="page">Create Product</li>
					</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid col-md-8">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title"> Create Product</h4>
				<hr />
				<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url() . 'index.php/Product/save'; ?>" enctype="multipart/form-data" novalidate>
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="productCategory">Product Category : <b style="color:red">*</b></label>
							<div class="controls">
								<select class="custom-select form-control" id="productCategory" name="productCategory" required>
									<option value="" readonly>Select Category..</option>
									<?php foreach ($category->result() as $cat) {
										echo '<option value="' . $cat->categorySName . '" id="'.$cat->code.'">' . $cat->categoryName . '</option>';
									} ?>
								</select>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="productCategory">Product Subcategory : <b style="color:red">*</b></label>
							<div class="controls">
								<select class="custom-select form-control" id="productSubCategory" name="productSubCategory" required readonly>
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
							<label for="minStock">Product Minimum Stock: </label>
							<input type="number" id="minStock" name="minStock" class="form-control">
						</div>
					</div>
					<div class="form-row"> 
						<div class="col-md-4 mb-3">
							<label for="hsnCode"> HSN Code : </label>
							<input type="text" class="form-control" id="hsnCode" name="hsnCode" required>
						</div>
						<div class="col-md-4 mb-3">
							<label for="taxPercent">Tax ( in  %) : </label>
							<select class="form-control" id="taxPercent" name="taxPercent" required>
								<option value="0.00">0</option>
								<option value="5.00">5</option>
								<option value="12.00">12</option>
								<option value="18.00">18</option>
								<option value="28.00">28</option>
							</select>
						</div>
						<div class="col-md-4 mb-3">
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
						<?php
						$cityoptions = "";
						if ($citymaster) {
							$citytoptions .= '<option value=""> -- Select City -- </option>';
							foreach ($citymaster->result_array() as $r) {
								$citytoptions .= '<option value="' . $r['code'] . '">' . $r['cityName'] . '</option>';
							}
						}
						?>
						<datalist id="citylist">
							<?= $citytoptions ?>
						</datalist>
						<datalist id="subunitlist"></datalist>
					</div>
					<label>City Wise Variants & Prices : </label>
                    <div class="form-group removeclassP0">					
						<div class="form-row mb-3">							
							<div class="col-md-4">
								<select class="form-control cityCode" required name="cityCode[]" id="cityCode0" onchange="checkDuplicateProduct(0)">
									<?= $citytoptions ?>
								</select>
							</div>
							<div class="col-md-4 mb-2">
									<select class="form-control subUnit" id="subUnit0" name="subUnit[]" onchange="checkDuplicateProduct(0)" required>
										<option value=""> -- Select Sub Unit -- </option>
									</select> 
							</div>
							<div class="col-md-3 mb-2">
									<input type="text" class="form-control quantity" id="quantity0" name="quantity[]" onchange="checkDuplicateProduct(0)" required placeholder="Quantity">
							</div>
						</div>
						<div class="form-row mb-3">
							<div class="col-md-3 mb-2">
									<input type="number" step="0.01" class="form-control regularPrice" id="regularPrice0" name="regularPrice[]" required placeholder="Regular Price (0.00)">
							</div>
							<div class="col-md-4">
								<input type="number" step="0.01" class="form-control sellingPrice" id="sellingPrice0" name="sellingPrice[]"  required placeholder="Selling Price (0.00)">	 
							</div>
							<div class="col-md-3">
								<select class="form-control" required id="productStatus0" name="productStatus[]">
									<option>Select Status</option>
									<option value="AVL">Available</option>
									<option value="OOS">Out Off Stock</option>
								</select>
							</div>
							<div class="col-md-1 mb-2 add_btn">
								<button type="button" data-id="0" class="btn btn-success add_price_fields"><i class="fa fa-plus"></i></button>
							</div> 
						</div>
					</div>
					<div id="price_fields"></div>
					<div class="form-row" id="pricesection_add_btn"></div>
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
	var org_unit = "";
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

	function checkDuplicateProduct(id){
		var subUnit = $("#subUnit"+id).val();
		var quantity = $("#quantity"+id).val();
		var cityCode = $("#cityCode"+id).val();
		var cls = document.getElementsByClassName('subUnit');
		var qty = document.getElementsByClassName('quantity');
		var city = document.getElementsByClassName('cityCode');
		//alert(cls.length+'---'+qty.length)
		for(row_id=0;row_id<cls.length;row_id++){
			var row_subUnit = document.getElementById("subUnit"+row_id).value;
			var row_quantity = document.getElementById("quantity"+row_id).value;
			var row_cityCode = document.getElementById("cityCode"+row_id).value;
			if((row_quantity==quantity && row_subUnit==subUnit && row_cityCode==cityCode) && id!=row_id){
				toastr.error("Varient with same City,SubUnit and Quantity already exists..","Duplicate Record");
				document.getElementById("subUnit"+id).value=''
				document.getElementById("quantity"+id).value=''
				document.getElementById("cityCode"+id).value=''
				return
			}
		}
	}
	
	function price_fields(count, flag) {
		var objTo = document.getElementById('price_fields');
		if (flag == 'edit') {
			if (room == 1) room = count;
		}
		else{
			room=count;
		}	
		var unit = $("#productUom").val()
		
		var subUnitData = $("#subunitlist").html();
		var cityData = $("#citylist").html();  
		if(unit!="") 
		{
			var ps,city,subunit = "";
			var qty, sp = 0;
			if(room ==0) {
				city = $("#cityCode0").val();
				subunit =  $("#subUnit0").val();
				qty = $("#quantity0").val().trim();
				sp = $("#sellingPrice0").val().trim();
				rp = $("#regularPrice0").val().trim();
				ps = $("#productStatus0").val();
			} else{
				city = $("#cityCode"+room).val();
				subunit =  $("#subUnit"+room).val();
				qty = $("#quantity"+room).val().trim();
				sp = $("#sellingPrice"+room).val().trim();
				rp = $("#regularPrice"+room).val().trim();
				ps = $("#productStatus"+room).val();
			}
			
			if(city=="") {
				toastr.error("Please select city","City Required");
				return false;
			}
			
			if(subunit=="") {
				toastr.error("Please select an selling unit","Selling Unit Required");
				return false;
			}
			
			if(qty="" || Number(qty) <=0) {
				 $("#quantity"+room).val(1);
				toastr.error("Quantity must be non zero value","Quantity");
				return false;
			}
			if((isNaN(rp)) || Number(rp)<=0) {
				$("#regularPrice"+room).val(1);
				toastr.error("Regular price should be an non zero value ","Regular Price");
				return false;
			}
			if((isNaN(sp)) || Number(sp)<=0) {
				$("#sellingPrice"+room).val(1);
				toastr.error("Selling price should be an non zero value ","Selling Price");
				return false;
			}
			if(Number(sp)>Number(rp)){
				toastr.error("Regular price must be greater than or equal to selling price","Regular Price");
				$("#sellingPrice"+room).val('');
				$("#regularPrice"+room).val('');
				return false;
			}
			if(ps=="") {
				toastr.error("Select products Status","Product Status");
				return false;
			}
			// if(room==0)
			// {
				// $(".add_btn0").empty().append('<button type="button" class="btn btn-danger remove_price_fields" data-id="0"><i class="fa fa-trash"></i></button>');
				// $("#pricesection_add_btn").append('<div class="col-md-1 mb-3"><button type="button" class="btn btn-success add_price_fields"><i class="fa fa-plus"></i></button></div>');
			// }
			
			$(".add_btn").empty().append('<button type="button" class="btn btn-danger remove_price_fields" data-id="'+room+'"><i class="fa fa-trash"></i></button>');
			
			
			room++;
		 
			var divtest = document.createElement("div");
			divtest.setAttribute("class", "form-group removeclassP" + room);
		
			$("#pricesection_add_btn").empty().append('<div class="col-md-1 mb-3"><button type="button" data-id="'+room+'" class="btn btn-success add_price_fields"><i class="fa fa-plus"></i></button></div>');
		 
			var elements = '<div class="form-row mb-1">';
			elements +=	'<div class="col-md-4 mb-2"><select class="form-control cityCode" id="cityCode'+room+'" name="cityCode[]" required onchange="checkDuplicateProduct('+room+')">'+cityData+'</select></div>';
			elements += '<div class="col-md-4 mb-2"><select class="form-control subUnit" name="subUnit[]" id="subUnit'+room+'" required onchange="checkDuplicateProduct('+room+')">'+subUnitData+'</select></div>';
			elements += '<div class="col-md-3 mb-2"><input type="text" class="form-control quantity" id="quantity'+room+'" name="quantity[]" placeholder="Quantity" required onchange="checkDuplicateProduct('+room+')"></div>';
			elements +=	'</div><div class="form-row mb-3">';
			elements += '<div class="col-md-3 mb-2"><input type="number" step="0.01" class="form-control regularPrice" id="regularPrice'+room+'" name="regularPrice[]" required placeholder="Regular Price (0.00)"></div>'
			elements += '<div class="col-md-4 mb-2"><input type="number" step="0.01" class="form-control sellingPrice" id="sellingPrice'+room+'" name="sellingPrice[]" required placeholder="Selling Price (0.00)"></div>'
			elements += '<div class="col-md-3 mb-2"><select class="form-control productStatus" required id="productStatus'+room+'" name="productStatus[]" required><option value="">Select Status</option><option value="AVL">Available</option><option value="OOS">Out Of Stock</option></select></div>';
			elements += '<div class="col-md-1 mb-2"><button type="button" class="btn btn-danger remove_price_fields" data-id="' + room + '"><i class="fa fa-trash"></i></button></div>';
			elements +=	'</div>'; 
			divtest.innerHTML = elements;  
			objTo.appendChild(divtest); 
			
			
		} else {
			$("#productUom").focus();
			toastr.error("Please select product unit before adding prices and varients","Unit Required");
			return false;
		} 
	} 
	
	function get_sub_units(main_unit) {
		if(main_unit!=""){
			$.ajax({
				type:"get",
				url :base_path+'Product/getSubUnits',
				data:{unit:main_unit},
				success:function(response){
					if(response) $("#subunitlist").html(response); $("#subUnit0").html(response);
				},error:function(error){
					console.log("Ajax Failed to get sub unit list");
				}
			});
		}
	}
	
	$("body").delegate(".remove_price_fields","click",function() {
		var remove_room_id = $(this).data('id');
		$(".removeclassP" + remove_room_id).remove();
		$("#pricesection_add_btn").empty().append('<div class="col-md-1 mb-3"><button type="button" data-id="'+(remove_room_id-1)+'" class="btn btn-success add_price_fields" ><i class="fa fa-plus"></i></button></div>');
	});
	
	$("body").delegate(".add_price_fields","click",function() {
		var pos = $(this).data('id');
		price_fields(pos,'add');
	});
	
		 $("body").delegate(".regularPrice","change",function(){
		var rp = $(this).val().trim();
		var rp_id= $(this).attr('id');
		var sp = $('#sellingPrice0').val().trim();
		if(isNaN(rp)){
			swal("Regular Price","Regular price must be valid amount","error");
			$("#"+rp_id).focus();
			return false;
		} else if(Number(rp)<=0) {
			swal("Regular Price","Regular price must be greater than 0.","error");
			$("#"+rp_id).focus();
			return false;
		} else {
			if(Number(sp)>Number(rp) && sp!=''){
				swal("Regular Price","Regular price must be greater than or equal to selling price","error");
				$(this).val('');
				$('#sellingPrice0').val('');
				$("#"+rp_id).focus();
			}
		}
	});
	$("body").delegate(".sellingPrice","change",function(){
		debugger;
		var sp = $(this).val().trim();
		var sp_id= $(this).attr('id');
		var rp= $('#regularPrice0').val().trim();
		if(isNaN(sp)){
			swal("Selling Price","Selling price must be valid amount","error");
			$("#"+sp_id).focus();
			return false;
		} else if(Number(sp)<=0) {
			swal("Selling Price","Selling price must be greater than 0.","error");
			$("#"+sp_id).focus();
			return false;
		} else {
			if(Number(sp)>Number(rp) && rp!=''){
				swal("Regular Price","Regular price must be greater than or equal to selling price","error");
				$(this).val('');
				$('#regularPrice0').val('');
				$("#"+sp_id).focus();
			}
		}
	});
	
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
		var productCategory = $(this).find('option:selected').attr('id');
		if(productCategory!=""){
			$.ajax({
				url: base_path + 'Product/getSubCategoryList',
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
	
	
	$("body").delegate("#productUom","focus",function() {
		org_unit = $(this).val();
	});
	
	$("body").delegate("#productUom","change",function() {
		var unit = $(this).val();
		if(unit!=""){
			if($("#price_fields").html().length == 0){
				get_sub_units(unit);
			} else {
				swal({
					title: "Are you sure?",
					text: "Make sure after changing product's unit, all city and price variants shall be removed! Okay?",
					type: "warning",
					showCancelButton: !0,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes",
					cancelButtonText: "No",
					closeOnConfirm: !1,
					closeOnCancel: !1
				}, function(e) {
					if (e) { 
						$("#price_fields").empty();
						get_sub_units(unit);
						swal.close();
					} else {
						$("#productUom").val(org_unit);
						swal("City & Price Variants", "Price Varients are safe :)", "error"); 
					}
				});
			}
		}  
	});
	
	function remove_benifits_fields(rid, flag, code) {
		$('.removeclass' + rid).remove(); 
	}
	function remove_price_fields(rid, flag, code) {
		$('.removeclassP' + rid).remove(); 
	}
</script>
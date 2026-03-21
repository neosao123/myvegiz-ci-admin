<div class="page-wrapper">

<div class="page-breadcrumb">
	<div class="row">
		<div class="col-12 align-self-center">
			<h4 class="page-title">Product</h4>
			<div class="d-flex align-items-center">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="#">Home</a></li>
						<li class="breadcrumb-item active" aria-current="page">Change In Product</li>
					</ol>
				</nav>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid col-md-8">
	<div class="card">
		<div class="card-body">
			<h4 class="card-title"> Change In Product</h4>
			<hr />

			<form class="needs-validation" method="post" id="service" action="<?php echo base_url() . 'index.php/Product/update'; ?>" enctype="multipart/form-data" novalidate>
				<?php foreach ($query->result() as $row) {
				?>
					<div class="form-row">
						<input type="text" name="code" class="form-control mb-3" id="code" value="<?= $row->code ?>" readonly>
					</div>
					<div class="form-row">
						
						<div class="col-md-6 mb-3">
							<label for="productCategory">Product Category : <b style="color:red">*</b></label>
							<div class="controls">
								<select class="custom-select form-control" id="productCategory" name="productCategory" required>
									<?php 
									if($category){
										foreach ($category->result() as $cat) {
											 $selected = $row->productCategory == $cat->categorySName ? 'selected' :'';
											echo '<option value="' . $cat->categorySName . '" id="'.$cat->code.'" '.$selected.'>' . $cat->categoryName . '</option>';
										}
									}										?>
								</select>
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="productSubCategory">Product Sub Category : <b style="color:red">*</b></label>
							<div class="controls">
							   
								<select class="custom-select form-control" id="productSubCategory" name="productSubCategory" required>
									<?php 
									if($subcategory){
										foreach ($subcategory->result() as $subcat) {
											$selected = $row->subCategoryCode == $subcat->code ? 'selected' :'';
											echo '<option value="' . $subcat->code . '" '.$selected.'>' . $subcat->subcategoryName . '</option>';
										}
									}										?>
								</select>
								
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<label for="productName">Product Name : <b style="color:red">*</b> </label>
							<input type="text" id="productName" name="productName" value="<?= $row->productName; ?>" class="form-control" required>

							<div class="invalid-feedback">
								Required Field!
							</div>
						</div>
						<div class="col-md-12 mb-3">
							<label for="productDescription">Product Description : </label>
							<div class="controls">
								<textarea id="summernote" name="productDescription" class="form-control">
									<?= $row->productDescription; ?>
								</textarea>
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="minimumSellingQuantity"> Minimum Selling Quantity :</label>
							<input type="number" id="minimumSellingQuantity" name="minimumSellingQuantity" value="<?= $row->minimumSellingQuantity; ?>" class="form-control">
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<div class="form-row"> 
						<div class="col-md-4 mb-3">
							<label for="hsnCode"> HSN Code : </label>
							<input type="text" class="form-control" id="hsnCode" name="hsnCode" value="<?= $row->hsnCode; ?>" required>
						</div>
						<div class="col-md-4 mb-3">
							<label for="taxPercent">Tax ( in  %) : </label>
							<select class="form-control" id="taxPercent" name="taxPercent" required>
								<option value="0.00" selected>0</option>
								<option value="5.00">5</option>
								<option value="12.00">12</option>
								<option value="18.00">18</option>
								<option value="28.00">28</option>
							</select>
							<script>
								var tax = "<?=$row->taxPercent?>";
								$("#taxPercent").val(tax);
							</script>
						</div>
						<div class="col-md-4 mb-3">
							<label for="productUom">Product UOM : </label>
							<select id="productUom" name="productUom" value="<?= $row->productUom; ?>" class="form-control">
								<?php foreach ($uommaster->result() as $uom) {
									echo '<option value="' . $uom->uomSName . '">' . $uom->uomName . '</option>';
								} ?>
							</select>
							<script>
								var productUom = "<?= $row->productUom ?>";
								$("#productUom").val(productUom);
							</script>
							<div class="invalid-feedback">
								Required Field !
							</div>
							<datalist id="subunitlist">
								<option value="">-- Select Sub Unit --</option>
								<?php 
									if($subunits) {
										foreach ($subunits->result() as $suom) {
											echo '<option value="' . $suom->subunitSName . '">' . $suom->subunitName . '</option>';
										}
									}
								?>
							</datalist>
							<datalist id="citylist">
								<option value="">-- Select City --</option>
								<?php
								if ($citymaster) {
									foreach ($citymaster->result_array() as $r) {
										echo '<option value="' . $r['code'] . '">' . $r['cityName'] . '</option>';
									}
								}
								?>
							</datalist>
						</div>
					</div>
					<label for="productBenefit">Product Benefits : </label>
					<div class="form-row">
						<div id="benefits_fields" class="col-md-12 m-t-20">
							<?php
							$j = 1;
							foreach ($benefits->result() as $line) { ?>
								<input type="hidden" name="lineCode[]" id="lineCode" value="<?= $line->code ?>">
								<div class="form-group removeclass<?php echo $j ?>">
									<div class="form-row">
										<div class="col-md-10 mb-3">
											<div class="form-group">
												<input type="text" id="productBenefit<?= $j ?>" name="productBenefit[]" value="<?= $line->productBenefit ?>" class="form-control">
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<button class="btn btn-danger" type="button" onclick="remove_benefits_fields(<?php echo $j ?>,'edit','<?php echo $line->code ?>');">
													<i class="fa fa-minus"></i>
												</button>
											</div>
										</div>
									</div>
								</div>
							<?php $j += 1;
							} ?>
						</div>
					</div>
					<div class="row" id="ratesAdd">
						<div class="col-md-1">
							<div class="form-group">
								<button class="btn btn-success" type="button" onclick="benefits_fields(<?php echo $j; ?>,'edit');"><i class="fa fa-plus"></i></button>
							</div>
						</div>
					</div>
					<datalist id="cityCode">
						<option value="">Select City</option>
						<?php
						if ($citymaster) {
							foreach ($citymaster->result_array() as $r) {
								echo '<option value="' . $r['code'] . '">' . $r['cityName'] . '</option>';
							}
						}
						?>
					</datalist>
					<label for="price_fields">City Wise Variants & Prices: </label>
				    <div id="price_fields"> 
						<?php
						if($prices)
						{
							foreach ($prices->result() as $line) { 
						?>
							<div class="removeclassP<?php echo $j ?>">
								<div class="form-row mb-1">
									<input type="hidden" name="lineCode[]" id="lineCode<?php echo $j ?>" value="<?= $line->code ?>">
									<div class="col-md-4">
										<select class="form-control cityCode" required name="cityCode[]" id="cityCode<?= $j ?>" onchange="checkDuplicateProduct(<?= $j ?>)">
											<option value="">Select City</option>
											<?php
											if ($citymaster) {
												foreach ($citymaster->result_array() as $r) {
													$selected = $line->cityCode == $r['code'] ? 'selected' : '';
													echo '<option value="' . $r['code'] . '" '.$selected.' >' . $r['cityName'] . '</option>';
												}
											}
											?>
										</select> 
									</div>
									<div class="col-md-4">
										<select class="form-control subUnit" name="subUnit[]" id="subUnit<?= $j ?>" required onchange="checkDuplicateProduct(<?= $j ?>)">
											<option value="">Select Sub Unit</option>
											<?php
											if($subunits) {
												foreach ($subunits->result() as $suom) {
													$selected =  $suom->subunitSName == $line->sellingUnit ? 'selected':'';
													echo '<option value="' . $suom->subunitSName . '" '.$selected .'>' . $suom->subunitName . '</option>';
												}
											}
											?>
										</select> 
									</div>
									<div class="col-md-3">
										<input type="text" id="quantity<?= $j ?>" required name="quantity[]" value="<?= $line->quantity ?>" onchange="checkDuplicateProduct(<?= $j ?>)" class="form-control quantity" placeholder="Quantity">
									</div>
								</div>
								<div class="form-row mb-1">	
									<div class="col-md-3">
										<input type="number" step="0.01" id="regularPrice<?= $j ?>" required name="regularPrice[]" value="<?= $line->regularPrice ?>" class="form-control regularPrice" placeholder="Regular Price (0.00)">
									</div>
									<div class="col-md-3">
										<input type="number" step="0.01" id="sellingPrice<?= $j ?>" required name="sellingPrice[]" value="<?= $line->sellingPrice ?>" class="form-control sellingPrice" placeholder="Selling Price (0.00)">
									</div>
									<div class="col-md-3">
										<select class="form-control" required id="productStatus<?= $j ?>" required name="productStatus[]">
											<option>Select Status</option>
											<option value="AVL">Available</option>
											<option value="OOS">Out Of Stock</option>
										</select>
										<script>
											var status = "<?= $line->productStatus ?>";
											$("#productStatus<?= $j ?>").val(status);
										</script>
									</div>
									<div class="col-md-1">
										<div class="form-group">
											<button class="btn btn-danger" type="button" onclick="remove_price_fields('<?php echo $j ?>','edit','<?php echo $line->code ?>');">
												<i class="fa fa-trash"></i>
											</button>
										</div>
									</div>
								</div>
							</div>
						<?php 
							$j += 1;
						}
					} 
					?>
					</div>					 
					<div class="row" id="newRateAdd">
						<div class="col-md-1 mb-3">
							<?php if($j==1) { ?>
								<button class="btn btn-success" type="button"  onclick="price_fields(1,'add');"><i class="fa fa-plus"></i></button>
							<?php } else {?>
								<button class="btn btn-success" type="button"  onclick="price_fields(<?=$j-1?>,'edit');"><i class="fa fa-plus"></i></button>
							<?php } ?>
						</div>
					</div>

					<div class="form-row">
						<div class="row el-element-overlay">
							<?php
							$i = 1;
							foreach ($productPhotos->result() as $photos) {
								$productPhoto = $photos->productPhoto;
							?>
								<div class="col-md-3 mb-3">

									<div class="card">
										<div class="el-card-item">
											<div class="el-card-avatar el-overlay-1"> <img src="<?php echo base_url() . 'uploads/product/' . $photos->productCode . '/' . $productPhoto . '?' . time(); ?>" alt="product photo File">
												<div class="el-overlay">
													<ul class="list-style-none el-info">
														<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo base_url() . 'uploads/product/' . $photos->productCode . '/' . $productPhoto . '?' . time(); ?>" target="_blank"><i class="icon-magnifier"></i></a></li>
														<!--<li class="el-item"><a class="btn default btn-outline el-link" onclick="buttonClick('images<?= $i; ?>')"><i class="icon-pencil"></i></a></li>-->
														<li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('<?= $photos->code; ?>')"><i class="icon-trash"></i></a></li>
													</ul>
												</div>
											</div>

										</div>
									</div>


								</div>
						<?php $i = $i + 1;
							}
					 ?>

						</div>
					</div>
					<div class="form-row">
						<div class="col-md-8 mb-3">

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
								<button type="button" data-repeater-create="" id="addMoreImages" class="btn btn-info waves-effect waves-light">Add More Images
								</button>
							</div>
						</div>

						</td>
					</div>


					<div class="form-group">
					 
						<div class="custom-control custom-checkbox">
							<?php
							foreach ($query->result() as $row) {
								if ($row->isActive == "1") {
									echo "<input type='checkbox'  value='1' class='custom-control-input' id='isActive' name='isActive' checked>
										<label class='custom-control-label' for='isActive'>Active</label>";
								} else {
									echo "<input type='checkbox'  value='1' class='custom-control-input' id='isActive' name='isActive'>
									<label class='custom-control-label' for='isActive'>Active</label>";
								} 
							}?>
						</div>
						
											 
						<div class="custom-control custom-checkbox">
							<?php
							foreach ($query->result() as $row) {
								if ($row->isPopular == "1") {
									echo "<input type='checkbox'  value='1' class='custom-control-input' id='isPopular' name='isPopular' checked>
										<label class='custom-control-label' for='isPopular'>Popular</label>";
								} else {
									echo "<input type='checkbox'  value='1' class='custom-control-input' id='isPopular' name='isPopular'>
									<label class='custom-control-label' for='isPopular'>Popular</label>";
								} 
							}?>
						</div>

					</div>

				<?php 
						if($tags->num_rows()>0){ 
						$str1='';
							if($row->tagCode==NULL || $row->tagCode==''){
								$str1='checked';
							}
						?>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-2 mb-1">
								<h6> Tags:</h6>
							</div>
						</div>
						<div class="row" style="margin-left:35px;">
							<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" class="custom-control-input" <?= $str1 ?> id="tagSection" name="tagCode" value="1">
									<label class="custom-control-label"  for="tagSection"><b>No Tag</b></label>
								</div>
							</div>
							<?php 
								foreach($tags->result_array() as $tag){
									$str = '';
									if($tag['code']==$row->tagCode){
										$str = 'checked';
									}
							?>
							<div class="col-sm-3 mb-3">
								 <div class="custom-control custom-radio custom-control-inline">
									<input type="radio" class="custom-control-input" id="tagSection<?= $tag['code']?>" <?= $str?> name="tagCode" value="<?= $tag['code']?>">
									<label class="custom-control-label" style="color:<?= $tag['tagColor']?>" for="tagSection<?= $tag['code']?>"><b><?= $tag['tagTitle']?></b></label>
								</div>
							</div>
							<?php }?>
						</div>
					</div>
						<?php }
				}?>
				<div class="text-xs-right">

					<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>

					<a href="<?php echo base_url() . 'index.php/Product/listRecords'; ?>">
						<button type="button" class="btn btn-reset">Back</button>
					</a>

				</div>


			</form>
		</div>
	</div>
</div>


</div>

<script>
	/*$("body").delegate(".sellingPrice","change",function() {
		var productRegularPrice = $("#productRegularPrice").val();
		var productSellingPrice = $(this).val();
		if (parseInt(productRegularPrice) < parseInt(productSellingPrice)) {
			alert('Regular Price must be higher than the Selling Price');
			$(this).val('');
		}
	});*/
	function buttonClick(value) {
		//alert(value);
		$("#images").click();
	}
	function deleteButton(value) {

		$.ajax({
			url: base_path + 'Product/deleteImage',
			method: "POST",
			data: {
				'value': value
			},
			datatype: "text",
			success: function(data) {

				if (data = "true") {
					location.reload();
				} else {
					alert('not deleted');
				}
			}

		});

	}
	$('document').ready(function() {
		$("#productName").maxlength({
			max: 30
		});
		$("#productDescription").maxlength({
			max: 256
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
	var room = 1;
	var room2 = 2;
    var org_unit = "";
	function checkDuplicateProduct(id){
		var subUnit = $("#subUnit"+id).val();
		var quantity = $("#quantity"+id).val();
		var cityCode = $("#cityCode"+id).val();
		var cls = document.getElementsByClassName('subUnit');
		var qty = document.getElementsByClassName('quantity');
		var city = document.getElementsByClassName('cityCode');
		for(row_id=1;row_id<=cls.length;row_id++){
			var row_subUnit = document.getElementById("subUnit"+row_id).value;
			var row_quantity = document.getElementById("quantity"+row_id).value;
			var row_cityCode = document.getElementById("cityCode"+row_id).value;
			if((row_quantity==quantity && row_subUnit==subUnit && row_cityCode==cityCode) && id!=row_id){
				toastr.error("Varient with same City,SubUnit and Quantity already exists..","Duplicate Record");
				document.getElementById("subUnit"+id).value=''
				document.getElementById("quantity"+id).value='';
				document.getElementById("cityCode"+id).value='';
				return
			}
		}
	}
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
		divtest.innerHTML = '<div class="form-row"><div class="col-md-10"> <div class="form-group"> <input type="text" id="productBenefit' + room + '" name="productBenefit[]" class="form-control" > </div> </div> <div class="col-md-2"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_benifits_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div>';
		objTo.appendChild(divtest)
		room++;
	}

	function price_fields(count, flag) {		
		var objTo = document.getElementById('price_fields');
		var unit = $("#productUom").val();
		var subUnitData = $("#subunitlist").html();
		var cityData = $("#citylist").html(); 
		if(unit!="")
		{
			var ps,city,subunit = "";
			var qty, sp = 0;
			if(flag=='edit') {
				var len = $(".cityCode").length;
				if(len>0){
					var cityElement = $(".cityCode");
					//debugger;
					for(var i=0; i<cityElement.length; i++) {
						var element = cityElement.eq(i).attr('id');
						var substr_id = element.substring(8);
						console.log(substr_id);
						
						city = $("#cityCode"+substr_id).val();
						subunit = $("#subUnit"+substr_id).val();
						qty = $("#quantity"+substr_id).val().trim();
						sp = $("#sellingPrice"+substr_id).val().trim();
						rp = $("#regularPrice"+substr_id).val().trim();
						ps = $("#productStatus"+substr_id).val();
					
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
						
					}
				} 
			}
			
		
			room++;
			
			var divtest = document.createElement("div");
			divtest.setAttribute("class", "form-group removeclassP" + room);
		
			$("#newRateAdd").empty().append('<div class="col-md-1 mb-3"><button type="button" data-id="'+room+'" data-flag="edit" class="btn btn-success add_price_fields" ><i class="fa fa-plus"></i></button></div>');
			
			var elements = '<div class="form-row mb-1">';
			elements +=	'<div class="col-md-4 mb-2"><select class="form-control cityCode" id="cityCode'+room+'" name="cityCode[]" required onchange="checkDuplicateProduct('+room+')">'+cityData+'</select></div>';
			elements += '<div class="col-md-4 mb-2"><select class="form-control subUnit" name="subUnit[]" id="subUnit'+room+'" required onchange="checkDuplicateProduct('+room+')">'+subUnitData+'</select></div>';
			elements += '<div class="col-md-3 mb-2"><input type="text" class="form-control quantity" id="quantity'+room+'" name="quantity[]" placeholder="Quantity" required onchange="checkDuplicateProduct('+room+')"></div>';
			elements +=	'</div><div class="form-row mb-3">';
			elements += '<div class="col-md-3 mb-2"><input type="number" step="0.01" class="form-control regularPrice" id="regularPrice'+room+'" name="regularPrice[]" required  placeholder="Regular Price (0.00)"></div>'
			elements += '<div class="col-md-3 mb-2"><input type="number" step="0.01" class="form-control sellingPrice" id="sellingPrice'+room+'" name="sellingPrice[]" required placeholder="Selling Price (0.00)"></div>'
			elements += '<div class="col-md-3 mb-2"><select class="form-control productStatus" required id="productStatus'+room+'" name="productStatus[]" required><option>Select Status</option><option value="AVL">Available</option><option value="OOS">Out Of Stock</option></select></div>';
			elements += '<div class="col-md-1 mb-2"><button type="button" class="btn btn-danger remove_price_fields"  data-id="' + room + '"><i class="fa fa-trash"></i></button></div>';
			elements +=	'</div>'; 
			divtest.innerHTML = elements;  
			objTo.appendChild(divtest); 
			//room++;  
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
	
	function remove_benefits_fields(rid, flag, code) {
		if (flag == 'edit') {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this benefit record!",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				cancelButtonText: "No, cancel please!",
				closeOnConfirm: !1,
				closeOnCancel: !1
			}, function(e) {
				console.log(e);
				if (e) {
					$.ajax({
						url: base_path + "Product/deleteLineRecord",
						type: 'POST',
						data: {
							'code': code
						},
						success: function(data) {
							//console.log(data);
							if (data != 'false') {
								swal({
									title: "Completed",
									text: "Successfully Deleted",
									type: "success"
								}, function(isConfirm) {
									if (isConfirm) {
										$('.removeclass' + rid).remove();
									}
								});
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							var errorMsg = 'Ajax request failed: ' + xhr.responseText;
							alert(errorMsg);
							console.log("Ajax Request for patient data failed : " + errorMsg);
						}
					});
				} else {
					swal("Cancelled", "Your benefit record is safe!", "error");
				}
			});
		} else {
			$('.removeclass' + rid).remove();
		}
	}
	function remove_price_fields(rid, flag, code) {
		if (flag == 'edit') {
			swal({
				title: "Are you sure?",
				text: "You will not be able to recover this Price record!",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, delete it!",
				cancelButtonText: "No, cancel please!",
				closeOnConfirm: !1,
				closeOnCancel: !1
				}, function(e) {
				console.log(e);
				if (e) {
					$.ajax({
						url: base_path + "Product/deletePriceRecord",
						type: 'POST',
						data: {
							'code': code
						},
						success: function(data) {
							//console.log(data);
							if (data != 'false') {
								swal({
									title: "Completed",
									text: "Successfully Deleted",
									type: "success"
								}, function(isConfirm) {
									if (isConfirm) {
										$('.removeclassP' + rid).remove();										 
									}
								});
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							var errorMsg = 'Ajax request failed: ' + xhr.responseText;
							alert(errorMsg);
							console.log("Ajax Request for patient data failed : " + errorMsg);
						}
					});
				} else {
					swal("Cancelled", "Your benefit record is safe :)", "error");
				}
			});
		} else {
			$('.removeclassP' + rid).remove();
		}
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
	$("body").delegate("#productUom","focus",function() {
		org_unit = $(this).val();
	});
	
	$("body").delegate("#productUom","change",function(){
		var unit = $(this).val();
		var productCode = $("#code").val();
		if(unit!=""){
			var html = $("#price_fields").html().trim();
			if(html.length == 0){
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
						$.ajax({
							type:"get",
							url :base_path+'Product/delete_all_rate_entries',
							data:{productCode:productCode},
							success:function(response){
								swal.close();
								if(response) {
									$("#price_fields").empty();
									get_sub_units(unit); 
								} else{
									swal("City & Price Variants", "Failed to remove variants. Please try again later", "error"); 
								}
							},error:function(error){
								console.log("Ajax Faile to get sub unit list");
							}
						});
						
					} else {
						$("#productUom").val(org_unit);
						swal("City & Price Variants", "Price Varients are safe :)", "error"); 
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
	
	$("body").delegate(".add_price_fields","click",function(){
		var pos = $(this).data('id');
		var flag = $(this).data('flag');
		price_fields(pos,flag);
	});
	
	$("body").delegate(".remove_price_fields","click",function() {
		var remove_room_id = $(this).data('id');
		$(".removeclassP" + remove_room_id).remove();
		//$("#newRateAdd").empty().append('<div class="col-md-1 mb-3"><button type="button" data-id="'+(remove_room_id-1)+'" class="btn btn-success add_price_fields" ><i class="fa fa-plus"></i></button></div>');
	});
	
	$("body").delegate("#productUom","focus",function(){
		org_unit = $(this).val();
	});
	$("body").delegate(".regularPrice","change",function(){
		 debugger;
		var rp = $(this).val().trim();
		var rp_id= $(this).attr('id');
		var sp = $('#sellingPrice'+rp_id[rp_id.length -1]).val().trim();
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
				$('#sellingPrice'+rp_id[rp_id.length -1]).val('');
				$("#"+rp_id).focus();
			}
		}
	});
	$("body").delegate(".sellingPrice","change",function(){
		var sp = $(this).val().trim();
		var sp_id= $(this).attr('id');
		var rp= $('#regularPrice'+sp_id[sp_id.length -1]).val().trim();
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
				$('#regularPrice'+sp_id[sp_id.length -1]).val('');
				$("#"+sp_id).focus();
			}
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
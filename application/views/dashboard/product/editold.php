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
						<div class="col-md-6 mb-3">
							<label for="productRegularPrice">Product Regular Price : </label>
							<input type="number" id="productRegularPrice" name="productRegularPrice" value="<?= $row->productRegularPrice; ?>" class="form-control">
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<div class="form-row">					  
						<div class="col-md-6 mb-3">
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
						</div>
					</div>
					<label for="productBenefit">Product Benefits: </label>
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
					<label for="price_fields">Product Selling Price: </label>
					<div class="form-row">
						<div id="price_fields">
							<?php
							$j = 1;
							foreach ($prices->result() as $line) { ?>
								<div class="form-row removeclassP<?php echo $j ?>">
									<input type="hidden" name="lineCode[]" id="lineCode" value="<?= $line->code ?>">
									<div class="col-md-4">
										<select class="form-control cityCode" required name="cityCode[]" id="cityCode<?= $j ?>">
											<option value="">Select City</option>
											<?php
											if ($citymaster) {
												foreach ($citymaster->result_array() as $r) {
													echo '<option value="' . $r['code'] . '">' . $r['cityName'] . '</option>';
												}
											}
											?>
										</select>
										<script>
											var cityCode = "<?= $line->cityCode ?>";
											$("#cityCode<?= $j ?>").val(cityCode);
										</script>
									</div>
									<div class="col-md-4">
										<input type="number" id="sellingPrice<?= $j ?>" required name="sellingPrice[]" value="<?= $line->sellingPrice ?>" class="form-control sellingPrice" placeholder="Selling Price (0.00)">
									</div>
									<div class="col-md-3">
										<select class="form-control" required id="productStatus<?= $j ?>" required name="productStatus[]">
											<option>Select Status</option>
											<option value="AVL">Available</option>
											<option value="OOS">Out Off Stock</option>
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
							<?php $j += 1;
							} ?>
						</div>
					</div>
					<div class="row" id="newRateAdd">
						<div class="col-md-1 mb-3">
							<button class="btn btn-success" type="button" onclick="price_fields(<?php echo $j; ?>,'edit');"><i class="fa fa-plus"></i></button>
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
	$("body").delegate(".sellingPrice","change",function() {
		var productRegularPrice = $("#productRegularPrice").val();
		var productSellingPrice = $(this).val();
		if (parseInt(productRegularPrice) < parseInt(productSellingPrice)) {
			alert('Regular Price must be higher than the Selling Price');
			$(this).val('');
		}
	});
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
	function benefits_fields(count, flag) {
		if (flag == 'edit') {
			if (room == 1) {
				// alert(count);
				room = count;
			}
		}
		var objTo = document.getElementById('benefits_fields')
		var divtest = document.createElement("div");
		divtest.setAttribute("class", "form-group removeclass" + room);
		var rdiv = 'removeclass' + room;
		var htmlrow = $("#ratesAdd").html();
		if (flag == 'edit') {
			divtest.innerHTML = '<div class="form-row"><div class="col-md-10"> <div class="form-group"> <input type="text" id="productBenefit' + room + '" name="productBenefitAdd[]" class="form-control" > </div> </div> <div class="col-md-2"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_benefits_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div>';
		} else {
			divtest.innerHTML = ' <div class="form-row"><div class="col-md-10"> <div class="form-group"> <input type="text" id="productBenefit' + room + '" name="productBenefit[]" class="form-control" > </div> </div> <div class="col-md-2"> <div class="form-group"> <button class="btn btn-danger" type="button" onclick="remove_benifits_fields(' + room + ');"> <i class="fa fa-minus"></i> </button> </div> </div></div>';
		}
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
		divtest.setAttribute("class", "removeclassP" + room2);
		var rdiv = 'removeclassP' + room2;
		var citydata = $("#cityCode").html();
		if (flag == 'edit') {
			divtest.innerHTML = '<div class="form-row"><div class="col-md-4"><select class="form-control cityCode" id="cityCode' + room2 + '" name="cityCode[]" required>' + citydata + '</select></div><div class="col-md-4"><input class="form-control sellingPrice" name="sellingPrice[]" id="sellingPrice' + room2 + '" placeholder="selling Price (0.00)"></div><div class="col-md-3"><select class="form-control" id="productStatus' + room2 + '" name="productStatus[]" required><option value="">Select Status</option><option value="AVL">Available</option><option value="OOS">Out Off Stock</option></select></div><div class="col-md-1"><button class="btn btn-danger" onclick="remove_price_fields(' + room2 + ');"><i class="fa fa-trash"></i></button></div></div>';
		} else {
			divtest.innerHTML = '<div class="form-row"><div class="col-md-4"><select class="form-control cityCode" id="cityCode' + room2 + '" name="cityCode[]" required>' + citydata + '</select></div><div class="col-md-4"><input class="form-control sellingPrice" name="sellingPrice[]" id="sellingPrice' + room2 + '" placeholder="selling Price (0.00)"></div><div class="col-md-3"><select class="form-control" id="productStatus' + room2 + '" name="productStatus[]" required><option value="">Select Status</option><option value="AVL">Available</option><option value="OOS">Out Off Stock</option></select></div><div class="col-md-1"><button class="btn btn-danger" onclick="remove_price_fields(' + room2 + ');"><i class="fa fa-trash"></i></button></div></div>';
		}
		objTo.appendChild(divtest)
		room2++;
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
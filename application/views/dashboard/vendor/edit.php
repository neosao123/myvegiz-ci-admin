<style>
	#myMap {
		height: 300px;
		width: 100%;
	}

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
		background: #588002
	}

	.select2-container--default .select2-selection--multiple .select2-selection__choice>span {
		color: white !important;
		forn-weight: bold
	}

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
		background-color: rgba(0, 0, 0, .7);
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
				<h4 class="page-title">Vendor</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Edit Vendor</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid col-md-8">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Change In Vendor</h4>
				<hr />

				<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url() . 'index.php/Vendor/update'; ?>" novalidate>
					<?php foreach ($query->result() as $row) {  ?>

						<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message)) {
							echo '<b>' . $error_message . '</b>';
						}
						echo "</div>";
						?>

						<div class="form-row">
							<div class="col-md-12 mb-3">

								<label for="code">Code :<b style="color:red">*</b></label>

								<input type="text" id="code" name="code" value="<?= $row->code ?>" class="form-control" readonly>

								<div class="invalid-feedback">
									Required Field!
								</div>

							</div>
						</div>

						<div class="form-row">
							<div class="col-md-4 mb-3">
								<label for="firstName">First Name :<b style="color:red">*</b></label>
								<input type="text" id="firstName" name="firstName" value="<?= $row->firstName ?>" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="middleName">Middle Name : </label>
								<input type="text" id="middleName" name="middleName" value="<?= $row->middleName ?>" class="form-control">
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="lastName">Last Name :<b style="color:red">*</b></label>
								<input type="text" id="lastName" name="lastName" value="<?= $row->lastName ?>" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-7 mb-3">
								<label for="entityName">Entity Name : <b style="color:red">*</b></label>
								<input type="text" id="entityName" name="entityName" value="<?= $row->entityName ?>" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							<div class="col-md-5 mb-3">
								<label for="entityImage"> Entity Image : </label>
								<div class="controls">
									<input type="file" id="entityImage" name="entityImage" class="form-control" onchange="document.getElementById('entityImageShow').src = window.URL.createObjectURL(this.files[0])">
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-4 mb-3">
								<label for="ownerContact">Owner Contact :<b style="color:red">*</b></label>
								<input type="text" id="ownerContact" name="ownerContact" class="form-control" required value="<?= $row->ownerContact ?>">
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="entityContact">Entity Contact : </label>
								<input type="text" id="entityContact" name="entityContact" class="form-control" value="<?= $row->entityContact ?>">
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>

							<div class="col-md-4 mb-3">
								<label for="entitycategoryCode">Entity Category :<b style="color:red">*</b></label>
								<select id="entitycategoryCode" name="entitycategoryCode" class="form-control" required>
									<option value="">Select Category</option>
									<?php if ($entitycategory) {
										foreach ($entitycategory->result() as $curren) {
											echo '<option value="' . $curren->code . '">' . $curren->entityCategoryName . '</option>';
										}
									} ?>
								</select>
								<div class="invalid-feedback">
									Required Field!
								</div>
								<script>
									var entitycategoryCode = "<?= $row->entitycategoryCode ?>";
									$("#entitycategoryCode").val(entitycategoryCode);
								</script>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="cityCode">City:<b style="color:red">*</b></label>
								<select id="cityCode" name="cityCode" class="form-control" required>
									<option value="">Select City</option>
									<?php
									if ($city) {
										foreach ($city->result() as $ra) {
											$select = $ra->code == $row->cityCode ? 'selected' : '';
											echo '<option value="' . $ra->code . '" ' . $select . '>' . $ra->cityName . '</option>';
										}
									}
									?>
								</select>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label for="addressCode">Area:<b style="color:red">*</b></label>
								<select id="addressCode" name="addressCode" class="form-control" required>
									<?php
									if ($customaddress) {
										echo '<option value="">Select Area</option>';
										foreach ($customaddress->result() as $rb) {
											$select = $rb->code == $row->addressCode ? 'selected' : '';
											echo '<option value="' . $rb->code . '" ' . $select . '>' . $rb->place . '</option>';
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
							<div class="col-md-12 mb-3">
								<label for="address">Address : <b style="color:red">*</b></label>
								<textarea type="text" id="address" name="address" class="form-control" required><?= $row->address ?></textarea>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="fssaiNumber"> FSSAI Number : <b style="color:red">*</b></label>
								<input type="text" id="fssaiNumber" name="fssaiNumber" value="<?= $row->fssaiNumber ?>" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label for="fssaiImage"> FSSAI Image :</label>
								<div class="controls">
									<input type="file" id="fssaiImage" name="fssaiImage" class="form-control" onchange="document.getElementById('fssaiImageShow').src = window.URL.createObjectURL(this.files[0])">
								</div>
							</div>
						</div>

						<div class="form-row mb-3">
							<div id="myMap">
							</div>
						</div>

						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="latitude"> Latitude : <b style="color:red">*</b></label>
								<input type="number" id="latitude" name="latitude" value="<?= $row->latitude ?? "18.533023" ?>" step="any" class="form-control required" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>

							<div class="col-md-6 mb-3">
								<label for="longitude"> Longitude : <b style="color:red">*</b></label>
								<input type="number" id="longitude" name="longitude" value="<?= $row->longitude ?? "73.834768" ?>" step="any" class="form-control required" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>

							<div class="col-md-12 mb-3">
								<label for="cuisineCode">Cuisines you serve : <b style="color:red">*</b></label>
								<select class="form-control js-example-responsive" name="cuisineCode[]" required id="cuisineCode" multiple="multiple" data-border-color="primary" data-border-variation="accent-2" required style="width:100%">
									<?php
									if ($cuisines) {
										foreach ($cuisines->result_array() as $c) {
											echo '<option value="' . $c['code'] . '">' . $c['cuisineName'] . '</option>';
										}
									}
									?>
								</select>
								<div class="invalid-feedback">
									Required Field!
								</div>
								<script>
									var cuisineslines = [];
									<?php
									if ($cuisineslines) {
										foreach ($cuisineslines->result() as $ven) {
									?>
											cuisineslines.push("<?php echo $ven->cuisineCode ?>");
									<?php
										}
									}
									?>
									$("#cuisineCode").val(cuisineslines);
								</script>
							</div>
						</div>

						<div class="form-row">
							<?php if ($row->entityImage != "") { ?>
								<div class="el-element-overlay">
									<div class="col-md-4 mb-3">
										<div class="card">
											<div class="el-card-item">
												<div class="el-card-avatar el-overlay-1"> <img id="entityImageShow" src="<?php echo base_url() . 'uploads/vendor/' . $row->code . '/' . $row->entityImage . '?' . time(); ?>" alt="product photo File">
													<label for="entityImage" class="mt-3"> Entity Image :</label>
													<div class="el-overlay">
														<ul class="list-style-none el-info">
															<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo base_url() . 'uploads/vendor/' . $row->code . '/' . $row->entityImage . '?' . time(); ?>" target="_blank"><i class="icon-magnifier"></i></a></li>
															<li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('entityImage','<?= $row->code; ?>','<?= $row->entityImage; ?>')"><i class="icon-trash"></i></a></li>
														</ul>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>
							<?php } ?>

							<?php if ($row->fssaiImage != "") { ?>
								<div class="el-element-overlay">
									<div class="col-md-4 mb-3">
										<div class="card">
											<div class="el-card-item">
												<div class="el-card-avatar el-overlay-1"> <img id="fssaiImageShow" src="<?php echo base_url() . 'uploads/vendor/' . $row->code . '/' . $row->fssaiImage . '?' . time(); ?>" alt="product photo File">
													<label for="fssaiImage" class="mt-3"> FSSAI Image :</label>
													<div class="el-overlay">
														<ul class="list-style-none el-info">
															<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo base_url() . 'uploads/vendor/' . $row->code . '/' . $row->fssaiImage . '?' . time(); ?>" target="_blank"><i class="icon-magnifier"></i></a></li>
															<li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('fssaiImage','<?= $row->code; ?>','<?= $row->fssaiImage; ?>')"><i class="icon-trash"></i></a></li>
														</ul>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>

						<h4> Configuration Settings </h4>
						<div class="row">
							<div class="col-md-7 mb-3">
								<label>Delivery Packaging Type : <b style="color:red">*</b></label>
								<div class="custom-control custom-radio">
									<input type="radio" id="productPacking" name="packagingType" value="PRODUCT" <?= $row->packagingType == "PRODUCT" ? 'checked' : '' ?> class="custom-control-input">
									<label class="custom-control-label" for="productPacking">Product Wise</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="cartPacking" name="packagingType" value="CART" <?= $row->packagingType == "CART" ? 'checked' : '' ?> class="custom-control-input">
									<label class="custom-control-label" for="cartPacking">Cart Wise</label>
								</div>
							</div>
							<div class="col-md-5 mb-3" id="cartPack" style="display:<?= $row->packagingType == "CART" ? 'block' : 'none' ?>">
								<label for="cartPackagingPrice">Delivery Packing Price <b style="color:red">*</b></label>
								<input type="number" id="cartPackagingPrice" maxlength="3" name="cartPackagingPrice" class="form-control" <?= $row->packagingType == "CART" ? 'required' : '' ?> value="<?= $row->cartPackagingPrice ?>">
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-7 mb-3">
								<label>GST Applicable Type : </label>
								<div class="custom-control custom-radio">
									<input type="radio" id="gstApplicableNo" name="gstApplicable" value="NO" <?= $row->gstApplicable == "NO" ? 'checked' : '' ?> class="custom-control-input">
									<label class="custom-control-label" for="gstApplicableNo">No (Not Applicable)</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="gstApplicableYes" name="gstApplicable" value="YES" <?= $row->gstApplicable == "YES" ? 'checked' : '' ?> class="custom-control-input">
									<label class="custom-control-label" for="gstApplicableYes">Yes (Applicable)</label>
								</div>
							</div>

							<div class="col-md-5 mb-3" id="gstPercentDiv" style="display:<?= $row->gstApplicable == "YES" ? 'block' : 'none' ?>">
								<label for="gstPercent">GST (%)</label>
								<input type="number" step="0.01" maxlength="4" id="gstPercent" maxlength="3" name="gstPercent" class="form-control" <?= $row->gstApplicable == "YES" ? 'required' : '' ?> value="<?= $row->gstPercent ?>">
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label for="gstNumber"> GST Number :</label>
								<div class="controls">
									<input type="text" id="gstNumber" name="gstNumber" value="<?= $row->gstNumber ?>" class="form-control" <?= $row->gstApplicable == "YES" ? 'required' : '' ?>>
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label for="gstImage"> GST Image :</label>
								<div class="controls">
									<input type="file" id="gstImage" name="gstImage" class="form-control" onchange="document.getElementById('gstImageShow').src = window.URL.createObjectURL(this.files[0])">
								</div>
							</div>
							<?php if ($row->gstImage != "") { ?>
								<div class="el-element-overlay">
									<div class="col-md-4 mb-3">
										<div class="card">
											<div class="el-card-item">
												<div class="el-card-avatar el-overlay-1"> <img id="gstImageShow" src="<?php echo base_url() . 'uploads/vendor/' . $row->code . '/' . $row->gstImage . '?' . time(); ?>" alt="product photo File">
													<label for="gstImage" class="mt-3"> GST Image :</label>
													<div class="el-overlay">
														<ul class="list-style-none el-info">
															<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo base_url() . 'uploads/vendor/' . $row->code . '/' . $row->gstImage . '?' . time(); ?>" target="_blank"><i class="icon-magnifier"></i></a></li>
															<li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('gstImage','<?= $row->code; ?>','<?= $row->gstImage; ?>')"><i class="icon-trash"></i></a></li>
														</ul>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>


						<?php
						$bankDetails = $row->bankDetails;
						$beneficiaryName = $bankName = $accountNumber = $ifscCode = "";
						if ($bankDetails != "") {
							$bankDetails = json_decode($bankDetails);
							$beneficiaryName = $bankDetails->beneficiaryName;
							$bankName = $bankDetails->bankName;
							$accountNumber = $bankDetails->accountNumber;
							$ifscCode = $bankDetails->ifscCode;
						}
						?>
						<h4> Bank Details </h4>
						<div class="form-row">
							<div class="col-md-6 mb-3">
								<label for="beneficiaryName"> Beneficiary Name : </label>
								<input type="text" id="beneficiaryName" name="beneficiaryName" value="<?= $beneficiaryName ?>" class="form-control">
							</div>

							<div class="col-md-6 mb-3">
								<label for="bankName"> Name of Bank :</label>
								<input type="text" id="bankName" name="bankName" value="<?= $bankName ?>" class="form-control">
							</div>

							<div class="col-md-6 mb-3">
								<label for="accountNumber"> Account Number :</label>
								<input type="number" id="accountNumber" name="accountNumber" value="<?= $accountNumber ?>" class="form-control">
							</div>

							<div class="col-md-6 mb-3">
								<label for="ifscCode"> IFSC Code :</label>
								<input type="text" id="ifscCode" name="ifscCode" value="<?= $ifscCode ?>" class="form-control">
							</div>
						</div>

						<div class="form-group">
							<div class="custom-control custom-checkbox mr-sm-2">
								<!--<label for="address">Active Status :</label>-->
								<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive' <?= $row->isActive == "1" ? 'checked' : '' ?>>
								<label class='custom-control-label' for='isActive'>Active</label>
							</div>

							<div class="custom-control custom-checkbox mr-sm-2">
								<!-- <label for="address">Serviceable Status :</label>-->
								<input type='checkbox' value='1' class='custom-control-input' id='isServiceable' name='isServiceable' <?= $row->manualIsServiceable == "1" ? 'checked' : '' ?>>
								<label class='custom-control-label' for='isServiceable'>Serviceable</label>
							</div>
							<div class="custom-control custom-checkbox mr-sm-2">
								<!--<label for="address">Popular :</label>-->
								<input type='checkbox' value='1' class='custom-control-input' id='isPopular' name='isPopular' <?= $row->isPopular == "1" ? 'checked' : '' ?>>
								<label class='custom-control-label' for='isPopular'>Popular</label>
							</div>
						</div>

						<div class="form-row">
							<input type="hidden" id="entityImageOld" name="entityImageOld" value="<?= $row->entityImage ?>" class="form-control" readonly>
							<input type="hidden" id="fssaiImageOld" name="fssaiImageOld" value="<?= $row->fssaiImage ?>" class="form-control" readonly>
							<input type="hidden" id="gstImageOld" name="gstImageOld" value="<?= $row->gstImage ?>" class="form-control" readonly>
						</div>
						<?php
						if ($tags->num_rows() > 0) {
							$str1 = '';
							if ($row->tagCode == NULL || $row->tagCode == '') {
								$str1 = 'checked';
							}
						?>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-2 mb-1">
										<h6><b> Tags:</b></h6>
									</div>
								</div>
								<div class="row" style="margin-left:35px;">
									<div class="col-sm-3 mb-3">
										<div class="custom-control custom-radio custom-control-inline">
											<input type="radio" class="custom-control-input" <?= $str1 ?> id="tagSection" name="tagCode" value="1">
											<label class="custom-control-label" for="tagSection"><b>No Tag</b></label>
										</div>
									</div>
									<?php
									foreach ($tags->result_array() as $tag) {
										$str = '';
										if ($tag['code'] == $row->tagCode) {
											$str = 'checked';
										}
									?>
										<div class="col-sm-3 mb-3">
											<div class="custom-control custom-radio custom-control-inline">
												<input type="radio" class="custom-control-input" id="tagSection<?= $tag['code'] ?>" <?= $str ?> name="tagCode" value="<?= $tag['code'] ?>">
												<label class="custom-control-label" style="color:<?= $tag['tagColor'] ?>" for="tagSection<?= $tag['code'] ?>"><b><?= $tag['tagTitle'] ?></b></label>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
					<div class="text-xs-right">
						<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
						<button type="Reset" class="btn btn-reset">Reset</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key=<?= PLACE_API_KEY ?>"></script>
<script type="text/javascript">
	function deleteButton(type, code, value) {
		$.ajax({
			url: base_path + 'Vendor/deleteImage',
			method: "POST",
			data: {
				'value': value,
				'code': code,
				'type': type,
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

	var map;
	var marker;
	var latdata = $('#latitude').val();
	var lngdata = $('#longitude').val();

	// var myLatlng = new google.maps.LatLng(20.268455824834792,85.84099235520011);
	var myLatlng = new google.maps.LatLng(latdata, lngdata);
	var geocoder = new google.maps.Geocoder();
	var infowindow = new google.maps.InfoWindow();

	function initialize() {
		var mapOptions = {
			zoom: 18,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		map = new google.maps.Map(document.getElementById("myMap"), mapOptions);

		marker = new google.maps.Marker({
			map: map,
			position: myLatlng,
			draggable: true
		});

		geocoder.geocode({
			'latLng': myLatlng
		}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					$('#latitude,#longitude').show();
					$('#address').val(results[0].formatted_address);
					$('#latitude').val(marker.getPosition().lat());
					$('#longitude').val(marker.getPosition().lng());
					infowindow.setContent(results[0].formatted_address);
					infowindow.open(map, marker);
				}
			}
		});

		google.maps.event.addListener(marker, 'dragend', function() {
			geocoder.geocode({
				'latLng': marker.getPosition()
			}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						$('#address').val(results[0].formatted_address);
						$('#latitude').val(marker.getPosition().lat());
						$('#longitude').val(marker.getPosition().lng());
						infowindow.setContent(results[0].formatted_address);
						infowindow.open(map, marker);
					}
				}
			});
		});
	}
	google.maps.event.addDomListener(window, 'load', initialize);
	 

	$('input[type=number]').on('mousewheel', function(e) {
		$(this).blur();
	});
	// Disable keyboard scrolling
	$('input[type=number]').on('keydown', function(e) {
		var key = e.charCode || e.keyCode;
		// Disable Up and Down Arrows on Keyboard
		if (key == 38 || key == 40) {
			e.preventDefault();
		} else {
			return;
		}
	});
	$(document).ready(function(e) {
		$("#cuisineCode").select2();
	});
	$("body").on("change", "input[name=packagingType]", function(e) {
		if ($(this).is(":checked")) {
			var thisVal = $(this).val();
			if (thisVal == "CART") {
				$("#cartPack").show();
				$("#cartPackagingPrice").attr("required", true);
			} else {
				$("#cartPack").hide();
				$("#cartPackagingPrice").removeAttr("required");
				$("#cartPackagingPrice").val(0);
			}
		}
	});
	$("body").on("change", "input[name=gstApplicable]", function(e) {
		if ($(this).is(":checked")) {
			var thisVal = $(this).val();
			if (thisVal == "YES") {
				$("#gstPercentDiv").show();
				$("#gstPercent").attr("required", true);
				$("#gstNumber").attr("required", true);
			} else {
				$("#gstPercentDiv").hide();
				$("#gstPercent").removeAttr("required");
				$("#gstPercent").val(0);
				$("#gstNumber").removeAttr("required");
			}
		}
	});
	$("body").on("change", "#cityCode", function(e) {
		var this_code = $(this).val().trim();
		if (this_code != undefined || this_code != "") {
			$.ajax({
				url: base_path + 'index.php/Vendor/getAddressess',
				data: {
					'cityCode': this_code
				},
				type: 'POST',
				success: function(response) {
					if (response != undefined || response != "") {
						$("#addressCode").empty();
						$("#addressCode").append(response);
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
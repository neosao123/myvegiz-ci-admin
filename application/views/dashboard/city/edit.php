 <div class="page-wrapper">
 	<div class="page-breadcrumb">
 		<div class="row">
 			<div class="col-12 align-self-center">
 				<h4 class="page-title">City</h4>
 				<div class="d-flex align-items-center">
 					<nav aria-label="breadcrumb">
 						<ol class="breadcrumb">
 							<li class="breadcrumb-item"><a href="#">Home</a></li>
 							<li class="breadcrumb-item active" aria-current="page">Change In City</li>
 						</ol>
 					</nav>
 				</div>
 			</div>
 		</div>
 	</div>
 	<div class="container-fluid col-md-6">
 		<div class="card">
 			<div class="card-body">
 				<h4 class="card-title">Change In City</h4>
 				<hr />
 				<form class="needs-validation" method="post" id="Uom" action="<?php echo base_url() . 'City/update'; ?>" novalidate>
 					<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message)) echo $error_message;
						echo "</div>";
						?>
 					<?php
						foreach ($query->result() as $row) {
						?>
 						<input type="hidden" name="code" value="<?php echo $row->code ?>">
 						<div class="form-row">
 							<div class="col-md-12 mb-3">
 								<label for="cityName">City Name : <b style="color:red">*</b></label>
 								<input type="text" id="cityName" name="cityName" value="<?php echo $row->cityName ?>" class="form-control" required>
 								<div class="invalid-feedback">
 									Required Field!
 								</div>
 							</div>
 						</div>
 						<h4 class="card-title d-none"><u>Per Order Delivery Cost</u> :</h4>
 						<div class="form-row d-none">
 							<label for="minOrder" class="col-md-4 mb-3">Min Order Amount <br> (For Delivery Charges) :</label>
 							<div class="col-md-4 mb-3">
 								<input type="number" id="minOrder" name="minOrder" value="<?= $row->minOrder ?>" class="form-control">
 								<div class="invalid-feedback">
 									Required Field!
 								</div>
 							</div>
 							<div class="col-md-4 mb-3">
 								<select id="minOrderCurrency" name="minOrderCurrency" class="form-control">
 									<option value="">Select Currency</option>
 									<?php
										foreach ($currency->result() as $curren) {
											echo '<option value="' . $curren->currencySName . '">' . $curren->currencySName . '</option>';
										}
										?>
 								</select>
 								<script>
 									var minOrderCurrency = "<?= $row->minOrderCurrency ?>";
 									$("#minOrderCurrency").val(minOrderCurrency);
 								</script>
 							</div>
 						</div>
 						<div class="form-row d-none">
 							<label for="minimumFreeDeliveryKM" class="col-md-4 mb-3">Default Delivery Km's: </label>
 							<div class="col-md-4 mb-3">
 								<input type="text" id="minFreeDeliveryKm" name="minFreeDeliveryKm" class="form-control"  value="<?php echo $row->minFreeDeliveryKm ?>">
 								<div class="invalid-feedback">
 									Required Field !
 								</div>
 							</div>
 						</div>
 						<div class="form-row d-none">
 							<label for="deliveryCharge" id="deliveryCharge" class="col-md-4 mb-3">Default Delivery Charge ( < <?= $row->minOrder ?> Rs Order Amount) : </label>
 									<div class="col-md-4 mb-3">
 										<input type="number" id="deliveryCharge" name="deliveryCharge" value="<?= $row->deliveryCharge ?>" class="form-control">
 										<div class="invalid-feedback">Required Field!</div>
 									</div>
 									<div class="col-md-4 mb-3">
 										<select id="deliveryChargeCurrency" name="deliveryChargeCurrency" class="form-control">
 											<option value="">Select Currency</option>
 											<?php foreach ($currency->result() as $curren) {
													echo '<option value="' . $curren->currencySName . '">' . $curren->currencySName . '</option>';
												} ?>
 										</select>
 										<script>
 											var deliveryChargeCurrency = "<?= $row->deliveryChargeCurrency ?>";
 											$("#deliveryChargeCurrency").val(deliveryChargeCurrency);
 										</script>
 									</div>
 						</div>
 						<div class="form-row d-none">
 							<label for="deliveryChargesPerKm" class="col-md-4 mb-3">Delivery Charges Per KM: </label>
 							<div class="col-md-4 mb-3">
 								<input type="text" id="deliveryChargesPerKm" name="deliveryChargesPerKm" class="form-control" value="<?php echo $row->deliveryChargesPerKm ?>">
 							</div>
 						</div>						
 						<div class="form-row mb-3">
						   
						     <label>Vegetables & Grocery Pickup Point :</label>	
								<div id="myMap" style="height: 300px;width: 680px;">
								</div>
						   
 						</div>
 						<div class="form-row">
 							<div class="col-md-6 mb-3">
 								<label for="latitude"> Latitude : <b style="color:red">*</b></label>
 								<input type="number" id="latitude" name="latitude" value="<?= $row->latitude ?? 18.533023 ?>" step="any" class="form-control required" required>
 								<div class="invalid-feedback">
 									Required Field!
 								</div>
 							</div>
 							<div class="col-md-6 mb-3">
 								<label for="longitude"> Longitude : <b style="color:red">*</b></label>
 								<input type="number" id="longitude" name="longitude" value="<?= $row->longitude ?? 73.834768 ?>" step="any" class="form-control required" required>
 								<div class="invalid-feedback">
 									Required Field!
 								</div>
 							</div>
 						</div>
 						<div class="form-group">
 							<div class="custom-control custom-checkbox mr-sm-2">
 								<div class="custom-control custom-checkbox">
 									<?php
										if ($row->isActive == "1") {
											echo "<input type='checkbox'   value='1' class='custom-control-input' id='isActive' name='isActive' checked>
												<label class='custom-control-label' for='isActive'>Active</label>";
										} else {
											echo "<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive'>
												<label class='custom-control-label' for='isActive'>Active</label>";
										}
										?>
 								</div>
 							</div>
 						</div>
 						<div class="text-xs-right">
 							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
 							<a href="<?php echo base_url() . 'index.php/City/listRecords'; ?>">
 								<button type="button" id="backUom" class="btn btn-reset"> Back</button>
 							</a>
 						</div>
 					<?php
						}
						?>
 				</form>
 			</div>
 		</div>
 	</div>
 	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false&key=<?= PLACE_API_KEY ?>"></script>
 	<script type="text/javascript">
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
 						//$('#address').val(results[0].formatted_address);
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
 							//$('#address').val(results[0].formatted_address);
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

 		function ValidateAlpha(evt) {
 			var keyCode = (evt.which) ? evt.which : evt.keyCode
 			if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32) return false;
 			return true;
 		}

 		$('document').ready(function() {
 			$("#cityName").maxlength({
 				max: 50
 			});
 			$('#minOrder').change(function() {
 				var minOrder = $(this).val();
 				$('#deliveryCharge').html('Delivery Charge </br> ( < ' + minOrder + ' Rs Order Amount) :');
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
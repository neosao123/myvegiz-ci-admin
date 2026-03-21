<style>
#myMap {
   height: 300px;
   width: 680px;
}

 #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      #pac-input:focus {
        border-color: #4d90fe;
      }
</style> 
	<div class="page-wrapper">
    
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Address</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Change In Address</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
	
		<div class="container-fluid col-md-6">
		  
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Change In Address</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="Address" action="<?php echo base_url().'Address/update';?>" novalidate>

						<?php foreach($query->result() as $row)
							{
							?>
							
							<input type="hidden" name="code" value="<?php echo $row->code?>">
							<div class="form-row">
								<div class="col-md-12 mb-3">						
									<label for="place">City : <b style="color:red">*</b></label>							
									<div class="controls">
										<select  id="cityCode" name="cityCode" class="form-control" list="currentPlaceList"  required>
											<option value="">Select City</option>
											<?php
											if (isset($city)) {
												foreach ($city->result() as $c) {
													$selected = $c->code == $row->cityCode ? "selected" : "";
													echo '<option value="' . $c->code . '" '.$selected.'>' . $c->cityName . '</option>';
												}
											}
											?>
										</select>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-12 mb-3">
								
									<label for="place">Place : <b style="color:red">*</b></label>
									
									<div class="controls">
										<input type="text" id="place" name="place" class="form-control" value="<?= $row->place ?>" list="currentPlaceList"  required>
										<datalist id="currentPlaceList">
								
										</datalist>
									</div>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col-md-12 mb-3">
									
									<label for="Taluka">Taluka : <b style="color:red">*</b></label>
									
									<div class="controls">
										
										<input type="text" id="taluka" name="taluka" class="form-control" value="<?= $row->taluka ?>" list="currentTalukaList" required>
										<datalist id="currentTalukaList">
								
										</datalist>
										
									</div>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col-md-12 mb-3">
									
									<label for="District">District : <b style="color:red">*</b></label>
									
									<input type="text" id="district" name="district" class="form-control" value="<?= $row->district ?>" list="currentDistrictList" required>
									<datalist id="currentDistrictList">
							
									</datalist>
								
								</div>
							</div>
							
							<div class="form-row">
								<div class="col-md-12 mb-3">
								
									<label for="pincode">Pincode : <b style="color:red">*</b></label>
									
									<div class="controls">
										<input type="text" onkeypress="return validateFloatKeyPress(this, event, 5, -1);" id="pincode" name="pincode" class="form-control" value="<?= $row->pincode ?>" pattern="^[1-9][0-9]{5}$" required>
									 
									</div>
								</div>
							</div>
							
							<div class="form-row">
								<div class="col-md-12 mb-3">
									
									<label for="State">State : <b style="color:red">*</b></label>
									
									<input type="text" id="state" name="state" class="form-control" value="<?= $row->state ?>" list="currentStateList" required>
									<datalist id="currentStateList">
							
									</datalist>
									
									<div class="invalid-feedback">
										Required Field!
									</div>
							   
								</div>
							</div>
							
							<div class="form-row mb-3">
								<div id="myMap">
								</div>
							</div>
					
							<div class="form-row">
								<div class="col-md-4 mb-3">                            
									<label for="latitude">Latitude : <b style="color:red">*</b></label>                            
									<input type="number" step="any" id="latitude" name="latitude" class="form-control" value="<?= $row->latitude ?>" required>				
									<div class="invalid-feedback">
										Required Field !
									</div>							
								</div>
								<div class="col-md-4 mb-3">                            
									<label for="longitude">Longitude : <b style="color:red">*</b></label>                            
									<input type="number" step="any" id="longitude" name="longitude" class="form-control" value="<?= $row->longitude ?>" required>								
									<div class="invalid-feedback">
										Required Field !
									</div>							
								</div>
								<div class="col-md-4 mb-3">                            
									<label for="radius">Radius : <b style="color:red">*</b></label>                            
									<input type="number" step="any" id="radius" name="radius" class="form-control" value="<?= $row->radius ?>" required>				
									<div class="invalid-feedback">
										Required Field !
									</div>							
								</div>
							</div>
					
							<?php
								echo "<div class='text-danger text-center' id='error_message'>";
								if (isset($error_message))
								{
								echo $error_message;
							   }
								echo "</div>";
							?>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox">
									   <?php 
											if($row->isService == "1"){
												echo "<input type='checkbox'   value='1' class='custom-control-input' id='isService' name='isService' checked>
												<label class='custom-control-label' for='isService'>Service Available For address?</label>";
											}
											else{ 
												echo "<input type='checkbox' value='1' class='custom-control-input' id='isService' name='isService'>
												<label class='custom-control-label' for='isService'>Service Available For address?</label>";
											}
										?>
										
									</div>
								</div>
							</div>
							
							<div class="form-group">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox">
									  
										<?php 
											if($row->isActive == "1"){
												echo "<input type='checkbox'   value='1' class='custom-control-input' id='isActive' name='isActive' checked>
												<label class='custom-control-label' for='isActive'>Active</label>";
											}
											else{ 
												echo "<input type='checkbox' value='1' class='custom-control-input' id='isActive' name='isActive'>
												<label class='custom-control-label' for='isActive'>Active</label>";
											}
										?>
										
									</div>
								</div>
							</div>
							
							<div class="text-xs-right">
							
								<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
								
								<a href="<?php echo base_url().'Address/listRecords';?>">
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
		
  </div>
<script type="text/javascript">
	var lt = $('#latitude').val();
	var lg = $('#longitude').val();
	changeLocator(lt,lg);
	
	function changeLocator(lat,lng){
		var map;
		var marker;
		// var myLatlng = new google.maps.LatLng(16.691307,74.244865);
		var myLatlng = new google.maps.LatLng(lat,lng);
		var geocoder = new google.maps.Geocoder();
		var infowindow = new google.maps.InfoWindow();
		function initialize(){
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
			// console.log(marker);
			geocoder.geocode({'latLng': myLatlng }, function(results, status) {
				// console.log(results);
				// console.log('_______________________________________________________');
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						$('#latitude,#longitude').show();
						$('#address').val(results[0].formatted_address);
						// alert(results[0].address_components[0]['long_name']);
						$('#latitude').val(marker.getPosition().lat());
						$('#longitude').val(marker.getPosition().lng());
						infowindow.setContent(results[0].formatted_address); 
						infowindow.open(map, marker);
					}
				}
			});

			google.maps.event.addListener(marker, 'dragend', function() {
				geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
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
	}
	
	
	var finalAddress = ""; 
	$("body").delegate("#cityCode,#place,#taluka,#district,#pincode,#state","change",function() {
		var selectedText = $("#cityCode option:selected").html();
		var place = $('#place').val();
		var taluka = $('#taluka').val();
		var district = $('#district').val();
		var pincode = $('#pincode').val();
		// alert(selectedText+' '+place+' '+taluka+' '+district+' '+pincode); 
		finalAddress = "";
		// || district!="" || pincode!=""
		if(selectedText!=""){
			finalAddress = selectedText;
		}
		if(place!=""){
			finalAddress = finalAddress+'+'+place;
		}
		if(taluka!=""){
			finalAddress = finalAddress+'+'+taluka;
		}
		if(district!=""){
			finalAddress = finalAddress+'+'+district;
		}
		if(pincode!=""){
			finalAddress = finalAddress+'+'+pincode;
		}
		
		finalAddress=finalAddress.replace(/ /g,"+");
		 
		if(finalAddress != ""){
			// alert(finalAddress);
			$.getJSON({
				// url  : 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + marker.getPosition().lat() + ',' + marker.getPosition().lng() + '&result_type=country',
				url  : 'https://maps.googleapis.com/maps/api/geocode/json?address=' + finalAddress + '&key=AIzaSyBjAeJLrBzCPACXrH1tuW3JIQUr35t6QaI',
				data : {
					sensor  : false
				},
				success : function( results, textStatus ) {
					
					console.log(results['status']);
					console.log('_____________________________');
					if (results.hasOwnProperty("error_message")) {}else{
						console.log(results['results'][0]['geometry']['location']['lat']);
						var lat = results['results'][0]['geometry']['location']['lat'];
						console.log(results['results'][0]['geometry']['location']['lng']);
						var lng = results['results'][0]['geometry']['location']['lng'];
						changeLocator(lat,lng);
					}
				}
			});
		}
	
	});

</script>


	<script>
	
		$('document').ready(function(){
		 
				//place keyup to get old records
			$("#place").keyup(function(){
				if($(this).val().length > 3)
				{
					var current_place = $(this).val();
					$.ajax({
						url:'<?php echo site_url('address/getAllData'); ?>',
						method:"GET",
						data:{place:current_place},
						datatype:"text",
						success: function(data)
						{
						 
						  $("#currentPlaceList").html(data);
						}
					});
				}
			});
			
			//state keyup function 
			$("#state").keyup(function(){
				if($(this).val().length > 3)
				{
					var current_state = $(this).val();
					$.ajax({
						url:'<?php echo site_url('address/getStateData'); ?>',
						method:"GET",
						data:{state:current_state},
						datatype:"text",
						success: function(data)
						{
						 
						  $("#currentStateList").html(data);
						}
					});
				}
			});
			
			//district data keyup function
			$("#district").keyup(function(){
				if($(this).val().length > 3)
				{
					var current_dist = $(this).val();
					$.ajax({
						url:'<?php echo site_url('address/getDistData'); ?>',
						method:"GET",
						data:{district:current_dist},
						datatype:"text",
						success: function(data)
						{
						 
						  $("#currentDistrictList").html(data);
						}
					});
				}
			});
			
			
			//taluka keyup function 
			$("#taluka").keyup(function(){
				if($(this).val().length > 3)
				{
					var current_tal = $(this).val();
					$.ajax({
						url:'<?php echo site_url('address/getTalData'); ?>',
						method:"GET",
						data:{taluka:current_tal},
						datatype:"text",
						success: function(data)
						{
						 
						  $("#currentTalukaList").html(data);
						}
					});
				}
			});
		 
		}); // End Ready
		
		// Page Leave Yes / No
			
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
		 setTimeout(function()
                    {
                     $('#error_message').hide('fast');
                   },4000
			      );
	</script>
	
	<script>
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

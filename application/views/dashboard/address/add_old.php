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
                <h4 class="page-title">ADDRESS</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add New Address</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
	
    <div class="container-fluid col-md-6">    
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">New Address</h4>
                <hr/>				
                <form class="needs-validation" method="post" id="Address" action="<?php echo base_url() . 'Address/save'; ?>" novalidate>
					<div class="form-row">
                        <div class="col-md-12 mb-3">						
                            <label for="place">City : <b style="color:red">*</b></label>							
                            <div class="controls">
                                <select  id="cityCode" name="cityCode" class="form-control" list="currentPlaceList"  required>
									<option value="">Select City</option>
									<?php
									if (isset($city)) {
										foreach ($city->result() as $c) {
											echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
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
                                <input type="text" id="place" name="place" class="form-control" list="currentPlaceList"  required>
								<datalist id="currentPlaceList">
								</datalist>
                            </div>
                        </div>
                    </div>					
					<div class="form-row">
                        <div class="col-md-12 mb-3">						
                            <label for="taluka">Taluka : <b style="color:red">*</b></label>							
                            <div class="controls">
                                <input type="text" id="taluka" name="taluka" class="form-control" list="currentTalukaList" required>
								<datalist id="currentTalukaList">								
								</datalist>
                            </div>
                        </div>
                    </div>					
					<div class="form-row">
                        <div class="col-md-12 mb-3">						
                            <label for="district">District : <b style="color:red">*</b> </label>							
                            <input type="text" id="district" name="district" class="form-control" list="currentDistrictList" required>
                            <datalist id="currentDistrictList">							
							</datalist>
                            <div class="invalid-feedback">
                                Required Field !
                            </div>							
                        </div>                      
                    </div>					
					<div class="form-row">
                        <div class="col-md-12 mb-3">					
                            <label for="pincode">Pincode : <b style="color:red">*</b></label>							
                            <div class="controls">
                                <input type="text" onkeypress="return validateFloatKeyPress(this, event, 5, -1);" id="pincode" name="pincode" class="form-control" pattern="^[1-9][0-9]{5}$" required>                             
                            </div>
                        </div>
                    </div>					
                    <div class="form-row">
                        <div class="col-md-12 mb-3">                            
							<label for="state">State : <b style="color:red">*</b></label>                            
							<input type="text" id="state" name="state" class="form-control" list="currentStateList" required>
                            <datalist id="currentStateList">							
							</datalist>							
							<div class="invalid-feedback">
                                Required Field !
                            </div>							
                        </div>
					</div>
					
					<div class="form-row mb-3">
						<div id="myMap">
						</div>
					</div>
					
					<div class="form-row d-none">
						<input id="pac-input" class="controls" type="text" placeholder="Search Box"/>
					</div>
					
					<div class="form-row">
                        <div class="col-md-4 mb-3">                            
							<label for="latitude">Latitude : <b style="color:red">*</b></label>                            
							<input type="text" id="latitude" name="latitude" class="form-control" required>				
							<div class="invalid-feedback">
                                Required Field !
                            </div>							
                        </div>
						<div class="col-md-4 mb-3">                            
							<label for="longitude">Longitude : <b style="color:red">*</b></label>                            
							<input type="text" id="longitude" name="longitude" class="form-control" required>								
							<div class="invalid-feedback">
                                Required Field !
                            </div>							
                        </div>
						<div class="col-md-4 mb-3">                            
							<label for="radios">Radios : <b style="color:red">*</b></label>                            
							<input type="text" id="radios" name="radios" class="form-control" required>				
							<div class="invalid-feedback">
                                Required Field !
                            </div>							
                        </div>
					</div>
					
					<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message)) {
							echo $error_message;
						}
						echo "</div>";
					?>
					<div class="form-group">
                        <div class="custom-control custom-checkbox mr-sm-2">
                            <div class="custom-control custom-checkbox">
								<input type="checkbox" value="1" class="custom-control-input" id="isService" name="isService">
                                <label class="custom-control-label" for="isService">Service Available For address?</label>
                            </div> 
                        </div>
                    </div>					
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mr-sm-2">
                            <div class="custom-control custom-checkbox">                               
								<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive">
                                <label class="custom-control-label" for="isActive">Active</label>								
                            </div>
                        </div>
                    </div>					
                    <div class="text-xs-right">
                        <button type="submit" id="Submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
                        <button type="reset" class="btn btn-reset">Reset</button>
					</div>					
                </form>
            </div>
        </div>
    </div>
 </div>
 <!--<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjAeJLrBzCPACXrH1tuW3JIQUr35t6QaI&callback=initAutocomplete&libraries=places&v=weekly"
      defer
    ></script>-->
<script type="text/javascript"> 
	// 16.69103980332084
	// 74.2447007368366
	var map;
	var marker;
	var myLatlng = new google.maps.LatLng(16.691307,74.244865);
	// var myLatlng = new google.maps.LatLng(16.69103980332084,74.2447007368366);
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
			console.log(results);
			console.log('_______________________________________________________');
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					$('#latitude,#longitude').show();
					$('#address').val(results[0].formatted_address);
					// alert(results[0].address_components[0]['long_name']);
					$('#latitude').val(marker.getPosition().lat());
					$('#longitude').val(marker.getPosition().lng());
					$.getJSON( {
						// url  : 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + marker.getPosition().lat() + ',' + marker.getPosition().lng() + '&result_type=country',
						url  : 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + marker.getPosition().lat() + ',' + marker.getPosition().lng(),
						data : {
							sensor  : false
						},
						success : function( data, textStatus ) {
							var filtered_array = results[0].address_components.filter(function(address_component){
								return address_component.types.includes("country");
							}); 
							var county = filtered_array.length ? filtered_array[0].long_name: "";
							alert('County: ' + county);
							
							var filtered_array1 = results[0].address_components.filter(function(address_component){
								return address_component.types.includes("administrative_area_level_1");
							}); 
							var administrative_area_level_1 = filtered_array1.length ? filtered_array1[0].long_name: "";
							alert('administrative_area_level_1: ' + administrative_area_level_1);
						}
					});

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
</script>

<script type="text/javascript"> 
  // var geocoder;

  // if (navigator.geolocation) {
    // navigator.geolocation.getCurrentPosition(successFunction, errorFunction);
// } 

// function successFunction(position) {
    // var lat = position.coords.latitude;
    // var lng = position.coords.longitude;
    // codeLatLng(lat, lng)
// }

// function errorFunction(){
    // alert("Geocoder failed");
// }

  // function initialize() {
    // geocoder = new google.maps.Geocoder();



  // }

  // function codeLatLng(lat, lng) {

    // var latlng = new google.maps.LatLng(lat, lng);
    // geocoder.geocode({'latLng': latlng}, function(results, status) {
      // if (status == google.maps.GeocoderStatus.OK) {
        // if (results[1]) {
        // var indice=0;
        // for (var j=0; j<results.length; j++)
        // {
            // if (results[j].types[0]=='locality')
                // {
                    // indice=j;
                    // break;
                // }
            // }
        // alert('The good number is: '+j);
        // console.log(results[j]);
        // for (var i=0; i<results[j].address_components.length; i++)
            // {
                // if (results[j].address_components[i].types[0] == "locality") {
                        // city = results[j].address_components[i];
                    // }
                // if (results[j].address_components[i].types[0] == "administrative_area_level_1") {
                        // region = results[j].address_components[i];
                    // }
                // if (results[j].address_components[i].types[0] == "country") {
                        // country = results[j].address_components[i];
                    // }
            // }
            // alert(city.long_name + " || " + region.long_name + " || " + country.short_name)


            // } else {
              // alert("No results found");
            // }
      // } else {
        // alert("Geocoder failed due to: " + status);
      // }
    // });
  // }
</script> 

 <script>
      // This example adds a search box to a map, using the Google Place Autocomplete
      // feature. People can enter geographical searches. The search box will return a
      // pick list containing a mix of places and predicted search terms.
      // This example requires the Places library. Include the libraries=places
      // parameter when you first load the API. For example:
      // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
      function initAutocomplete() {
        const map = new google.maps.Map(document.getElementById("myMap1"), {
          center: { lat: -33.8688, lng: 151.2195 },
          zoom: 13,
          mapTypeId: "roadmap",
        });
        // Create the search box and link it to the UI element.
        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // Bias the SearchBox results towards current map's viewport.
        map.addListener("bounds_changed", () => {
          searchBox.setBounds(map.getBounds());
        });
        let markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener("places_changed", () => {
          const places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }
          // Clear out the old markers.
          markers.forEach((marker) => {
            marker.setMap(null);
          });
          markers = [];
          // For each place, get the icon, name and location.
          const bounds = new google.maps.LatLngBounds();
          places.forEach((place) => {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            const icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25),
            };
            // Create a marker for each place.
            markers.push(
              new google.maps.Marker({
                map,
                icon,
                title: place.name,
                position: place.geometry.location,
              })
            );
		
            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
      }
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
			
		},4000); // End Set Time Function

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








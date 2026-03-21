<style>
	#myMap {
		height: 500px;
		width: 1280px;
	}
</style>
<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
 			<div class="col-12 align-self-center">
 				<h4 class="page-title">Order Tracking Details</h4>
 				<div class="d-flex align-items-center">
 					<nav aria-label="breadcrumb">
 						<ol class="breadcrumb">
 							<li class="breadcrumb-item"><a href="#">Home</a></li>
 							<li class="breadcrumb-item active" aria-current="page">View</li>
 						</ol>
 					</nav>
 				</div>
 			</div>
 		</div>
	</div>

	<div class="container-fluid col-md-12">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Tracking Details </h4>
 				<hr />
				<?php 
				if($query){
					foreach($query->result() as $row) { ?>
 				<div class="form-row">
 					<div class="col-md-4 mb-3">
 						<label for="orderCode"> Order Code : </label>
 						<input type="text" id="orderCode" name="orderCode" class="form-control-line" value="<?= $row->code ?>" readonly>
 						<input type="hidden" id="latitude" name="latitude" class="form-control-line" value="<?= $latitude ?>" readonly>
 						<input type="hidden" id="longitude" name="longitude" class="form-control-line" value="<?= $longitude ?>" readonly>
						<input type="hidden" id="ResLatitude" name="ResLatitude" class="form-control-line" value="<?= $ResLatitude ?>" readonly>
 						<input type="hidden" id="ResLongitude" name="ResLongitude" class="form-control-line" value="<?= $ResLongitude ?>" readonly>
 						<input type="hidden" id="clLatitude" name="clLatitude" class="form-control-line" value="<?= $clLatitude ?>" readonly>
 						<input type="hidden" id="clLongitude" name="clLongitude" class="form-control-line" value="<?= $clLongitude ?>" readonly>
 						<input type="hidden" id="clLabel" name="clLabel" class="form-control-line" value="<?= '<b>'.$clientName.'<br>'.$clientMobile.'</b>' ?>" readonly>
 						<input type="hidden" id="dlbLabel" name="dlbLabel" class="form-control-line" value="<?= '<b>'.$dlbName.'<br>'.$dlbMobile.'</b>' ?>" readonly>
 						<input type="hidden" id="resLabel" name="resLabel" class="form-control-line" value="<?= '<b>'.$ResName.'<br>'.$ResMobile.'</b>' ?>" readonly>
 						<input type="hidden" id="dlbProfilePic" name="dlbProfilePic" class="form-control-line" value="<?= $dlbPic ?>" readonly>
					</div>
 					<div class="col-md-3 mb-3">
 						<label for="deliveryBoyCode"> Delivery Boy: </label>
 						<input type="text" id="deliveryBoyCode" name="deliveryBoyCode" value="<?= $dlbName ?>" class="form-control-line" readonly>
 					</div>
 					<div class="col-md-5 mb-3">
 						<label for="mobile"> Mobile: </label>
 						<input type="text" id="mobile" name="mobile" value="<?= $dlbMobile ?>" class="form-control-line" disabled>
 					</div>
 				</div>
					<?php }
				}?>
				<div class="form-row mb-3">
					<div id="myMap">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	getLocation();
	function getLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		} else {
		   console.log("Geolocation is not supported by this browser.");
	    }
	}
	var myLatlng;
	function showPosition(position) {
	  var lat = position.coords.latitude;
	  var lng = position.coords.longitude;
	  myLatlng =new google.maps.LatLng(lat, lng);
    }
	var map;
	var marker;
	var startMarker;
	var endMarker;
	var geocoder = new google.maps.Geocoder();
	var directionsDisplay;
    var directionsService = new google.maps.DirectionsService();
	var ResLatitude=$('#ResLatitude').val();
	var ResLongitude=$('#ResLongitude').val();
	var clLatitude=$('#clLatitude').val();
	var clLongitude=$('#clLongitude').val();
	var latitude=$('#latitude').val();
	var longitude=$('#longitude').val();
	var resLabel=$('#resLabel').val();
	var clLabel=$('#clLabel').val();
	var dlbLabel=$('#dlbLabel').val();
	var dlbImage = "<img src='"+$('#dlbProfilePic').val()+"' width='40px;' height='40px;'>";
	function initialize() {
		directionsDisplay = new google.maps.DirectionsRenderer({suppressMarkers: true});
		var mapOptions = {
			zoom: 18,
			center: myLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("myMap"), mapOptions);
		marker = new google.maps.Marker({
			map: map,
			position: myLatlng,
			draggable: false
		});
		var waypts = [];
		var startPoint = new google.maps.LatLng(ResLatitude,ResLongitude);
        var endPoint = new google.maps.LatLng(clLatitude,clLongitude);
        var waypoint = new google.maps.LatLng(latitude,longitude);
		var icons = {
		  end: new google.maps.MarkerImage(
			"<?= base_url() ?>"+"assets/order_tracking/user_1.png",
		  ),
		  start: new google.maps.MarkerImage(
		  
		  )
		 };
		/*waypts.push({
			location: waypoint,
			stopover: true
		});*/
        var bounds = new google.maps.LatLngBounds();
        bounds.extend(startPoint);
        bounds.extend(endPoint);
        map.fitBounds(bounds);
        var request = {
            origin: startPoint,
            destination: endPoint,
			//waypoints: waypts,
			//optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function (response, status) {
			//debugger;
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
				/*var icon = {
					url:  "<?= base_url() ?>"+"assets/order_tracking/delivery.png",
					scaledSize: new google.maps.Size(30,30), // scaled size
					origin: new google.maps.Point(0,0), // origin
					anchor: new google.maps.Point(0, 0) // anchor
				};*/
				waymarker = new google.maps.Marker({
					map: map,
					position: waypoint,
					icon : "<?= base_url() ?>"+"assets/order_tracking/delivery.png",
					title:"Delivery Boy"
				});
				 var infowindow = new google.maps.InfoWindow({
					pixelOffset: new google.maps.Size(0, -40)
				});
				  infowindow.setContent(resLabel)
				  infowindow.setPosition(startPoint);
				  infowindow.open(map);
				  var infowindow = new google.maps.InfoWindow({
					pixelOffset: new google.maps.Size(0, -42)
				});
				  infowindow.setContent(clLabel)
				  infowindow.setPosition(endPoint);
				  infowindow.open(map);
				   var infowindow = new google.maps.InfoWindow({
					pixelOffset: new google.maps.Size(0, -42)
				});
				  infowindow.setContent('<div class="float-left">'+dlbImage+'</div><div class="float-right ml-2">'+dlbLabel+'</div>')
				  infowindow.setPosition(waypoint);
				  infowindow.open(map);
				  var leg = response.routes[ 0 ].legs[ 0 ];
				 makeMarker( leg.start_location, icons.start, "Restaurant" );
				 makeMarker( leg.end_location, icons.end, "Customer" );
                directionsDisplay.setMap(map);
            } else {
                alert("Directions Request from " + startPoint.toUrlValue(6) + " to " + endPoint.toUrlValue(6) + " failed: " + status);
            }
        });
	}
	google.maps.event.addDomListener(window, 'load', initialize);
	function makeMarker( position, icon, title ) {
	 new google.maps.Marker({
	  position: position,
	  map: map,
	  icon: icon,
	  title: title
	 });
	}
</script>
 
	<div class="page-wrapper">
		
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Delivery Charge</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Create Delivery Charge</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid ">
		
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Company Details</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/DeliveryCharge/save';?>" >
							
					<div class="form-row">
					    <div class="col-md-6">
						     <label for="companyName">Company Name</label>
							<input type="text" id="companyName" name="companyName" class="form-control" required>
						</div>
						<div class="col-md-6">
						     <label for="companyRegNo">Company Register No</label>
							<input type="text" id="companyRegNo" name="companyRegNo" class="form-control" required>
						</div>
					</div><hr>
					
					<div class="form-row">
					    <div class="col-md-4">
						     <label for="contactNo">Contact No</label>
							<input type="text" id="contactNo" name="contactNo" pattern="[789][0-9]{9}" class="form-control"  >
						</div>
						<div class="col-md-4">
						     <label for="altContactNo">Alt Contact No</label>
							<input type="text" id="altContactNo" name="altContactNo" pattern="[789][0-9]{9}" class="form-control" >
						</div>
						<div class="col-md-4">
						     <label for="email">Email</label>
							<input type="email" id="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control" >
						</div>
					</div></br></br>
					<hr>
					<h4 class="card-title">Shipping Address:</h4> 
					
					<div class="form-row">
					
					    <div class="col-md-8">
						     <label for="shippingAddress">Address :</label>
							<input type="text" id="shippingAddress" name="shippingAddress" class="form-control" >
						</div>
						<div class="col-md-4">
						     <label for="shippingPinCode">Pincode :</label>
							<input type="text" id="shippingPinCode" name="shippingPinCode" class="form-control" >
						</div>
						
					</div>
					<div class="form-row">
					  <div class="col-md-2">
						     <label for="shippingPlace">Place :</label>
							<input type="text" id="shippingPlace" name="shippingPlace" class="form-control" >
						</div>
						<div class="col-md-2">
						     <label for="shippingTaluka">Taluka :</label>
							<input type="text" id="shippingTaluka" name="shippingTaluka" class="form-control" >
						</div>
						<div class="col-md-2">
						     <label for="shippingDistrict">District :</label>
							<input type="text" id="shippingDistrict" name="shippingDistrict" class="form-control" >
						</div>
						<div class="col-md-3">
						     <label for="shippingState">State :</label>
							<input type="text" id="shippingState" name="shippingState" class="form-control" >
						</div>
						<div class="col-md-3">
						     <label for="shippingCountry">Country :</label>
							<input type="text" id="shippingCountry" name="shippingCountry" class="form-control" >
						</div>
						
					</div>
					</br></br>
					<div class="row">
					<div class="col-md-5"><h4 class="card-title">Billing Address:</h4></div>
					<div class="col-md-7">
					  <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name ="isBillingAddressSame" id="isBillingAddressSame" value="1">
                                        <label class="custom-control-label" for="isBillingAddressSame"> Check if Billing address is same to Current address</label>
                                      </div>
						</div>
					</div>
					<div class="form-row">
					
					    <div class="col-md-8">
						     <label for="billingAddress">Address :</label>
							<input type="text" id="billingAddress" name="billingAddress" class="form-control" >
						</div>
						<div class="col-md-4">
						     <label for="billingPinCode">Pincode :</label>
							<input type="text" id="billingPinCode" name="billingPinCode" class="form-control" >
						</div>
						
					</div>
					<div class="form-row">
					  <div class="col-md-2">
						     <label for="billingPlace">Place :</label>
							<input type="text" id="billingPlace" name="billingPlace" class="form-control" >
						</div>
						<div class="col-md-2">
						     <label for="shippingTaluka">Taluka :</label>
							<input type="text" id="billingTaluka" name="billingTaluka" class="form-control" >
						</div>
						<div class="col-md-2">
						     <label for="shippingDistrict">District :</label>
							<input type="text" id="billingDistrict" name="billingDistrict" class="form-control" >
						</div>
						<div class="col-md-3">
						     <label for="shippingState">State :</label>
							<input type="text" id="billingState" name="billingState" class="form-control" >
						</div>
						<div class="col-md-3">
						     <label for="shippingCountry">Country :</label>
							<input type="text" id="billingCountry" name="billingCountry" class="form-control" >
						</div>
						
					</div>
					</br></br>
					
						<hr>
						
						<h4 class="card-title"><u>Pre Order Delivery Cost</u> :</h4>

						<div class="form-row d-none">
						<label for="minOrder" class="col-md-4 mb-3">Min Order Amount</br>(For Free Delivery) :</label>
							<div class="col-md-4 mb-3">
								<input type="number" id="minOrder" name="minOrder" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							<div class="col-md-2 mb-3">
							    <select id="minOrderCurrency" name="minOrderCurrency"  class="form-control">
								<option value="">Select Currency</option>
							<?php foreach($currency->result() as $curren)
							{
								echo'<option value="'.$curren->currencySName.'">'.$curren->currencySName.'</option>';
							}?>
							</select>
							      
						  </div>
						</div>

						<div class="form-row d-none">
							   <label for="deliveryCharge" id="deliveryCharge" class="col-md-4 mb-3">Delivery Charge </br>
								(< Rs Order Amount) : </label>
								<div class="col-md-4 mb-3">
								
								<input type="number" id="deliveryCharge" name="deliveryCharge" class="form-control" onKeyPress="return ValidateAlpha(event);" required>
								
								<div class="invalid-feedback">
									Required Field!
								 </div>
								</div>
								<div class="col-md-2 mb-3">
					     <select id="minOrderCurrency" name="minOrderCurrency"  class="form-control">
								<option value="">Select Currency</option>
							<?php foreach($currency->result() as $curren)
							{
								echo'<option value="'.$curren->currencySName.'">'.$curren->currencySName.'</option>';
							}?>
							</select>
							      
						  </div>
						</div></br></br>
						
					

											
						<div class="text-xs-right">
							
							<button type="submit" class="btn btn-myve" onclick="page_isPostBack=true;">Submit</button>
							<button type="Reset" class="btn btn-reset">Reset</button>
							
						</div>
					</form>
				</div>
			</div>
		</div>
	
	</div>
	<script>
	$('document').ready(function() {
		$('#shippingPinCode').change(function(){
			var shippingPinCode=$(this).val();
			$.ajax({
			url: '<?php echo site_url('AddressInfo/getAddressFromPin');?>', 
			method:"GET", 
			data: {
				'pinCode':shippingPinCode
			}
			, datatype:"text", 
			success: function(data) {
				console.log(data);
				var res=$.parseJSON(data);
				console.log(res);
				$("#shippingPlace").val(res.place);
				$("#shippingTaluka").val(res.taluka);
				$("#shippingDistrict").val(res.district);
				$("#shippingState").val(res.state);
				$("#shippingCountry").val(res.country);
			}
		});
				
	 });
		 $('#isBillingAddressSame').change(function()
                  {
                    if($(this).prop('checked'))
                    {
                      $('#billingAddress').attr('readonly', 'readonly');
                     // $('#permanentLandmark').attr('readonly', 'readonly');
                      $('#billingPinCode').attr('readonly', 'readonly');
                      $('#billingPlace').attr('readonly', 'readonly');
                      $('#billingTaluka').attr('readonly', 'readonly');
                      $('#billingDistrict').attr('readonly', 'readonly');
                      $('#billingState').attr('readonly', 'readonly');
                      $('#billingCountry').attr('readonly', 'readonly');
                    

                      var shippingAddress = $('#shippingAddress').val();
                    //  var shippingLandmark = $('#currentLandmark').val();
                      var shippingPinCode = $('#shippingPinCode').val();
                      var shippingPlace = $('#shippingPlace').val();
                      var shippingTaluka = $('#shippingTaluka').val();
                      var shippingDistrict = $('#shippingDistrict').val();
                      var shippingState = $('#shippingState').val();
                      var shippingCountry = $('#shippingCountry').val();

                      $('#billingAddress').val(shippingAddress);
                      //$('#permanentLandmark').val(currentLandmark);
                      $('#billingPinCode').val(shippingPinCode);
                      $('#billingPlace').val(shippingPlace);
                      $('#billingTaluka').val(shippingTaluka);
                      $('#billingDistrict').val(shippingDistrict);
                      $('#billingState').val(shippingState);
                      $('#billingCountry').val(shippingCountry);
            
                  }
                  else
                  {
                    $('#billingAddress').removeAttr('readonly', 'readonly');
                   // $('#permanentLandmark').removeAttr('readonly', 'readonly');
                    $('#billingPinCode').removeAttr('readonly', 'readonly');
                    $('#billingPlace').removeAttr('readonly', 'readonly');
                    $('#billingTaluka').removeAttr('readonly', 'readonly');
                    $('#billingDistrict').removeAttr('readonly', 'readonly');
                    $('#billingState').removeAttr('readonly', 'readonly');
                    $('#billingCountry').removeAttr('readonly', 'readonly');
                  }
                });
				
		$('#minOrder').change(function() {
			var minOrder=$(this).val();
			//alert(minOrder);
			$('#deliveryCharge').html('Delivery Charge </br> ( < ' +minOrder +' Rs Order Amount) :');
		});
	});
	</script>
	
	












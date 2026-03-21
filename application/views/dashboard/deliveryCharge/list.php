 
	<div class="page-wrapper">
		
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Company Details</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Company Details</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid col-md-10">
		
			<div class="card">
				<div class="card-body">
	
					<h4 class="card-title">Company Details</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/DeliveryCharge/update';?>" novalidate>
					<?php  foreach($delivery->result() as $row) { ?> 
					 <input type="hidden" id="code" name="code" value="<?=$row->code?>" class="form-control" >
					 
					<div class="form-row">
					    <div class="col-md-6">
						     <label for="companyName">Company Name</label>
							<label  type="text" id="companyName" name="companyName" class="form-control" ><?=$row->companyName?>
								</label>
						</div>
						<div class="col-md-6">
						     <label for="companyRegNo">Company Register No</label>
							  <label type="text" id="companyRegNo" name="companyRegNo" value="" class="form-control" ><?=$row->companyRegNo?>
							</label>
						</div>
					</div><hr>
					
						<div class="form-row">
					    <div class="col-md-4">
						     <label for="contactNo">Contact No</label>
							<label type="number" id="contactNo" name="contactNo" value="" class="form-control" ><?=$row->contactNo?>
							</label>
						</div>
						<div class="col-md-4">
						     <label for="altContactNo">Alt Contact No</label>
							<label type="number" id="altContactNo" name="altContactNo" value="" class="form-control" ><?=$row->alternateContactNo?>
						</label>
						</div>
						<div class="col-md-4">
						     <label for="email">Email</label>
							<label type="email" id="email" name="email" value="" class="form-control" ><?=$row->email?>
							</label>
						</div>
					</div></br></br>
					<hr>
					<h4 class="card-title">Shipping Address:</h4> 
					
					
										<div class="form-row">
					
					    <div class="col-md-8">
						     <label for="shippingAddress">Address :</label>
							<label type="text" id="shippingAddress" name="shippingAddress" value=""  class="form-control" ><?=$row->shippingAddress?>
						</label>
						</div>
						<div class="col-md-4">
						     <label for="shippingPinCode">Pincode :</label>
							<label type="text" id="shippingPinCode" name="shippingPinCode" value=""  class="form-control" >
							<?=$row->shippingPinCode?>
							</label>
						</div>
						
					</div>
					<div class="form-row">
					  <div class="col-md-2">
						    <label for="shippingPlace">Place :</label>
							<label type="text" id="shippingPlace" name="shippingPlace" value=""  class="form-control" ><?=$row->shippingPlace?>
						    </label>
						</div>
						<div class="col-md-2">
						     <label for="shippingTaluka">Taluka :</label>
							<label type="text" id="shippingTaluka" name="shippingTaluka" value="" class="form-control" ><?=$row->shippingTaluka?>
						  </label>
						</div>
						<div class="col-md-2">
						     <label for="shippingDistrict">District :</label>
							<label type="text" id="shippingDistrict" name="shippingDistrict" value="" class="form-control" ><?=$row->shippingDistrict?>
						</label>
						</div>
						<div class="col-md-3">
						     <label for="shippingState">State :</label>
							<label type="text" id="shippingState" name="shippingState" value="" class="form-control" ><?=$row->shippingState?>
								</label>
						</div>
						<div class="col-md-3">
						     <label for="shippingCountry">Country :</label>
							<label type="text" id="shippingCountry" name="shippingCountry" value=""  class="form-control" ><?=$row->shippingCountry?>
							</label>
						</div>
						
					</div>
					</br></br>
					
					<div class="row">
					<div class="col-md-5"><h4 class="card-title">Billing Address:</h4></div>
					<div class="col-md-7">
					           <div class="custom-control custom-checkbox">
                                         <?php if($row->xyz == '1'){
                                            echo '<input type="checkbox" class="custom-control-input" disabled name="isBillingAddressSame" id="isBillingAddressSame" value="1" checked>';
                                        }else{
                                            echo '<input type="checkbox" class="custom-control-input" disabled	 name="isBillingAddressSame" id="isBillingAddressSame" value="1">';
                                        }?>
                                        <label class="custom-control-label" for="isBillingAddressSame">Check if Billing Address is same to Current address</label>
                                      </div>
						</div>
					</div>
					<div class="form-row">
					
					    <div class="col-md-8">
						     <label for="billingAddress">Address :</label>
							<label type="text" id="billingAddress" name="billingAddress" value=""  class="form-control" ><?=$row->billingAddress?>
							</label>
						</div>
						<div class="col-md-4">
						     <label for="billingPinCode">Pincode :</label>
							<label type="text" id="billingPinCode" name="billingPinCode" value=""  class="form-control" ><?=$row->billingPinCode?>
							</label>
						</div>
						
					</div>
					<div class="form-row">
					  <div class="col-md-2">
						     <label for="billingPlace">Place :</label>
							<label type="text" id="billingPlace" name="billingPlace" value="" class="form-control" ><?=$row->billingPlace?>
							</label>
						</div>
						<div class="col-md-2">
						     <label for="shippingTaluka">Taluka :</label>
							<label type="text" id="billingTaluka" name="billingTaluka" value="" class="form-control" ><?=$row->billingTaluka?>
							</label>
						</div>
						<div class="col-md-2">
						     <label for="shippingDistrict">District :</label>
							<label type="text" id="billingDistrict" name="billingDistrict" value="" class="form-control" ><?=$row->billingDistrict?>
							</label>
						</div>
						<div class="col-md-3">
						     <label for="shippingState">State :</label>
							<label type="text" id="billingState" name="billingState" value="" class="form-control" ><?=$row->billingState?>
							</label>
						</div>
						<div class="col-md-3">
						     <label for="shippingCountry">Country :</label>
							<label type="text" id="billingCountry" name="billingCountry" value="" class="form-control" ><?=$row->billingCountry?>
							</label>
						</div>
						
					</div>
					</br></br>
					<hr>
						
						

					
					<div class="form-row d-none">
						<h4 class="card-title"><u>Pre Order Delivery Cost</u> :</h4>
						  <label for="minOrder" class="col-md-5 mb-3">Min Order Amount(For Free Delivery) :</label>
							<div class="col-md-5 mb-3">
								<label type="number" id="minOrder" name="minOrder" value="" class="form-control" required><?=$row->minOrder?>
								</label>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
							<div class="col-md-2 mb-3">
							    <select id="minOrderCurrency" name="minOrderCurrency"  class="form-control" readonly >
								<option value="">Select Currency</option>
							<?php foreach($currency->result() as $curren)
							{
								echo'<option value="'.$curren->currencySName.'">'.$curren->currencySName.'</option>';
							}?>
							</select>
							       <script>
									var minOrderCurrency= "<?=$row->minOrderCurrency?>";
									$("#minOrderCurrency").val(minOrderCurrency);
									</script>
						  </div>
						</div>

						<div class="form-row">
							   <label for="deliveryCharge" id="deliveryCharge" class="col-md-5 mb-3">Delivery Charge 
								(< <?=$row->minOrder?> Rs Order Amount) : </label>
								<div class="col-md-5 mb-3">
								
								<label type="number" id="deliveryCharge" name="deliveryCharge" value="" class="form-control" onKeyPress="return ValidateAlpha(event);" ><?=$row->deliveryCharge?>
								</label>
								</div>
								
								<div class="invalid-feedback">
									Required Field!
								</div>
							  
							<div class="col-md-2 mb-3">
							<select id="deliveryChargeCurrency" name="deliveryChargeCurrency"  class="form-control" readonly >
							<option value="">Select Currency</option>
							<?php foreach($currency->result() as $curren)
							{
								echo'<option value="'.$curren->currencySName.'">'.$curren->currencySName.'</option>';
							}?>
							</select>
							       <script>
									var deliveryChargeCurrency= "<?=$row->deliveryChargeCurrency?>";
									$("#deliveryChargeCurrency").val(deliveryChargeCurrency);
									</script>
						   </div>
						
					<?php }?>					
							<div class="text-xs-right">
							
							<a type="button" class="btn btn-primary" href="<?php echo base_url().'DeliveryCharge/edit';?>">Edit</a>
							
							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	</div>
	<script>
	$('document').ready(function() {
		var isBillingAddressSame= $('#isBillingAddressSame').val();
		 //alert(isBillingAddressSame);
		 if(isBillingAddressSame=='1'){
		$('#billingAddress').attr('readonly', 'readonly');
                     // $('#permanentLandmark').attr('readonly', 'readonly');
                      $('#billingPinCode').attr('readonly', 'readonly');
                      $('#billingPlace').attr('readonly', 'readonly');
                      $('#billingTaluka').attr('readonly', 'readonly');
                      $('#billingDistrict').attr('readonly', 'readonly');
                      $('#billingState').attr('readonly', 'readonly');
                      $('#billingCountry').attr('readonly', 'readonly');
		 }
		 
		
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
				//console.log(data);
				var res=$.parseJSON(data);
				//console.log(res);
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
	  $( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  toastr.success(obj.message, 'Delivery Charge', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Delivery Charge', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
	</script>
	
	












<div class="page-wrapper"> 
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Offer</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Create Offer</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid col-md-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Create Offer</h4>
				<hr/>
				<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url().'index.php/Offer/save';?>" novalidate onsubmit="validateFlatAmount()">
					<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message))
						{
						echo '<b>'.$error_message.'</b>';
					   }
						echo "</div>";
					?>
						
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="coupanCode">Coupon Code :<b style="color:red">*</b></label>
							<input type="text" id="coupanCode" name="coupanCode" class="form-control" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div> 
						
						<div class="col-md-6 mb-3">
							<label for="offerType">Offer Type :<b style="color:red">*</b></label>
							<select id="offerType" name="offerType" class="form-control" required>
								<option value="">Select type</option>
								<option value="flat">Flat</option>
								<option value="cap">Cap</option>
							</select>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div>
						
					</div>
					
					<div class="form-row">
						<div class="col-md-4 mb-3">
							<label for="minimumAmount"> Minimum Amount : <b style="color:red">*</b></label>
							<input type="number" id="minimumAmount" name="minimumAmount" class="form-control" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div> 	
						
						<div class="col-md-4 mb-3 d-none" id="discountDiv">
							<label for="discount"> Discount (%): <b style="color:red">*</b></label>
							<input type="number" id="discount" name="discount" class="form-control" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div> 	

						<div class="col-md-4 mb-3 d-none" id="capDiv">
							<label for="capLimit"> Cap Limit : <b style="color:red">*</b></label>
							<input type="number" id="capLimit" name="capLimit" class="form-control">
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div> 	
							<div class="col-md-4 mb-3 d-none" id="flatAmountDiv">
                            <label for="flatAmount"> Flat Amount: <b style="color:red">*</b></label>
                            <input type="number" id="flatAmount" name="flatAmount" class="form-control">
                            <div class="invalid-feedback">
                                Required Field!
                            </div>
                        </div>
					</div>
					
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="perUserLimit">Per User Limit : <b style="color:red">*</b></label>
							<input type="number" id="perUserLimit" name="perUserLimit" class="form-control" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div> 						
					</div> 
					
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="termsAndConditions">Terms & Conditions : </label>
							<textarea class="form-control" id="termsAndConditions" name="termsAndConditions" placeholder="Terms and Conditions"></textarea>
						</div>
					</div>
							
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<div class="input-daterange input-group">
							<span> <label> Offer Dates :</label> </span>
								<div class="input-daterange input-group" id="productDateRange">
									<input type="text" class="form-control date-inputmask col-sm-5" name="startDate"  id="startDate" placeholder="dd/mm/yyyy" required>
									<div class="input-group-append">
									<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
								  </div>
								<input type="text" class="form-control date-inputmask toDate" name="endDate" id="endDate" placeholder="dd/mm/yyyy" required>
								</div>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
						</div>
					</div>
					
					<div class="form-row">
						<div class="col-md-3">
							<div class="custom-control custom-checkbox"> 
								<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive">
								<label class="custom-control-label" for="isActive">Active</label>
							</div>
						</div>
					</div>
					<div class="text-xs-right">							
						<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
						<button type="Reset" class="btn btn-reset">Reset</button>
					</div>
				</form>
			</div>
		</div>
	</div>	
</div>

<script>
$( document ).ready(function() {
	$('input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
		// Disable keyboard scrolling
		$('input[type=number]').on('keydown',function(e) {
		var key = e.charCode || e.keyCode;
		// Disable Up and Down Arrows on Keyboard
		if(key == 38 || key == 40 ) {
			e.preventDefault();
		} 
		else 
		{
			return;
		}
	});
	
	$("#termsAndConditions").summernote({
		placeholder: 'Terms and conditions....',
		height: 200
	});
	$('.btn-reset').click(function(){
		$('#termsAndConditions').summernote('reset');
	});
	
	var date = new Date();
	var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
	$('#startDate').datepicker({
		dateFormat: "dd/mm/yy",
		showOtherMonths: true,
		selectOtherMonths: true,
		autoclose: true,
		changeMonth: true,
		changeYear: true,
		todayHighlight: true,
		orientation: "top",
		startDate: today,
	});
	$('#endDate').datepicker({
		dateFormat: "dd/mm/yy",
		showOtherMonths: true,
		selectOtherMonths: true,
		autoclose: true,
		changeMonth: true,
		changeYear: true,
		todayHighlight: true,
		orientation: "top",
		startDate: today,
	});
		
	$("body").on("change","#offerType",function(e){
		var typeValue = $(this).val().trim();
		 if (typeValue == "flat") {
                $('#discountDiv').addClass('d-none');
                $("#discount").prop('required', false);
                $('#capDiv').addClass('d-none');
                $("#capLimit").prop('required', false);
                $('#flatAmountDiv').removeClass('d-none');
                $("#flatAmount").prop('required', true);
            } else if (typeValue == "cap") {
                $('#discountDiv').removeClass('d-none');
                $("#discount").prop('required', true);
                $('#capDiv').removeClass('d-none');
                $("#capLimit").prop('required', true);
                $('#flatAmountDiv').addClass('d-none');
                $("#flatAmount").prop('required', false);
            } else {
                $('#discountDiv').addClass('d-none');
                $("#discount").prop('required', false);
                $('#capDiv').addClass('d-none');
                $("#capLimit").prop('required', false);
                $('#flatAmountDiv').addClass('d-none');
                $("#flatAmount").prop('required', false);
            }
	});

});
	function validateFlatAmount(){
		var minimumAmount = $('#minimumAmount').val();
		var offerType = $('#offerType').val();
		var flatAmount = $('#flatAmount').val();
		if(offerType=='flat'){
			if(minimumAmount!='' && flatAmount!=''){
				if(Number(flatAmount)>Number(minimumAmount)){
					toastr.error("Flat amount should be less than or equal to minimum amount", 'Vendor Offer', {"progressBar": true});
					$('#flatAmount').val('');
					$('#flatAmount').focus();
					return true;
				}
			}
		}
		
	}
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
	setTimeout(function(){$('#error_message').hide('fast');},8000);
 
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
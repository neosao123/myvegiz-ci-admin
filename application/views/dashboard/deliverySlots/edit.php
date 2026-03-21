<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Delivery Slots</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Change In Delivery Slots</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	<div class="container-fluid col-md-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Change In Delivery Slots</h4>
				<hr/>
				<form class="needs-validation" method="post" id="Uom" action="<?php echo base_url().'DeliveryChargesSlots/update';?>" novalidate>	
					<?php foreach($query->result() as $row){?>
					<input type="hidden" name="code" value="<?php echo $row->code?>">
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="slotTitle">Slot Title : <b style="color:red">*</b></label>
							<input type="text" id="slotTitle" name="slotTitle" class="form-control" required value="<?= $row->slotTitle?>">
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>	
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="startTime">Start Time : <b style="color:red">*</b> </label>
							<input type="time" id="startTime" name="startTime" class="form-control" required value="<?= $row->startTime?>">
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="endTime">End Time:<b style="color:red">*</b> </label>
							<input type="time" id="endTime" name="endTime" class="form-control" required value="<?= $row->endTime?>">  
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<div class="form-row">
						<div class="col-md-12 mb-3">
							<label for="deliveryCharge">Delivery Charge:<b style="color:red">*</b> </label>
							<input type="text" id="deliveryCharge" name="deliveryCharge" class="form-control" required value="<?= $row->deliveryCharge?>">  
							<div class="invalid-feedback">
								Required Field !
							</div>
						</div>
					</div>
					<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message))	{
							echo $error_message;
						}
						echo "</div>";
						$str='';
						if($row->isActive==1){
							$str ='checked' ;
						}
					?>
					
					<div class="form-group">
						<div class="custom-control custom-checkbox mr-sm-2">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" value="1" class="custom-control-input" id="isActive" name="isActive" <?= $str ?>>
								<label class="custom-control-label" for="isActive">Active</label>
							</div>
						</div>
					</div>
					<div class="text-xs-right">
						<button type="submit" id="Submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
						<button type="reset" class="btn btn-reset">Reset</button>
					</div>
					<?php
					}
					?>
						
				</form>
			</div>
		</div>
	</div>
</div>
<script>
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

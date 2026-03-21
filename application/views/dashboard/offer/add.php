	<div class="page-wrapper">
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Offers</h4>
					<div class="d-flex align-items-center"
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Offers</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid col-md-8">

			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Create Offer</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/Offer/save';?>" enctype="multipart/form-data" novalidate>
						<?php
							echo "<div class='text-danger text-center'>";
							if (isset($error_message)) 		
							{
							echo $error_message;
							}
						   echo "</div>";
						?>
						<div class="form-row"> 
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<span> <label for="cityCode">For City:</label> </span>
									<select  class="form-control" id="cityCode" name="cityCode">
										<option value="">Select City</option>
										<?php
										if($city){
											foreach ($city->result() as $c) {
												echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
											} 
										}?>
									</select>
									<span><?= form_error('cityCode')?></span>
								</div>
							</div>
							<div class="col-md-12 mb-3">
								<label for="offerTitle">Title : <b style="color:red">*</b></label>
								<input type="text" id="offerTitle" name="offerTitle" class="form-control" required>
								<div class="invalid-feedback">
									Required Field !
								</div>
							</div> 							
							<div class="col-md-12 mb-3">
								<label for="offerDescription">Description : </label>
								<textarea rows="4" cols="50" class="form-control" name="offerDescription"></textarea>
							</div>					 
							<div class="col-md-12 mb-3">
								<label for="offetTerms_condi">Terms & Conditions: </label>
								<textarea class="form-control" id="offetTerms_condi" name="offetTerms_condi" placeholder="Terms and Conditions"></textarea>
							</div>							
							<div class="col-md-8 mb-3">
								<div class="input-daterange input-group">
									<label>Dates :<b style="color:red">*</b></label>  
									<div class="input-daterange input-group" id="productDateRange">
										<input type="text" class="form-control date-inputmask" name="start"  id="offerDateStart" placeholder="dd/mm/yyyy" required/>
										<div class="invalid-feedback">
											Required Field !
										</div>	
										<div class="input-group-append">
											<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
										</div>
										<input type="text" class="form-control date-inputmask toDate" name="end" id="offerDateEnd" placeholder="dd/mm/yyyy" required/>
										<div class="invalid-feedback">
											Required Field !
										</div>   
									</div>
								</div>
							</div>											  
							<div class="col-md-6 mb-3"> 
								<label for="offerImage">Offer Images: </label>
								<div class="email-repeater form-group">
									<div data-repeater-list="repeater-group">
										<div data-repeater-item class="row m-b-15">
											<div class="col-md-12">
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="offerImage" name="offerImage">
													<label class="custom-file-label" for="offerImage">Choose file</label>
												</div>
												<small>Please upload images of 400 X 400 (width x height) size.</small>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">  
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox">
										<input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive">
										<label class="custom-control-label" for="isActive">Active</label>
									</div>
								</div>
							</div>
						</div>
						<div class="text-xs-right form-row">
							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
							<button type="reset" class="btn btn-reset ml-1">Reset</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		
	 </div>
       
	<script>
		$('document').ready(function(){
			$("#offetTerms_condi").summernote({
				placeholder: 'Terms and conditions....',
				height: 250
			});
			$('.btn-reset').click(function(){
				$('#offetTerms_condi').summernote('reset');
			});
			$('#offerDateStart').datepicker({
				dateFormat: "mm/dd/yy",
				showOtherMonths: true,
				selectOtherMonths: true,
				autoclose: true,
				changeMonth: true,
				changeYear: true,
				todayHighlight: true,
				orientation: "bottom left",
			});
			$('#offerDateEnd').datepicker({
				dateFormat: "mm/dd/yy",
				showOtherMonths: true,
				selectOtherMonths: true,
				autoclose: true,
				changeMonth: true,
				changeYear: true,
				todayHighlight: true,
			   orientation: "bottom left",
			});
				
			// $("#dzid").dropzone({ url: "/file/post" });
			$("#offerTitle").maxlength({max: 100});
				
			
		});  
		
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
	
	
	

 
	<div class="page-wrapper">
    
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">UOM</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Change In UOM</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
	
		<div class="container-fluid col-md-6">
		  
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Change In UOM</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="Uom" action="<?php echo base_url().'Uom/update';?>" novalidate>

						<?php foreach($query->result() as $row)
							{
							?>
							
							<input type="hidden" name="code" value="<?php echo $row->code?>">

							<div class="form-row">
								<div class="col-md-12 mb-3">
									
									<label for="uomName">UOM Name : <b style="color:red">*</b></label>
									
									<input type="text" id="currencyName" name="uomName" value="<?php echo $row->uomName?>" class="form-control" maxlength="30" required>
									
									<div class="invalid-feedback">
										Required Field!
									</div>
							   
								</div>
							</div>

							<div class="form-row">
								<div class="col-md-12 mb-3">
									
									<label for="uomSName">UOM Short Name :</label>
									
									<input type="text" id="uomSName" value="<?php echo $row->uomSName?>" name="uomSName" class="form-control" onKeyPress="return ValidateAlpha(event);" readonly>
								
								
								</div>
							</div>
							
							<script type="text/javascript">
								function ValidateAlpha(evt) {
									var keyCode = (evt.which) ? evt.which : evt.keyCode
									if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

										return false;
									return true;
								}
							</script>
							
							<div class="form-row">
								<div class="col-md-12 mb-3">
									
									<label for="uomDescription">UOM Description : </label>
									
									<div class="controls">
										
										<textarea id="uomDescription" name="uomDescription" class="form-control" row="2" cols="50"><?php echo $row->uomDescription?></textarea>

										
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
								
								<a href="<?php echo base_url().'index.php/uom/listRecords';?>">
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
		

		
	<script>
	
		$('document').ready(function(){
		 
		 $("#uomName").maxlength({max: 30});
		 $("#uomSName").maxlength({max: 3});
		 $("#uomDescription").maxlength({max: 256});
		 
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

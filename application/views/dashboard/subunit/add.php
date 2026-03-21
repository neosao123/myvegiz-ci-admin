 
<div class="page-wrapper">
 
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title">Sub Unit</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Sub Unit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    
	
    <div class="container-fluid col-md-6">
    
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Create Sub Unit</h4>
                <hr/>
				
                <form class="needs-validation" method="post" action="<?php echo base_url().'Subunit/save';?>" novalidate>

                    <div class="form-row">
							<div class="col-md-12 mb-3">
								<label for="GroceryCategory">UOM: <b style="color:red">*</b></label>
								<select class="custom-select form-control" id="uomCode" name="uomCode" required>
									<option value="" readonly>Select UOM..</option>
									<?php foreach ($uom->result() as $u) {
										echo '<option value="' . $u->code . '">' . $u->uomName . '</option>';
									} ?>
								</select>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div>
					</div>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            
							<label for="subunitName">Sub Unit Name : <b style="color:red">*</b></label>
                            
							<input type="text" id="subunitName" name="subunitName" class="form-control" required>
                            
							<div class="invalid-feedback">
                                Required Field !
                            </div>
							
                        </div>
					</div>

                    <div class="form-row">
                        <div class="col-md-12 mb-3">
						
                            <label for="subunitSName">Sub Unit Short Name : <b style="color:red">*</b> </label>
							
                            <input type="text" id="subunitSName" name="subunitSName" class="form-control" onKeyPress="return ValidateAlpha(event);" required>
                            
                            <div class="invalid-feedback">
                                Required Field !
                            </div>
							
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
						
                            <label for="subunitDescription">Sub Unit Description : </label>
							
                            <div class="controls">
                                <textarea id="subunitDescription" name="subunitDescription" class="form-control" row="2" cols="50"></textarea>
                             
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

	<script>
	 
	
		$('document').ready(function(){
		 
			$("#subunitName").maxlength({max: 30});
			$("#subunitSName").maxlength({max: 3});
			$("#subunitDescription").maxlength({max: 256});
		 
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








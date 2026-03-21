
  
    <div class="page-wrapper">

		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Department</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Department </li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid col-md-6">

			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Change In Department</h4>
					<hr/>

					<form class="needs-validation" method="post" id="department" action="<?php echo base_url().'index.php/department/update';?>" enctype="multipart/form-data" novalidate>
						<?php

						  foreach($query->result() as $row)
						  {
							?>

							<div class="form-row">
								<div class="col-md-12 mb-3">
									<label for="code">Code :</label>
									<input type="text" id="code" readonly name="code" class="form-control" value="<?php echo $row->code?>">
								</div>

							</div>

							<div class="form-row">
								<div class="col-md-12 mb-3">
									<label for="departmentName">Department Name : <b style="color:red">*</b></label>
									<input type="text" id="departmentName" name="departmentName" class="form-control" value="<?php echo $row->departmentName?>" required>

									<div class="invalid-feedback">
										Required Field!
									</div>

								</div>
							</div>

							<div class="form-row">
								<div class="col-md-12 mb-3">
									<label for="departmentDescription">Department Description : </label>
									<div class="controls">
										<textarea id="departmentDescription" name="departmentDescription" class="form-control" row="2" cols="50">
											<?php echo $row->departmentDescription?>
										</textarea>

									</div>
								</div>
							</div>
                                       <?php
							echo "<div class='text-danger text-center'>";
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

											if($row->isActive == "1")
											{

											  echo"   <input id='isActive' type='checkbox'value='1' class='custom-control-input' name='isActive' checked='checked'>  
												 ";
											}
											else
											{
											  echo"   <input id='isActive' type='checkbox'value='1' class='custom-control-input'name='isActive'>  
												  ";
										  }?>
											<label class="custom-control-label" for="isActive">Active</label>
									</div>
								</div>
							</div>
							
						<?php 
						}
						?>

						<div class="text-xs-right">
							<button type="submit" name='s' class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>

							<a href="<?php echo base_url().'index.php/department/listRecords';?>">
								<button type="button" class="btn btn-reset">Back</button>
							</a>

						</div>
						
					</form>
				</div>
			</div>
		</div>
		
	</div>
          
		
		
	<script>
	
		$('document').ready(function(){
	 
			$("#departmentName").maxlength({max: 30});
			$("#departmentDescription").maxlength({max: 256});
	 
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
	
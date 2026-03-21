<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Vendor</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Update Credentials</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
		
	<div class="container-fluid col-md-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title">Update Credentials</h4>
				<hr/>
				
				<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url().'index.php/MyProfile/update';?>" novalidate>
					<?php
						echo "<div class='text-danger text-center' id='error_message'>";
						if (isset($error_message))
						{
						    echo '<b>'.$error_message.'</b>';
					    }
						echo "</div>";
				        if($query) 
				        { 
				            foreach($query->result() as $row)
				            {  
				    ?>  
					<div class="form-row"> 
						<input type="hidden" id="code" name="code" value="<?=$row->code?>" class="form-control" readonly> 
						<div class="col-md-6 mb-3">
							<label for="ownerContact">Owner Contact :<b style="color:red">*</b></label>
							<input type="number" id="ownerContact" name="ownerContact" class="form-control" value="<?=$row->ownerContact?>" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div>
					</div>	
					<div class="form-row">
						<div class="col-md-6 mb-3">
							<label for="password">Password : </label>
							<input type="password" id="password" name="password" class="form-control" value="" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div>
						<div class="col-md-6 mb-3">
							<label for="confirmPassword">Confirm Password: </label>
							<input type="password" id="confirmPassword" name="confirmPassword" class="form-control" value="" required>
							<div class="invalid-feedback">
								Required Field!
							</div>
						</div>
					</div>	 
					<?php 
				            } 
				        }
					?>
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
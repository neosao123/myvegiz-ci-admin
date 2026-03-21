	<div class="page-wrapper"> 
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Offer</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Update Offer</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container-fluid col-md-6">
		
			<div class="card">
				<div class="card-body">
					<h4 class="card-title">Update Offer</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" enctype="multipart/form-data" action="<?php echo base_url().'index.php/Offer/update';?>" novalidate>
						<?php
							echo "<div class='text-danger text-center' id='error_message'>";
							if (isset($error_message))
							{
								echo '<b>'.$error_message.'</b>';
							}
							echo "</div>";
							if($query)
							{
								foreach($query->result_array() as $r)
								{
						?>						 
						<div class="form-row">
							<div class="col-md-12 mb-3">
								<input value="<?= $r['code']?>" id="code" name="code" readonly hidden>
								<label for="coupanCode">Coupan Code :<b style="color:red">*</b></label>
								<input type="text" id="coupanCode" name="coupanCode" value="<?= $r['coupanCode']?>" class="form-control" required readonly>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
						</div>

						<div class="form-row">
							<div class="col-md-12 mb-3">
								<label for="discount"> Discount (%): <b style="color:red">*</b></label>
								<input type="number" id="discount" value="<?=$r['discount']?>" name="discount" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
						</div> 
						
						<div class="form-row">
							<div class="col-md-12 mb-3">
								<label for="minimumAmount"> Minimum Amount : <b style="color:red">*</b></label>
								<input type="number" id="minimumAmount" value="<?=$r['minimumAmount']?>" name="minimumAmount" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 
						</div> 
							
						<div class="form-row">
							<div class="col-md-12 mb-3">
								<label for="amountLimit"> Amount Limit : <b style="color:red">*</b></label>
								<input type="number" id="amountLimit" value="<?=$r['amountLimit']?>" name="amountLimit" class="form-control" required>
								<div class="invalid-feedback">
									Required Field!
								</div>
							</div> 	
						</div>
						
						 
						
						 
						<div class="row">
							<div class="form-group col-md-3">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox"> 
										<input type="checkbox" value="1" class="custom-control-input" <?= $r['isActive']==1 ? "checked" : "";?> id="isActive" name="isActive">
										<label class="custom-control-label" for="isActive">Active</label>
									</div>
								</div>
							</div>
						</div>
						
						<div class="text-xs-right">							
							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
							<a href="<?php echo base_url() . 'index.php/Offer/listRecords'; ?>">
								<button type="button" class="btn btn-reset">Back</button>
							</a>
						</div>
						<?php
								}
							}
						?>
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
	function buttonClick(value){
		// alert(value);
		if(value == 'categoryImage')
		{
			$("#itemImage").click();
		}
	}
	$("body").on("change","#itemImage",function(e){ 
		$("#photoError").empty();
		var file =  $('#itemImage')[0].files[0];
		fileType= file.type.split('/');
		if(fileType[0]=='image')
		{
			var filename = $(this).val();
			var lastIndex = filename.lastIndexOf("\\");
			if (lastIndex >= 0) {
				filename = filename.substring(lastIndex + 1);
			}
			$(this).next("label").text(filename); 
		} else {
			$(this).val("");
			$("#photoError").html("Please upload an image (jpeg,jpg,png,bmp)!");
			return false;
		} 
	});
	
	$("body").on("change","#menuCategoryCode",function(e){
		var this_code = $(this).val().trim();
		if (this_code!=undefined || this_code!="")
		{ 
			$.ajax({
				url:base_path+'../index.php/Food/Vendoritem/getsubCategoryItems',
				data:{'menuCategoryCode':this_code},
				type:'get',
				success:function(response)
				{
					if(response!=undefined|| response!="")
					{
						$("#menuSubCategoryCode").empty();
						$("#menuSubCategoryCode").append(response);
					}
				}	
			}) ;
		}
	});
	
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
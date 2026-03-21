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
					<h4 class="card-title">Edit Offer</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/Offer/update';?>" enctype="multipart/form-data" novalidate>
						<?php foreach($offerdata->result() as $row) { ?>
						<input name="code" id="code" value="<?= $row->code  ?>" readonly type="hidden">
						<div class="form-row"> 
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<span> <label for="cityCode">For City:</label> </span>
									<select  class="form-control" id="cityCode" name="cityCode">
										<option value="">Select City</option>
										<?php
										if($city){
											foreach ($city->result() as $c) {
												$selected = $row->cityCode==$c->code ? 'selected' : '';
												echo '<option value="' . $c->code . '" '.$selected.'>' . $c->cityName . '</option>';
											} 
										}
										?>
									</select>
									<span><?= form_error('cityCode')?></span>
								</div>
							</div>
							<div class="col-md-12 mb-3">
								<label for="offerTitle">Title : <b style="color:red">*</b></label>
								<input type="text" id="offerTitle" name="offerTitle" class="form-control" required value="<?= $row->offerTitle?>">
								<div class="invalid-feedback">
									Required Field !
								</div>
							</div> 							
							<div class="col-md-12 mb-3">
								<label for="offerDescription">Description : </label>
								<textarea rows="4" cols="50" class="form-control" name="offerDescription"><?= $row->description?></textarea>
							</div>
					 
							<div class="col-md-12 mb-3">
								<label for="offetTerms_condi">Terms & Conditions: </label>
								<textarea class="form-control" id="offetTerms_condi" name="offetTerms_condi" place-holder="Terms and Conditions.."><?= $row->termsCondition?></textarea>
							</div>
							
							<div class="col-md-6 mb-3">
								<div class="input-daterange input-group">
									 <label>Dates :</label>  
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control date-inputmask col-sm-5" name="start"  id="offerDateStart" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y',strtotime($row->startDate))?>"/>
											<div class="input-group-append">
												<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
											</div>
											<input type="text" class="form-control date-inputmask toDate" name="end" id="offerDateEnd" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y',strtotime($row->expireDate))?>"/>
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
								<?php 
									if($row->image!=""){
										echo '<img src="'.base_url().'uploads/offer/'.$row->image.'" style="height:100px;width:100px;">';
									}
								?>
							</div>  			
						 
							
							<div class="form-group">  
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox">
										<input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive" <?php if($row->isActive==1) echo 'checked'; ?>>
										<label class="custom-control-label" for="isActive">Active</label>
									</div>
								</div>
							</div>  
					
							
						</div>
						<?php } ?>
						<div class="text-xs-right form-row">
								<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
								<a href="<?= base_url()?>index.php/Offer/listRecords" class="btn btn-warning ml-1">Back</a>
							</div>
					</form>
				</div>
			</div>
		</div>
		
	 </div>
       
	<script>
	// $("#productRegularPrice").change(function(){
				// var productRegularPrice= $(this).val();
				// var productSellingPrice= $("#productSellingPrice").val();
				
				// if(parseInt(productRegularPrice) < parseInt(productSellingPrice))
				// {
					// alert('Regular Price must be high than Selling Price');	
					// $("#productRegularPrice").val('');
				// }
			// });
	 // var path=base_path+"product/uploadFile/";
			 // $(".dropzone").dropzone({
			
			// url:path,
			// paramName:'file',
			
            // success : function(file, response) {
				
				// if(response!='')
				// {
					// str+=response+',';
					// $('#upload_path').val(str);
				// }
				// else
				// {
					// alert('File Not Upload Successfully...');
				// }
            // }
        // });
	
		$('document').ready(function(){
			
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
			$("#offetTerms_condi").summernote({
				placeholder: 'Terms and conditions....',
				height: 250
			});
			 // $("#dzid").dropzone({ url: "/file/post" });
			  $("#offerTitle").maxlength({max: 100});
			  $('.btn-reset').click(function(){
					$('#offetTerms_condi').summernote('reset');
			  });
			
		}); // End Ready
		
		// Page Leave Yes / No
			
		// var page_isPostBack = false;
		// function windowOnBeforeUnload()
		// {
			// if ( page_isPostBack == true )
				// return; // Let the page unload

			// if ( window.event )
				// window.event.returnValue = 'Are you sure?'; 
			// else
				// return 'Are you sure?'; 
		// }
	
		// window.onbeforeunload = windowOnBeforeUnload;
		
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
	
	
	

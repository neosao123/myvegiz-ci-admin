	
	<div class="page-wrapper">
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Slider</h4>
					<div class="d-flex align-items-center"
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Create Slider</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid col-md-6">

			<div class="card">
				<div class="card-body">
					<h4 class="card-title"> Create Slider</h4> 
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/Slider/save';?>" enctype="multipart/form-data" novalidate>
						<div class="form-row mb-2"> 
							<label for="productCode">City : <b style="color:red">*</b></label> 
							<select required type="file" id="productCode" name="productCode" class="form-control">
								<option value="">Select City</option>
								<?php if($citymaster){foreach($citymaster->result() as $name){
									echo'<option value="'.$name->code.'">'.$name->cityName.'</option>';
								} }?>
							</select>							 
						</div>
						<div class="form-row mb-2">
                            <img src="<?= base_url()?>assets/images/preview-icon.png" id="preview_logo" class="img-fluid">
                        </div>
						<div class="form-row mb-2">                              
							<label for="imagePath">Slider File: <b style="color:red">*</b></label> 
							<input required type="file" id="imagePath" name="imagePath" class="form-control" accept="image/*,video/*">
							<input name="type" id="type" value="image" readonly type="hidden">
							 <span id="profilePhotoerr" class="text-danger"></span>
							<small>Please upload an image(431 W x 231 H) or an mp4 video(5mb max)</small>
						</div>
						<div class="form-row mb-2">  
							<div class="custom-control custom-checkbox">
								<div class="custom-control custom-checkbox">
									<input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive">
									<label class="custom-control-label" for="isActive">Active</label>
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
						<div class="text-xs-right">
							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
							<button type="reset" class="btn btn-reset">Reset</button>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	<script src="<?php echo base_url().'assets/admin/assets/extra-libs/checkImageSize/jquery.checkImageSize.min.js';?>"></script>
	<script>
		var reader = new FileReader();
		$("#imagePath").change(function(){
		    var formData = new FormData();
            formData.append('imagePath', $('#imagePath')[0].files[0]);
		    $.ajax({
                url : base_path+'Slider/checkMimeType',
                type : 'POST',
                data : formData,
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success : function(data) {
                   console.log(data);
                }
            });
		    
			$(".validate").remove();
			var file =  $('#imagePath')[0].files[0];
			fileType= file.type.split('/');
			if(fileType[0]=='video' || fileType[0]=='image'){
				$("#type").val(fileType[0]);
				if(fileType=='video'){
					if(file.size>5242880){
						$(this).val("");
						$(this).after("<span class='text-danger validate'>Please upload an video less than 5 mb</span>");
						return false;
					}					
				} else {
					fileDiamensionsValidate(this,431,231);  
				}
			} else {
				$(this).val("");
				$(this).after("<span class='text-danger validate'>Please upload an image (jpeg,jpg,png,bmp) or an video(mp4,mkv,mpeg)!</span>");
				return false;
			}
		});
		function readURL(input, image_ctrl_id) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#' + image_ctrl_id).attr('src', e.target.result);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
		var _URL = window.URL || window.webkitURL;
		function fileDiamensionsValidate(fdata,width,height){
			var file, img;
			if ((file = fdata.files[0])) {
				img = new Image();
				img.onload = function() {                
					if(this.width < width || this.height < height){
						$('#profilePhotoerr').text('PLease upload image of at least '+ width+'(w)x '+height+'(h) diamension');
						$("#imagePath").val("");
						return false;
					} else {
						var image_ctrl_id = "preview_logo";
						readURL(fdata, image_ctrl_id);
					}
				};
				img.onerror = function() {
					$('#profilePhotoerr').text('PLease upload image or video');
					$("#categoryImage").val("");
					return false;
				};
				img.src = _URL.createObjectURL(file);
			} else {
				return false;
			}
		}
		
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
	
	
	

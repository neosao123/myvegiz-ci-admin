	
	<div class="page-wrapper">
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-12 align-self-center">
					<h4 class="page-title">Grocery Slider</h4>
					<div class="d-flex align-items-center"
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">change Grocery Slider</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid col-md-6">

			<div class="card">
				<div class="card-body">
					<h4 class="card-title"> Change In Grocery Slider</h4>
					<hr/>
					
					<form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/Groceryslider/update';?>" enctype="multipart/form-data" novalidate>
						<?php foreach($query->result() as $row){?>
						
							<div class="form-row  mb-2" > 
								<label for="code">Code : <b style="color:red">*</b></label>   
								<input type="text" id="code" name="code" value="<?=$row->code?>" readonly class="form-control" >
							</div>						
							<div class="form-row mb-2"> 
								<label for="code">City : <b style="color:red">*</b></label>   
								<select required id="productCode" name="productCode" class="form-control">
									<option value="">Select City</option>
									<?php foreach($citymaster->result() as $name){
										echo'<option value="'.$name->code.'">'.$name->cityName.'</option>';
									}?>
								</select>	
								<div class="invalid-feedback">
									Required Field !
								</div>
								<script>
									var productName = "<?php echo $row->productCode?>";
									$("#productCode").val(productName);
								</script>					
							</div>
							<div class="form-row mb-2">
                                <img src="<?= base_url()?>assets/images/preview-icon.png" id="preview_logo" class="img-fluid d-none">
                            </div>
							<div class="form-row mb-2"> 								 
								<label for="imagePath">Slider File : </label> 
								<input type="file" id="imagePath" name="imagePath" class="form-control" accept="video/*,image/*,">
								<input name="type" id="type" readonly type="hidden" value="<?= $row->type ?>">
								<small>Please upload an image(431 W x 231 H) or an mp4 video(5mb max)</small>
							</div>
							<?php $imagePath=$row->imagePath?>
							<?php if($row->type=='image'){?>
								<div class="row el-element-overlay">
									<div class="col-md-6 mb-3">
										<div class="card">
											<div class="el-card-item">
												<div class="el-card-avatar el-overlay-1"> <img src="<?php echo $imagePath.'?'.time() != "" ? base_url().'uploads/slider/'.$imagePath.'?'.time() : base_url().'assets/admin/assets/images/users/1.jpg';?>" alt="Profile File"/>
													<div class="el-overlay">
														<ul class="list-style-none el-info">
															<li class="el-item"><a class="btn default btn-outline image-popup-vertical-fit el-link" href="<?php echo $imagePath.'?'.time() != "" ? base_url().'uploads/slider/'.$imagePath.'?'.time() : base_url().'assets/admin/assets/images/users/1.jpg';?>" target="_blank"><i class="icon-magnifier"></i></a></li>
															<li class="el-item"><a class="btn default btn-outline el-link" onclick="deleteButton('<?= $row->code ;?>','<?= $row->imagePath;?>')"><i class="icon-trash"></i></a></li>
														</ul>
													</div>
												</div>
												<div class="el-card-content">
													<h4 class="m-b-0">Product Photo</h4> <!-- <span class="text-muted">Graphics Designer</span> -->
												</div>
												<span>Please upload images of 431 X 231 (with x height) size.</span>
											</div>
										</div>
									</div>
								</div>	
							<?php } else if($row->type=='video'){ ?>
								<div class="col-md-12 mb-3">
									<video width="100%" height="240" controls>
										<source src="<?= base_url().'uploads/slider/'.$imagePath ?>">										 
										Your browser does not support the video tag.
									</video>
									<a class="btn default btn-outline-danger el-link" onclick="deleteButton('<?= $row->code ;?>','<?= $row->imagePath;?>')"><i class="icon-trash"></i></a></li>
								</div>
							<?php
							} else {
							    echo '<small>No Photo/Image is present..</small>';
							}
							?>
											
							<div class="form-group">
								<div class="custom-control custom-checkbox mr-sm-2">
									<div class="custom-control custom-checkbox">									   
										<?php

											if($row->isActive == "1")
											{

											  echo"   <input id='isActive' type='checkbox'value='1' class='custom-control-input'  name='isActive' checked='checked'>  
												 ";
											}
											else
											{
											  echo"   <input id='isActive' type='checkbox'value='1' class='custom-control-input'name='isActive'>  
												  ";
											}
										?>
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
						<?php }?>				
						<div class="text-xs-right">
							<button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
							<a href="<?= base_url().'index.php/Groceryslider/listRecords'?>" class="btn btn-reset" >Back</a>
						</div>						
					</form>
				</div>
			</div>
		</div>
	</div>	
	 
    <script src="<?php echo base_url().'assets/admin/assets/extra-libs/checkImageSize/jquery.checkImageSize.min.js';?>"></script>
	<script>
		function deleteButton(code,name){		
			$.ajax({
				 url: base_path+'Groceryslider/deleteProductImage',
				 method:"POST",
				 data:{'code':code,'name':name},
				 datatype:"text",
				 success: function(data)
				 {
					location.reload();				 
				 },
				 complete: function(data){
					
				 }
			});
		}
		function readURL(input, image_ctrl_id) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					$('#' + image_ctrl_id).attr('src', e.target.result);
					$('#' + image_ctrl_id).removeClass("d-none");
					$('.el-element-overlay').addClass("d-none");
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
		
		$('document').ready(function(){
			$("#imagePath").change(function(){
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
		}); 
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
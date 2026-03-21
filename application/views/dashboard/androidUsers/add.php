 
            

  <!--============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 align-self-center">
                    <h4 class="page-title">Android Users</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Android User </li>
                            </ol>
                        </nav>
                    </div>
                </div>
             </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid col-md-6">
            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->

            
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Android User Record</h4>
						<hr/>
						
                        <form class="needs-validation" method="post" id="myForm" action="<?php echo base_url().'index.php/AndroidUsers/save';?>" novalidate>
                            
							<div class="col-sm-6 mb-3">
								<div class="form-group">
									<span> <label for="cityCode">Service City:</label> </span>
									<select  class="form-control" id="cityCode" name="cityCode">
										<option value="">Select City</option>
										<?php
										foreach ($city->result() as $c) {
											echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
										} ?>
									</select>
								</div>
							</div>
                               

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="name">Client Name : <b style="color:red">*</b></label>
                                        <input type="text" id="name" name="name" class="form-control" required>
                                 <div class="invalid-feedback">
                                        Required Field!
                                </div>
								</div>
                               
                            </div>  
                              <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="mobile">Mobile Number : <b style="color:red">*</b></label>
                                        <input type="text" id="mobile" onkeypress="return validateFloatKeyPress(this, event, 9, -1);" name="mobile" class="form-control" required>
                                   <div class="invalid-feedback">
                                        Required Field !
                                </div>
								</div>
                                <div class="col-md-6 mb-3">
                                    <label for="emailId">Email ID :<b style="color:red">*</b> </label>
                                    <div class="controls">
									<input type="email" id="emailId" name="emailId" class="form-control"/ required>                              
									<div class="invalid-feedback">
                                        Required Field !
                                </div>
								</div>     
                                </div>    
                            </div>
							<div class="form-row">
                                 
								<div class="col-md-6 mb-3">
                                    <label for="local">Local :<b style="color:red">*</b> </label>
                                    <div class="controls">
										<input type="text" id="local" name="local" class="form-control"/ required> 
											<div class="invalid-feedback">
												Required Field !
											</div>
								
									</div>
                                </div> 								
                                <div class="col-md-6 mb-3">
                                    <label for="password">Password :<b style="color:red">*</b> </label>
									     <div class="controls">
										 <input type="password" id="password" name="password" class="form-control"/ required>                              
										 </div>
									</div>
                                </div>    
							<div class="form-row">
                                   
                                <div class="col-md-6 mb-3">
                                    <label for="flat">Flat :</label>
                                    <div class="controls">
									<input type="text"  id="flat" name="flat" class="form-control"/>
                                   <div class="invalid-feedback">
                                        Required Field !
									</div>								
									</div>
                                </div> 

								<div class="col-md-6 mb-3">
                                    <label for="landMark">Landmark : </label>
                                    <div class="controls">
									<input type="text" id="landMark" name="landMark" class="form-control"/>
                                   <div class="invalid-feedback">
                                        Required Field !
									</div>								
									</div>
                                </div>                                    								
                            </div>
							<div class="form-row">
                                <div class="col-md-8 mb-3">
                                    <label for="city">City :<b style="color:red">*</b> </label>
                                    <div class="controls">
                                <input type="text" id="city" name="city" class="form-control"/ required>
                                   
									</div>
                                </div>  
								<div class="col-md-4 mb-3">
                                    <label for="pincode">Pincode : <b style="color:red">*</b></label>
                                    <div class="controls">
                                <input type="text" id="pincode" onkeypress="return validateFloatKeyPress(this, event, 5, -1);" name="pincode" class="form-control"/ required>
                                   
									</div>
                                </div>								
                            </div>
							<div class="form-row">
                                 <div class="col-md-4 mb-3">
                                    <label for="gender">Gender :<b style="color:red">*</b> </label>
									     <div class="controls">
										      <div class="radio-inline">
												  <label><input type="radio" name="gender" value="f">Female</label>
											    
												 <label><input type="radio" name="gender"  value="m">Male</label>
												</div>
										</div>
                                </div>   
                                <div class="col-md-8 mb-3">
                                    <label for="state">State :<b style="color:red">*</b> </label>
                                    <div class="controls">
                                <input type="text" id="state" name="state" class="form-control"/ required>
                                   
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
                                        <input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive">
                                        <label class="custom-control-label" for="isActive">Active</label>
                                    </div>
                                </div>
                            </div>                    
                            <div class="text-xs-right"><center>
                                <button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
                                <!-- <button   class="btn btn-inverse">Back</button> -->
                                <button type="reset" class="btn btn-reset">Reset</button>
                                </center>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
           
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Right sidebar -->
            <!-- ============================================================== -->
            <!-- .right-sidebar -->
            <!-- ============================================================== -->
            <!-- End Right sidebar -->
            <!-- ============================================================== -->
       
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ==============================================================-->
		
<script>
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
	
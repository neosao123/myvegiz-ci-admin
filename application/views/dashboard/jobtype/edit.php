
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
                    <h4 class="page-title">Job Type</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Change In Job Type</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- <div class="col-7 align-self-center">
                    <div class="d-flex no-block justify-content-end align-items-center">
                        <div class="m-r-10">
                            <div class="lastmonth"></div>
                        </div>
                        <div class=""><small>LAST MONTH</small>
                            <h4 class="text-info m-b-0 font-medium">$58,256</h4></div>
                    </div>
                </div> -->
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

            <!-- <div class="col-md-4"> -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Change In Job Type</h4>
						<hr/>
						
                        <form class="needs-validation" method="post" id="myform" action="<?php echo base_url().'index.php/Jobtype/update';?>" novalidate>
                       <?php
                    
                      foreach($query->result() as $row)
                      {
                        ?>
                             <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="code">Code : </label>
                                        <input type="text" id="code" name="code" readonly class="form-control" value="<?php echo $row->code?>">
                                
                                </div>
                               
                            </div>
                            

                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="jobTypeName">Job Type Name :<b style="color:red">*</b> </label>
                                        <input type="text" id="jobTypeName" name="jobTypeName" class="form-control" value="<?php echo $row->jobTypeName?>" required>
                                
								       <div class="invalid-feedback">
                                        Required Field!
                                </div>
                                </div>
                               
                            </div>    
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="jobTypeDescription">Job Type Description : </label>
                                    <div class="controls">
									<textarea type="text" id="jobTypeDescription" name="jobTypeDescription" class="form-control"> <?php echo $row->jobTypeDescription?></textarea>
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
                                        <!--  <input type="checkbox"  value="1" class="custom-control-input" id="isActive" name="isActive">  -->
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
                      }?>
                                        <label class="custom-control-label" for="isActive">Active</label>
                                    </div>
                                </div>
                            </div> 
                            <?php }?>                    
                            <div class="text-xs-right">
                                <button type="submit" class="btn btn-success" onclick="page_isPostBack=true;">Submit</button>
								<a href="<?php echo base_url().'index.php/Jobtype/listRecords';?>">
								<button type="button" class="btn btn-reset">Back</button>
								</a>
                                
                            </div>
                        </form>
                    </div>
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
	
		$('document').ready(function(){
		 
			$("#jobTypeName").maxlength({max: 30});
			$("#jobTypeDescription").maxlength({max: 256});
		  
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
	
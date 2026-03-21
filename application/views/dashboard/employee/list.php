        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Employee</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Employee List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            
                            <div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/employee/add';?>">Create Employee</a></div>
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
            <div class="container-fluid">
				<!-- ============================================================== -->
				<!-- Start Page Content -->
				<!-- ============================================================== -->
				<!-- basic table -->
				<div class="card">
					<div class="card-body">
						<h3 class="card-title"> Employee Filter :</h3>
						<form  class="form-horizontal">
							<hr>
							<div class="form-row">
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="firstName">First Name :</label> </span>
										<input type="text"  class="form-control" list="firstNameList" id="firstName" name="firstName" placeholder="Enter First Name Here">
										<datalist id="firstNameList">
										</datalist>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="middleName">Middle Name :</label> </span>
										<input type="text"  class="form-control" list="middleNameList" id="middleName" name="middleName" placeholder="Enter Middle Name Here">
										<datalist id="middleNameList">
										</datalist>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="lastName">Last Name :</label> </span>
										<input type="text"  class="form-control" list="lastNameList" id="lastName" name="lastName" placeholder="Enter Last Name Here">
										<datalist id="lastNameList">
										</datalist>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<label for="departmentName"> Department Name: </label>
									<input type="text"  class="form-control" list="departmentNameList" id="departmentName" name="departmentName" placeholder="Enter Department Name Here">
									<datalist id="departmentNameList">
										<?php
										foreach ($queryDept->result() as $dept) {
											echo "<option value='" . $dept->code . "'>" . $dept->departmentName . "</option>";
										}
										?>
									</datalist>
								</div>
								<div class="col-sm-2 mb-3">
									<label for="Gender">Gender: </label>
									<select id="gender" name="gender" class="form-control required">
										<option value="">Select option</option>
										<option value="m">Male</option>
										<option value="f">Female</option>
									</select>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="employeeDistrict">Employee District :</label> </span>
										<input type="text"  class="form-control" list="employeeDistrictList" id="employeeDistrict" name="employeeDistrict" placeholder="Enter Employee District Here">
										<datalist id="employeeDistrictList">
											<?php
											foreach ($querydistrict->result() as $dis) {
												echo "<option value='" . $dis->currentDistrict . "'>" . $dis->currentDistrict . "</option>";
											}
											?>
										</datalist>
									</div>
								</div>
								<div class="form-group col-sm-2 mb-3">
									<span> <label for="empPincode">Employee Pincode :</label> </span>
									<input type="text" onkeypress="return validateFloatKeyPress(this, event, 5, -1);" class="form-control" list="empPincodeList" id="empPincode" name="empPincode" placeholder="Enter Employee Pincode Here">
								</div>
								<div class="col-sm-3 mb-3">
									<label for="employmentStatus"> Employment Status: </label>
									<input type="text"  class="form-control" list="employmentStatusList" id="employmentStatus" name="employmentStatus" placeholder="Enter Employment Status Here">
									<datalist id="employmentStatusList">
										<?php
										foreach ($queryEmploymentStatus->result() as $empStatus) {
											echo "<option value='" . $empStatus->employmentStatusSName . "'>" . $empStatus->employmentStatusName . "</option>";
										}
										?>
									</datalist>
								</div>
								<div class="col-sm-2 mb-3">
									<label for="employeeDesignation"> Designation: </label>
									<div class="clearfix">
										<input type="text"  class="form-control" list="employeeDesignationList" id="employeeDesignation" name="employeeDesignation" placeholder="Enter Employee Designation Here">
										<datalist id="employeeDesignationList">
											<?php
											foreach ($queryDesignation->result() as $designation) {
												echo "<option value='" . $designation->code . "'>" . $designation->designationName . "</option>";
											}
											?>
										</datalist>
									</div>   
								</div>
								<div class="col-sm-2 mb-3">
									<label for="employeeJobType"> Job Type: </label>
									<div class="clearfix">
										<input type="text"  class="form-control" list="employeeJobTypeList" id="employeeJobType" name="employeeJobType" placeholder="Enter employee Job Type Here">
										<datalist id="employeeJobTypeList">
											<?php
											foreach ($queryJobType->result() as $jobType) {
												echo "<option value='" . $jobType->code . "'>" . $jobType->jobTypeName . "</option>";
											}
											?>
										</datalist>
									</div>   
								</div>
							</div>
							<div class="row">
								<div class="card-body">
									<div class="form-group m-b-0  text-center">
										<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
										<button type="Reset" class="btn btn-dark waves-effect waves-light btn btn-inverse" id="btnClear">Clear</button>
									</div>
								</div>
							</div>
						</form>	
					</div>

					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-body">
									<h4 class="card-title">Employee List</h4>
									<div class="table-responsive">
										<table id="datatableEmployee" class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Sr.No.</th>
													<th>Code</th>
													<th>Name</th>
													<th>Contact</th>
													<th>Email</th>
													<th>Employment Status</th>
													<th>Job Type</th>
													<th>Status</th>
													<th>Operations</th>  
												</tr>
											</thead>

										</table>
									</div>

								</div>
							</div>
						</div>
					</div>	
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
			
		</div>
            </div>		
        </div>    
  <!-- sample modal content -->
	<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
		<div class="modal-dialog">
		
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Employee</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
				</div>
				<div class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	 
	<!-- /.modal -->
				<script>
		   $('document').ready(function() {
			   $('#firstName').keyup (function () {
                  if($(this).val().length > 3)
                  {
                    var firstName = $(this).val();
                    $.ajax({
                             url:'<?php echo site_url('Employee/getemployeeName');?>',
                             method:"GET",
                             data:{firstName:firstName},
                             datatype:"text",
                             success: function(data)
                             {
								////console.log(data);
								
							   $("#firstNameList").html(data);
                             }
							 
                    });
                  }
				  
                });
				$('#middleName').keyup (function () {
                  if($(this).val().length > 3)
                  {
                    var middleName = $(this).val();
                    $.ajax({
                             url:'<?php echo site_url('Employee/getemployeeName');?>',
                             method:"GET",
                             data:{middleName:middleName},
                             datatype:"text",
                             success: function(data)
                             {
								//console.log(data);
								
							   $("#middleNameList").html(data);
                             }
							 
                    });
                  }
				  
                });
					$('#lastName').keyup (function () {
                  if($(this).val().length > 3)
                  {
                    var lastName = $(this).val();
                    $.ajax({
                             url:'<?php echo site_url('Employee/getemployeeName');?>',
                             method:"GET",
                             data:{lastName:lastName},
                             datatype:"text",
                             success: function(data)
                             {
								//console.log(data);
								
							   $("#lastNameList").html(data);
                             }
							 
                    });
                  }
				  
                });
				$('#employeeDistrict').keyup (function () {
                  if($(this).val().length > 3)
                  {
                    var District = $(this).val();
                    $.ajax({
                             url:'<?php echo site_url('Employee/getemployeeDistrict');?>',
                             method:"GET",
                             data:{District:District},
                             datatype:"text",
                             success: function(data)
                             {
								//console.log(data);
								
							   $("#employeeDistrictList").html(data);
                             }
							 
                    });
                  }
				  
                });

				
			   var  keypincode,keyfirstName,keymiddleName,keylastName,keydepartmentName,keygender,keyemployeeDistrict,keyemploymentStatus,keyemployeeDesignation,keyemployeeJobType="";
			getDataTable(keypincode,keyfirstName,keymiddleName,keylastName,keydepartmentName,keygender,keyemployeeDistrict,keyemploymentStatus,keyemployeeDesignation,keyemployeeJobType);
 $('#btnSearch').on('click', function (e){
		$('#datatableEmployee').DataTable().state.clear();
		keyfirstName=$('#firstName').val();
		
		keymiddleName=$('#middleName').val();
		keylastName=$('#lastName').val();
		// keyorganizationName=$('#organizationName').val();
		// keyofficeName=$('#officeName').val();
		keydepartmentName=$('#departmentName').val();
		
		// keycontractName=$('#contractName').val();
		keygender=$('#gender').val();
		keyemployeeDistrict=$('#employeeDistrict').val();
		keyemploymentStatus=$('#employmentStatus').val();
		keyemployeeDesignation=$('#employeeDesignation').val();
		keyemployeeJobType=$('#employeeJobType').val();
		keypincode=$('#empPincode').val();
		
	
		getDataTable(keypincode,keyfirstName,keymiddleName,keylastName,keydepartmentName,keygender,keyemployeeDistrict,keyemploymentStatus,keyemployeeDesignation,keyemployeeJobType);
	});			
			   		   //loadTable();
				 function getDataTable(p_keypincode,p_keyfirstName,p_keymiddleName,p_keylastName,p_keydepartmentName,p_keygender,p_keyemployeeDistrict,p_keyemploymentStatus,p_keyemployeeDesignation,p_keyemployeeJobType)
				{
					//console.log('cname: '+p_keycontractName);
						 if ($.fn.DataTable.isDataTable("#datatableEmployee")) {
						 $('#datatableEmployee').DataTable().clear().destroy();
					}
							
			   var dataTable = $('#datatableEmployee').DataTable({  
					stateSave: true,
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": false,
				   "ajax":{  
						url: base_path+"Employee/getEmployeeList", 
						data:{'firstName':p_keyfirstName,
								'middleName':p_keymiddleName,
								'lastName':p_keylastName,
						 
						 
						 'departmentName':p_keydepartmentName,
						 
						 'gender':p_keygender,
						 'employeeDistrict':p_keyemployeeDistrict,
						 'employmentStatus':p_keyemploymentStatus,
						 'employeeDesignation':p_keyemployeeDesignation,
						 'employeeJobType':p_keyemployeeJobType,
						 'pincode':p_keypincode
						},						
						type:"GET" , 
				    "complete": function(response) {
									$(".blue").click(function(){
									 var code=$(this).data('seq');
									//alert(code)
									 $.ajax({
											url: base_path+"Employee/view",  
											method:"GET",
											data:{code:code},
											datatype:"text",
											success: function(data)
											{	
											//alert(data)
												$(".modal-body").html(data);
												//console.log(data)
											}
										});
									});
												  //delete
			 $('.mywarning').on("click", function() {
				var code=$(this).data('seq');
				//alert(code);
					swal({
						title: "Are you sure?",
						text: "You want to delete the Employee Record of  "+code,
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, delete it!",
						cancelButtonText: "No, cancel it!",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						//console.log(e);
						if(e)
						{
							$.ajax({
								url: base_path+"Employee/delete",
								type: 'POST',
								data:{
								  'code':code
								},
								success: function(data) {
								
								  if(data)
								  {
									swal({ 
										  title: "Completed",
										   text: "Successfully Deleted",
											type: "success" 
										  },
										  function(isConfirm){
									  if (isConfirm) {
										//location.reload(true);
										getDataTable();
										
									  }
									});
									
								  }
								  else
								  {
								  swal("Failed", "Record Not Deleted", "error");
								  }
								},
								error: function(xhr, ajaxOptions, thrownError) {
								   var errorMsg = 'Ajax request failed: ' + xhr.responseText;
								   alert(errorMsg);
								   //console.log("Ajax Request for patient data failed : " + errorMsg);
								}
							   });
						}
						else
						{
							swal("Cancelled", "Your Employee Record is safe :)", "error");
						}
					});
				});	
								  }
				   }
			  });
	}   
		   
		   
		   
		   
		   $('#btnClear').click(function(){
			   var  keypincode,keyfirstName,keymiddleName,keylastName,keyorganizationName,keyofficeName,keydepartmentName,keycontractName,keygender,keyemployeeDistrict,keyemploymentStatus,keyemployeeDesignation,keyemployeeJobType="";
			   getDataTable(keypincode,keyfirstName,keymiddleName,keylastName,keyorganizationName,keyofficeName,keydepartmentName,keycontractName,keygender,keyemployeeDistrict,keyemploymentStatus,keyemployeeDesignation,keyemployeeJobType);
		   });
		   
	   });
		   
		   
		   	   $( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  
	
         toastr.success(obj.message, 'Employee', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Employee', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
		</script>
	
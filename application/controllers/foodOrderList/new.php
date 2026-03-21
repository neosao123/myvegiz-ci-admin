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
                        <h4 class="page-title">User List</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">User List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                      <div class="col-7 align-self-center">
                    <div class="d-flex no-block justify-content-end align-items-center">
                        
                        <div class="m-r-10">
						 
                        <!-- <div class=""><small>LAST MONTH</small>
                            <h4 class="text-info m-b-0 font-medium">$58,256</h4></div>
                    </div> --> 
                </div> 
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
         <h3 class="card-title"> Food Filter Commission :</h3>
			<form class"form-horizontal">
            <hr>
				<div class="form-row">
					<div class="col-sm-3">
						<div class="form-group">
							<span> <label>Employee Name :</label> </span>
							<input type="text"  class="form-control" list="employeeCodeList" id="employeeCode" name="employeeCode" placeholder="Enter Employee Name Here ">
							 <datalist id="employeeCodeList">
							  <?php foreach($employee->result() as $emp){
								echo'<option value="'.$emp->code.'">'.$emp->firstName.' '. $emp->middleName.' '. $emp->lastName.'</option>';
							}?></datalist>
						</div>
									
								
									
								
							
							
					</div>
				</div>
			   	<hr/>
				<div class="form-group m-b-0  text-center">
					<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
					<button type="reset" id="btnClear" class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
				</div>
			</form>
			</div>
			</div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Food User Commission List</h4>
                                <div class="table-responsive">
                                    <table id="datatableCommission" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>Employee Name</th>
												<th>Points</th>
												<th>Operations</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
					
					<!--vegitable list-->

         
					<!--end-->
					 
                </div>
            </div>  
			
			   <div class="container-fluid">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
				<div class="card">
        <div class="card-body">
         <h3 class="card-title"> Filter Commission :</h3>
			<form class"form-horizontal">
            <hr>
				<div class="form-row">
					<div class="col-sm-3">
						<div class="form-group">
							<span> <label>Employee Name :</label> </span>
							<input type="text"  class="form-control" list="employeeCodeList" id="employeeCode" name="employeeCode" placeholder="Enter Employee Name Here ">
							 <datalist id="employeeCodeList">
							  <?php foreach($employee->result() as $emp){
								echo'<option value="'.$emp->code.'">'.$emp->firstName.' '. $emp->middleName.' '. $emp->lastName.'</option>';
							}?></datalist>
						</div>
					</div>
				</div>
			   	<hr/>
				<div class="form-group m-b-0  text-center">
					<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
					<button type="reset" id="btnClear" class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
				</div>
			</form>
			</div>
			</div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">User Commission List</h4>
                                <div class="table-responsive">
                                    <table id="datatableCommission1" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>Employee Name</th>
												<th>Points</th>
                                                <th>Operations</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
					 <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">User Commission History</h4>
                                <div class="table-responsive" id="historydiv">
                                     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
			<!-- sample modal content -->
			<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Commission </h4>
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
			  </div>		
					<script>
					
			 $( document ).ready(function() { //nitin 05 DEC 2018
		$("#orderStatus option[value=SHP]").hide();
		$("#orderStatus option[value=DEL]").hide();
	    $("#orderStatus option[value=PLC]").hide();
		$("#orderStatus option[value=RJT]").hide();
		$('#fromDate').datepicker({
			dateFormat: "mm/dd/yy",
			showOtherMonths: true,
			selectOtherMonths: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			todayHighlight: true,
			orientation: "bottom left",
		});
		$('#toDate').datepicker({
			dateFormat: "mm/dd/yy",
			showOtherMonths: true,
			selectOtherMonths: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			todayHighlight: true,
			orientation: "bottom left",
		});	
			
		$('.btn-inverse').click(function(){
			dataTable();
		});		
					
		   $( document ).ready(function() {
			   	   var employeeCode="",userName="",userRole="",fromDate,toDate;
			
			function clearGlobalVariables()
			{
				employeeCode="",userName="",userRole="",fromDate,toDate;
				loadTable();	
			
			}
			$('#btnClear').on('click', function (e){
				clearGlobalVariables();	
			});
			loadTable();
			$('#btnSearch').on('click', function (e){
				$('#datatableCommission').DataTable().state.clear();
				employeeCode=$('#employeeCode').val();
				fromDate=$('#fromDate').val();
				toDate=$('#toDate').val();
				loadTable(employeeCode);		
			}); 
			   // loadTable();
							function loadTable(p_employeeCode){
								if ($.fn.DataTable.isDataTable("#datatableCommission")) {
								  $('#datatableCommission').DataTable().clear().destroy();
								}
			 
			     $('#datatableCommission').DataTable({  
					stateSave: true,
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": false,
				   "ajax":{  
						url: base_path+"Commission/getCommissionList",  
						data:{
							'userCode':p_employeeCode
							
							 
						},						
						type:"GET",  
				   
				     complete: function(json) {
						 //console.log(json);
									$(".blue").click(function(){
									 var code=$(this).data('seq');
									  console.log(code);
									$(".modal-body").empty();
									 $.ajax({
											url: base_path+"Commission/viewCurrentHistory",  
											type:"GET",
											data:{code:code},
											datatype:"text",
											success: function(data)
											{
												$(".modal-body").html(data);
											}
										});
									});
										//paid history
										$(".history").click(function(){
											var code=$(this).data('seq');
											$("#historydiv").empty();
											$.ajax({
												url: base_path+"Commission/showhistory",  
												method:"GET",
												data:{code:code},
												datatype:"text",
												success: function(data)
												{
													$("#historydiv").html(data);
												}
											});
										});
			  
			  }
				   }
			  });
		}   
		   });
	$( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  toastr.success(obj.message, 'Commission', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Commission', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
		</script>
					
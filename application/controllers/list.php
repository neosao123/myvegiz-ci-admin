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
                        <h4 class="page-title">UserList</h4>
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
<div class=""><a class="btn btn-primary" href="<?php echo base_url().'index.php/usermaster/add';?>">Create User</a></div>    
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">User List</h4>
                                
                                <div class="table-responsive">
                                    <table id="datatableUserMaster" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>Code</th>
                                                <th>Employee name</th>
                                                <th>UserName</th>
                                               <!-- <th>password </th>-->
                                                <th>User Role</th>
                                                 <th>Status</th>
                                                 <th>Operation</th>
                                            </tr>
                                        </thead>
                                        
                                    </table>
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
							<h4 class="modal-title">View user</h4>
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
		   $( document ).ready(function() {
			   loadTable();
							function loadTable(){
								if ($.fn.DataTable.isDataTable("#datatableJobType")) {
								  $('#datatableJobType').DataTable().clear().destroy();
								}
			   
			   var dataTable = $('#datatableUserMaster').DataTable({  
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": false,
				   "ajax":{  
						url: base_path+"Usermaster/getUserMasterList",  
						type:"GET",  
				   
				     "complete": function(settings, json) {
									$(".blue").click(function(){
									 var code=$(this).data('seq');
									
									 $.ajax({
											url: base_path+"Usermaster/view",  
											method:"GET",
											data:{code:code},
											datatype:"text",
											success: function(data)
											{
												
												$(".modal-body").html(data);
												
											}
										});
									});
										//delete
			 $('.mywarning').on("click", function() {
				var code=$(this).data('seq');
				//alert(code);
					swal({
						title: "Are you sure?",
						text: "You will not be able to recover this imaginary file!",
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, delete it!",
						cancelButtonText: "No, cancel plx!",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						console.log(e);
						if(e)
						{
							$.ajax({
								url: base_path+"Usermaster/delete",
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
										loadTable();
										
									  }
									});
									
								  }
								  else
								  {
								   toastr.success('Record Not Deleted', 'Failed', { "progressBar": true });
								  }
								},
								error: function(xhr, ajaxOptions, thrownError) {
								   var errorMsg = 'Ajax request failed: ' + xhr.responseText;
								   alert(errorMsg);
								   console.log("Ajax Request for patient data failed : " + errorMsg);
								}
							   });
						}
						else
						{
							swal("Cancelled", "Your imaginary file is safe :)", "error");
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
		  toastr.success(obj.message, 'User Master', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'User Master', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
		</script>
					
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
                <h4 class="page-title">City List</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url().'admin/index';?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">City List</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-7 align-self-center">
                <div class="d-flex no-block justify-content-end align-items-center">
                    <div class=""><a class="btn btn-myve" href="<?php echo base_url().'City/add';?>">Create City</a></div>
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
                        <h4 class="card-title"> City List</h4>
                        <div class="table-responsive">
                            <table id="datatableUom" class="table table-striped table-bordered ">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Code</th>
                                        <th>City Name</th>
                                        <th>Status</th>
										<!--<th>Min. Order</th>
										<th>Delivery Charge</th>-->
                                        <th>Operations</th>
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
</div>
<!-- sample modal content -->
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View City</h4>
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
					if ($.fn.DataTable.isDataTable("#datatableUom")) {
					  $('#datatableUom').DataTable().clear().destroy();
					}
			        var dataTable = $('#datatableUom').DataTable({  
					stateSave: false,
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": true,
				   "ajax":{  
						url: base_path+"City/getCityList",  
						type:"POST" , 
				   
				    "complete": function(response) {
					 $(".blue").click(function(){
									 var code=$(this).data('seq');
									 $.ajax({
											url: base_path+"City/view",  
											method:"POST",
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
				// alert('hii')
				var code=$(this).data('seq');
				//alert(code);
					swal({
						title: "Are you sure?",
						text: "You want to delete City Record of "+code,
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, delete it!",
						cancelButtonText: "No, cancel it!",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						console.log(e);
						if(e)
						{
							$.ajax({
								url: base_path+"City/delete",
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
								  swal("Failed", "Record Not Deleted", "error");
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
							swal("Cancelled", "Your City Record is safe :)", "error");
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
		  toastr.success(obj.message, 'City', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'City', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
		</script>
		<script>
    //=============================================//
    //    File export                              //
    //=============================================//
    // $('#file_export').DataTable({
        // dom: 'Bfrtip',
        // buttons: [
            // 'copy', 'csv', 'excel', 'pdf', 'print'
        // ]
    // });
    // $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-cyan text-white mr-1');
    </script>
				





 

 
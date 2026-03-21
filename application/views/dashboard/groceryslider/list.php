 

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
                        <h4 class="page-title">Grocery Slider</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Grocery Slider List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">                            
                            <div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/Groceryslider/add';?>">Create Grocery Slider</a></div>
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
                                <h4 class="card-title"> Filter Grocery Slider List</h4>
								<div class="form-row">
									<div class="col-sm-3 mb-3">
										<div class="form-group">
											<span> <label for="cityCode">City:</label> </span>
											<select  class="form-control" id="cityCode" name="cityCode">
												<option value="">Select City</option>
												<?php
												if($city){
													foreach ($city->result() as $c) {
														echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
													} 
												}
												?>
											</select>
										</div>
									</div>
									<div class="col-sm-3 mb-3">
										<div class="form-group">
											<span> <label for="cityCode">Slider Type</label> </span>
											<select class="form-control" id="sliderType" name="sliderType">
												<option value="">Select Type</option>
												<option value="image">Image</option>
												<option value="video">Video</option>
											</select>
										</div>
									</div>
								</div>
								<center>
									<div class="row">
										<div class="card-body">
											<div class="form-group m-b-0  text-center">
												<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
												<button type="Reset" class="btn btn-dark waves-effect waves-light btn btn-inverse" id="btnClear">Clear</button>
											</div>
										</div>
									</div>	
								</center>
							</div>
						</div>
					</div>	
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Grocery Slider List</h4>                              
                                <div class="table-responsive">
                                    <table id="datatableGroceryslider" class="table table-striped table-bordered ">
                                        <thead>
                                            <tr>
												<th>ID</th>
												<th>Code</th>                                               
												<th>Main Category</th>                                               
												<th>City</th>
												<th>Image/Video</th>
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
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
			<!-- sample modal content -->
			<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">View Slider</h4>
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
	$(document).ready(function() {
		loadTable();
		var cityCode , type = "";
		$('#btnSearch').on('click', function (e){
			$('#datatableGroceryslider').DataTable().state.clear();
			type=$('#sliderType').val();
			cityCode =$("#cityCode").val();
			loadTable(cityCode,type);
		});			
		$('#btnClear').on('click', function (e){
			$("#cityCode").val('');
			$('#sliderType').val('');
			loadTable();
		});
		function loadTable(cityCode,type){
			if ($.fn.DataTable.isDataTable("#datatableGroceryslider")) {
				$('#datatableGroceryslider').DataTable().clear().destroy();
			}
			var dataTable = $('#datatableGroceryslider').DataTable({  
				stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"Groceryslider/getSliderList",  
					data:{'cityCode':cityCode,'type':type},
					type:"GET",  
					"complete": function(response) {
						$(".blue").click(function(){
							var code=$(this).data('seq');
							$.ajax({
								url: base_path+"Groceryslider/view",  
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
								text: "You want to delete Slider Record of "+code,
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
										url: base_path+"Groceryslider/delete",
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
										  
											swal("Failed","Record Not Deleted",'error');
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
									swal("Cancelled", "Your Slider Record is safe :)", "error");
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
			 swal('Grocery Slider',obj.message,'success');
	   
		  }
		  else
		  {
			  swal('Grocery Slider',obj.message,'error');
			 }
		}
		//end show alerts
	});
</script>
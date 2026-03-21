 
<div class="page-wrapper">
	<!-- ============================================================== -->
	<!-- Bread crumb and right sidebar toggle -->
	<!-- ============================================================== -->
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Offer </h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Offer List</li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">
					
					<div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/Offer/add';?>">Add Offer</a></div>
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
		<div class="card">
			<div class="card-body">
				<h3 class="card-title"> Filter Offer :</h3>
				<form class"form-horizontal">
					<hr>
					<div class="form-row">
				 
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<span> <label for="cityCode">For City:</label> </span>
								<select  class="form-control" id="cityCode" name="cityCode">
									<option value="">Select City</option>
									<?php
									if($city){
										foreach ($city->result() as $c) {
											echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
										} 
									}?>
								</select>
								<span><?= form_error('cityCode')?></span>
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
		<!-- basic table -->
		<div class="row">
			<div class="col-12 ">
				<div class="card ">
					<div class="card-body">
						<h4 class="card-title">Offer List</h4>
						
						<div class="table-responsive">
							<table id="datatable-offer" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Sr.No.</th>
										<th>Code</th>
										<th>For City</th>
										<th>Offer Title</th>
										<th>Image</th> 
										<th>Description</th> 												
										<th>Start Date</th> 
										<th>Expiry Date</th>
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
					<h4 class="modal-title">View Offer </h4>
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
	$( document ).ready(function() {		 
		var cityCode="";
		getDataTable('');
		function clearGlobalVariables()
		{
			cityCode="";
		} 
		// End clearGlobalVariables Function
		$('#btnSearch').on('click', function (e){
			$('#datatable-offer').DataTable().state.clear();
			cityCode=$('#cityCode').val();				 
			getDataTable(cityCode);
		}); 
		// End Search Click
		function getDataTable(cityCode)
		{
			if ($.fn.DataTable.isDataTable("#datatable-offer")) {
				$('#datatable-offer').DataTable().clear().destroy();
			}			 
			var dataTable = $('#datatable-offer').DataTable({  
				stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"Offer/getOfferList", 
					data:{'cityCode':cityCode},						
					type:"GET" , 
					"complete": function(json) {
						$(".blue").click(function(){
							var code=$(this).data('seq');									 
							$.ajax({
								url: base_path+"Offer/view",  
								method:"GET",
								data:{'code':code},
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
								text: "You want to delete Product Record of "+code+", product against stock also deleted.",
								type: "warning",
								showCancelButton: !0,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes, delete it!",
								cancelButtonText: "No, cancel plx!",
								closeOnConfirm: !1,
								closeOnCancel: !1
							}, function(e) {
								if(e)
								{
									$.ajax({
										url: base_path+"Offer/delete",
										type: 'POST',
										data:{'code':code},
										success: function(data) {
											if(data)
											{
												swal({ 
													  title: "Completed",
													   text: "Successfully Deleted",
														type: "success" 
												},
												function(isConfirm){
													if (isConfirm)  getDataTable('','','','','');
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
										}
									});
								}
								else
								{
									swal("Cancelled", "Your Product Record is safe :)", "error");
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
		<?php if($error!="") { ?>
		var data='<?php echo $error; ?>';
		if(data!='')
		{
			var obj=JSON.parse(data);
			if(obj.status) toastr.success(obj.message, 'Offer', { "progressBar": true });
			else  toastr.error(obj.message, 'Address', { "progressBar": true });
		}
		<?php }?>
		//end show alerts
	});
</script>
			
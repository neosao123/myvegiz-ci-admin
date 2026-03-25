<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Cancel / Reject Orders List</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Cancel Order List</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h3 class="card-title"> Filter :</h3>
						<hr>
						<form>
							<div class="form-row">
									<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderCode">Order Code :</label> </span>
										<input type="text"  class="form-control" list="orderlist" id="orderCode" name="orderCode" placeholder="Enter Order Code Here ">
											<datalist id="orderlist">
											<?php if($orderCode){ foreach($orderCode->result() as $od){
											echo'<option value="'.$od->code.'"></option>';
											} } ?>
											</datalist>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="vender">Vendor:</label> </span>
										<input type="text"  class="form-control" list="vendorList" id="vendorCode" name="vendorCode" placeholder="Enter Vendor Name Here ">
											<datalist id="vendorList">
											<?php if($vendor){ foreach($vendor->result() as $v){
											echo'<option value="'.$v->code.'">'.$v->entityName.'</option>';
											} } ?>
											</datalist>	
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="deliveryboy">Delivery Boy:</label> </span>
										<input type="text"  class="form-control" list="deliveryboy" id="deliveryboyCode" name="deliveryboyCode" placeholder="Enter Name Of Delivery Boy Here ">
											<datalist id="deliveryboy">
											<?php print_r($deliveryboy->result()); if($deliveryboy){ foreach($deliveryboy->result() as $d){
											echo'<option value="'.$d->code.'">'.$d->firstName.' '.$d->lastName.'</option>';
											} } ?>
											</datalist>
									</div>
								</div>
							
								<!--
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="cityCode">City:</label> </span>
										<select  class="form-control" id="cityCode" name="cityCode">
											<option value="">Select City</option>
											<?php
											foreach ($city->result() as $c) {
												echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
											} ?>
										</select>
									</div>
								</div>
								<!--<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Order Status :</label> </span>
										<select  class="form-control" id="orderStatus" name="orderStatus">
											<option value="">Select Option</option>
											<?php
											foreach ($orderStatus->result() as $status) {
											echo '<option value="' . $status->statusSName . '">' . $status->statusName . '</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span><label for="pinCode">Pincode :</label> </span>
										<input type="text"  class="form-control" onkeypress="return validateFloatKeyPress(this, event, 5, -1);"  id="pinCode" name="pinCode" >
									</div>
								</div>-->
								<!--<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Area:</label> </span>
										<input type="text" name="areaCode" id="areaCode" class="form-control" list="areaCodeList">
										<datalist id="areaCodeList">
											<?php
											foreach ($address->result() as $addr) {
											echo '<option value="' . $addr->code . '">' . $addr->place . '</option>';
											}?>
										</datalist>
									</div>
								</div>-->
								<?php	
									$todayDate = date('d/m/Y');
									$previousDate = date('d/m/Y', strtotime('- 7 days'));
								?>
								<div class="col-sm-5">
									<div class="input-daterange input-group">
									<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control date-inputmask col-sm-5" name="start"  id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>"/>
											<div class="input-group-append">
											<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
										  </div>
										<input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" placeholder="dd/mm/yyyy" value="<?= $todayDate ?>"/>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="form-group  text-center">
										<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
										<button type="Reset" class="btn btn-dark waves-effect waves-light btn btn-inverse" id="btnClear">Clear</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- basic table -->
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Cancel / Reject Orders List</h4>
						<div class="table-responsive">
							<table id="datatablePending" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Sr. No</th>
										<th>Code</th>
										<th>Client Name</th>
										<th> Vendor</th>
										<th>Address</th>
										<th>Mobile No</th>
										<th>Order Status</th>
										<th>Amount</th>
										<th>Order Date</th>
										<th>Delivery Boy</th>
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
	<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Blog</h4>
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
	    var cityCode,orderCode,orderStatus,pinCode,fromDate,toDate,areaCode,deliveryboyCode,ForListUrls="";
		url: base_path+"index.php/userCancelOrder/UserCancelOrder/getcancelorderlist", 
		dataTable(orderCode,cityCode,areaCode,fromDate,toDate);
	    $('#btnSearch').on('click', function (e){
			orderCode=$('#orderCode').val();
			//cityCode=$('#cityCode').val();
			//orderStatus=$('#orderStatus').val();
			//pinCode=$('#pinCode').val();
			deliveryboyCode=$('#deliveryboyCode').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
			vendorCode=$('#vendorCode').val();
			dataTable(orderCode,vendorCode,fromDate,toDate,deliveryboyCode);	
		});	
		function dataTable(orderCode,vendorCode,fromDate,toDate,deliveryboyCode)
		{
			$.fn.DataTable.ext.errMode = 'none';
			if ($.fn.DataTable.isDataTable("#datatablePending")) {
				$('#datatablePending').DataTable().clear().destroy();
			}
			$('#datatablePending').DataTable({  
				stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"index.php/userCancelOrder/UserCancelOrder/getcancelorderlist",
					type:"POST" , 
					data:{
						'call':'call from pending',
						'placeList':0,
						'orderCode':orderCode,
						'vendorCode':vendorCode,
						//'pincode':pinCode,
						'fromDate':fromDate,
						'deliveryboyCode':deliveryboyCode,
						'toDate':toDate
						
					} , 
					"complete": function(response) {			    
						$(".blue").click(function(){
							var code=$(this).data('seq'); 
							$.ajax({
								url:'<?php echo site_url('Blog/view'); ?>',
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
								if(e){
									$.ajax({
										url: base_path+"Blog/delete",
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
										}
									});
								}
								else
								{
									swal("Cancelled", "Your imaginary file is safe :)", "error");
								}
							});
						}); //mywarning
					}//complete
				}//datatable ajax
			});//datatable call
		} //main datatable function	   
	});
   	$( document ).ready(function() {
		//show alerts
		var data='<?php echo $error; ?>';
		if(data!='')
		{
			var obj=JSON.parse(data);
			if(obj.status)
			{ 
				toastr.success(obj.message, 'Order', { "progressBar": true });
			}
			else
			{
			  toastr.error(obj.message, 'Order', { "progressBar": true });
			}
		}
		//end show alerts
   });
</script>
		

<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Pending Orders List</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Pending List</li>
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
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="orderCode">Order Code :</label> </span>
										<input type="text" class="form-control" list="orderlist" id="orderCode" name="orderCode" placeholder="Enter Order Code Here ">
										<datalist id="orderlist">
											<?php if ($orderCode) {
												foreach ($orderCode->result() as $od) {
													echo '<option value="' . $od->code . '"></option>';
												}
											} ?>
										</datalist>
									</div>
								</div>
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="cityCode">City:</label> </span>
										<select class="form-control" id="cityCode" name="cityCode">
											<option value="">Select City</option>
											<?php
											foreach ($city->result() as $c) {
												echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Order Status :</label> </span>
										<select class="form-control" id="orderStatus" name="orderStatus">
											<option value="">Select Option</option>
											<?php
											foreach ($orderStatus->result() as $status) {
												echo '<option value="' . $status->statusSName . '">' . $status->statusName . '</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Area:</label> </span>
										<input type="text" name="areaCode" id="areaCode" class="form-control" list="areaCodeList">
										<datalist id="areaCodeList">
											<?php
											foreach ($address->result() as $addr) {
												echo '<option value="' . $addr->code . '">' . $addr->place . '</option>';
											} ?>
										</datalist>
									</div>
								</div>
								<?php
								$todayDate = date('d/m/Y');
								$previousDate = date('d/m/Y', strtotime('- 7 days'));
								?>
								<div class="col-sm-5">
									<div class="input-daterange input-group">
										<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control date-inputmask col-sm-5" name="start" id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>" />
											<div class="input-group-append">
												<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
											</div>
											<input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" placeholder="dd/mm/yyyy" value="<?= $todayDate ?>" />
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="form-group  text-center">
										<button type="button" id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
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
						<h4 class="card-title">Pending Orders List</h4>
						<div class="table-responsive">
							<table id="datatablePending" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Sr. No</th>
										<th>Code</th>
										
										<th>Client Name</th>
										
										<th>Area</th>
										<th>Address</th>
										<th>Mobile No</th>
										<th>Order Status</th>
										<th>Amount</th>
										<th>Delivery Boy</th>
										<th>Order Date</th>
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
					<h4 class="modal-title">Release & Reassign</h4>
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
	var cityCode, orderCode, orderStatus, pinCode, areaCode, ForListUrls = "";
	var fromDate = $('#fromDate').val();
	var	toDate = $('#toDate').val();
	ForListUrls = base_path + "Order/getOrderList";
	
	$("body").on("click",'#transfer_change',function () { 
		var slt = $("#select_boy").val();
		var order_code =$("#order_code").val();
		var areaCode =$("#areaCode").val();
		//alert(slt);
		if(slt===''){
			alert('please select delivery boy');
		}else{ 
			$.ajax({
				url: base_path+"Order/delivery_update",  
				method:"POST",
				data:{'slt':slt,'order_code':order_code,'areaCode':areaCode},
				datatype:"text",
				success: function(data)
				{
					var obj=JSON.parse(data);
					if(obj.status)
					{
						toastr.success(obj.message, 'Transfer', { "progressBar": true });
					    dataTable(orderCode, cityCode, orderStatus, areaCode, fromDate, toDate);
					}
					else
					{
						toastr.error(obj.message, 'Transfer', { "progressBar": true });
					}
					$("#responsive-modal").modal("hide");
				}
			});
		}
	});	
	 	
	function dataTable(orderCode, cityCode, orderStatus, areaCode, fromDate, toDate) {
			$.fn.DataTable.ext.errMode = 'none';
			if ($.fn.DataTable.isDataTable("#datatablePending")) {
				$('#datatablePending').DataTable().clear().destroy();
			}
			$('#datatablePending').DataTable({
				stateSave: true,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": false,
				"paging": true,
				"ajax": {
					url: base_path + "Order/getOrderList",
					type: "GET",
					data: {
						'call': 'call from pending',
						'cityCode': cityCode,
						'placeList': 0,
						'orderCode': orderCode,
						'orderStatus': orderStatus, 
						'fromDate': fromDate,
						'toDate': toDate,
						'areaCode': areaCode
					},
					"complete": function(response) {
						$(".blue").click(function() {
							var code = $(this).data('seq');
							$.ajax({
								url: '<?php echo site_url('Blog/view'); ?>',
								method: "GET",
								data: {
									code: code
								},
								datatype: "text",
								success: function(data) {
									$(".modal-body").html(data);
								}
							});
						});
						$(".transfer").click(function() {
							var code = $(this).data('seq');
							$.ajax({
								url: base_path+'Order/transfer',
								method: "GET",
								data: {
									code: code
								},
								datatype: "text",
								success: function(data) {
									$(".modal-body").html(data);
									$("#responsive-modal").modal("show");
								}
							});
						}); 
						//delete
						$('.mywarning').on("click", function() {
							var code = $(this).data('seq');
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
								if (e) {
									$.ajax({
										url: base_path + "Blog/delete",
										type: 'POST',
										data: {
											'code': code
										},
										success: function(data) {
											if (data) {
												swal({
														title: "Completed",
														text: "Successfully Deleted",
														type: "success"
													},
													function(isConfirm) {
														if (isConfirm) {
															loadTable();
														}
													});
											} else {
												swal("Failed", "Record Not Deleted", "error");
											}
										},
										error: function(xhr, ajaxOptions, thrownError) {
											var errorMsg = 'Ajax request failed: ' + xhr.responseText;
										}
									});
								} else {
									swal("Cancelled", "Your imaginary file is safe :)", "error");
								}
							});
						}); //mywarning
					} //complete
				} //datatable ajax
			}); //datatable call
		} //main datatable function	   	
	 	
	$(document).ready(function() { //nitin 05 DEC 2018
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
		$('.btn-inverse').click(function() {
			dataTable();
		});
	
		dataTable(orderCode, cityCode, orderStatus, areaCode, fromDate, toDate);
		$('#btnSearch').on('click', function(e) {
			orderCode = $('#orderCode').val();
			cityCode = $('#cityCode').val();
			orderStatus = $('#orderStatus').val(); 
			fromDate = $('#fromDate').val();
			toDate = $('#toDate').val();
			areaCode = $('#areaCode').val();
			dataTable(orderCode, cityCode, orderStatus, areaCode, fromDate, toDate);
		});  
	});
	$(document).ready(function() {
		//show alerts
		var data = '<?php echo $error; ?>';
		if (data != '') {
			var obj = JSON.parse(data);
			if (obj.status) {
				toastr.success(obj.message, 'Order', {
					"progressBar": true
				});
			} else {
				toastr.error(obj.message, 'Order', {
					"progressBar": true
				});
			}
		}
		//end show alerts
	});
</script>
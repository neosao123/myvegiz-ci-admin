<style>
	.select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--single .select2-selection__arrow, .select2-container--default .select2-selection--single .select2-selection__rendered {
		border-color: rgba(0,0,0,0.25);
		height: auto;  			 
	}
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background:#588002
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected],.select2-container--default .select2-results__option[aria-selected=true]  {background:#96bf3fbd}
	.select2-container--default .select2-selection--multiple .select2-selection__choice > span { color:white!important;forn-weight:bold}
</style>
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
				<h4 class="page-title">Order List</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Order List</li>
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
	<div class="container-fluid">
		<!-- ============================================================== -->
		<!-- Start Page Content -->
		<!-- ============================================================== -->
		<!-- basic table -->
		<div class="card">
			<div class="card-body">
				<h3 class="card-title"> Filter Order :</h3>
				<form class"form-horizontal">
					<hr>
					<div class="form-row">
						<div class="col-sm-3">									 
							<span> <label for="cityCode">City:</label> </span>
							<select  class="form-control" id="cityCode" name="cityCode">
								<option value="">Select City</option>
								<?php
								foreach ($city->result() as $c) {
									echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
								} ?>
							</select>									 
						</div>
						<div class="col-sm-4">
							<span> <label>Product Name :</label> </span>
							<input type="text"  class="form-control" list="prlist" id="productCode" name="productCode" placeholder="Enter Product Name">
							<datalist id="prlist">
							  <?php foreach($productmaster->result() as $row){
								echo'<option value="'.$row->code.'">'.$row->productName.'</option>';
								}?>
							</datalist>
						</div>
						
						
							<?php	
									$todayDate = date('d/m/Y');
									$previousDate = date('d/m/Y', strtotime('- 7 days'));
								?>
								<div class="col-sm-5">
									<div class="input-daterange input-group">
										<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control date-inputmask col-sm-5" name="start" id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>"/>
											<div class="input-group-append">
												<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
											</div>
											<input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" placeholder="dd/mm/yyyy" value="<?= $todayDate ?>"/>
										</div>
									</div>
								</div>
						<div class="col-sm-6">							
							<label for="address">Place/District where services available : </label>
							<select class="form-control js-example-responsive" name="addressCode[]" required id="addressCode" multiple="multiple" data-border-color="primary" data-border-variation="accent-2" required  style="width:100%" >
							</select>							
						</div>
						<div class="col-sm-2">									 
							<span> <label>Order Status :</label> </span>
							<select type="text"  class="form-control"  id="orderType" name="orderType">
								<option value="">Select Status</option>
								<option value="PLC">Placed</option>
								<option value="PND">Pending</option>
							</select>									 
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
						<h4 class="card-title">Order List</h4>
						
						<div class="table-responsive">
							<table id="datatable" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>Sr.No</th>
										<th>Product Code</th>
										<th>Product Name</th>
										<th>Unit</th>
										<th>Quantity</th>
										<th>Main Quantity</th>
										<th>Amount</th>
										<th>Date</th>
									</tr>
								</thead>
								
							</table>
							<h3>Total <span id="total">0.00</span></h3>
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
</div>
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog model-lg">
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
<table id="orders_export" class="table table-striped table-bordered d-none">
	<thead>
		<tr>
			<th>Sr.No</th>
			<th>Product Code</th>
			<th>Product Name</th>
			<th>Unit</th>
			<th>Quantity</th>
            <th>Main Quantity</th>
			<th>Amount</th>
			<th>Date</th>
		</tr>
	</thead>
</table>  
<script>
	$(document).ready(function() {
		var addressCode="",cityCode="",keyproductCode = "", keyProductDateStart = "", keyProductDateEnd = "",keyorderType="";
		$("#btnClear").click(function(){
			clearGlobalVariables();
		});
		function clearGlobalVariables() {
			addressCode="",cityCode="",keyproductCode = "", keyProductDateStart = "", keyProductDateEnd = "",keyorderType="";
			loadTable();
		}
		$("#cityCode").change(function(){
			var cityCode= $(this).val().trim();
			if(cityCode!=""){					 
				$('#addressCode').val(null).trigger('change');
				$("#addressCode").select2({
					ajax: { 
						url:  base_path+'Usermaster/getAddressByCity', 
						type: "get",
						delay:250,
						dataType: 'json',
						data: function (params) {
							return {
								cityCode:cityCode // search term
							};
						}, 
						processResults: function (response) {							
							return {
								results: response
							};							
						},
						cache: true
					}
				});	
			}	 
		});
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
		loadTable();
		$('#btnSearch').on('click', function(e) {
			$('#datatable').DataTable().state.clear();
			keyproductCode = $('#productCode').val();
			keyProductDateStart = $('#fromDate').val();
			keyProductDateEnd = $('#toDate').val();
			keyorderType = $('#orderType').val();
			cityCode = $('#cityCode').val();
			var addressCode = [];
			$.each($("#addressCode option:selected"), function(){            
				addressCode.push($(this).val());
			});
			 
			var fromDate = $('#fromDate').val();
			var toDate = $('#toDate').val();

			if (fromDate != '' && toDate != '') {
				keyProductDateStart = moment(fromDate, 'DD/MM/YYYY').format("YYYY/MM/DD");
				keyProductDateEnd = moment(toDate, "DD/MM/YYYY").format("YYYY/MM/DD");
				//alert(keyInwardDateEnd);
				if (moment(keyProductDateStart) > moment(keyProductDateEnd)) {
					alert('Date Should Be Greater Than From Date');
					keyProductDateStart = '';
					keyProductDateEnd = '';
				}
			}
			
			loadTable(addressCode,cityCode,keyproductCode, keyorderType, keyProductDateStart, keyProductDateEnd);

		});
		// loadTable();
		function loadTable(p_addressCode,cityCode,p_productCode, p_ordertype, p_dateStart, p_dateEnd) {
			if ($.fn.DataTable.isDataTable("#datatable")) {
				$('#datatable').DataTable().clear().destroy();
			}
			var totalRecords=0;
			jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
				if ( this.context.length ) {
					var jsonResult = $.ajax({
						url: base_path+"Reports/gertOrders",
						data: {
							'export':1,
							'productCode': p_productCode,
							'orderType': p_ordertype,
							'dateStart': p_dateStart,
							'dateEnd': p_dateEnd,
							'cityCode':cityCode,
							'addressCode':p_addressCode,
							'page':totalRecords,
							draw:0
						},
						type:"GET", 
						success: function (result) {
						},
						async: false
					});
					var jencode=JSON.parse(jsonResult.responseText);
					return {body: jencode.data, header: $("#orders_export thead tr th").map(function() { return this.innerHTML; }).get()};
				}
			});
			$('#datatable').DataTable({
				dom: 'Bflrtip',
				buttons: [					 
					{
						extend: 'excel',
						title: 'Orders',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
					},
					{
						extend: 'pdf',
						orientation: 'portrait',
						pageSize: 'LEGAL',
						title: 'Orders',
						charset: "utf-8",
						text: '<i class="fa fa-file-pdf-o"></i> PDF',
					}
				],
				"lengthMenu": [10, 25, 50, 100, 200, "All"],
				stateSave: true,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": false,
				"ajax": {
					url: base_path + "Reports/gertOrders",
					data: {
						'export':0,
						'productCode': p_productCode,
						'orderType': p_ordertype,
						'dateStart': p_dateStart,
						'dateEnd': p_dateEnd,
						'cityCode':cityCode,
						'addressCode':p_addressCode
					},
					type: "GET",
					complete: function(json) {
						$("#total").text(json.responseJSON['totalamt'].toFixed(2)); 
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
			if(obj.status){
				toastr.success(obj.message, 'User Master', { "progressBar": true });
			} else {
				toastr.error(obj.message, 'User Master', { "progressBar": true });
			}
		}
		//end show alerts
   });
</script>

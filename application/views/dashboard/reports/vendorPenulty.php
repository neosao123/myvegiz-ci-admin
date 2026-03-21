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
				<h4 class="page-title">Vendor Penalty Report</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Vendor Penalty Report</li>
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
				<h3 class="card-title"> Filter:</h3>
				<form class = "form-horizontal">
					<hr>
					<div class="form-row">
						<div class="col-sm-4">									 
							<span> <label for="dbCode">Vendor:</label> </span>
							<input type="text" class="form-control" id="vendorCode" name="vendorCode" list="vendorList" placeholder="Select Vendor">
							<datalist id="vendorList">
								<?php
								foreach ($vendorList->result() as $dlb) {
									echo '<option value="' . $dlb->code . '">' . $dlb->entityName . '</option>';
								} ?>
							</datalist>									 
						</div>
						<?php	
							$todayDate = date('d/m/Y');
							$previousDate = date('d/m/Y', strtotime('- 7 days'));
						?>
						<div class="col-sm-4">
							<div class="input-daterange input-group">
								<span> <label> Search Dates :</label> </span>
								<div class="input-daterange input-group" id="productDateRange">
									<input type="text" class="form-control date-inputmask col-sm-5" name="start" id="fromDate" disabled placeholder="dd/mm/yyyy" value="<?= $previousDate ?>"/>
									<div class="input-group-append">
										<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
									</div>
									<input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" disabled placeholder="dd/mm/yyyy" value="<?= $todayDate ?>"/>
								</div>
							</div>
						</div>
						<div class="col-sm-4" style="margin-top:25px;">
							<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
							<button type="reset" id="btnClear" class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Vendor Penalty Report</h4>
						
						<div class="table-responsive">
							<table id="datatable" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>Sr.No</th>
										<th>Vendor Name</th>
										<th>Date</th>
										<th>Order Code</th>
										<th>Order Amount</th>
										<th>Penalty Amount</th>
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
		$('#datatable').DataTable();
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
		$('#btnSearch').on('click', function(e) {
			$('#datatable').DataTable().state.clear();
			keyvendorcode = $('#vendorCode').val();
			keyFromDate = $('#fromDate').val();
			keyToDate = $('#toDate').val();
			loadTable(keyvendorcode,keyFromDate,keyToDate);

		});
		// loadTable();
		function loadTable(p_keyvendorcode,p_keyFromDate,p_keyToDate) {
			if ($.fn.DataTable.isDataTable("#datatable")) {
				$('#datatable').DataTable().clear().destroy();
			}
			var totalRecords=0;
			jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
				if ( this.context.length ) {
					var jsonResult = $.ajax({
						url: base_path+"Reports/getVendorpenaltyList",
						data: {
							'export':1,
							'vendorCode': p_keyvendorcode,
							'fromDate': p_keyFromDate,
							'toDate': p_keyToDate 
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
						title: 'Vendor Penulty Report',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
					},
					{
						extend: 'pdf',
						orientation: 'portrait',
						pageSize: 'LEGAL',
						title: 'Vendor Penulty Report',
						charset: "utf-8",
						text: '<i class="fa fa-file-pdf-o"></i> PDF',
					}
				],
				lengthMenu: [10, 25, 50, 200, 500, 700, 1000],
				stateSave: false,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": false,
				"ajax": {
					url: base_path + "Reports/getVendorpenaltyList",
					data: {
						'export':0,
						'vendorCode': p_keyvendorcode,
					    'fromDate': p_keyFromDate,
					    'toDate': p_keyToDate,
					},
					/* type: "GET", */
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

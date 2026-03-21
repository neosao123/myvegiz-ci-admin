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
				<h4 class="page-title">Delivery Boy Penulty Report</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Delivery Boy Penulty Report</li>
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
				<h3 class="card-title"> Filter: </h3>
				<form class "form-horizontal">
					<hr>
					<div class="form-row">
						<div class="col-sm-3">									 
							<span> <label for="dbCode">Delivery Boy:</label> </span>
							<input type="text" class="form-control" id="dbCode" name="dbCode" list="dbList" placeholder="Select Delivery Boy">
							<datalist id="dbList">
								<?php
								foreach ($dbList->result() as $dlb) {
									echo '<option value="' . $dlb->code . '">' . $dlb->name . '</option>';
								} ?>
							</datalist>									 
						</div>
						<div class="col-sm-3">									 
							<span> <label for="orderType">Order Type:</label> </span>
							<select class="form-control" id="orderType" name="orderType">
								<option selected value="">Select Order Type</option>
								<option value="food">Food</option>
								<option value="vegetable">Vegetable</option>
							<select>
						</div>
						<div class="col-sm-3">
								<span> <label> Date :</label> </span>
								
									<input type="date" class="form-control" name="start" id="fromDate" disabled value="<?= date('Y-m-d') ?>"/>
								
						</div>
						<div class="col-sm-3" style="margin-top:25px;">	
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
						<h4 class="card-title">Delivery Boy Penulty Report</h4>
						<div class="table-responsive">
							<table id="datatable" class="table table-striped table-bordered" style="width:100%">
								<thead>
									<tr>
										<th>Sr.No</th>
										<th>Date</th>
										<th>Order Code</th>
										<th>Order Amount</th>
										<th>Penulty Amount</th>
										<th>Order Type</th>
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
			<th>Date</th>
			<th>Order Code</th>
			<th>Order Amount</th>
			<th>Penulty Amount</th>
			<th>Order Type</th>
		</tr>
	</thead>
</table>  
<script>
	$(document).ready(function() {
		$('#datatable').DataTable();
		$('#btnSearch').on('click', function(e) {
			$('#datatable').DataTable().state.clear();
			keyDeliveryBoyCode = $('#dbCode').val();
			keyOrderType = $('#orderType').val();
			loadTable(keyDeliveryBoyCode);
		});
		function loadTable(p_keyDeliveryBoyCode,p_keyOrderType) {
			if ($.fn.DataTable.isDataTable("#datatable")) {
				$('#datatable').DataTable().clear().destroy();
			}
			var totalRecords=0;
			jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
				if ( this.context.length ) {
					var jsonResult = $.ajax({
						url: base_path+"Reports/getDeliveryBoypenaltyList",
						data: {
							'export':1,
							'dbCode': p_keyDeliveryBoyCode,
							'orderType': p_keyOrderType,
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
						title: 'Delivery Boy Penalty Report',
						text: '<i class="fa fa-file-excel-o"></i> Excel',
					},
					{
						extend: 'pdf',
						orientation: 'portrait',
						pageSize: 'LEGAL',
						title: 'Delivery Boy Penalty Report',
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
					url: base_path + "Reports/getDeliveryBoypenaltyList",
					data: {
						'export':0,
						'dbCode': p_keyDeliveryBoyCode,
						'orderType': p_keyOrderType,
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
		var data='<?php echo $error; ?>';
		if(data!=''){
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

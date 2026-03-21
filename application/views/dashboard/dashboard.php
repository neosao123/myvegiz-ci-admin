<style>
	.food {
		color: #FF7F00;
	}

	.vege {
		color: #17c725;
	}
</style>
<!--============================================================== -->
<!-- Page wrapper  -->
<!-- ============================================================== -->
<div class="page-wrapper">
	<!-- ============================================================== -->
	<!-- Bread crumb and right sidebar toggle -->
	<!-- ============================================================== -->
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-7 align-self-center">
				<h4 class="page-title">Dashboard (Updates Every 30 sec)</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="#">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="col-5 text-right">
				<h4><span id="timeOut"></span>'s</h4>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-3 col-md-6">
				<div class="card border-bottom border-info">
					<div class="card-body">
						<div class="d-flex no-block align-items-center">
							<div>
								<h2> <span id="totalDelvBoysFood" class="food">0</span> | <span id="totalDelvBoysVege" class="vege">0</span> </h2>
								<h6 class="text-info">Total Delv. Boys</h6>
							</div>
							<div class="ml-auto">
								<span class="text-info display-6"><i class="ti-user"></i></span>
							</div>
						</div>
						<a class="float-right label label-success text-light loadDelBoy" data-id="all">View</a>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card border-bottom border-success">
					<div class="card-body">
						<div class="d-flex no-block align-items-center">
							<div>
								<h2> <span id="presentDelBoysFood" class="food">0</span> | <span id="presentDelBoysVege" class="vege">0</span> </h2>
								<h6 class="text-cyan">Present Delv. Boys</h6>
							</div>
							<div class="ml-auto">
								<span class="text-success display-6"><i class="ti-user"></i></span>
							</div>
						</div>
						<a class="float-right label label-success text-light loadDelBoy" data-id="present">View</a>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card border-bottom border-danger">
					<div class="card-body">
						<div class="d-flex no-block align-items-center">
							<div>
								<h2> <span id="absentDelvBoysFood" class="food">0</span> | <span id="absentDelvBoysVege" class="vege">0</span> </h2>
								<h6 class="text-success">Absent Delv. Boys</h6>
							</div>
							<div class="ml-auto">
								<span class="text-danger display-6"><i class="ti-user"></i></span>
							</div>
						</div>
						<a class="float-right label label-success text-light loadDelBoy" data-id="absent">View</a>
					</div>
				</div>
			</div>
			<div class="col-lg-3 col-md-6">
				<div class="card border-bottom border-orange">
					<div class="card-body">
						<div class="d-flex no-block align-items-center">
							<div>
								<h2> <span id="orderAssignedDelvBoyFood" class="food">0</span> | <span id="orderAssignedDelvBoyVege" class="vege">0</span> </h2>
								<h6 class="text-orange">Assigned Orders</h6>
							</div>
							<div class="ml-auto">
								<span class="text-orange display-6"><i class="ti-user"></i></span>
							</div>
						</div>
						<a class="float-right label label-success text-light loadDelBoy" data-id="assigned">View</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<!-- Total -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-view-list text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Today's Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="totalOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="all">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Pending -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-network-question text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Pending Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="pendingOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="PND">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- cancelled -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-close-box text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Cancelled Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="cancelledOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="CAN">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Released -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-undo-variant text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Released Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="releasedOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadReleasedOrders">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Rejected -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-food-off text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Rejected Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="rejectedOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="RJT">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Confirmed -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-food text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Confirmed Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="placedOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="PLC">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Picked -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-basket text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">On the Way Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="pickedOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="PUP">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Delivered -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<div class="card bg-dash">
					<div class="card-body">
						<div class="d-flex align-items-center">
							<div class="m-r-10">
								<h1 class="m-b-0"><i class="mdi mdi-check-all text-white"></i></h1>
							</div>
							<div>
								<h6 class="font-12 text-white m-b-5 op-7">Delivered Orders</h6>
							</div>
							<div class="ml-auto">
								<div class="crypto">
									<h4 class="text-white font-medium m-b-0" id="deliveredOrders"></h4>
								</div>
							</div>
						</div>
						<div class="row text-right text-white" id="contractInfo">
							<div class="col-12">
								<a class="loadOrders" data-id="DEL">View</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="delboy_div" style="display:none">
			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<h5 class="p-2">Delivery Boy List</h5>
							<div class="table-responsive">
								<table id="datatable_delBoy" class="table table-bordered table-stripped" width="100%">
									<thead>
										<tr>
											<th>Sr. No</th>
											<th>Name</th>
											<th>Contact Number</th>
											<th>Delivery Type</th> 										 
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="order_div" style="display:none">
			<div class="row">
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<h5 class="p-2">Vegetable/Grocery Orders</h5>
							<div class="table-responsive">
								<table id="datatable_VegeGrocery" class="table table-bordered table-stripped" width="100%">
									<thead>
										<tr>
											<th>Sr. No</th>
											<th>Code</th>
											<th>Date</th>
											<th>Client</th>
											<th>Amount</th>
											<th>Delivery Boy</th>
											<th>Options</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
							<h5 class="p-2">Food Orders</h5>
							<div class="table-responsive">
								<table id="datatable_Food" class="table table-bordered table-stripped" width="100%">
									<thead>
										<tr>
											<th>Sr. No</th>
											<th>Code</th>
											<th>Date</th>
											<th>Client</th>
											<th>Amount</th>
											<th>Vendor</th>
											<th>Delivery Boy</th>
											<th>Options</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!----------This Page JS-------------->

<script>
	var i = 0;
	$("#timeOut").text(i);

	function getOrderCounts() {
		$.ajax({
			type: "get",
			url: base_path + "Admin/getOrderCounts",
			data: {},
			success: function(response) {
				//debugger;
				if (response) {
					var res = JSON.parse(response);
					$("#cancelledOrders").text(res['cancelledOrders']);
					$("#releasedOrders").text(res['releasedOrders']);
					$("#deliveredOrders").text(res['deliveredOrders']);
					$("#rejectedOrders").text(res['rejectedOrders']);
					$("#pickedOrders").text(res['pickedOrders']);
					$("#placedOrders").text(res['placedOrders']);
					$("#pendingOrders").text(res['pendingOrders']);
					$("#totalOrders").text(res['totalOrders']);

					$("#totalDelvBoysFood").text(res['totalDelvBoysFood']);
					$("#totalDelvBoysVege").text(res['totalDelvBoysVege']);
					$("#presentDelBoysFood").text(res['presentDelBoysFood']);
					$("#presentDelBoysVege").text(res['presentDelBoysVege']);
					$("#absentDelvBoysFood").text(res['absentDelvBoysFood']);
					$("#absentDelvBoysVege").text(res['absentDelvBoysVege']);
					$("#orderAssignedDelvBoyFood").text(res['orderAssignedDelvBoyFood']);
					$("#orderAssignedDelvBoyVege").text(res['orderAssignedDelvBoyVege']);

				}
			}
		});
	}
	function loadDelBoys(k_statusType){
		$.fn.DataTable.ext.errMode = 'none';
		if ($.fn.DataTable.isDataTable("#datatable_delBoy")) {
			$('#datatable_delBoy').DataTable().clear().destroy();
		}
		var dataTable = $('#datatable_delBoy').DataTable({
			"paging": true,
			"processing": true,
			"serverSide": true,
			"order": [],
			"searching": false,
			"ajax": {
				url: base_path + "Admin/getDeliveryBoys",
				type: "GET",
				data: {
					"statusType": k_statusType
				},
				"complete": function(response) { 
				}
			}
		});
		$("#delboy_div").show();
		$("#order_div").hide(); 
		$("html, body").delay(500).animate({
			scrollTop: $('#delboy_div').offset().top 
		}, 600);
	}
	function loadOrders(k_orderStatus) {
		$.fn.DataTable.ext.errMode = 'none';
		if ($.fn.DataTable.isDataTable("#datatable_VegeGrocery")) {
			$('#datatable_VegeGrocery').DataTable().clear().destroy();
		}
		var dataTable = $('#datatable_VegeGrocery').DataTable({
			"paging": true,
			"processing": true,
			"serverSide": true,
			"order": [],
			"searching": false,
			"ajax": {
				url: base_path + "Admin/getVegeGroceryOrders",
				type: "GET",
				data: {
					"orderStatus": k_orderStatus
				},
				"complete": function(response) { 
				}
			}
		});
		$.fn.DataTable.ext.errMode = 'none';
		if ($.fn.DataTable.isDataTable("#datatable_Food")) {
			$('#datatable_Food').DataTable().clear().destroy();
		}
		var dataTable = $('#datatable_Food').DataTable({
			"paging": true,
			"processing": true,
			"serverSide": true,
			"order": [],
			"searching": false,
			"ajax": {
				url: base_path + "Admin/getFoodOrders",
				type: "GET",
				data: {
					"orderStatus": k_orderStatus
				},
				"complete": function(response) { 
				}
			}
		});
		$("#order_div").show();
		$("#delboy_div").hide(); 
		$("html, body").delay(500).animate({
      scrollTop: $('#order_div').offset().top 
    }, 600);
	}
	function loadReleasedOrders() {
		$.fn.DataTable.ext.errMode = 'none';
		if ($.fn.DataTable.isDataTable("#datatable_VegeGrocery")) {
			$('#datatable_VegeGrocery').DataTable().clear().destroy();
		}
		var dataTable = $('#datatable_VegeGrocery').DataTable({
			"paging": true,
			"processing": true,
			"serverSide": true,
			"order": [],
			"searching": false,
			"ajax": {
				url: base_path + "Admin/getVegeGroceryReleasedOrders",
				type: "GET",
				data: {},
				"complete": function(response) { 
				}
			}
		});
		$.fn.DataTable.ext.errMode = 'none';
		if ($.fn.DataTable.isDataTable("#datatable_Food")) {
			$('#datatable_Food').DataTable().clear().destroy();
		}
		var dataTable = $('#datatable_Food').DataTable({
			"paging": true,
			"processing": true,
			"serverSide": true,
			"order": [],
			"searching": false,
			"ajax": {
				url: base_path + "Admin/getFoodReleasedOrders",
				type: "GET",
				data: {},
				"complete": function(response) { 
				}
			}
		});
		$("#order_div").show();
		$("#delboy_div").hide(); 
		$("html, body").delay(500).animate({
      scrollTop: $('#order_div').offset().top 
    }, 600);
	}
	$("body").on("click", ".loadOrders", function() {
		var orderstatus = $(this).data("id");
		loadOrders(orderstatus);
	});
	$("body").on("click", ".loadReleasedOrders", function() {
		loadReleasedOrders();
	});
	$("body").on("click", ".loadDelBoy", function() {
		var statusa = $(this).data("id");
		loadDelBoys(statusa);
	});	
	$(document).ready(function() {
		//loadTable();   
		setInterval(function(e) {
			if (i == 30) {
				i = 0;
				$("#timeOut").text(i);
				getOrderCounts();
			} else {
				i++;
				$("#timeOut").text(i);
			}
		}, 1000);
		getOrderCounts();
	}); //Document Ready
</script>
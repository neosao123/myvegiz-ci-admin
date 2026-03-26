<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-12 align-self-center">
				<h4 class="page-title">Offer</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'Home/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Offer List</li>
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
										<span> <label for="vendorCode">Restaurant Name:</label> </span>
										<select id="vendorCode" name="vendorCode" class="form-control" required>
											<option value="">Select Restaurant Name</option>
											<?php
if ($vendors) {
	foreach ($vendors->result() as $p) {
		echo '<option value="' . $p->code . '">' . $p->entityName . '</option>';
	}
}
?>
										</select> 
									</div>
								</div>
							    
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="coupanCode">Coupon Code :</label> </span>
										<input type="text" class="form-control" list="coupanlist" id="coupanCode" name="coupanCode" placeholder="Enter coupon Code Here ">
										<datalist id="coupanlist">
											<?php
if ($coupan) {
	foreach ($coupan->result() as $p) {
		echo '<option value="' . $p->code . '">' . $p->coupanCode . '</option>';
	}
}
?>
										</datalist>
									</div>
								</div>

								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="offerType">Offer Type:</label> </span>
										<select id="offerType" name="offerType" class="form-control" required>
											<option value="">Select Offer Type</option>
											<option value="flat">Flat</option>
											<option value="cap">Cap</option>
										</select>
									</div>
								</div> 
								
								<div class="col-sm-3 mb-3 d-none">
									<div class="form-group">
										<span> <label for="discount">Discount :</label> </span>
										<input type="text" class="form-control" list="discountlist" id="discountCode" name="discountCode" placeholder="Enter Discount Here ">
										<datalist id="discountlist">
											<?php if ($discount) {
	foreach ($discount->result() as $p) {
		echo '<option value="' . $p->code . '">' . $p->discount . '</option>';
	}
}?>
										</datalist>
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
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Offer List</h4>
						<div class="table-responsive">
							<table id="datatableOffer" class="table table-striped table-bordered ">
								<thead>
									<tr>
										<th>Sr no.</th>
										<th>Code</th>
										<th>Restaurant</th>
										<th>Coupon Code</th>
										<th>Offer Type</th>
										<th>Discount (%)</th>
										<th>Minimum Amount</th>
										<th>Approved</th>
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
</div>

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
<script src="<?php echo base_url() . 'assets/admin/assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js'; ?>"></script>
<!--sound script end -->
<script>
	//sound js start
	var audioUrl = 'http://www.pachd.com/a/button/button1.wav';

	$('.btn').click(() => new Audio(audioUrl).play());
	//sound js end
	$(document).ready(function() { 
		
		$('.btn-inverse').click(function() {
			loadTable();
		});
		
		var vendorCode ="",
		    coupanCode = "",
			offerType = "",
			discountCode = "", 
			ForListUrls = base_path + "Food/Offer/getOfferList";
		loadTable(coupanCode, offerType, discountCode,vendorCode);
		
		$('#btnSearch').on('click', function(e) {
			coupanCode = $('#coupanCode').val();
			offerType = $('#offerType').val();
			discountCode = $('#discountCode').val(); 
			vendorCode = $('#vendorCode').val(); 
			loadTable(coupanCode, offerType, discountCode,vendorCode);
		});
 
		function loadTable(kcoupanCode, kofferType, kdiscountCode,kvendorCode) {
			if ($.fn.DataTable.isDataTable("#datatableOffer")) {
				$('#datatableOffer').DataTable().clear().destroy();
			}
			var dataTable = $('#datatableOffer').DataTable({
				stateSave: true,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": false,
				"ajax": {
					url: base_path + "Food/Offer/getOfferList",
					type: "POST",
					data: {
						'call': 'call from pending',
						'placeList': 0,
						'coupanCode': kcoupanCode,
						'offerType': kofferType,
						'discountCode': kdiscountCode,
						'vendorCode':kvendorCode
					},
					"complete": function(response) {

						$(".blue").click(function() {
							var code = $(this).data('seq');
							$.ajax({
								url: base_path + "Food/Offer/view",
								method: "POST",
								data: {
									'code': code
								},
								// datatype:"text",
								success: function(data) {
									$(".modal-body").html(data);
									// $(".userModal-body").html(data);					
								}
							});
						}); 
					}
				}
			});
		}

	});
	$(document).ready(function() {
		//show alerts
		var data = '<?php echo $error; ?>';
		if (data != '') {
			var obj = JSON.parse(data);
			if (obj.status) {
				toastr.success(obj.message, 'Offer', {
					"progressBar": true
				});
			} else {
				toastr.error(obj.message, 'Offer', {
					"progressBar": true
				});
			}
		}
		//end show alerts
	});
</script>
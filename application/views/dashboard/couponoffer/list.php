<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Vegitable Offer</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'Home/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Vegitable Offer List</li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">
					<div class=""><a class="btn btn-myve" href="<?php echo base_url() . 'Couponoffer/add'; ?>">Create Offer</a></div>
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
										<span> <label for="coupanCode">Coupon Code :</label> </span>
										<input type="text" class="form-control" list="coupanlist" id="coupanCode" name="coupanCode" placeholder="Enter coupon Code Here ">
										<datalist id="coupanlist">
											<?php if ($coupan) {
	foreach ($coupan->result() as $p) {
		echo '<option value="' . $p->code . '">' . $p->coupanCode . '</option>';
	}
}?>
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



								<div class="col-sm-3 mb-3">
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
										<th>Coupan Code</th>
										<th>Offer Type</th>
										<th>Discount</th>
										<th>Minimum Amount</th>
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
		loadTable(); 
		$('.btn-inverse').click(function() {
			loadTable();
		});
		
		var coupanCode = "",
			offerType = "",
			discountCode = "", 
			ForListUrls = base_path + "Couponoffer/getOfferList";
		loadTable(coupanCode, offerType, discountCode);
		
		$('#btnSearch').on('click', function(e) {
			coupanCode = $('#coupanCode').val();
			offerType = $('#offerType').val();
			discountCode = $('#discountCode').val(); 
			loadTable(coupanCode, offerType, discountCode);
		});
 
		function loadTable(kcoupanCode, kofferType, kdiscountCode) {
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
					url: base_path + "Couponoffer/getOfferList",
					type: "POST",
					data: {
						'call': 'call from pending',
						'placeList': 0,
						'coupanCode': kcoupanCode,
						'offerType': kofferType,
						'discountCode': kdiscountCode 
					},
					"complete": function(response) {

						$(".blue").click(function() {
							var code = $(this).data('seq');
							$.ajax({
								url: base_path + "Couponoffer/view",
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

						//delete
						$('.mywarning').on("click", function() {
							var code = $(this).data('seq');
							swal({
								title: "You want to Delete this Offer " + code + " ?",
								// text: " Category against Product and stock also deleted.",
								type: "warning",
								showCancelButton: !0,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes, delete it!",
								cancelButtonText: "No, cancel it!",
								closeOnConfirm: !1,
								closeOnCancel: !1
							}, function(e) {
								console.log(e);
								if (e) {
									$.ajax({
										url: base_path + "Couponoffer/delete",
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
															//location.reload(true);
															loadTable();

														}
													});

											} else {
												swal("Failed", "Record Not Deleted", "error");
											}
										},
										error: function(xhr, ajaxOptions, thrownError) {
											var errorMsg = 'Ajax request failed: ' + xhr.responseText;
											alert(errorMsg);
											console.log("Ajax Request for patient data failed : " + errorMsg);
										}
									});
								} else {
									swal("Cancelled", "Your Offer record is safe :)", "error");
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
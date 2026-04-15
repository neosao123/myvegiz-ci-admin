<link href="<?php echo base_url() . 'assets/admin/assets/libs/bootstrap-switch/dist/css/bootstrap3/bootstrap-switch.min.css'; ?>" rel="stylesheet">
<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Vendor</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Vendor List</li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">
					<div class=""><a class="btn btn-myve" href="<?php echo base_url() . 'Vendor/add'; ?>">Create Vendor</a></div>
				</div>
			</div>
		</div>
	</div>

	<div class="container-fluid">

		<div class="card">
			<div class="card-body">
				<h3 class="card-title"> Filter Vendor :</h3>
				<form class"form-horizontal">
					<hr>
					<div class="form-row">
						<div class="col-sm-3">
							<div class="form-group">
								<span> <label>Entity Name :</label> </span>
								<input type="text" class="form-control" list="vendorList" id="vendorCode" name="vendorCode" placeholder="Enter Entity Name Here ">
								<datalist id="vendorList">
									<?php if ($vendor) {
	foreach ($vendor->result() as $v) {
		echo '<option value="' . $v->code . '">' . $v->entityName . '</option>';
	}
}?>
								</datalist>
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group">
								<span> <label>Owner Contact :</label> </span>
								<!--<input type="text"  class="form-control" id="ownerContact" name="ownerContact">-->
								<input type="text" class="form-control" list="ownerContactList" id="ownerContact" name="ownerContact" placeholder="Enter Owner Contact Here ">
								<datalist id="ownerContactList">
									<?php if ($vendor) {
	foreach ($vendor->result() as $v) {
		echo '<option value="' . $v->ownerContact . '">' . $v->ownerContact . '</option>';
	}
}?>
								</datalist>
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group">
								<label for="entitycategoryCode">Entity Category :</label>
								<select id="entitycategoryCode" name="entitycategoryCode" class="form-control" required>
									<option value="">Select Category</option>
									<?php if ($entitycategory) {
	foreach ($entitycategory->result() as $curren) {
		echo '<option value="' . $curren->code . '">' . $curren->entityCategoryName . '</option>';
	}
}?>
								</select>
							</div>
						</div>
					</div>
					<hr />
					<div class="form-group m-b-0  text-center">
						<button type="button" id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
						<button type="reset" id="btnClear" class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
					</div>
				</form>
			</div>
		</div>


		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Vendor List</h4>
						<div class="table-responsive">
							<table id="datatableVendor" class="table table-striped table-bordered ">
								<thead>
									<tr>
										<th>Sr no.</th>
										<th>Code</th>
										<th>Vendor Name</th>
										<th>Entity Name</th>
										<th>Owner Contact</th>
										<th>Serviceable</th>
										<th>Status</th>
										<th>Popular</th>
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
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Vendor</h4>
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
</div>
<script src="<?php echo base_url('assets/admin/assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js'); ?>"></script>
<script>
	$(document).ready(function() {
		var vendorCode = "",
			ownerContact = "",
			entitycategoryCode = "";
		loadTable('', '', '');

		$('#btnClear').on('click', function(e) {
			$('#datatableVendor').DataTable().state.clear();
			loadTable('', '', '');
		});

		$('#btnSearch').on('click', function(e) {
			$('#datatableVendor').DataTable().state.clear();
			vendorCode = $('#vendorCode').val();
			ownerContact = $('#ownerContact').val();
			entitycategoryCode = $('#entitycategoryCode').val();
			loadTable(vendorCode, ownerContact, entitycategoryCode);
		});

		function loadTable(vendorCode, ownerContact, entitycategoryCode) {
			// alert(vendorCode);

			if ($.fn.DataTable.isDataTable("#datatableVendor")) {
				$('#datatableVendor').DataTable().clear().destroy();
			}
			var dataTable = $('#datatableVendor').DataTable({
				stateSave: true,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": true,
				"ajax": {
					url: base_path + "vendor/getVendorList",
					type: "POST",
					data: {
						'vendorCode': vendorCode,
						'ownerContact': ownerContact,
						'entitycategoryCode': entitycategoryCode,
					},
					"complete": function(response) {
						$(".toggle").bootstrapSwitch({
							'size': 'mini',
							'onSwitchChange': function(event, state) {
								var code = $(this).attr('id');
								// var action = $(this).val();
								var action = $(this).bootstrapSwitch('state');
								// alert(code);
								// alert(action);
								if (action) {
									var flag = 1;
								} else {
									var flag = 0;
								}
								// return false;
								$.ajax({
									url: base_path + "Vendor/changeServiceable",
									type: 'POST',
									data: {
										'code': code,
										'flag': flag
									},
									success: function(data) {
										if (data) {
											toastr.success("Serive Flag Updated", 'Vendor', {
												"progressBar": true
											});
										} else {
											toastr.success("No Change!", 'Vendor', {
												"progressBar": true
											});
										}
									}
								});
							},
							'AnotherName': 'AnotherValue'
						});
						$(".blue").click(function() {
							var code = $(this).data('seq');
							$.ajax({
								url: base_path + "vendor/view",
								method: "POST",
								data: {
									code: code
								},
								datatype: "text",
								success: function(data) {
									$(".modal-body").html(data);
								}
							});
						});
						//delete
						$('.mywarning').on("click", function() {
							var code = $(this).data('seq');
							//alert(code);
							swal({
								title: "You want to delete vendor " + code + " ?",
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
										url: base_path + "vendor/delete",
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
									swal("Cancelled", "Your vendor Record is safe :)", "error");
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
				toastr.success(obj.message, 'Vendor', {
					"progressBar": true
				});
			} else {
				toastr.error(obj.message, 'Vendor', {
					"progressBar": true
				});
			}
		}
		//end show alerts
	});
</script>
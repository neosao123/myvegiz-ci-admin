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
				<h4 class="page-title">Product </h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Product List</li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">

					<div class=""><a class="btn btn-myve" href="<?php echo base_url() . 'Product/add'; ?>">Add Product</a></div>
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
				<h3 class="card-title"> Filter Goods Receipt :</h3>
				<form class="form-horizontal">
					<hr>
					<div class="form-row"> 
						<div class="col-sm-2">
							<div class="form-group">
								<span> <label>Product Name :</label> </span>
								<input type="text" class="form-control" list="productCodeList" id="productCode" name="productCode" placeholder="Enter Product Name Here ">
								<datalist id="productCodeList">
									<?php if($productmaster){foreach ($productmaster->result() as $product) {
										echo '<option value="' . $product->code . '">' . $product->productName . '</option>';
									} }?>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
								<span> <label>Category Name :</label> </span>
								<select type="text" class="form-control" id="categoryCode" name="categoryCode">
									<option value="">Select Option</option>
									<?php if($categorymaster){ foreach ($categorymaster->result() as $cat) {
										echo '<option value="' . $cat->categorySName . '">' . $cat->categoryName . '</option>';
									} }?>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="input-daterange input-group">
								<span> <label>Date :</label> </span>
								<div class="input-daterange input-group" id="productDateRange">
									<input type="text" class="form-control date-inputmask col-sm-5" name="start" id="productDateStart" placeholder="dd/mm/yyyy" />
									<div class="input-group-append">
										<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
									</div>
									<input type="text" class="form-control date-inputmask toDate" name="end" id="productDateEnd" placeholder="dd/mm/yyyy" />
								</div>
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
		<!-- basic table -->
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Product List</h4>

						<div class="table-responsive">
							<table id="datatableProduct" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Sr.No.</th>
										<th>Code</th>
										<th>Product Name</th>
										<th>Category Name</th>
										<th>Subcategory Name</th>
										<th>Minimum Selling Quantity</th>
										<th>Product UOM</th>
										<th>Add Date</th>
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
	<!-- ============================================================== -->
	<!-- End Container fluid  -->
	<!-- ============================================================== -->
	<!-- sample modal content -->
	<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Product </h4>
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
	$(document).ready(function() {
		$('#productDateStart').datepicker({
			dateFormat: "mm/dd/yy",
			showOtherMonths: true,
			selectOtherMonths: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			todayHighlight: true,
			orientation: "bottom left",
		});
		$('#productDateEnd').datepicker({
			dateFormat: "mm/dd/yy",
			showOtherMonths: true,
			selectOtherMonths: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			todayHighlight: true,
			orientation: "bottom left",
		});
		var keyProductCode = "",
			keyProductDateStart = "",
			keyProductDateEnd = "",
			keyStorageCode = "",
			keyCategoryCode = "";
		getDataTable('', '', '', '', '');

		function clearGlobalVariables() {
			keyProductCode = '', keyProductDateStart = '', keyProductDateEnd = '', keyStorageCode = '', keyCategoryCode = "";

		} // End clearGlobalVariables Function
		$('#btnSearch').on('click', function(e) {

			$('#datatableProduct').DataTable().state.clear();
			keyProductCode = $('#productCode').val();
			//alert(keyProductCode);
			keyProductDateStart = $('#productDateStart').val();
			keyProductDateEnd = $('#productDateEnd').val();
			keyStorageCode = $('#storageCode').val();
			keyCategoryCode = $('#categoryCode').val();


			var fromDate = $('#productDateStart').val();
			var toDate = $('#productDateEnd').val();

			if (fromDate != '' && toDate != '') {
				keyProductDateStart = moment(fromDate, 'DD/MM/YYYY').format("YYYY/MM/DD");
				keyProductDateEnd = moment(toDate, "DD/MM/YYYY").format("YYYY/MM/DD");
				//alert(keyProductDateStart);
				if (moment(keyProductDateStart) > moment(keyProductDateEnd)) {
					alert('Date Should Be Greater Than From Date');
					keyProductDateStart = '';
					keyProductDateEnd = '';
				}
			}

			getDataTable(keyProductCode, keyStorageCode, keyCategoryCode, keyProductDateStart, keyProductDateEnd);

		}); // End Search Click



		function getDataTable(p_keyProductCode, p_keyStorageCode, p_keyCategoryCode, p_keyProductDateStart, p_keyProductDateEnd) {

			if ($.fn.DataTable.isDataTable("#datatableProduct")) {
				$('#datatableProduct').DataTable().clear().destroy();
			}
			//alert(p_keyCategoryCode);
			//alert(p_keyProductDateEnd);
			var dataTable = $('#datatableProduct').DataTable({
				stateSave: true,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": true,
				"ajax": {
					url: base_path + "Product/getProductList",
					data: {
						'productCode': p_keyProductCode,
						'startDate': p_keyProductDateStart,
						'endDate': p_keyProductDateEnd,
						'storageCode': p_keyStorageCode,
						'categoryCode': p_keyCategoryCode
					},
					type: "POST",
					//model views
					"complete": function(json) {
						//console.log(json);
						$(".blue").click(function() {
							var code = $(this).data('seq');
							$.ajax({
								url: base_path + "Product/view",
								method: "POST",
								data: {
									'code': code
								},
								datatype: "text",
								success: function(data) {
									$(".modal-body").html(data);
									//console.log(data)
								}
							});
						});

						//delete
						$('.mywarning').on("click", function() {
							var code = $(this).data('seq');
							//alert(code);
							swal({
								title: "Are you sure?",
								text: "You want to delete Product Record of " + code + ", product against stock also deleted.",
								type: "warning",
								showCancelButton: !0,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes", 
								cancelButtonText: "No",
								closeOnConfirm: !1,
								closeOnCancel: !1
							}, function(e) {
								//console.log(e);
								if (e) {
									$.ajax({
										url: base_path + "Product/delete",
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
															getDataTable('', '', '', '', '');

														}
													});

											} else {
												swal("Failed", "Record Not Deleted", "error");
											}
										},
										error: function(xhr, ajaxOptions, thrownError) {
											var errorMsg = 'Ajax request failed: ' + xhr.responseText;
											alert(errorMsg);
											//console.log("Ajax Request for patient data failed : " + errorMsg);
										}
									});
								} else {
									swal("Cancelled", "Your Product Record is safe :)", "error");
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
				toastr.success(obj.message, 'Product', {
					"progressBar": true
				});

			} else {
				toastr.error(obj.message, 'Product', {
					"progressBar": true
				});

			}
		}
		//end show alerts
	});
</script>
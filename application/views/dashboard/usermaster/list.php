        <style>
		.t1{ margin: 0 5px 0 0; }
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
        				<h4 class="page-title">User List</h4>
        				<div class="d-flex align-items-center">
        					<nav aria-label="breadcrumb">
        						<ol class="breadcrumb">
        							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'admin/index'; ?>">Home</a></li>
        							<li class="breadcrumb-item active" aria-current="page">User List</li>
        						</ol>
        					</nav>
        				</div>
        			</div>
        			<div class="col-7 align-self-center">
        				<div class="d-flex no-block justify-content-end align-items-center">
        					<div class="m-r-10">
        						<div class=""><a class="btn btn-myve" title="For more features contact Administrator" href="<?php echo base_url() . 'usermaster/add'; ?>">Create User</a></div>
        					</div>
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
        				<h3 class="card-title"> Filter :</h3>
        				<form class="form-horizontal">
        					<hr>
        					<div class="form-row">
        						<div class="col-sm-3">
        							<div class="form-group">
        								<span> <label>City</label> </span>
        								<select class="form-control cityCode" id="cityCode" name="cityCode">
        									<option value="">Select City</option>
        									<?php foreach ($city->result() as $c) {
	echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
}?>
        								</select>
        							</div>
        						</div>
        						<div class="col-sm-3">
        							<div class="form-group">
        								<span> <label>User Name :</label> </span>
        								<input type="text"  class="form-control" list="userList" id="userName" name="userName" placeholder="Enter Name Of User ">
											<datalist id="userList">
											<?php if ($query) {
	foreach ($query->result() as $d) {
		echo '<option value="' . $d->code . '">' . $d->name . '</option>';
	}
}?>
											</datalist>

        							</div>
        						</div>

        						<div class="col-sm-2">
        							<div class="form-group">
        								<span> <label>User Role</label> </span>
        								<select class="form-control storageCode" id="userRole" name="userRole">
        									<option value="">Select option</option>
        									<option value="ADM">Admin</option>
        									<option value="DLB">Delivery Boy</option>
        									<option value="USR">User</option>
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
        						<h4 class="card-title">User List</h4>
        						<div class="table-responsive">
        							<table id="datatableUserMaster" class="table table-striped table-bordered">
        								<thead>
        									<tr>
        										<th>Sr.No</th>
        										<th>Code</th>
        										<th>IN - City</th>
        										<th>Name</th>
        										<th>User Name</th>        									 
        										<th>User Role</th>
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
        	<!-- ============================================================== -->
        	<!-- End Container fluid  -->
        	<!-- ============================================================== -->
        	<!-- sample modal content -->
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
        </div>
        <script>
        	$(document).ready(function() {
        		loadTable();
        		$('#btnSearch').on('click', function(e) {
        			var keyProductDateStart = $('#productDateStart').val();
        			var keyProductDateEnd = $('#productDateEnd').val();
        			var userName = $('#userName').val();
        			var userRole = $('#userRole').val();
        			var cityCode = $("#cityCode").val();

        			var fromDate = $('#productDateStart').val();
        			var toDate = $('#productDateEnd').val();
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

        			loadTable(cityCode, userName, userRole);

        		});
        		// loadTable();
        		$('#btnClear').on('click', function(e) {
        			loadTable("", "", "");
        		});

        		function loadTable(p_cityCode, p_userName, p_userRole) {
        			if ($.fn.DataTable.isDataTable("#datatableUserMaster")) {
        				$('#datatableUserMaster').DataTable().clear().destroy();
        			}
        			$('#datatableUserMaster').DataTable({
        				stateSave: false,
        				"processing": true,
        				"serverSide": true,
        				"order": [],
        				"searching": false,
        				"ajax": {
        					url: base_path + "Usermaster/getUserMasterList",
        					data: {
        						'cityCode': p_cityCode,
        						'userName': p_userName,
        						'userRole': p_userRole
        					},
        					type: "POST",
        					complete: function(json) {
        						$(".blue").click(function() {
        							var code = $(this).data('seq');
        							$.ajax({
        								url: base_path + "Usermaster/view",
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
        								title: "Are you sure?",
        								text: "You want to delete the  User Record of " + code,
        								type: "warning",
        								showCancelButton: !0,
        								confirmButtonColor: "#DD6B55",
        								confirmButtonText: "Yes, delete it!",
        								cancelButtonText: "No, cancel it!",
        								closeOnConfirm: !1,
        								closeOnCancel: !1
        							}, function(e) {
        								//console.log(e);
        								if (e) {
        									$.ajax({
        										url: base_path + "Usermaster/delete",
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
        											alert(errorMsg);
        										}
        									});
        								} else {
        									swal("Cancelled", "Your User Record is safe :)", "error");
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
        				toastr.success(obj.message, 'User Master', {
        					"progressBar": true
        				});

        			} else {
        				toastr.error(obj.message, 'User Master', {
        					"progressBar": true
        				});

        			}
        		}
        		//end show alerts
        	});
        </script>
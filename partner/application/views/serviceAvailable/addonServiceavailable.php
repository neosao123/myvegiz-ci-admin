<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Addon/Choice Service Available</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/Home/index';?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Addon/Choice Service Available</li>
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
                                <div class="col-sm-6 mb-3">
                                    <div class="form-group"> 
										<span> <label for="itemCode">Item:</label> </span>
                                        <select class="form-control" id="itemCode" name="itemCode"> 
											<option value="">Select Item</option>
                                           	<?php  if($vendoritem){ foreach($vendoritem->result() as $mi){
												echo'<option value="'.$mi->code.'">'.$mi->itemName.'</option>';
											} } ?>
										</select>                                        
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
                        <h4 class="card-title">Addon/Choice Service Available</h4> 
                        <div class="table-responsive">
                            <table id="datatableAddonServiceAvaliable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr no.</th>
                                        <th>Code</th>
										<th>Item Name</th>
                                        <th>Customize Category</th> 
										<th>Addon/Choice</th> 
                                        <th>Service On/Off</th>
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
 
<script src="<?php echo base_url().'assets/admin/assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js';?>"></script>  
 
<script>  
	$(document).ready(function() {
		loadTable(); 
		$('.btn-inverse').click(function() {
			loadTable();
		});
		var menucategoryCode = "",
		itemCode = "", 
		ForListUrls = base_path + "index.php/Serviceavailable/getVendorItemList";
		loadTable(itemCode);
		$('#btnSearch').on('click', function(e) {
			menucategoryCode = $('#menucategoryCode').val();
			itemCode = $('#itemCode').val();
			fromDate = $('#fromDate').val();
			toDate = $('#toDate').val(); 
			loadTable(itemCode);
		}); 
		function loadTable(kitemCode) {
			if ($.fn.DataTable.isDataTable("#datatableAddonServiceAvaliable")) {
				$('#datatableAddonServiceAvaliable').DataTable().clear().destroy();
			}
			var dataTable = $('#datatableAddonServiceAvaliable').DataTable({
				stateSave: true,
				"processing": true,
				"serverSide": true,
				"order": [],
				"searching": false,
				"ajax": {
					url: base_path + "index.php/Serviceavailable/addonServiceavailableList",
					type: "GET",
					data: {  'vendorItemCode': kitemCode}, 
					"complete": function(response) { 
						$(".toggleAddon").bootstrapSwitch({
							'size': 'mini',
							'onSwitchChange': function(event, state) {
								var code = $(this).attr('id'); 
								var action = $(this).bootstrapSwitch('state'); 
								if (action) {
									var flag = 1;
								}
								else {
									var flag = 0;
								}
								// return false;
								$.ajax({
									url: base_path + "index.php/Serviceavailable/changeServiceAddon",
									type: 'POST',
									data: {
										'code': code,
										'flag': flag
									},
									success: function(data) {
										if (data) {
											swal({
												title: "Completed",
												text: "Successfully updated the status",
												type: "success"
											},
											function(isConfirm) {
												if (isConfirm) {
													loadTable(kitemCode); 
												} else {
													loadTable(kitemCode); 
												}
											});
										}
										else {
											swal("Failed", "Item Active Status failed to update", "error");
											loadTable(kitemCode); 
										}
									}
								});
							} 
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
				toastr.success(obj.message, 'Customize Addon', {
					"progressBar": true
				});
			}
			else {
				toastr.error(obj.message, 'Customize Addon', {
					"progressBar": true
				});
			}
		}
		//end show alerts  
	});
</script>

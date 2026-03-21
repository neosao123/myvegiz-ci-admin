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
                        <h4 class="page-title">Vendor Commission List </h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/Home/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Vendor Commission List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
						<div class="d-flex no-block justify-content-end align-items-center"> 
							<div class="m-r-10"> 
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
         <h3 class="card-title"> Filter Commission :</h3>
			<form class="form-horizontal"> 
            <hr>
				<div class="form-row">
					
					            <?php	
									$todayDate = date('d/m/Y');
									$previousDate = date('d/m/Y',strtotime(' - 7 days'));
								?>
								<div class="col-sm-5">
									<div class="input-daterange input-group">
									<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
												<div class="input-daterange input-group" id="productDateRange">
													<input type="text" class="form-control date-inputmask col-sm-5" name="start"  id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>"/>
													<div class="input-group-append">
													<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
												  </div>
												<input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" placeholder="dd/mm/yyyy" value="<?= $todayDate ?>"/>
												</div>
										</div>
									</div>
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
                                <h4 class="card-title">Food Commission List</h4>
                                <div class="table-responsive">
                                    <table id="datatableCommission_food" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>Order Date</th>
                                                <th>Order id</th>
                                                <th>Total Order Amount</th>
												<TH>Commission %</TH>
												<th>Commission Amount</th>
												
												<th>Vendor Amount</th> 
												<th>Status</th> 
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            
							<h3 style="float: right;margin: 20px 0;">Total: <span id="total">0.00</span></h3>
							</div>
                        </div>
                    </div> 
                </div>
            </div> 
		</div>
	<script>
		$(document).ready(function() {
            var vendorCode = "",
			addDate = ""; 
			toDate = ""; 
			$('#fromDate ').datepicker({
			dateFormat: "mm/dd/yy",
			showOtherMonths: true,
			selectOtherMonths: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			todayHighlight: true,
			orientation: "bottom left",
		});
		$('#toDate ').datepicker({
			dateFormat: "mm/dd/yy",
			showOtherMonths: true,
			selectOtherMonths: true,
			autoclose: true,
			changeMonth: true,
			changeYear: true,
			todayHighlight: true,
			orientation: "bottom left",
		});
            function clearGlobalVariables() { 
                vendorCode = "", userName = "";
				var fromDate = $('#fromDate').val();
				var toDate= $('#toDate').val();
				
                loadTableFood(vendorCode, fromDate, toDate);
                //loadTableVege(vendorCode, addDate);
            }
            $('#btnClear').on('click', function(e) {
                clearGlobalVariables();
            }); 
            $('#btnSearch').on('click', function(e) { 
                $('#datatableCommission').DataTable().state.clear();
                vendorCode = $('#vendorCode').val();
                var fromDate = $('#fromDate').val();
				var toDate= $('#toDate').val();
                loadTableFood(vendorCode, fromDate,toDate);
                //loadTableVege(vendorCode, addDate);
            }); 
			clearGlobalVariables();
            function loadTableFood(p_deliveryboyCode, p_fromDate,p_toDate) {
                if ($.fn.DataTable.isDataTable("#datatableCommission_food")) {
                    $('#datatableCommission_food').DataTable().clear().destroy();
                } 
                $('#datatableCommission_food').DataTable({
                    stateSave: true,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "searching": false,
                    "ajax": {
                        url: base_path + "index.php/Commission/getVendorCommissionList",
                        data: {
                            'vendorCode': p_deliveryboyCode,
                            'fromDate': p_fromDate,
							'toDate':p_toDate,
                            'orderType': 'food'
                        },
                        type: "GET", 
                        complete: function(json) {
                            //console.log(json.responseJSON['vendorAmount1']);
							$('#total').text(json.responseJSON['vendorAmount1']);
                            
                             
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
                    toastr.success(obj.message, 'Commission', {
                        "progressBar": true
                    });

                } else {
                    toastr.error(obj.message, 'Commission', {
                        "progressBar": true
                    });

                }
            }
            //end show alerts
        });
	</script>
					
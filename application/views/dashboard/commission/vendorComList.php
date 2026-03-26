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
                        <h4 class="page-title">Vendor Payment List</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Payment List</li>
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
         <h3 class="card-title"> Filter Payment :</h3>
			<form class = "form-horizontal">
            <hr>
				<div class="form-row">
						<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="vender">Vendor:</label> </span>
										<input type="text"  class="form-control" list="vendorList" id="vendorCode" name="vendorCode" placeholder="Enter Vendor Name Here ">
											<datalist id="vendorList">
											<?php if($vendor){ foreach($vendor->result() as $v){
											echo'<option value="'.$v->code.'">'.$v->entityName.'</option>';
											} } ?>
											</datalist>	
									</div>
								</div>
					<?php	
									$date = date('Y-m-d');
			                       $addDate=date('d/m/Y',strtotime($date.' -1 days'));
									
								?>
								<div class="col-sm-3">
									<div class="input-daterange input-group">
									<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control" disabled name="start"  id="addDate" placeholder="dd/mm/yyyy" value="<?= $addDate ?>"/>
											
										</div>
									</div>
								</div>
								
								<div class="col-sm-4" style="margin-top:28px;">
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
                                <h4 class="card-title">Payment List</h4>
                                <div class="table-responsive">
                                    <table id="datatableCommission_food" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
												<th>Vendor</th>
                                                <th>Total Order Amount</th>
												<th>Deliver Amount</th>
												<th>Vendor Amount</th>
												<th>Operation</th>
												
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
	<script>
		$(document).ready(function() {
            var vendorCode = "",
			addDate = ""; 
			var currentDate = new Date();
			var yesterdayDate = currentDate.setDate(currentDate.getDate()- 1);
			$('#addDate').datepicker({
    			dateFormat: "mm/dd/yy",
    			showOtherMonths: true,
    			selectOtherMonths: true,
    			autoclose: true,
    			changeMonth: false,
    			changeYear: false, 
    			endDate: "-1d",
    			orientation: "bottom left",
    		});
            function clearGlobalVariables() {
                vendorCode="";
				addDate = $('#addDate').val();
				vendorCode=$('#vendorCode').val();
                loadTableFood(vendorCode, addDate);
               
            }
            $('#btnClear').on('click', function(e) {
                clearGlobalVariables();
            }); 
            $('#btnSearch').on('click', function(e) { 
                $('#datatableCommission_food').DataTable().state.clear();
                vendorCode=$('#vendorCode').val();
                addDate = $('#addDate').val(); 
                loadTableFood(vendorCode, addDate);
              
            }); 
			clearGlobalVariables();
            function loadTableFood(kvendorCode,kaddDate) {
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
                        url: base_path + "Vendorcommission/getVendorCommissionList",
                        data: {
                            'vendorCode':kvendorCode,
                            'date': kaddDate 
                        },
                        type: "POST", 
                        complete: function(json) { 
                            $(".paybtn").click(function() {
                                var code = $(this).data('id');
                                var paymentDate =  $('#addDate').val();
                                swal({
            						title: "Confirm Payment for selected vendor?",
            						text: "Orders for the selected restaurant will be marked as paid..",
            						type: "warning",
            						showCancelButton: !0,
            						confirmButtonColor: "#DD6B55",
            						confirmButtonText: "Yes, delete it!",
            						cancelButtonText: "No, cancel it!",
            						closeOnConfirm: !1,
            						closeOnCancel: !1
            					}, function(e) {
            						console.log(e);
            						if(e)
            						{
            							$.ajax({
            								url: base_path+"Vendorcommission/updateVendorPaymentFlag",
            								type: 'POST',
            								data:{
            								  'vendorCode':code,
            								  'paymentDate':paymentDate
            								},
            								success: function(data) { 
            								  if(data)
            								  {
            									swal({ 
            										  title: "Vendor Payment",
            										   text: "Vendor Payment Flag updated succesfully",
            											type: "success" 
            										  },
            										  function(isConfirm){
            									  if (isConfirm) {
            										 
            										loadTableFood(kvendorCode,kaddDate); 
            									  }
            									}); 
            								  }
            								  else
            								  {
            								   swal("Failed", "Record Not Deleted", "error");
            								  }
            								},
            								error: function(xhr, ajaxOptions, thrownError) {
            								   var errorMsg = 'Ajax request failed: ' + xhr.responseText;
            								   alert(errorMsg);
            								   console.log("Ajax Request for patient data failed : " + errorMsg);
            								}
            							   });
            						}
            						else
            						{
            							swal("Cancelled", "No changes were made.", "error");
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
                    toastr.success(obj.message, 'Vendor Payment', {
                        "progressBar": true
                    });

                } else {
                    toastr.error(obj.message, 'Vendor Payment', {
                        "progressBar": true
                    });

                }
            }
            //end show alerts
        });
	</script>
					
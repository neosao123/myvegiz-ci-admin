<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Vendor Commission List</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
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
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title"> Filter Commission :</h3>
                <form class="form-horizontal">
                    <hr>
                    <div class="form-row">
                        <div class="col-sm-3 mb-3">
                            <div class="form-group">
                                <span> <label for="vender">Vendor:</label> </span>
                                <input type="text" class="form-control" list="vendorList" id="vendorCode" name="vendorCode" placeholder="Enter Vendor Name Here ">
                                <datalist id="vendorList">
                                    <?php
                                    if ($vendor) {
                                        foreach ($vendor->result() as $v) {
                                            echo '<option value="' . $v->code . '">' . $v->entityName . '</option>';
                                        }
                                    }
                                    ?>
                                </datalist>
                            </div>
                        </div>
                        <?php
                        $todayDate = date('d/m/Y');  
                        $previousDate = date('d/m/Y', strtotime(' - 7 days'));
                        ?>
                        <div class="col-sm-5">
                            <div class="input-daterange input-group">
                                <span> <label> Search Dates :</label> </span>
                                <div class="input-daterange input-group" id="productDateRange">
                                    <input type="text" class="form-control date-inputmask" name="start" id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>" />
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
                                    </div>
                                    <input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" placeholder="dd/mm/yyyy" value="<?= $todayDate ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-top:28px;">
                            <button type="button" id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
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
                        <h4 class="card-title">Food Commission List</h4>
                        <div class="table-responsive">
                            <table id="datatableCommission_food" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Vendor Name</th>
                                        <th>Total Order Amount</th>
                                        <th>Sub Total Amount</th>
                                        <th>Commission Amount</th>
                                        <th>Vendor Amount</th>
										<th>Paid Amount</th>
										<th>Remaining Amount</th>
                                        <th>Operations</th>
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
    <!-- sample modal content -->
    <div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Pay Commission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <input type="hidden" class="form-control" id="vendor"
                                            name="vendor">
                                        <?php
										$todayDate = date('d/m/Y');
										$previousDate = date('d/m/Y', strtotime(' - 7 days'));
										?>
                                        <div class="col-sm-12">
                                            <div class="input-daterange input-group">
                                                <span> <label> Search Dates :</label> </span>
                                                <div class="input-daterange input-group">
                                                    <input type="text" class="form-control date-inputmask" name="start"
                                                        id="fromDateNew" placeholder="dd/mm/yyyy"
                                                        value="<?= $previousDate ?>" />
                                                    <div class="input-group-append">
                                                        <span
                                                            class="input-group-text bg-myvegiz b-0 text-white">TO</span>
                                                    </div>
                                                    <input type="text" class="form-control date-inputmask toDate"
                                                        name="end" id="toDateNew" placeholder="dd/mm/yyyy"
                                                        value="<?= $todayDate ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <span> <label>Pay Commission:</label> </span>
											
                                            <input type="text" class="form-control" id="payCommission"
                                                name="payCommission" readonly>
                                        </div>
                                        <div class="col-sm-12" style="margin-top:28px;">
                                            <button type="button" id="searchBtn" name="btnSearch"
                                                class="btn btn-myve waves-effect waves-light">Search</button>
                                            
											<button type="reset" id="payBtn"
                                                class="btn btn-dark waves-effect waves-light btn btn-inverse">Pay</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal -->
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#datatableCommission_food').DataTable();
        var vendorCode = "",
            addDate = "";
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
		
		$('#fromDateNew').datepicker({
					dateFormat: "mm/dd/yy",
					showOtherMonths: true,
					selectOtherMonths: true,
					autoclose: true,
					changeMonth: true,
					changeYear: true,
					todayHighlight: true,
					orientation: "bottom left",
				});

		$('#toDateNew').datepicker({
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
            fromDate = $('#fromDate').val();
			toDate = $('#toDate').val();
            loadTableFood("", fromDate, toDate);
        }
        $('#btnClear').on('click', function(e) {
            clearGlobalVariables();
        });
        $('#btnSearch').on('click', function(e) {
            $('#datatableCommission_food').DataTable().state.clear();
            vendorCode = $('#vendorCode').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            kfromDate = kToDate = '';
            if (fromDate != '' && toDate != '') {
                kfromDate = moment(fromDate, 'DD/MM/YYYY').format("YYYY/MM/DD");
                kToDate = moment(toDate, "DD/MM/YYYY").format("YYYY/MM/DD");
                //alert(keyInwardDateEnd);
                if (moment(kfromDate) > moment(kToDate)) {
                    alert('Date Should Be Greater Than From Date');
                    kfromDate = '';
                    kToDate = '';
                }
            }
            loadTableFood(vendorCode, kfromDate, kToDate);
        });
        clearGlobalVariables();
        function loadTableFood(p_deliveryboyCode, p_kfromDate, p_kToDate) {
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
                    url: base_path + "Vendorordercommission/getVendorCommissionList",
                    data: {
                        'vendorCode': p_deliveryboyCode,
                        'fromDate': p_kfromDate,
                        'toDate': p_kToDate,
                        'orderType': 'food'
                    },
                    type: "GET",
                    complete: function(json) {
                        //console.log(json);
					
                        $('#total').text(json.responseJSON['vendorAmount1']);
                        $(".blue").click(function() {
                            var vendorCode = $(this).data('vendor');
							$("#vendor").val(vendorCode);
                             $.ajax({
                                    url: base_path + "Vendorordercommission/viewUnpaid",
                                    type: "GET",
                                    data: {
                                        vendorCode: vendorCode,
                                        fromDate: p_kfromDate,
                                        toDate: p_kToDate,
                                    },
                                    datatype: "JSON",
                                    success: function (response) {
                                        var obj = JSON.parse(response);
                                        $("#payCommission").val(obj.commission);
                                    }
                             });
                        });
						
						$("#searchBtn").click(function () {
							debugger;
							 var code= $("#vendor").val();
							 var fromDate=$('#fromDateNew').val();
							 var toDate=$('#toDateNew').val();
							 $("vendor").val(code);
							 kfromDate = kToDate = '';
							 if (fromDate != '' && toDate != ''){
								kfromDate = moment(fromDate, 'DD/MM/YYYY').format("YYYY/MM/DD");
								kToDate = moment(toDate, "DD/MM/YYYY").format("YYYY/MM/DD");
								//alert(keyInwardDateEnd);
								if (moment(kfromDate) > moment(kToDate)) {
									$('#fromDateNew').val("");
									$('#toDateNew').val("");
									$("#payCommission").val("");
									toastr.error("Date Should Be Greater Than From Date", 'Commission', {
										"progressBar": true
									});
									return false;   
								}
								 $.ajax({
									url: base_path + "Vendorordercommission/viewUnpaid",
									type: "GET",
									data: {
										vendorCode: code,
										fromDate: fromDate,
										toDate: toDate,
									},
									datatype: "JSON",
									success: function (response) {
										var obj = JSON.parse(response);
										$("#payCommission").val(obj.commission);
									}
								});
							}
							
						});
						
						$("#payBtn").click(function () {
								var code= $("#vendor").val(); 
								var fromDate=$('#fromDateNew').val();
								var toDate=$('#toDateNew').val();
								$('#responsive-modal').modal('hide');
								$.ajax({
                                    url: base_path + "Vendorordercommission/paidStatus",
                                    type: "GET",
                                    data: {
                                        code: code,
                                        fromDate: fromDate,
                                        toDate: toDate,
                                    },
                                    datatype: "JSON",
                                    success: function (response) {
                                        var obj = JSON.parse(response);
                                        if (obj.status==true) {
											toastr.success("Vendor commission paid successfully", 'Commission', {
												"progressBar": true,
												"onHidden": function() {
												  window.location.reload();
												},
											});
                                      
										} else {
											toastr.error("Something went to wrong", 'Commission', {
												"progressBar": true
											});

										}
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
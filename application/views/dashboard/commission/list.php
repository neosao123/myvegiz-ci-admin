<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Commission List</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Commission List</li>
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <span> <label>Delivery Boy Name :</label> </span>
                                <input type="text" class="form-control" list="deliveryboyCodeList" id="deliveryboyCode"
                                    name="deliveryboyCode" placeholder="Enter Delivery Boy Name Here ">
                                <datalist id="deliveryboyCodeList">
                                    <?php if ($employee) {
                                        foreach ($employee->result() as $del) {
                                            echo '<option value="' . $del->code . '">' . $del->name . '</option>';
                                        }
                                    } ?>
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
                                    <input type="text" class="form-control date-inputmask" name="start" id="fromDate"
                                        placeholder="dd/mm/yyyy" value="<?= $previousDate ?>" />
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
                                    </div>
                                    <input type="text" class="form-control date-inputmask toDate" name="end" id="toDate"
                                        placeholder="dd/mm/yyyy" value="<?= $todayDate ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4" style="margin-top:28px;">
                            <button type="button" id="btnSearch" name="btnSearch"
                                class="btn btn-myve waves-effect waves-light">Search</button>
                            <button type="reset" id="btnClear"
                                class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Commission List</h4>
                        <div class="table-responsive">
                            <table id="datatableCommission" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Delivery Boy</th>
                                        <th>Earn Commission</th>
										<th>Paid Commission</th>
										<th>Remaining Commission</th>
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
                                        <input type="hidden" class="form-control" id="deliveryboyCode"
                                            name="deliveryboyCode">
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
											<input type="hidden" class="form-control" id="dBoyCode"
                                                name="dBoyCode">
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
    <script>
        $(document).ready(function () {
            var deliveryboyCode = "",
                fromDate = "",
                toDate = "";
            $('#fromDate').datepicker({
                dateFormat: "mm/dd/yy",
                showOtherMonths: true,
                selectOtherMonths: true,
                autoclose: true,
                changeMonth: true,
                changeYear: true,
                todayHighlight: true,
                orientation: "bottom left",
            });

            $('#toDate').datepicker({
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
                deliveryboyCode = "", userName = "";
                fromDate = $('#fromDate').val();
                toDate = $('#toDate').val();
                loadTable(deliveryboyCode, fromDate, toDate);
            }
            $('#btnClear').on('click', function (e) {
                clearGlobalVariables();
            });
            $('#btnSearch').on('click', function (e) {
                $('#datatableCommission').DataTable().state.clear();
                deliveryboyCode = $('#deliveryboyCode').val();
                fromDate = $('#fromDate').val();
                toDate = $('#toDate').val();
				kfromDate = kToDate = '';
				if (fromDate != '' && toDate != '') {
					kfromDate = moment(fromDate, 'DD/MM/YYYY').format("YYYY/MM/DD");
					kToDate = moment(toDate, "DD/MM/YYYY").format("YYYY/MM/DD");
					//alert(keyInwardDateEnd);
					if (moment(kfromDate) > moment(kToDate)) {
						toastr.success("Date Should Be Greater Than From Date", 'Commission', {
											"progressBar": true
										});
										return false;
						kfromDate = '';
						kToDate = '';
					}
				}
                loadTable(deliveryboyCode, fromDate, toDate);
            });
            clearGlobalVariables();

            function loadTable(p_deliveryboyCode, p_fromDate, p_toDate) {
                if ($.fn.DataTable.isDataTable("#datatableCommission")) {
                    $('#datatableCommission').DataTable().clear().destroy();
                }
                $('#datatableCommission').DataTable({
                    stateSave: true,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "searching": false,
                    "ajax": {
                        url: base_path + "Commission/getDeliveryBoyCommissionsList",
                        data: {
                            'deliveryboyCode': p_deliveryboyCode,
                            'fromDate': p_fromDate,
                            'toDate': p_toDate
                        },
                        type: "POST",
                        complete: function (json) {
                            $(".blue").click(function () {
                                var code = $(this).data('seq');
                                $("#dBoyCode").val(code);
                                $.ajax({
                                    url: base_path + "Commission/viewUnpaid",
                                    type: "POST",
                                    data: {
                                        code: code,
                                        fromDate: p_fromDate,
                                        toDate: p_toDate,
                                    },
                                    datatype: "JSON",
                                    success: function (response) {
                                        var obj = JSON.parse(response);
                                        $("#payCommission").val(obj.commission);
                                    }
                                });
                            });
							
							 $("#searchBtn").click(function () {
								 var code= $("#dBoyCode").val();
								 var fromDate=$('#fromDateNew').val();
								 var toDate=$('#toDateNew').val();
								 kfromDate = kToDate = '';
								if (fromDate != '' && toDate != '') {
									kfromDate = moment(fromDate, 'DD/MM/YYYY').format("YYYY/MM/DD");
									kToDate = moment(toDate, "DD/MM/YYYY").format("YYYY/MM/DD");
									//alert(keyInwardDateEnd);
									if (moment(kfromDate) > moment(kToDate)) {
										$('#fromDateNew').val("");
										$('#toDateNew').val("");
										$("#payCommission").val("");
										toastr.error("End date should be greater than from date", 'Commission', {
											"progressBar": true
										});
										return false; 
									}
								}
								 $.ajax({
                                    url: base_path + "Commission/viewUnpaid",
                                    type: "POST",
                                    data: {
                                        code: code,
                                        fromDate: fromDate,
                                        toDate: toDate,
                                    },
                                    datatype: "JSON",
                                    success: function (response) {
                                        var obj = JSON.parse(response);
                                        $("#payCommission").val(obj.commission);
                                    }
                                });
							 });
							 
							$("#payBtn").click(function () {
								var code= $("#dBoyCode").val();
								var fromDate=$('#fromDateNew').val();
								var toDate=$('#toDateNew').val();
								$('#responsive-modal').modal('hide');
								$.ajax({
                                    url: base_path + "Commission/paidStatus",
                                    type: "POST",
                                    data: {
                                        code: code,
                                        fromDate: fromDate,
                                        toDate: toDate,
                                    },
                                    datatype: "JSON",
                                    success: function (response) {
                                        var obj = JSON.parse(response);
                                        if (obj.status==true) {
											toastr.success("Delivery Boy commission paid successfully", 'Commission', {
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
        $(document).ready(function () {
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
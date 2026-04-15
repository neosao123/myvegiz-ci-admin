<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Commission List</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
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
                                                  
                         <input type="hidden" class="form-control" id="deliveryboyCode" name="deliveryboyCode" value="<?php echo $code; ?>">
           
        
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
                        <h4 class="card-title">Vegetable/Grocery Commission List</h4>
                        <div class="table-responsive">
                            <table id="datatableCommission_vege" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Delivery Boy</th>
                                        <th>Total Order Amount</th>
                                        <th>Receive Amount</th>
                                        <th>Delivery Boy Amount</th>
										<th>Payment Status</th>
                                        <th>Operations</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Food Commission List</h4>
                        <div class="table-responsive">
                            <table id="datatableCommission_food" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Delivery Boy</th>
                                        <th>Total Order Amount</th>
                                        <th>Receive Amount</th>
                                        <th>Delivery Boy Amount</th>
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

    <!-- sample modal content -->
    <div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">View Commission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal -->
</div>
<script>
    $(document).ready(function() {
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

        function clearGlobalVariables() {
			deliveryboyCode = "", userName = "";
		    deliveryboyCode = $('#deliveryboyCode').val();           
            fromDate = $('#fromDate').val();
            toDate = $('#toDate').val();
            loadTableFood(deliveryboyCode, fromDate, toDate);
            loadTableVege(deliveryboyCode, fromDate, toDate);
        }
        $('#btnClear').on('click', function(e) {
            clearGlobalVariables();
        });
        $('#btnSearch').on('click', function(e) {
            $('#datatableCommission').DataTable().state.clear();
            deliveryboyCode = $('#deliveryboyCode').val();
            fromDate = $('#fromDate').val();
            toDate = $('#toDate').val();
            loadTableFood(deliveryboyCode, fromDate, toDate);
            loadTableVege(deliveryboyCode, fromDate, toDate);
        });
        clearGlobalVariables();

        function loadTableFood(p_deliveryboyCode, p_fromDate, p_toDate) {
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
                    url: base_path + "Commission/getDeliveryBoyCommissionList",
                    data: {
                        'deliveryboyCode': p_deliveryboyCode,
                        'fromDate': p_fromDate,
                        'toDate': p_toDate,
                        'orderType': 'food'
                    },
                    type: "POST",
                    complete: function(json) {
                        //console.log(json);
                        $(".blue").click(function() {
                            var code = $(this).data('seq');
                            var order = $(this).data('order');
                            var orderType = $(this).data('ordertype');
                            var p_addDate = $('#addDate').val();
                            //console.log(p_addDate);
                            $(".modal-body").empty();
                            $.ajax({
                                url: base_path + "Commission/viewCurrentHistory",
                                type: "POST",
                                data: {
                                    code: code,
                                    fromDate: p_fromDate,
                                    toDate: p_toDate,
                                    order: order,
                                    orderType: orderType
                                },
                                datatype: "text",
                                success: function(data) {
                                    $(".modal-body").html(data);
                                }
                            });
                        });
                        //paid history
                        $(".history").click(function() {
                            var code = $(this).data('seq');
                            $("#historydiv").empty();
                            $.ajax({
                                url: base_path + "Commission/showhistory",
                                method: "POST",
                                data: {
                                    code: code
                                },
                                datatype: "text",
                                success: function(data) {
                                    $("#historydiv").html(data);
                                }
                            });
                        });
                    }
                }
            });
        }

        function loadTableVege(p_deliveryboyCode, p_fromDate, p_toDate) {
            if ($.fn.DataTable.isDataTable("#datatableCommission_vege")) {
                $('#datatableCommission_vege').DataTable().clear().destroy();
            }
            $('#datatableCommission_vege').DataTable({
                stateSave: true,
                "processing": true,
                "serverSide": true,
                "order": [],
                "searching": false,
                "ajax": {
                    url: base_path + "Commission/getDeliveryBoyCommissionList",
                    data: {
                        'deliveryboyCode': p_deliveryboyCode,
                        'fromDate': p_fromDate,
                        'toDate': p_toDate,
                        'orderType': 'vegetable'
                    },
                    type: "POST",

                    complete: function(json) {
                        //console.log(json);
                        $(".blue").click(function() { 
                            var code = $(this).data('seq');
                            var order = $(this).data('order');
                            var orderType = $(this).data('ordertype');
                            var fromDate = $('#fromDate').val();
							var toDate = $('#toDate').val();
                            console.log('vegitable:' + orderType);
                            $(".modal-body").empty();
                            $.ajax({
                                url: base_path + "Commission/viewCurrentHistory",
                                type: "POST",
                                data: {
                                    code: code,
                                    fromDate: fromDate,
                                    toDate: toDate,
                                    order: order,
                                    orderType: orderType
                                },
                                datatype: "text",
                                success: function(data) {
                                    $(".modal-body").html(data);
                                }
                            });
                        });
                        //paid history
                        $(".history").click(function() {
                            var code = $(this).data('seq');
                            $("#historydiv").empty();
                            $.ajax({
                                url: base_path + "Commission/showhistory",
                                method: "POST",
                                data: {
                                    code: code
                                },
                                datatype: "text",
                                success: function(data) {
                                    $("#historydiv").html(data);
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
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
                <h4 class="page-title">Charges Settings</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Charges Settings</li>
                        </ol>

                    </nav>
                </div>
            </div>
            <div class="col-7 align-self-center">
                <div class="d-flex no-block justify-content-end align-items-center">

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
                <h3 class="card-title"> Filter:</h3>
                <form class"form-horizontal">
                    <hr>
                    <div class="form-row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <span> <label>City</label> </span>
                                <select class="form-control cityCode" id="cityCode" name="cityCode">
                                    <option value="">Select City</option>
                                    <?php foreach ($city->result() as $c) {
														echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
													} ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="form-group m-b-0  text-center">
                        <button type="button" id="btnSearch" name="btnSearch"
                            class="btn btn-myve waves-effect waves-light">Search</button>
                        <button type="reset" id="btnClear"
                            class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
                    </div>
                </form>
            </div>
        </div>



        <!-- basic table -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <h4 class="card-title">Charges Settings</h4>
                        <div class="table-responsive">
                            <table id="datatableCharges" class="table table-striped table-bordered ">
                                <thead>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Code</th>
                                        <th>City</th>
                                        <th>Service Name</th>
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
    <div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> View Charges Settings</h4>
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
    $(document).ready(function () {

        loadTable();

        $('#btnSearch').on('click', function (e) {
            var cityCode = $("#cityCode").val();
            loadTable(cityCode);

        });
        $('#btnClear').on('click', function (e) {
            loadTable("");
        });
        function loadTable(p_cityCode) {

            if ($.fn.DataTable.isDataTable("#datatableCharges")) {
                $('#datatableCharges').DataTable().clear().destroy();
            }

            var dataTable = $('#datatableCharges').DataTable({
                stateSave: true,
                "processing": true,
                "serverSide": true,
                "order": [],
                "searching": false,
                "ajax": {
                    url: base_path + "chargessettings/getChargeList",
                    data: {
						'cityCode': p_cityCode
					},
                    type: "GET",
                    "complete": function (response) {
                        $(".blue").click(function () {
                            var code = $(this).data('seq');
                            $.ajax({
                                url: base_path + "chargessettings/view",
                                method: "GET",
                                data: { code: code },
                                datatype: "text",
                                success: function (data) {
                                    $(".modal-body").html(data);

                                }
                            });
                        });
                        //delete
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


                toastr.success(obj.message, 'Charges settings', { "progressBar": true });

            }
            else {
                toastr.error(obj.message, 'Charges settings', { "progressBar": true });

            }
        }
        //end show alerts
    });


</script>
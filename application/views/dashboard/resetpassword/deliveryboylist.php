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
                        <h4 class="page-title">Delivery Boy Password Reset List</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Password Reset List</li>
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
				 <div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div class="form-row">
								<div class="col-md-4">
									<label>City</label>
									<select id="cityCode" class="form-control">
										<option value="">Select City</option>
										<?php	
											if($city){
												foreach($city->result_array() as $r){
													echo '<option value="'.$r['code'].'">'.$r['cityName'].'</option>';
												}
											}
										?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			  <div class="col-12">
				<div class="card">
				  <div class="card-body">
					<div class="alert alert-dark alt_msg background-primary alt_msg" style="display: none;">
					  <strong></strong>
					  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<i class="icofont icofont-close-line-circled text-white"></i>
					  </button>
					</div>
					<h4 class="card-title">Passord Reset List</h4>
					<br>
					<div class="table-responsive">
					  <table id="datatable-city" class="table table-striped table-bordered ">
						<thead>
						  <tr>
							<th>Sr. No</th>
							<th>Code</th>
							<th>City</th>
							<th>User name</th>
							<th>Email</th>
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
		  <!-- ============================================================== -->
		  <!-- End Container fluid  -->
</div>
<script>
  $( document ).ready(function() {
  setTimeout(function() {
  $(".alt_msg").delay(3200).fadeOut(300);
  });
  $('#fiterSearch').hide();

  getDataTable();
	$("#cityCode").change(function(){
		var cityCode = $(this).val();
		getDataTable(cityCode);
	});
  function getDataTable(cityCode)
  {
  $.fn.DataTable.ext.errMode = 'none';
  if ($.fn.DataTable.isDataTable("#datatable-city")) {
  $('#datatable-city').DataTable().clear().destroy();
  }

  var dataTable = $('#datatable-city').DataTable({  
	stateSave: true,
  "processing":true, 
  "serverSide":true,
  "ordering":true,
  "searching": true,
  paging:true,
  "ajax":{
  url: base_path+"index.php/resetpassword/getDeliveryBoyPasswordList",
  data:{'cityCode':cityCode},
  type:"GET", 
  "complete": function(response) {
  operations();
  }
  }
  });
  }

  function operations(){
   
    $('.resetpswd').on("click", function() {
					 var code = $(this).attr('id');
						swal({
							title: "Can you confirm to reset the password for the selected user?",
							text: "Password will be reset to '123456'!",
							type: "warning",
							showCancelButton: !0,
							confirmButtonColor: "#DD6B55",
							confirmButtonText: "Yes, Reset It!",
							cancelButtonText: "No",
							closeOnConfirm: !1,
							closeOnCancel: !1
						}, function(e) {
							if(e)
							{
								$.ajax({
									url:"<?= base_url() . 'index.php/resetpassword/resetDeliveryPwd' ?>",
									type: 'POST',
									data:{
									  'code':code
									},
									success: function(data) {
									
									  if(data)
									  {
										swal({ 
											  title: "Completed",
											   text: "Successfully Reseet the Password to '123456'!",
												type: "success" 
											  },
											  function(isConfirm){
										  if (isConfirm) {
											//location.reload(true);
											getDataTable();
										  }
										});
									  }
									  else
									  {
									  swal("Failed", "Failed to reset password! Please try later.", "error");
									  }
									},
									error: function(xhr, ajaxOptions, thrownError) {
									   var errorMsg = 'Ajax request failed: ' + xhr.responseText;
									 //  alert(errorMsg);
									 //  console.log("Ajax Request for patient data failed : " + errorMsg);
									}
								   });
							}
							else
							{
								swal("Cancelled", "Canceled the reset request :)", "error");
							}
						});
					}); //mywarning
  }
  <?php if ($this->session->flashdata('alertMsg')) { ?>
    var alertmsg=JSON.parse('<?php echo $this->session->flashdata("alertMsg"); ?>');
    // alert(alertmsg.check_msg);
    if(alertmsg.check_msg == 'success')
    {
    $('.alt_msg').html(alertmsg.message).show();
    }
    else{
    $('.alt_msg').hide();
    }
  <?php } ?>
  });

</script>
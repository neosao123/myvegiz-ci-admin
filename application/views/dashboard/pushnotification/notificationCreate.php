<style>
	.select2-container--classic .select2-selection--single, .select2-container--default .select2-selection--multiple, .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--single .select2-selection__arrow, .select2-container--default .select2-selection--single .select2-selection__rendered {
		border-color: rgba(0,0,0,0.25);
		height: auto;  			 
	}
	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		background:#588002
	}
	.select2-container--default .select2-results__option--highlighted[aria-selected],.select2-container--default .select2-results__option[aria-selected=true]  {background:#96bf3fbd}
	.select2-container--default .select2-selection--multiple .select2-selection__choice > span { color:white!important;forn-weight:bold}
    .select2-selection.select2-selection--single, #select2-client-container { height:45px; line-height: 45px;	 } 
	#select2-client-container{ border-color: black; }
	.select2-selection__arrow{ height:45px!important;}
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
                        <h4 class="page-title">Notification</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Notification</li>
                                </ol>
                            </nav>
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
				
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Notification </h4>
								<form>
								<div class="col-md-4 mx-auto">
									<select id='purpose' class="selectpicker form-control" title="Choose Type">
										<option value="">Select Notification Type</option>
										<option value="1">Product Notification</option>
										<option value="2">Custom Notification</option>
									</select>
								</div>
								</form>
                                <div class="border border-dark col-md-12 p-2 mt-2" style='display:none;' id='simple'>
								<h3 class="box-title">Product Notification Panel</h3>
									<!-- form start -->
									<form class="form-horizontal" action="<?= base_url().'Notification/notificationprocess'?>" method="post" enctype="multipart/form-data">
										<div class="row">
											<div class="form-group col-sm-4">
												<label for="inputEmail3" class="control-label">Title</label>
												<input type="text" class="form-control" id="inputEmail3" placeholder="Title" name="title" required >
											</div>
											
											<div class="form-group col-sm-6">
												<label for="cityCode" class="control-label">City</label>
												<!--<select class="form-control" id="cityCode" name="cityCode" required > -->
												<select class="form-control js-example-responsive" multiple="multiple" id="cityCode" name="cityCode[]" data-border-color="primary" data-border-variation="accent-2" required  style="width:100%">
													<option value="">Select City</option>
													<?php
														if($city){
															foreach($city->result_array() as $c){
																echo '<option value="'.$c['code'].'">'.$c['cityName'].'</option>';
															}
														}
													?>
												</select>
											</div>
											
											<div class="form-group col-sm-12">
												<label for="inputPassword3" class="control-label">Message</label>
												<input type="text" class="form-control" id="inputPassword3" placeholder="Message"  name="msg" required>
											</div>
											
											<div class="form-group col-md-6">
												<label for="inputEmail3" class="control-label">Product</label>
												<select type="text" class="form-control" id="productCode" placeholder="Product Name"  name="place" required>
												</select>
											</div>
											<div class="form-group col-md-4">
												<div class="col-sm-10">
												<center>
													<br><img id="imm" src=""  height="200" width="150"  class="d-none"> </center> <br> 
													<input type="hidden" class="form-control" id="imgHide" placeholder="Message"  name="img"  >
													<input type="submit" class="btn btn-primary " value="Send Notification" name="simple">
												</div>
											</div>
											<script>
												$(document).ready(function(){
													// code to get all records from table via select box
													// $("#cityCode").change(function(){
														// var id = $(this).val();
														// $.ajax({
															// type:'get',
															// url: base_path+"Notification/getProductListByCity",
															// data: {'cityCode':id},
															// success: function(d){
																// $("#productCode").html(d);
															// }
														// });
													// });
													$("#cityCode").select2({});
													$("#cityCode").change(function(){
														var cityCode = [];
														$.each($("#cityCode option:selected"), function(){            
															cityCode.push($(this).val());
														});
														var id = $(this).val();
														// console.log(id);
														// return false;
														$.ajax({
															type:'get',
															url: base_path+"Notification/getProductListByCity",
															data: {'cityCode':id},
															success: function(d){
																// console.log(d);
																// return false; 
																$("#productCode").html(d);
															}
														});
													});
													
													$("#productCode").change(function() {
														var id = $(this).val();
														$.ajax({
															type:'get',
															url: base_path+"Notification/getProductImg",
															data: {'id':id},
															success: function(d){
																var path  = JSON.parse(d);
																if(path['status']=='yes'){
																var img = base_path+'uploads/product/'+id+'/'+path['img'];
																   $('#imm').attr('src', img);
																   $('#imgHide').val(img);
																   $('#imm').removeClass('d-none').addClass('d-block');
																} else {																 
																	var img = base_path+'assets/images/MYVEGIZ LOGO 1st -01-cut.png';
																	$('#imm').attr('src', img);
																	$('#imgHide').val(img);
																	$('#imm').removeClass('d-none').addClass('d-block');
																}														   
															}											  
														});
													})
												});                  
											</script>
										</div>
									</form> 
								</div>	
								    <!-- custom  notification send--->
								<div class="border border-dark col-md-12 p-2 mt-2" style='display:none;' id='custom'>
									<h3 class="box-title">Custom Notification Panel</h3>
									<!-- form start -->
									<form class="form-horizontal" action="<?= base_url().'index.php/Notification/notificationprocess'?>" method="post" enctype="multipart/form-data">
										<div class="form-row">	
    										<div class="form-group col-sm-4">
    										    <label for="inputEmail3" class="control-label">Title</label>
    											<input type="text" class="form-control" id="inputEmail3" placeholder="Title" name="title" required >
    										</div>										
    										<div class="form-group col-sm-12">
    											<label for="inputPassword3" class=" control-label">Message</label>
    											<input type="text" class="form-control" id="inputPassword3" placeholder="Message"  name="msg" required>
    										</div>
    										<div class="form-group col-sm-4">
    											<label for="cityCode" class="control-label">City</label>
    											<!--<select class="form-control" id="cityCode2" name="cityCode2" required>-->
												<select class="form-control js-example-responsive" multiple="multiple" id="cityCode2" name="cityCode2[]" data-border-color="primary" data-border-variation="accent-2" required  style="width:100%">
    												<option value="">Select City</option>
    												<?php
    													if($city){
    														foreach($city->result_array() as $c){
    															echo '<option value="'.$c['code'].'">'.$c['cityName'].'</option>';
    														}
    													}
    												?>
    											</select>
    										</div>
    										<div class="form-group col-md-4">
    											<label for="inputEmail3" class="control-label">Client Name</label>
    											<select class="form-control js-example-responsive" id="client" placeholder="Client Name" name="client" data-border-color="primary" data-border-variation="accent-2" style="width:100%">
    											</select>
    										</div>
    										<div class="form-group col-md-4">
    											<label for="inputEmail3" class="control-label">Product</label>
    											<input type="text" class="form-control" id="place" placeholder="Product Name"  name="place" list="productList" required>
    											<datalist id="productList">
    											</datalist>
    										</div>
    										<div class="form-group col-md-4">
    											<input type="file" id="exampleInputFile" class="form-control" name="catimg">
    											<center><br><img id="immg" src="" height="200" width="150" class="d-none"> </center> <br> 
    											<input type="hidden" class="form-control" id="imgHide2" placeholder="Message"  name="img"  >
    											<script>
        											$(document).ready(function(){
        												// code to get all records from table via select box
        												$("#place").change(function() {
            												var id = $(this).val();
            												$.ajax({
            													type:'get',
            												    url: base_path+"Notification/getProductImg",
            												    data: {'id':id},
            												    success: function(d){
            												        var patha  = JSON.parse(d);
            												        var img = base_path+'uploads/product/'+id+'/'+patha['img'];
																	if(patha['status']==='yes'){
																	    $('#immg').attr('src', img);
																		// console.log(img); 
																	    $('#imgHide2').val(img);
																	    $('#immg').removeClass('d-none').addClass('d-block');
																	}
                												} 
            												});
        												})
        											});
    											</script>
    										</div>
    										<div class="col-sm-4">
    										    <input type="submit" class="btn btn-primary pull-right" value="Send Notification" name="custom">
    										</div>
    										<script>
    											$(document).ready(function(){
    												// code to get all records from table via select box
    												// $("#cityCode2").change(function(){
    													// var id = $(this).val();
    													// $.ajax({
															// type:'get',
															// url: base_path+"Notification/getProductListByCity",
															// data: {'cityCode':id},
															// success: function(d){
																// $("#productList").html(d);
															// }
														// });
    													// $.ajax({
    														// type:'get',
    														// url: base_path+"Notification/getCustomersListByCity",
    														// data: {'cityCode':id},
    														// success: function(d){
    															// $("#client").html(d);
    														// }
    													// });
    												// });	
													$("#client").select2({});
													$("#cityCode2").select2({});
													$("#cityCode2").change(function(){
														var cityCode2 = [];
														$.each($("#cityCode2 option:selected"), function(){            
															cityCode2.push($(this).val());
														});
														
    													var id = $(this).val();
    													$.ajax({
															type:'get',
															url: base_path+"Notification/getProductListByCity",
															data: {'cityCode':id},
															success: function(d){
																$("#productList").html(d);
															}
														});
    													$.ajax({
    														type:'get',
    														url: base_path+"Notification/getCustomersListByCity",
    														data: {'cityCode':id},
    														success: function(d){
																// console.log(d);
    															$("#client").html(d);
    														}
    													});
    												});	
    											});
    										</script>
    									</div>
    								</form>
								</div>
							</div>
						</div>
					</div>			 
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
			<!-- sample modal content -->
		</div>
	</div>
</div>
<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none; ">
	<div class="modal-dialog">	
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View Employee</h4>
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
<script>
	$(document).ready(function(){
		$('#purpose').on('change', function() {
		   
		  if ( this.value == '1')
		  {
			$("#simple").show();
		  }
		  else
		  {
			$("#simple").hide();
		  }
		   if ( this.value == '2')
		  {
			$("#custom").show();
		  }
		  else
		  {
			$("#custom").hide();
		  }
		});
	});
	$( document ).ready(function() {
		<?php if ($this->session->flashdata('message')) { ?>
		var alertmsg = '<?php echo $this->session->flashdata('message'); ?>';
		if (alertmsg)
		{
			toastr.success(alertmsg, 'Notification', { "progressBar": true });
		}
		else {
			toastr.error(alertmsg, 'Notification', { "progressBar": true });
		}
		<?php } ?>
   });
</script>
	
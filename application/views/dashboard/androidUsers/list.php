  

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
                        <h4 class="page-title">Android Users</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Android Users List</li>
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
							<h3 class="card-title"> Andorid Users Filter :</h3>
							<form  class="form-horizontal">
                            <hr>
							
                            <div class="form-row">
								<div class="col-sm-3 mb-1">
								<div class="form-group">
								<span> <label for="code">Client Name :</label> </span>
                                     <input type="text" id="code" list="clientNameList" name="code" value=""  class="form-control" placeholder="Enter Client Name Here" > 
									<datalist id="clientNameList">
									                                           
									  <?php foreach ($query->result() as $row) {
	echo "<option value='" . $row->code . "'>" . $row->name . "</option>";
}?>
								 
									</datalist>
							</div>
							</div>
							<div class="col-sm-3 mb-1">
								<div class="form-group">
									<span> <label for="cityCode">Login City:</label> </span>
									<select  class="form-control" id="cityCode" name="cityCode">
										<option value="">Select City</option>
										<?php
foreach ($city->result() as $c) {
	echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
}?>
									</select>
								</div>
							</div>
							<div class="col-sm-3 mb-1">
								<div class="form-group">
								<span> <label for="mobile">Mobile Number :</label> </span>
							<input type="text"  class="form-control" list="mobileNumberList" id="mobile" name="mobile" placeholder="Enter Mobile Number Here">
								<datalist id="mobileNumberList">
								<?php foreach ($query->result() as $row) {
	echo "<option value='" . $row->mobile . "'>" . $row->mobile . "</option>";
}?>
								 
									 
								</datalist>
							</div>
							</div>
							<div class="col-sm-3 mb-1">
								<div class="form-group">
								<span> <label for="emailId">Email Id:</label> </span>
							<input type="text"  class="form-control" list="emailIdList" id="emailId" name="emailId" placeholder="Enter Email Id Here">
									<datalist id="emailIdList">
									<?php foreach ($query->result() as $row) {
	echo "<option value='" . $row->emailId . "'>" . $row->emailId . "</option>";
}?>
								 
									</datalist>
							</div>
							</div>
							 <div class="col-sm-3 mb-1">
                                   <label for="isActive">Status: </label>
                                        <select id="isActive" name="isActive" class="form-control required">
                                        <option value="">Select option</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                        
                                        </select>
							</div> 
							</div> 
							<center>
							<div class="row">
								<div class="card-body">
									<div class="form-group m-b-0  text-center">
										<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
										<button type="Reset" class="btn btn-dark waves-effect waves-light btn btn-inverse" id="btnClear">Clear</button>
									</div>
								</div>
							</div>							
							</div></center>
							 <div class="col-12">
                                <h4 class="card-title"> Android Users List</h4>
								
                               <div class="table-responsive">
                                    <table id="datatableAndroidUsers" class="table table-striped table-bordered ">
                                        <thead>
                                            <tr>
                                               <th>Sr. No</th>
                                              <th>Code</th>
                                              <th>Client Name</th>
												<th>Mobile</th>
                                               <th>Email ID</th>
                                               <th>Local</th>
                                               <th>City</th>
                                               <th>Pin Code</th>
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
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
		<!-- sample modal content -->
			<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title"> View Android User</h4>
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
<table id="androidusers_export" class="table table-striped table-bordered d-none">
	<thead>
		<tr>
			<th>Sr.No</th>
			<th>Code</th>
			<th>Client Name</th>
			<th>Mobile</th>
			<th>Email ID</th>
			<th>Local</th>
			<th>City</th>
			<th>Pin Code</th>
			<th>Status</th> 
		</tr>
	</thead>
</table>  			 
<!-- /.modal -->
<script>
	$( document ).ready(function() {
		loadTable();
		function loadTable(){
			var  cityCode,keyclientName,keymobileNumber,keyemailId,keystatus="";
			getDataTable(keyclientName,keymobileNumber,keyemailId,keystatus);
		}
		$('#btnSearch').on('click', function (e){
			keyclientName=$('#code').val();		 
			$('#datatableAndroidUsers').DataTable().state.clear();
			keymobileNumber=$('#mobile').val();
			keyemailId=$('#emailId').val();
			cityCode =$("#cityCode").val();
			keystatus=$('#isActive').val();
			getDataTable(cityCode,keyclientName,keymobileNumber,keyemailId,keystatus);
		});			
		$('#btnClear').on('click', function (e){
			getDataTable();
		});
	 
		function getDataTable(p_keycityCode,p_keyclientName,p_keymobileNumber,p_keyemailId,p_keystatus)
		{
			if ($.fn.DataTable.isDataTable("#datatableAndroidUsers")) {
				$('#datatableAndroidUsers').DataTable().clear().destroy();
			} 
			var totalRecords=0;
			jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
				if ( this.context.length ) {
					var jsonResult = $.ajax({
						url: base_path+"AndroidUsers/getAndroidUsersList",
						data: {
							'export':1,
							'code':p_keyclientName,
							'mobile':p_keymobileNumber,
							'cityCode':p_keycityCode,
							'emailId':p_keyemailId,	 
							'isActive':p_keystatus,
							'page':totalRecords,
							draw:0
						},
						type:"POST", 
						success: function (result) {
						},
						async: false
					});
					var jencode=JSON.parse(jsonResult.responseText);
					return {body: jencode.data, header: $("#androidusers_export thead tr th").map(function() { return this.innerHTML; }).get()};
				}
			});
			var dataTable = $('#datatableAndroidUsers').DataTable({  
				stateSave: false,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": true,
				"ajax":{  
					url: base_path+"AndroidUsers/getAndroidUsersList",  
					data:{
						'export':0,
						'code':p_keyclientName,
						'mobile':p_keymobileNumber,
						'cityCode':p_keycityCode,
						'emailId':p_keyemailId,	 
						'isActive':p_keystatus
						},	
					type:"POST", 
					"complete": function(response) {
						$(".blue").click(function(){
							var code=$(this).data('seq');
							$.ajax({
								url: base_path+"AndroidUsers/view",  
								method:"POST",
								data:{code:code},
								datatype:"text",
								success: function(data)
								{
									$(".modal-body").html(data);
									
								}
							});
						});
						//delete						 
						$('.mywarning').on("click", function() {
							var code=$(this).data('seq');				
							swal({
								title: "Are you sure?",
								text: "You want to delete the Android Users Record of  "+code,
								type: "warning",
								showCancelButton: !0,
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Yes, delete it!",
								cancelButtonText: "No, cancel it!",
								closeOnConfirm: !1,
								closeOnCancel: !1
							}, function(e) {
								//console.log(e);
								if(e)
								{
									$.ajax({
										url: base_path+"AndroidUsers/delete",
										type: 'POST',
										data:{
										  'code':code
										},
										success: function(data) {
										
										  if(data)
										  {
											swal({ 
												  title: "Completed",
												   text: "Successfully Deleted",
													type: "success" 
												  },
												  function(isConfirm){
											  if (isConfirm) {
												//location.reload(true);
												loadTable();
												
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
										   //console.log("Ajax Request for patient data failed : " + errorMsg);
										}
									   });
								}
								else
								{
									swal("Cancelled", "Your Designation Record is safe :)", "error");
								}
							});
						});	
					}
				}
			});
		}
		$('#btnClear').click(function(){
			var  keyclientName,keymobileNumber,keyemailId,keypinCode="";
			getDataTable(keyclientName,keymobileNumber,keyemailId,keypinCode);
		});
	});
 	  $( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  
	
         toastr.success(obj.message, 'Android USers', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Android USers', { "progressBar": true });
       
      }
    }
	//end show alerts
   });				
</script>
 
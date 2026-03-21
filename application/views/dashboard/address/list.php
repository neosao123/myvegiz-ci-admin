  

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
                        <h4 class="page-title">Address List</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Address List</li>
                                </ol>

                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            
                            <div class=""><a class="btn btn-myve" href="<?php echo base_url().'address/add';?>">Add New Address</a></div>
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
			
				<div class="card">
        <div class="card-body">
         <h3 class="card-title"> Filter Address :</h3>
         <form class"form-horizontal">
            <hr>
            <div class="form-row">
				
				<div class="col-sm-3">
                  <div class="form-group">
                     <span> <label>City</label> </span>
                     <select class="form-control cityCode"  id="cityCode" name="cityCode">
						 <option value="">Select City</option>
						 <?php foreach($city->result() as $c){
							echo'<option value="'.$c->cityName.'">'.$c->cityName.'</option>';
						}?>
                     </select>
                  </div>
               </div>
				
			
               <div class="col-sm-3">
                  <div class="form-group">
				   <span> <label>Place :</label> </span>
                    <input type="text" class="form-control" list="placelist" id="place" name="place" placeholder="Select Place ">
					 <datalist id="placelist">
						<?php foreach($place->result() as $pl){
						echo'<option value="'.$pl->place.'">'.$pl->place.'</option>';
						}?>
					  </datalist>
                    </div>
				</div> 
             
			   
				<div class="col-sm-3">
                  <div class="form-group">
				   <span> <label>State :</label> </span>
                    <input type="text"  class="form-control" list="stateList" id="stateCode" name="stateCode" placeholder="Select State Name Here ">
					 <datalist id="stateList">
						<?php foreach($state->result() as $state){
						echo'<option value="'.$state->state.'">'.$state->state.'</option>';
						}?>
					  </datalist>
                    </div>
				</div>
			   
			    <div class="col-sm-2">
                  <div class="form-group">
				   <span> <label>District :</label> </span>
                    <input type="text"  class="form-control" id="district" name="district" list="distList" placeholder="Select Your District">
						<datalist id="distList">
							<?php foreach($district->result() as $dist){
							echo'<option value="'.$dist->district.'">'.$dist->district.'</option>';
							}?>
						</datalist>
                    </div>
               </div>
			   
			   <div class="col-sm-2">
                  <div class="form-group">
                     <span> <label>Service Available?</label> </span>
                     <select class="form-control storageCode"  id="serviceAvl" name="serviceAvl">
						 <option value="">Select option</option>
						 <option value="1">Service Available</option>
						 <option value="0">Service Unavailable</option>
                     </select>
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
			
			
			
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-body">
                                <h4 class="card-title"> Address List</h4>
                              
                                
                                <div class="table-responsive">
                                    <table id="datatableAdd" class="table table-striped table-bordered ">
                                        <thead>
                                            <tr>
												<th>Sr.No</th>
												<th>Code</th>
												<th>City</th>
												<th>State</th>
												<th>District</th>
												<th>Place</th>
												<th>Pincode</th>
												<th>Service Available Status</th>
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
			<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">View Address</h4>
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
		   $( document ).ready(function() {
			   
			   var cityCode='',place='',stateCode="",districtCode="",service="";
			
			function clearGlobalVariables()
			{
				cityCode='',stateCode="",districtCode="",service="",place='';
				
			
			}
			    loadTable();
			$('#btnClear').on('click',function(){
				loadTable('','','','','');
			});	
			
			$('#btnSearch').on('click', function (e){

				$('#datatableAdd').DataTable().state.clear();
				stateCode=$('#stateCode').val();
				cityCode=$('#cityCode').val();
				districtCode=$('#district').val();
				service=$('#serviceAvl').val();
				place=$('#place').val();
				loadTable(cityCode,stateCode,districtCode,service,place);
			});
			
			function loadTable(p_cityCode,p_stateCode,p_districtCode,p_service,p_place){
					if ($.fn.DataTable.isDataTable("#datatableAdd")) {
						$('#datatableAdd').DataTable().clear().destroy();
					}
			   var dataTable = $('#datatableAdd').DataTable({  
					stateSave: true,
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": true,
				   "ajax":{  
						url: base_path+"Address/getAddrList",  
						data:{
							'cityCode':p_cityCode,
							'state':p_stateCode,
							'district':p_districtCode,
							'service':p_service,
							'place':p_place
						},
						type:"GET" , 
				   
				    "complete": function(response) {
					 $(".blue").click(function(){
									 var code=$(this).data('seq');
									 $.ajax({
											url: base_path+"Address/view",  
											method:"GET",
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
				// alert('hii')
				var code=$(this).data('seq');
				//alert(code);
					swal({
						title: "Are you sure?",
						text: "You want to delete Address Record of "+code,
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
								url: base_path+"Address/delete",
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
								   console.log("Ajax Request for patient data failed : " + errorMsg);
								}
							   });
						}
						else
						{
							swal("Cancelled", "Your Address Record is safe :)", "error");
						}
					});
				});	
								  }
				   }
			  });
			}   
		   });
		    $( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  toastr.success(obj.message, 'Address', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Address', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
		</script>
		<script>
    //=============================================//
    //    File export                              //
    //=============================================//
    // $('#file_export').DataTable({
        // dom: 'Bfrtip',
        // buttons: [
            // 'copy', 'csv', 'excel', 'pdf', 'print'
        // ]
    // });
    // $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-cyan text-white mr-1');
    </script>
				





 

 
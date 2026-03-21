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
                        <h4 class="page-title">Service Unavailable Orders List</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Service Unavailable List</li>
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
							<h3 class="card-title"> Filter :</h3>
							 <form>
                            <div class="form-row">
								
										<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderCode">Order Code :</label> </span>
										<input type="text"  class="form-control" list="orderlist" id="orderCode" name="orderCode" placeholder="Enter Order Code Here ">
											<datalist id="orderlist">
											<?php if($orderCode){ foreach($orderCode->result() as $od){
											echo'<option value="'.$od->code.'"></option>';
											} } ?>
											</datalist>
									</div>
								</div>
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="cityCode">City:</label> </span>
										<select  class="form-control" id="cityCode" name="cityCode">
											<option value="">Select City</option>
											<?php
											foreach ($city->result() as $c) {
												echo '<option value="' . $c->code . '">' . $c->cityName . '</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Order Status :</label> </span>
										<select  class="form-control" id="orderStatus" name="orderStatus">
											<option value="">Select Option</option>
											<option value="PND">Pending</option>
											<option value="RJT">Rejected</option>
											<option value="CAN">Cancel</option>
										</select>
								
									</div>
								</div>
								
								<div class="card-body">
								<div class="form-group  text-center">
								<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
								<button type="Reset" class="btn btn-dark waves-effect waves-light btn btn-inverse" id="btnClear">Clear</button>
								</div>
								</div>
                            <hr>
                            </div>
							</form>
								</div>
							</div>
						</div>
                     </div>
					 
				
									
                <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Placed Orders List</h4>
                                
                                <div class="table-responsive">
                                    <table id="datatableServices" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                 <th>Sr. No</th>
                                                 <th>Code</th>
                                                 <th>Client Name</th>
												 <th>Area</th>
                                                 <th>Address</th>
												 <th>Mobile No</th>
												 <th>Order Status</th>
                                                 
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
				<div class="modal-dialog ">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">View Blog</h4>
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
	   dataTable();
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
		// $("#orderStatus option[value=PND]").hide();
		// $("#orderStatus option[value=CAN]").hide();
		// $("#orderStatus option[value=RJT]").hide();
	   
	    var cityCode,orderCode,orderStatus,pincode,ForListUrls="";
			ForListUrls= base_path+"order/getOrderUnavailableList";  
			dataTable(cityCode,orderCode,orderStatus,pincode,ForListUrls);
			
	    $('.btn-inverse').on('click', function (e){
			location.reload();
		});
	   
		 $('#btnSearch').on('click', function (e){
			orderCode=$('#orderCode').val();
			cityCode=$('#cityCode').val();
			orderStatus=$('#orderStatus').val();
			 
				dataTable(cityCode,orderCode,orderStatus,pincode,ForListUrls);	
			});	
			 
			   function dataTable(cityCode,orderCode,orderStatus,pincode,ForListUrls)
			   {
			   	$.fn.DataTable.ext.errMode = 'none';
				   if ($.fn.DataTable.isDataTable("#datatableServices")) {
					  $('#datatableServices').DataTable().clear().destroy();
					}
	     
	  $('#datatableServices').DataTable({  
			stateSave: true,
			"processing":true,  
           "serverSide":true,  
           "order":[],
		   "searching": false,
           "ajax":{  
                url:ForListUrls,  
                type:"GET",
				data:{
					'cityCode':cityCode,	
					  'orderCode':orderCode,
					  'orderStatus':orderStatus,
					  'pincode':pincode
					  
					  } , 
					
           complete: function(json) {
			   //console.log(json);
			   
			    $(function () {
				  $('[data-toggle="tooltip"]').tooltip() //for tooltip 
				})
				
				var status='';
				$(".orderStatus").click(function(){
					if ($("#radio1").prop("checked")) {
					   // do something
					}
				status=$(this).val();
				var getId=$(this).attr('id');
				
				// var getId=$(this).attr('name');
				//alert(getId);
				var shortStatus=status.substring(0,3);
				
				var string="";
				if(shortStatus=='SHP'){
				string="Shipped";	
				}
				else if(shortStatus=='DEL'){
				string="Delivered";	
				}
				else if(shortStatus=='RJT'){
					string="Rejected";	
				}
				
		swal({
				title: "Status Change",
				text: "Status Will Be Changed As "+string,
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Confirm",
				cancelButtonText: "Cancel",
				closeOnConfirm: !1,
				closeOnCancel: !1
			}, function(e) {
				//console.log(e);
				if(e)
				{
					$.ajax({
						url:'<?php echo site_url('Order/orderOperations'); ?>',
						method:"POST",
						data:{'status':status},
						datatype:"text",
						success: function(data) {
						//console.log(data);
						  if(data==1)
						  {
							swal({ 
								  title: "Completed",
								   text: "Successfully Order Status Changed",
									type: "success" 
								  },
								  function(isConfirm){
							  if (isConfirm) {
								 
								orderCode=$('#odCode').val();
								orderStatus=$('#orderStatus').val();
								pincode=$('#pincode').val();
								dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,ForListUrls);
								//toastr.success('Order Status','Order Status Successfully Changed',{"progressBar": true });
							 }
						
							});
							
						  }else{
							  //alert('else');
							
							swal('Order Status','Order Status Faield To Change','error');  
						  }
						  
						},
						error: function(xhr, ajaxOptions, thrownError) {
						   var errorMsg = 'Ajax request failed: ' + xhr.responseText;
						   alert(errorMsg);
						  // console.log("Ajax Request for patient data failed : " + errorMsg);
						}
					   });
				}
				else
				{
					//alert('else');
					$('#'+getId).prop('checked',false);
					swal("Cancelled", "Your Order Reject Request Failed", "error");
					rderCode=$('#odCode').val();
					orderStatus=$('#orderStatus').val();
					pincode=$('#pincode').val();
					fromDate=$('#fromDate').val();
					toDate=$('#toDate').val();
					cityCode=$('#cityCode').val();
				    dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,ForListUrls);	
				}
			});
					
		 });
			$(".blue").click(function(){
			 var code=$(this).data('seq');
			// alert(code)
			 $.ajax({
					url:'<?php echo site_url('Blog/view'); ?>',
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
									
			}
		   }
      });
	 }
	 
	 
			$('#btnClear').click(function () {
		    var orderCode,orderStatus,pincode,fromDate,toDate,ForListUrls="";
			 ForListUrls= base_path+"Order/getOrderList";  
			dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,ForListUrls);
			}); // End Clear Click
	   
   });
   	$( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  
	
         toastr.success(obj.message, 'Order', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Order', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
</script>
		

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.3/daterangepicker.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.3/daterangepicker.js"></script>  
<style>

.placeholded{
	margin-bottom:10px;
}
.daterangespace{
	margin-left:200px;
}
.placeholdedbutton{
	margin-top:30px;
	margin-bottom:30px;
	
}
</style>

  <!--============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 align-self-center">
                    <h4 class="page-title">Android Users</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Android Users</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- <div class="col-7 align-self-center">
                    <div class="d-flex no-block justify-content-end align-items-center">
                        <div class="m-r-10">
                            <div class="lastmonth"></div>
                        </div>
                        <div class=""><small>LAST MONTH</small>
                            <h4 class="text-info m-b-0 font-medium">$58,256</h4></div>
                    </div>
                </div> -->
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
                        <h4 class="card-title"> Android Users </h4>
						<hr/>
						 <form>
						<?php foreach($query->result() as $row)
						{
							if($row->isActive == "1")
							{
								$activeStatus='<span class="label label-sm label-success">Active</span>';
							}
							else 
							{
								$activeStatus='<span class="label label-sm label-warning">Inactive</span>';
							}
						?>

						<div class="form-row">
							<div class="col-md-2 mb-3">
								<label><b> Code:</b> </label>
								<input type="text" value="<?php echo $row->code?>" class="form-control-line" id="clientCode" readonly>
							</div>
							<div class="col-md-4 mb-3">
								<label><b> Client Name:</b> </label>
								<input type="text" value="<?php echo $row->name?>" class="form-control-line rpadding"  readonly>
							</div>	
							<div class="col-md-3 mb-3">
								<label><b> Mobile Number:</b></label>
								<input type="text" value="<?php echo $row->mobile?>" class="form-control-line rpadding"  readonly>
							</div>
							<div class="col-md-3 mb-3">
								<label><b>Email ID:</b> </label>
								<input type="text" value="<?php echo $row->emailId?>" class="form-control-line rpadding"  readonly>
							</div>
						</div>
 
						<div class="form-row">
												
						<?php 						
						foreach($queryprofile->result() as $profileRow)
						
                            {
                            ?>
						
						<div class="col-md-3 mb-3"><label><b> Local:</b> </label>
							<input type="text" value="<?php echo $profileRow->local?>" class="form-control-line rpadding"  readonly>
						</div>
						<div class="col-md-3 mb-3">
							<label><b> Flat :</b> </label>
							<input type="text" value="<?php echo $profileRow->flat?>" class="form-control-line rpadding"  readonly>
						</div>
						<div class="col-md-4 mb-3"><label><b> Landmark :</b> </label>
							<input type="text" value="<?php echo $profileRow->landMark?>" class="form-control-line rpadding"  readonly>
						</div>
						<div class="col-md-2 mb-3"><label> <b>City :</b> </label>
							<input type="text" class="form-control-line rpadding" value="<?php echo $cityname ?>"  readonly>
						</div> 
						<div class="col-md-2 mb-3"><label> <b>Pincode  :</b> </label>
							<input type="text" class="form-control-line rpadding" value="<?php echo $profileRow->pincode?>"  readonly>				
						</div> 
						<div class="col-md-2 mb-3"><label><b> State: </b></label>
							<input type="text" class="form-control-line rpadding" value="<?php echo $profileRow->state?>"  readonly>
						</div>     
						<div class="col-md-2 mb-3">
							<label><b> Status: </b></label>
							<div class="form-group"><?php echo $activeStatus?></div>
						</div>						
					</div>     
				<div class="form-group"></div>
							<?php } }?>
							
						</form>
                    </div>
                </div>								
                <!-- basic table -->
                <div class="row">
				
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
							  <h4 class="card-title placeholded"> Filter Order List:</h4>
								 <form class="form-horizontal ">
						            <div class="form-row daterangespace">
											
											<div class="col-md-4 demo">
												<span> <label>Date to filter records:</label> </span>
														<input type="text" id="config-demo" class="form-control placeholded" placeholder="Select Date">
													
											</div>
											<div class=" col-sm-3 text-center placeholdedbutton">
												<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-info waves-effect waves-light">Search</button>
												<button type="reset" id="btnClear" class="btn btn-dark waves-effect waves-light btn btn-inverse">Clear</button>
											</div></center>
										</div>
										
									</form>
									<hr>
                                <h4 class="card-title">Placed Orders List</h4>
                                
                                <div class="table-responsive">
                                    <table id="datatableOrder" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                 <th>Sr. No</th>
                                                 <th>Order Code</th>
                                                 <th>Payment Mode</th>
												 <th>Payment Status</th>
												 <th>Order Status</th>
												 <th>Address</th>
												 <th>Total Price</th>
                                                 <th>Product</th>
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
							<h4 class="modal-title">View Product List</h4>
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
			 var clientCode,startDate="",endDate="",flag="";
			 clientCode=$('#clientCode').val();

			 getDataTable(clientCode,startDate,endDate);
			 
			$('#orderDateRange').datepicker({
				toggleActive: true
				
			}); // End orderDateRange datepicker
			 $('.demo i').click(function() {
				  $(this).parent().find('input').click();
				});
        updateConfig();

				function updateConfig() {
					var start = moment().startOf('week');
					var end = moment().endOf('week');
					var options = {};
					options.startDate= start;
					options.endDate= end;
					options.opens = "center";
					options.ranges = {
					  'This Week': [moment().startOf('week'), moment().endOf('week')],
					  'This Month': [moment().startOf('month'), moment().endOf('month')],
					  'This Year': [moment().startOf('year'), moment().endOf('year')]
					};
					$('#config-demo').daterangepicker(options, function(start, end, label) { 
						startDate = start.format('YYYY-MM-DD'); 
						endDate = end.format('YYYY-MM-DD');
							//alert(startDate+" "+endDate);
							flag="1";
					});
				}
				
		$('#btnSearch').on('click', function (e){
			clientCode=$('#clientCode').val();

			 if(flag=="1")
			 {
				 getDataTable(clientCode,startDate,endDate);
			 }
			 else
			 {
				fromDate = $('#orderDateStart').val();
				toDate = $('#orderDateEnd').val();
				
				//startDate = moment(fromDate).format('YYYY-MM-DD'); 
				//endDate = moment(toDate).format('YYYY-MM-DD');
				
				startDate = moment(fromDate,'DD/MM/YYYY').format("YYYY-MM-DD");
				endDate = moment(toDate,'DD/MM/YYYY').format("YYYY-MM-DD");

				//alert(fromDate);
				getDataTable(clientCode,startDate,endDate);
			 }
			 flag="";
			 startDate="";
			 endDate="";
			
				
		}); // End Search Click
			
			
			function getDataTable(clientCode,startDate,endDate)
			{
				//alert(startDate+"- --"+ endDate);
				if ($.fn.DataTable.isDataTable("#datatableOrder")) {
						  $('#datatableOrder').DataTable().clear().destroy();
						}
				var dataTable = $('#datatableOrder').DataTable({  
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": false,
				   "ajax":{  
						url: base_path+"AndroidUsers/getOrderList",  
						type:"GET",
						data:{'clientCode':clientCode,
						'startDate':startDate,
						'endDate':endDate
						},

				 complete: function(json) {
					 console.log(json);
					 	$(".blue").click(function(){
									 var code=$(this).data('seq');
									 $.ajax({
											url: base_path+"AndroidUsers/productView",  
											method:"GET",
											data:{code:code},
											datatype:"text",
											success: function(data)
											{
												$(".modal-body").html(data);
											
											}
										});
									});
					
					 
					}
				   }
			  });
			}
		
  });
</script>


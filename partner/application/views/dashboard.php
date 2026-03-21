<!--============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper"> 
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-5 align-self-center">
					<h4 class="page-title">Dashboard</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="#">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page">Dashboard</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row">
						
				<div class="col-sm-12 col-lg-4">
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class="m-r-10 cc XRP text-white"></i></h1></div>
								<div>
									<h6 class="font-12 text-white m-b-5 op-7">Total Orders</h6>
									<h6 class="text-white font-medium m-b-0"><?php echo $totalOrders;?></h6>
								</div>
								<div class="ml-auto">
									<div class="crypto"></div>
								</div>
							</div>
							<div class="row text-center text-white m-t-30">
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $placeOrder;?></span>
									<span class="font-medium">Placed order</span>
								</div>
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $deliverdOrder;?></span>
									<span class="font-medium">Delivered order</span>
								</div>
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $rejectOrder;?></span>
									<span class="font-medium">Rejected order</span>
								</div>
							</div>
							<!-- <center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/Confirmorder';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>-->
							
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-lg-4"> 
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class="cc ETH text-white"></i></h1></div>
								<div>
									<h6 class="font-12 text-white m-b-5 op-7">Total Items</h6>
									<h6 class="text-white font-medium m-b-0"><?php echo $vendoritems;?></h6>
								</div>
								<div class="ml-auto">
									<div class="crypto"></div>
								</div>
							</div>
							<div class="row text-center text-white m-t-30">
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $custChoiceCat;?></span> 
									<span class="font-medium">choice</span>
								</div>
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $custAddonCat;?></span>
									<span class="font-medium">Add On's</span>
								</div>
								
							</div>
							  <!--<center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/Pendingorders';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>-->
						</div>
					</div>
				</div>
		
				<!-- crypto -->
				<div class="col-sm-12 col-lg-4">
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class="cc ETC text-white"></i></h1></div>
								<div>
									<h6 class="font-12 text-white m-b-5 op-7">Total Offers</h6>
									<h4 class="text-white font-medium m-b-0"><?= $vendoroffer ?></h4>
								</div>
								<div class="ml-auto">
									<div class="crypto"></div>
								</div>
							</div>
								
							<div class="row text-center text-white m-t-30" id="contractInfo">
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $vendorofferActive;?></span>
									<span class="font-medium">Active</span>
								</div>
								<div class="col-4">
									<span class="font-14 d-block"><?php echo $vendorofferInactive;?></span>
									<span class="font-medium">Inactive</span>
								</div>
							</div>
						        
							<!--<center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/Vendoritem/listRecords';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>-->	
							 
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
			
				<div class="col-sm-12 col-lg-4">
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class="cc ARDR text-white"></i></h1></div>
								<div>
									<h6 class="font-12 text-white m-b-5 op-7">Total Earnings</h6>
									<h4 class="text-white font-medium m-b-0"><?= $totalEarning ?></h4>
								</div>
								<div class="ml-auto">
									<div class="crypto"></div>
								</div>
							</div>
								
							<div class="row text-center text-white m-t-30" id="contractInfo">
							
							</div>
						        
							<!--<center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/Vendoritem/listRecords';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>-->	
							 
						</div>
					</div>
				</div>
				
				
				<!--<div class="col-sm-12 col-lg-3">
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class=" fas fa-object-ungroup text-white"></i></h1></div>
								<div>
								 
									<h6 class="font-12 text-white m-b-5 op-7">Today's purchase</h6>
									<h6 class="text-white font-medium m-b-0"></h6>
								</div>
								<div class="ml-auto">
									<div class="crypto">
										<h4 class="text-white font-medium m-b-0">
										<?php  
											if($purchaseAmt->total == '')
											{
												echo "0";
											}
											else
											{
												echo number_format($purchaseAmt->total);
											}
										?></h4>
									</div>
								</div>
							</div>
								
							<div class="row text-center text-white m-t-30" id="contractInfo">
								
							</div>
						        
							<center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/Inward/listRecords';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>	
							 
						</div>
					</div>
				</div>
				
				<div class="col-sm-12 col-lg-3">
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class="fas fa-map-marker-alt text-white"></i></h1></div>
								<div>
								 
									<h6 class="font-12 text-white m-b-5 op-7">Active Places</h6>
									
								</div>
								<div class="ml-auto">
									<div class="crypto"><h4 class="text-white font-medium m-b-0"><?php echo $activePlace;?></h4></div>
								</div>
							</div>
								
							<div class="row text-center text-white m-t-30" id="contractInfo">
								
							</div>
						        
							<center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/Address/listRecords';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>	
							 
						</div>
					</div>
				</div>
				
				
				<div class="col-sm-12 col-lg-3">
					<div class="card bg-dash">
						<div class="card-body">
							<div class="d-flex align-items-center">
								<div class="m-r-10">
									<h1 class="m-b-0"><i class="m-r-10 mdi mdi-account-multiple text-white"></i></h1></div>
								<div>
								 
									<h6 class="font-12 text-white m-b-5 op-7">Valueable Customer</h6>
									
								</div>
								<div class="ml-auto">
									<div class="crypto"><h4 class="text-white font-medium m-b-0"><?php echo $customer;?></h4></div>
								</div>
							</div>
								
							<div class="row text-center text-white m-t-30" id="contractInfo">
								
							</div>
						        
							<center class="mt-2 mb-2"> <a href="<?php echo base_url().'index.php/AndroidUsers/listRecords';?>">
								  <p class="" style="color:#49b5f3e3;margin-bottom:-18px;text-decoration: underline;font-weight:700">View Information</p>
							   </a></center>	
							 
						</div>
					</div>
				</div>
			</div>-->
			
		</div>
		</div>
		<!----------This Page JS-------------->
		 <script src="<?php echo base_url().'assets/admin/assets/libs/chartist/dist/chartist.min.js';?>"></script>
		 <script src="<?php echo base_url().'assets/admin/dist/js/pages/chartist/chartist-plugin-tooltip.js';?>"></script>
      
		<script>
	$(document).ready(function(){
		// loadTable();
		// function loadTable(){
			 // $.fn.DataTable.ext.errMode = 'none';
			// if ($.fn.DataTable.isDataTable("#datatableAdd")) {
				// $('#datatableAdd').DataTable().clear().destroy();
			// }
			// var dataTable = $('#datatableAdd').DataTable({  
				// "paging": false,
				// "processing":true,  
			   // "serverSide":true,  
			   // "order":[],
			   // "searching": false,
			   // "ajax":{  
					// url: base_path+"Admin/getInwardList",  
					// type:"GET" , 
					// "complete": function(response) {
				 
		 
					// }
				// }
			// });
		// }
				
				
				//product keyup to get old records
				$("#productCode").keyup(function(){
					if($(this).val().length > 3)
					{
						var current_prod = $(this).val();
						$.ajax({
							url:'<?php echo site_url('admin/getAllProductData'); ?>',
							method:"GET",
							data:{product:current_prod},
							datatype:"text",
							success: function(data)
							{
							 
							  $("#ppList").html(data);
							}
						});
					}
				});
				
				$("#btnSearch").click(function(){
					
					var prCode = $("#productCode").val();
					// alert(prName);
					getDatatable(prCode);
				});
				
				function getDatatable(p_prCode){
					 $.fn.DataTable.ext.errMode = 'none';
					if ($.fn.DataTable.isDataTable("#datatablePurchase")) {
						$('#datatablePurchase').DataTable().clear().destroy();
					}
					var dataTable = $('#datatablePurchase').DataTable({ 
						
						"processing":true,  
						"serverSide":true,  
						"order":[],
						"searching": false,
						"ajax":{  
							url: base_path+"Admin/getProductList",  
							type:"GET" ,
							data:{productCode:p_prCode},
							"complete": function(response) {
						 
				 
							}
						}
					});
				}
				
			});//Document Ready
		</script>
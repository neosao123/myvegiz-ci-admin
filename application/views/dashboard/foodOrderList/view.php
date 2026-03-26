<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-12 align-self-center">
                <h4 class="page-title">Order Details</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
 	
    <div class="container-fluid ">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Order Details</h4>
                    <hr/>
                    <!-- class="needs-validation" method="post" id="order" action=" base_url().'index.php/Pendingorders/confirm';" novalidate -->
                    <form>
    					 <?php  
    						foreach($query->result() as $row)
                            {
    						 ?>
                        <div class="form-row">
    						<div class="col-md-3 mb-3">
                                <label for="orderCode"> Order Code  : </label>
                                <input type="text" id="orderCode" name="orderCode" class="form-control-line" value="<?=$row->code?>" required readonly>
                                <input type="hidden" id="cityCode" name="cityCode" class="form-control-line" value="<?=$row->cityCode?>" required readonly>
    							<input type="hidden" id="areaCode" name="areaCode" class="form-control-line" value="<?=$row->areaCode?>" required readonly>
    						 </div>
    						<div class="col-md-3 mb-3">
    							<label for="clientCode"> Client Code  : </label>
    							<input type="text" id="clientCode" name="clientCode" value="<?=$row->clientCode?>" class="form-control-line" required readonly>
    						</div>
    						<div class="col-md-3 mb-3">
    							<label for="clientName"> Client Name  : </label>
    							<input type="text" id="clientName" name="clientName" value="<?=$row->name?>" class="form-control-line" required disabled>
    						</div>
    						<div class="col-md-3 mb-3">
    							<label for="phone"> Phone  : </label>
    							<input type="number" id="phone" name="phone" class="form-control-line" value="<?=$row->mobile?>" readonly>
    						 </div>						 
    					</div>
    					 <div class="form-row">
    						<div class="col-md-3 mb-3">
                                <label for="paymentStatus"> Payment Status  : </label>
    							<input type="hidden"  id="paymentStatus" name="paymentStatus" value="<?=$row->paymentStatus?>"/>
    							<input type="hidden"  id="deliveryBoyCode" name="deliveryBoyCode" value="<?=$row->deliveryBoyCode?>"/>
                                <select  id="paymentStatusl" name="paymentStatusl" class="form-control-line" value="<?=$row->paymentStatus?>" disabled>
                                <option value="">Select option</option>
    							<?php
    							foreach($paymentStatus->result() as $pay){
    							echo '<option value="'.$pay->statusSName.'">'.$pay->statusName.'</option>';
    							}?>
    							</select>
    							 <script>
    						   var paymentStatus='<?=$row->paymentStatus?>';
    						   $('#paymentStatusl').val(paymentStatus);
    						    </script>
    						 </div>
    						 <div class="col-md-3 mb-3">
                                <label for="orderStatus"> Order Status  : </label>
                                <select  id="orderStatus" name="orderStatus" class="form-control-line" disabled >
    							<option value="">Select option</option>
    							<?php
    							foreach($orderStatus->result() as $status){
    							echo '<option value="'.$status->statusSName.'">'.$status->statusName.'</option>';
    							}?>
    						   </select>
    						   <script>
    						   var orderStatus='<?=$row->orderStatus?>';
    						   $('#orderStatus').val(orderStatus);
    						   </script>
    						 </div>
    						 <div class="col-md-3 mb-3">
                                <label for="paymentmode"> Payment Mode  : </label>
                                <input type="text" id="paymentmode" name="paymentmode" class="form-control-line" value="<?=$row->paymentMode?>" readonly>
                                
    						 </div>
							 <div class="col-md-3 mb-3">
    							<label for="coupanCode"> Coupon Code  : </label>
    							<input type="text" id="coupanCode" name="coupanCode" class="form-control-line" value="<?= $row->coupanCode ?>" readonly>
    						 </div>
    					 </div>
    					 
    					<div class="form-row">
    						<!--<div class="col-md-3 mb-3">
    							<label for="address"> Order from City  : </label>
    							<input type="text" id="city" name="city" class="form-control-line" value="<?= $city?>" readonly>
    						</div>-->
    						
    						 <div class="col-md-2 mb-3 d-none">
    							<label for="discount"> Discount Amount : </label>
    							<input type="text" id="discount" name="discount" class="form-control-line" value="<?= $row->discount ?>" readonly>
    						 </div>
    						<div class="col-md-12 mb-3">
    							<label for="address"> Address  : </label>
    							<!--<input type="text" id="address" name="address" class="form-control-line" value="<?= preg_replace('/\s+/', '',$row->address)?>" readonly>-->
    							<input type="text" id="address" name="address" class="form-control-line" value="<?= $row->address ?>" readonly>
    						 </div>
    					</div>
    						
    					<?php
    								echo "<div class='text-danger text-center' id='error_message'>";
    								if (isset($error_message))
    								{
    								echo $error_message;
    							   }
    								echo "</div>";
    							?>
    							
									<label for="clientName" ><h2>Item List </h2> </label>		
    							  <div class="table-responsive">
                                        <table id="datatableOrderDetailsVendor" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
    												 <th>Item Code</th>
                                                     <th>Item Name</th> 
    												 <th>Item Price</th>
    												 <th>Quantity </th> 
    												 <th>Total Price</th> 
                                                 </tr>
                                            </thead>
                                        </table>
                                    </div>
    								 <div class="form-row mt-2">
										<div class="float-left" style="width:50%;">
											<?php if($row->orderStatus !='RJT' && $row->orderStatus !='CAN'){?>
												<div class="col-md-12 mt-3 text-center">
													<label for="transferDeliveryBoy"><h3>Delivery Boy:<b style="color:black">(<?=$row->username?>)</b></h3></label><i class="fas fa-sync" id="pendingDBGet" style="font-size:20px" title="click here to get current available delivery boy"></i>
													<?php if($row->orderStatus !='DEL'){?>
													<select class="custom-select form-control" id="transferDeliveryBoy" name="transferDeliveryBoy" required>
														 <option value="" readonly>Select another delivery boy</option>
													</select>
													<?php } ?>
													<input type="hidden" class="form-control" id="deliveryBoyCode" name="deliveryBoyCode" value="<?= $row->deliveryBoyCode ?>">
												</div>
												<?php if($row->orderStatus !='DEL'){?>
												<div class="col-md-12 mt-5">							
													<div class="custom-control custom-checkbox">
													<input type="checkbox" value="1" class="custom-control-input" style="width:40px;height:40px;" id="isExpired" name="isExpired" <?php if($row->isExpired == 1) { echo "checked";} ?>>
													<label class="custom-control-label" for="isExpired">Order Expire</label>
													</div>
												</div>
											<?php } ?>
											<?php } ?>
										</div>
										<div class="float-right" style="width:50%;">
											<div class="col-md-6 offset-md-6">
												<b style="width:100%"><label>Item Total: </label> <span class="float-right"><?= number_format($row->subTotal+$row->discount, 2, '.', '') ?></span></b>
											</div>
											<div class="col-md-6 offset-md-6">
												<b style="width:100%"><label>Discount (-): </label><span class="float-right"><?= number_format($row->discount, 2, '.', '') ?></span></b>
											</div>
											<div class="col-md-6 offset-md-6">
												<div style="border-bottom:2px dashed;margin:10px 0"></div>
											</div>
											<div class="col-md-6 offset-md-6">
												<b style="width:100%"><label>Sub Total: </label><span class="float-right"><?= number_format($row->subTotal, 2, '.', '') ?></span></b>
											</div>
											<div class="col-md-6 offset-md-6">
												<b style="width:100%"><label>Tax (+): </label><span class="float-right"><?= number_format($row->tax, 2, '.', '') ?></span></b>
											</div>
											<div class="col-md-6 offset-md-6">
												<b style="width:100%;"><label>Packaging Charges (+): </label><span class="float-right"><?= number_format($row->totalPackgingCharges, 2, '.', '') ?></span></b>
											</div>
											<div class="col-md-6 offset-md-6">
												<b style="width:100%;"><label>Shipping Charges (+): </label><span class="float-right"><?= number_format($row->shippingCharges, 2, '.', '') ?></span></b>
											</div>
											<div class="col-md-6 offset-md-6">
												<div style="border-bottom:2px dashed;margin:10px 0"></div>
											</div>
											<div class="col-md-6 offset-md-6">
												<b style="width:100%;"><label>Grand Total: </label><span class="float-right"><?= number_format($row->grandTotal, 2, '.', '') ?></span></b>
											</div>
										</div>
    								</div>
    					<?php }?>
                    </form>
    				 
                </div>
            </div>
        </div>
        <div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Order Status</h4>
                    <hr/>
                    <div class="table-responsive">
                        <table id="datatable_orderStatus" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Sr.No.</th>
									<th>Status</th>
                                    <th>Date-Time</th> 
									<th>Status By</th>
									<th>Reason</th> 
                                 </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script> 
	$( document ).ready(function() {
		loadTable(); 
		getPendingDeliveryBoy();
		function loadTable()
		{
		    if ($.fn.DataTable.isDataTable("#datatableOrderDetailsVendor")) {
		        $('#datatableOrderDetailsVendor').DataTable().clear().destroy();
		    }
		    var orderCode=$('#orderCode').val();	 
	        var dataTable = $('#datatableOrderDetailsVendor').DataTable({  
    			"processing":true,  
                "serverSide":true,  
                "order":[],
    		    "searching": false,
                "ajax":{  
                    url: base_path+"foodOrderList/FoodOrderList/getOrderDetails",  
                    type:"POST" , 
    				data:{'orderCode':orderCode},
                    "complete": function(response) {
						
    		        }
		        }
            });
            
            if ($.fn.DataTable.isDataTable("#datatable_orderStatus")) {
		        $('#datatable_orderStatus').DataTable().clear().destroy();
		    }
		    var orderCode=$('#orderCode').val();	 
	        var dataTable = $('#datatable_orderStatus').DataTable({  
    			"processing":true,  
                "serverSide":true,  
                "order":[],
    		    "searching": false,
                "ajax":{  
                    url: base_path+"foodOrderList/FoodOrderList/getOrderStatusList",  
                    type:"POST" , 
    				data:{'orderCode':orderCode},
                    "complete": function(response) {              
    		        }
		        }
            });
	    }
	    
	   $("#pendingDBGet").on("click",function() {
	       $(this).addClass("fa-spin");
		   var deliveryBoyCode = $('#deliveryBoyCode').val();
		   var cityCode = $('#cityCode').val();
			 $.ajax({
				url:base_path+"foodOrderList/FoodOrderList/getPendingDeliveryBoys",
				method:"POST",
				data:{
					'deliveryBoyCode':deliveryBoyCode,
					'cityCode':cityCode,
				},
				datatype:"text",
				success: function(data)
				{
					if(data){
						$("#pendingDBGet").removeClass("fa-spin");
						$("#transferDeliveryBoy").html(data);
					}
					else
					{
						 $("#pendingDBGet").removeClass("fa-spin");
						 swal({
						  title: "warning!",
						  text: "no delivery boy found!",
						  type: "warning",
						  button: "ok",
						});
					}
				}
			});
	   });
	   
	   $('#transferDeliveryBoy').on('change', function() {
          //alert( this.value );
          var fromDeliveryBoy=$('#deliveryBoyCode').val();	
           var toDeliveryBoy=$(this).val();
		   if(toDeliveryBoy=="")return false;
          var orderCode=$('#orderCode').val();
          var orderStatus=$('#orderStatus').val();
          var orderType="food";
          swal({
				title: "Confirmation",
				text: "Do you want to transfer this order to another delivery boy?",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "yes",   
				closeOnConfirm: false 
			},
			function(isConfirm) {
				if (isConfirm) {
					//alert(fromDeliveryBoy+" "+toDeliveryBoy+" "+orderCode+" "+orderStatus+" "+orderType);
					
					 $.ajax({
						url:base_path+"foodOrderList/FoodOrderList/transferOrder",
						method:"POST",
						data:{
							fromDeliveryBoy,toDeliveryBoy,orderCode,orderStatus,orderType
						},
						datatype:"text",
						success: function(data)
						{
							console.log(data);
							var obj=JSON.parse(data);
							if(obj.status)
							{
								swal("Transfered!", obj.message, "success"); 
							}
							else
							{
								swal("Transfered!", obj.message, "error"); 
							}
						}
					});
				}
			});					
        });


   }); // End Ready
	function getPendingDeliveryBoy(){
		//debugger
		var deliveryBoyCode = $('#deliveryBoyCode').val();
		var cityCode = $('#cityCode').val();
			$.ajax({
				url:base_path+"foodOrderList/FoodOrderList/getPendingDeliveryBoys",
				method:"POST",
				data:{
					'deliveryBoyCode':deliveryBoyCode,
					'cityCode':cityCode,
				},
				datatype:"text",
				success: function(data){
					//debugger;
					if(data){
						$("#transferDeliveryBoy").html(data);
					}else{
					}
				}
			});
	}
   
   // Expired By Admin Swal
		$('#isExpired').on('click', function() {         
        
		var isExpired = $("#isExpired").val();
		
		if($("#isExpired").prop('checked') == true)
		{
		
          var isExpired=$(this).val();
		  var orderCode=$('#orderCode').val();
          var orderStatus=$('#orderStatus').val();
         
			$.ajax({
				url: "<?php echo site_url('foodOrderList/FoodOrderList/checkDeliveryBoyOrders'); ?>",
				method: "POST",
				data: {
					"code": orderCode
				},
				datatype: "text",
				success: function(data) {
					console.log(data);
					var obj1=JSON.parse(data);
					if (obj1.status) {
						    swal({
								title: "Are you sure?",
								text: "The Delivery Boy has Orders, Click OK to expire this order.",
								type: "warning",
								button:"Ok",
								showCancelButton: !0,
								closeOnConfirm: false 
							 }, function(isConfirm) {
									if (isConfirm) {
										expireOrder(isExpired,orderCode,orderStatus,obj1.dbCode); 
									}
									else
									{
										$('#isExpired').prop('checked',false);	
									}
								}
							);
        				
					}
					else
					{
						 swal({
								title: "Confirmation",
								text: "Do you want to Make This Action?",
								type: "warning",
								showCancelButton: true,
								confirmButtonColor: "#DD6B55",   
								confirmButtonText: "yes",   
								closeOnConfirm: false 
							},
							function(isConfirm) {
								if (isConfirm) {
									expireOrder(isExpired,orderCode,orderStatus,""); 
								}
								else
								{
									$('#isExpired').prop('checked',false);	
								}
							});
					}
					
				}
			});
	
         }				
 								
        });

   
   // Expired By Admin
   function expireOrder(isExpired,orderCode,orderStatus,dbCode) 
{
	
	$.ajax({
		url:base_path+"foodOrderList/FoodOrderList/expiredByAdmin",
		method:"POST",
		data:{
			isExpired,orderCode,orderStatus,dbCode
		},
		datatype:"text",
		success: function(data)
		{
			//console.log(data);
			var obj=JSON.parse(data);
			if(obj.status)
			{
				swal("Assigned!", obj.message, "success"); 
				window.location.href = base_path + "foodOrderList/FoodOrderList/";
			}
			else
			{
				swal("Assigned!", obj.message, "error"); 
			}
		}
	});					
}
</script>
	 
	 
 
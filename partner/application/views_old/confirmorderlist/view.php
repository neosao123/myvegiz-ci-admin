 <?php echo '<input type="text" name="placeFlag" id="placeFlag" value="'.$placeFlag.'">';
  ?>
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
 	
    <div class="container-fluid col-md-12">
    
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Order Details</h4>
                <hr/>
				
                <form class="needs-validation" method="post" id="order" action="<?php echo base_url().'index.php/Confirmorder/confirm';?>" novalidate>
					 <?php // print_r($query->result());
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
                            <select  id="orderStatus" name="orderStatus" class="form-control-line">
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
					 </div>
					 
					<div class="form-row">
						<!--<div class="col-md-3 mb-3">
							<label for="address"> Order from City  : </label>
							<input type="text" id="city" name="city" class="form-control-line" value="<?= $city?>" readonly>
						</div>-->
						<div class="col-md-3 mb-3">
							<label for="coupanCode"> Coupon Code  : </label>
							<input type="text" id="coupanCode" name="coupanCode" class="form-control-line" value="<?= $row->coupanCode ?>" readonly>
						 </div>
						 <div class="col-md-2 mb-3">
							<label for="discount"> Discount Amount : </label>
							<input type="text" id="discount" name="discount" class="form-control-line" value="<?= $row->discount ?>" readonly>
						 </div>
						<div class="col-md-7 mb-3">
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
												 <!--<th>Weight</th> 												
												 <th>Product Uom </th>--> 
												 <th>Item Price</th>
												 <th>Quantity </th> 
												 <th>Total Price</th> 
                                             </tr>
                                        </thead>
                                    </table>
                                </div>
								<div class="form-row ">
									<div class="col-md-3 mt-3 text-center">
										<label><h4>Sub Total :</h4></label><label><h4><i>&nbsp;<?= $row->subTotal ?></i></h4></label>
									</div>
									<div class="col-md-3 mt-3 text-center">
										<label><h4>Discount(%) :</h4></label><label><h4><i>&nbsp;<?= $discountInPercent ?></i></h4></label>
									</div>
									<div class="col-md-3 mt-3 text-center">
										<label><h4>Tax :</h4></label><label><h4><i>&nbsp;<?= $row->tax ?></i></h4></label>
									</div>
									<div class="col-md-3 mt-3 text-center">
										<label><h4>Shipping Charges :</h4></label><label><h4><i>&nbsp;<?= $row->shippingCharges=="" ? '0' : $row->shippingCharges?></i></h4></label>
									</div>
								</div>
								<div class="col-sm-4 offset-sm-8 mt-1">
									<h4 class="border p-2">Grand Total :<span class="float-right"><?= $row->grandTotal?></span></h4>
								</div>
					<?php }?>
                    <div class="text-xs-right">
						 <?php 
						 if($row->orderStatus=='PRE'){
								echo '<button data-id="'.$row->code.'" data-action="Update order to ready for pickup?" data-status="RFP" class="actionBtn waves-effect waves-light btn btn-success mr-1">Ready For Pickup?</button>';
							} else if($row->orderStatus=='RCH') {
								echo '<button data-id="'.$row->code.'" data-action="Update order to ready for pickup?" data-status="RFP" class="actionBtn waves-effect waves-light btn btn-success mr-1">Ready For Pickup?</button>';
							} else if($row->orderStatus=='PUP'){
								echo '<div class="btn btn-success">On the Way</div>';
							}else if($row->orderStatus=='DEL'){
								echo '<div class="btn btn-success">Delivered</div>';
							}
							else{
								echo '<div class="btn btn-success">Waiting for delivery boy pickup...</div>';
							}
						 ?>
						<a href="<?= base_url().'index.php/Confirmorder'?>"></a><button class="btn btn-reset">Back</button>
						<!--<button type="button" id="discard" class="btn btn-reset" onclick="page_isPostBack=true;">Reject</button>
						<button type="button" id="revoke" class="btn btn-primary" onclick="page_isPostBack=true;">Revoke</button>-->
					</div>
					
                </form>
				 
            </div>
        </div>

    </div>
</div>
	<script>
	 
	
		 $( document ).ready(function() {
			 // $("#orderStatus").attr("disabled",true); 
			$("#orderStatus option[value=CAN]").hide();
			$("#orderStatus option[value=DEL]").hide();
			$("#orderStatus option[value=PLC]").hide();
			$("#orderStatus option[value=RJT]").hide();
			//$("#orderStatus option[value=PUP]").hide();
			$("#orderStatus option[value=PND]").hide();
			$("#orderStatus option[value=PUP]").text('On the Way');
			
			  var orderStatus=$("#orderStatus").val(); 
				$("#revoke").hide(); 
			 
			 var placeFlag=$('#placeFlag').val();
			 var orderCode='';
			 var url='<?php echo base_url().'Order/shipped'?>';
			 if(placeFlag==1){
				 $("#discard").text("Reject Order"); 
				 $("#submit").text("Submit"); 
				 $("#order").attr("action",url); 
				 $("#orderStatus").prop("disabled",false); 
				 $("#orderStatus").attr("class","form-control"); 
				 $("#paymentStatus").attr("disabled",false); 
				 $("#paymentStatus").attr("class","form-control"); 
				 $("#orderStatus").attr("class","form-control"); 
			 }
			  if(orderStatus=='RJT'){
				$("#submit").hide(); 
				$("#discard").hide(); 
				$("#revoke").show(); 
			 }
			 else if(orderStatus=='CAN'){
				$("#submit").hide(); 
				$("#discard").hide();
				$("#revoke").hide(); 
			 }else if(orderStatus=='FRE'){
				$("#submit").hide(); 
				$("#discard").hide(); 
				$("#revoke").show(); 
			 }
			 
						 
			loadTable();
			   
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
                url: base_path+"index.php/Confirmorder/getOrderDetails",  
                type:"GET" , 
				data:{'orderCode':orderCode},
           "complete": function(response) {
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
	   
   }); // End Ready
		
		// Page Leave Yes / No
			
		var page_isPostBack = false;
		
		function windowOnBeforeUnload()
		{
			if ( page_isPostBack == true )
				return; // Let the page unload

			if ( window.event )
				window.event.returnValue = 'Are you sure?'; 
			else
				return 'Are you sure?'; 
		}
	
		window.onbeforeunload = windowOnBeforeUnload;
		
		// End Page Leave Yes / No
		
		setTimeout(function()
		{
			$('#error_message').hide('fast');
			
		},4000); // End Set Time Function

		
		$('#discard').click(function() {
			// debugger;
			orderCode=$('#orderCode').val();
			placeFlag=$('#placeFlag').val();

			swal({
				title: "Are you sure?",
				text: "Order Will Be Rejected..",
				type: "warning",
				showCancelButton: !0,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Reject",
				cancelButtonText: "Cancel",
				closeOnConfirm: !1,
				closeOnCancel: !1
			}, function(e) {
				if(e)
				{
					$.ajax({
						url: base_path+"index.php/Confirmorder/reject",
						type: 'POST',
						data:{'code':orderCode},
						success: function(data) {
							if(data!='false')
							{
								swal({ 
									title: "Completed",
									text: "Successfully Rejected Order",
									type: "success" 
								},
								function(isConfirm){
									if (isConfirm) {
										if(placeFlag==1){
											window.location.href = "<?= base_url().'index.php/Confirmorder'?>";
										}else{
											window.location.href = "<?= base_url().'index.php/Confirmorder'?>";
										}
									}
								});
							}else{
								toastr.error('Something Went Wrong', 'Order', { "progressBar": true });
							}
						},
						error: function(xhr, ajaxOptions, thrownError) {
							var errorMsg = 'Ajax request failed: ' + xhr.responseText;
							alert(errorMsg);
						}
					});
				}
				else
				{
					swal("Cancelled", "Your Order Reject Request Failed", "error");
				}
			});
		});
		
	 $("body").on("click", ".actionBtn", function(e) {
		var orderCode = $(this).data("id");
		var orderStatus = $(this).data("status");
		var dataAction = $(this).data("action");
		swal({
			title: "Are you sure?",
			text: dataAction, 
			type: "warning",
			showCancelButton: !0,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Ok",
			cancelButtonText: "Cancel",
			closeOnConfirm: !1,
			closeOnCancel: !1
		}, function(e) {
			if (e) {
				$.ajax({
					url: base_path + "Home/updateOrderStatus",
					type: 'POST',
					data: {
						'orderCode': orderCode,
						'orderStatus':orderStatus
					},
					success: function(response) {
						var res = JSON.parse(response); 
						if (res.status) {
							swal({
									title: "Order",
									text: res.message,
									type: "success"
								},
								function(isConfirm) {
									if (isConfirm) { 
                                       window.location.reload();
									}
								});
						} else {
							toastr.error(res.message, 'Order', {
								"progressBar": true
							});
							window.location.reload();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						var errorMsg = 'Ajax request failed: ' + xhr.responseText;
						alert(errorMsg);
					}
				});
			} else {
				swal("Cancelled", "Failed to update the order...", "error");
			}
		});
	});
	</script>
	
	<script>
	
	  // Example starter JavaScript for disabling form submissions if there are invalid fields
		/*(function() {
			'use strict';
			window.addEventListener('load', function() {
				// Fetch all the forms we want to apply custom Bootstrap validation styles to
				var forms = document.getElementsByClassName('needs-validation');
				// Loop over them and prevent submission
				var validation = Array.prototype.filter.call(forms, function(form) {
					form.addEventListener('submit', function(event) {
						if (form.checkValidity() === false) {
							event.preventDefault();
							event.stopPropagation();
						}
						form.classList.add('was-validated');
					}, false);
				});
			}, false);
		})();*/
		
    </script>
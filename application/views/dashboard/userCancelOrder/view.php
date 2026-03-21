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
				
                <form class="needs-validation" method="post" id="order" action="<?php echo base_url().'Order/confirm';?>" novalidate>
					 <?php // print_r($query->result());
						foreach($query->result() as $row)
                      {
						 ?>
                    <div class="form-row">
						<div class="col-md-4 mb-3">
                            <label for="orderCode"> Order Code  : </label>
                            <input type="text" id="orderCode" name="orderCode" class="form-control-line" value="<?=$row->code?>" required readonly>
                            <input type="hidden" id="cityCode" name="cityCode" class="form-control-line" value="<?=$row->cityCode?>" required readonly>
							<input type="hidden" id="areaCode" name="areaCode" class="form-control-line" value="<?=$row->areaCode?>" required readonly>
						 </div>
						    <div class="col-md-3 mb-3">
								<label for="clientCode"> Client Code  : </label>
								<input type="text" id="clientCode" name="clientCode" value="<?=$row->clientCode?>" class="form-control-line" required readonly>
							</div>
							
							<div class="col-md-4 mb-3">
								<label for="clientName"> Client Name  : </label>
								<input type="text" id="clientName" name="clientName" value="<?=$clientName?>" class="form-control-line" required disabled>
							</div>
						 
					</div>
					 <div class="form-row">
						<div class="col-md-3 mb-3">
                            <label for="paymentStatus"> Payment Status  : </label>
							<input type="hidden"  id="paymentStatus" name="paymentStatus" value="<?=$row->paymentStatus?>"/>
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
                            <input type="text" id="paymentmode" name="paymentmode" class="form-control-line" value="<?=$row->paymentmode?>" readonly>
                            
						 </div>
						 <div class="col-md-3 mb-3">
                            <label for="phone"> Phone  : </label>
                            <input type="number" id="phone" name="phone" class="form-control-line" value="<?=$row->phone?>" readonly>
                         </div>
					 </div>
					 
					<div class="form-row">
						<div class="col-md-3 mb-3">
							<label for="address"> Order from City  : </label>
							<input type="text" id="city" name="city" class="form-control-line" value="<?= $city?>" readonly>
						</div>
						<div class="col-md-9 mb-3">
							<label for="address"> Address  : </label>
							<input type="text" id="address" name="address" class="form-control-line" value="<?= preg_replace('/\s+/', '',$row->address)?>" readonly>
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
							
					<label for="clientName" ><h2>Product List </h2> </label>		
							  <div class="table-responsive">
                                    <table id="datatableOrderDetails" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
												 <th> Product Code</th>
                                                 <th>Product Name</th> 
												 <th>Weight</th> 												
												 <th>Product Uom </th> 
												 <th>Product Price</th>
												 <th>Quantity </th> 
												 <th>Total Price</th> 
												 <!-- <th>Action</th> -->
                                             </tr>
                                        </thead>
                                    </table>
                                </div>
								 <div class="form-row ">
									<div class="col-md-4 mt-3 " >
									<?php
									$totalamount= $row->totalPrice-$row->shippingCharges;
									?>
									<label><h4>Actual Price:</h4></label><label><h4><i>&nbsp;<?= $totalamount?></i></h4></label>
									</div>
									<div class="col-md-4 mt-3 " >
									
									<label><h4>Shipping Cahrges:</h4></label><label><h4><i>&nbsp;<?= $row->shippingCharges=="" ? '0' : $row->shippingCharges?></i></h4></label>
									</div>
									<div class="col-md-4 mt-3 " >
									
									<label><h4>Total Price:</h4></label><label><h4><i>&nbsp;<?= $row->totalPrice?></i></h4></label>
									</div>
											
								</div>
								<div class="form-row">
									<div class="col-md-4 mb-3">
										<label for="select_boy">Delivery Boy</label>
										<select class="form-control" id="select_boy" name="delbCode" required>
											<option value="">Select delivery boy</option>
											<?php
												if($dBoyList){
													foreach ($dBoyList as $row2)
													{
														echo '<option value="' . $row2['code'] . '">' . $row2['firstName'] . ' ' . $row2['lastName'] . '</option>';
													}
												}
											?>
										</select>
									</div>
								</div>
					
                  
					<?php }?>
                    <div class="text-xs-right">
						
						 <button type="submit" id="submit" class="btn btn-success" onclick="page_isPostBack=true;">Confirm</button>
						 <button type="button" id="discard" class="btn btn-reset" onclick="page_isPostBack=true;">Discard</button>
						 <button type="button" id="revoke" class="btn btn-primary" onclick="page_isPostBack=true;">Revoke</button>
					</div>
					
                </form>
				 
            </div>
        </div>

    </div>
</div>
	<script>
	 
	
		 $( document ).ready(function() {
			 $("#orderStatus").attr("disabled",true); 
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
			 }
			 
						 
			loadTable();
			   
			   function loadTable()
			   {
				   if ($.fn.DataTable.isDataTable("#datatableOrderDetails")) {
					  $('#datatableOrderDetails').DataTable().clear().destroy();
					}
			var orderCode=$('#orderCode').val();		
	   
	   var dataTable = $('#datatableOrderDetails').DataTable({  
			"processing":true,  
           "serverSide":true,  
           "order":[],
		   "searching": false,
           "ajax":{  
                url: base_path+"Order/getOrderDetails",  
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
				
				 orderCode=$('#orderCode').val();
				 placeFlag=$('#placeFlag').val();
				 
				 swal({
						title: "Are you sure?",
						text: "Order Will Be Cancelled..",
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Reject",
						cancelButtonText: "Cancel",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						//console.log(e);
						if(e)
						{
							$.ajax({
								url: base_path+"Order/delete",
								type: 'POST',
								data:{
								 'code':orderCode
								},
								success: function(data) {
								// console.log(data);
								  if(data!='false')
								  {
									swal({ 
										  title: "Completed",
										   text: "Successfully Rejected Order",
											type: "success" 
										  },
										  function(isConfirm){
									  if (isConfirm) {
										//  alert(placeFlag);
										
										if(placeFlag==1){
										window.location.href = "<?= base_url().'Order/placedListRecords'?>";
										}else{
										window.location.href = "<?= base_url().'Order/pendingListRecords'?>";
										}
									 }
								
									});
									
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
							swal("Cancelled", "Your Order Reject Request Failed", "error");
						}
					});
				 
			 });	
			 
			  $('#revoke').click(function() {
				
				 orderCode=$('#orderCode').val();
				 //alert(orderCode);
				 placeFlag=$('#placeFlag').val();
				 
				 swal({
						title: "Revoke",
						text: "Are You Sure Revoke This Entry.",
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Revoke",
						cancelButtonText: "Cancel",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						//console.log(e);
						if(e)
						{
							$.ajax({
								url: base_path+"Order/revoke",
								type: 'POST',
								data:{
								 'orderCode':orderCode
								},
								success: function(data) {
								//console.log(data);
								  if(data!='false')
								  {
									swal({ 
										  title: "Completed",
										   text: "Successfully  Revoked Order",
											type: "success" 
										  },
										  function(isConfirm){
									  if (isConfirm) {
										  //alert(placeFlag);
										
										location.reload();
									 }
								
									});
									
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
							swal("Cancelled", "Your Order Remains Same", "error");
						}
					});
				 
			 });
	</script>
	
	<script>
	
	  // Example starter JavaScript for disabling form submissions if there are invalid fields
		(function() {
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
		})();
		
    </script>








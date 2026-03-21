<div class="page-wrapper">        
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Placed Orders List</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Placed List</li>
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
	<div class="container-fluid">
		 <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h3 class="card-title"> Filter :</h3>
						<hr>
						<form>
							<div class="form-row">
							
	
										<div class="col-sm-3 mb-3">
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
								
								
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Order Status :</label> </span>
										<select class="form-control" id="orderStatus" name="orderStatus">
										<option value="">Select Option</option>
											<?php
											foreach($orderStatus->result() as $status){
												if(strtoupper($status->statusName) != 'SHIPPED') {
												echo '<option value="'.$status->statusSName.'">'.$status->statusName.'</option>';}
											}?>
										</select>
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
								
								<!--<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="pincode">Pincode :</label> </span>
										<input type="text" onkeypress="return validateFloatKeyPress(this, event, 5, -1);" class="form-control" list="" id="pincode" name="pincode">

									</div>
								</div>-->

								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Area:</label> </span>
										<input type="text" name="areaCode" id="areaCode" class="form-control" list="areaCodeList">
										<datalist id="areaCodeList">
											<?php
											if($address){
											foreach($address->result() as $addr){
											echo '<option value="'.$addr->code.'">'.$addr->place.'</option>';
											} }?>
										</datalist>
									</div>
								</div>

								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Delivery Boy</label> </span>
										<input type="text" name="deliveryCode" id="deliveryCode" class="form-control" list="deliveryCodeList">
										<datalist id="deliveryCodeList">
											<?php
											if($user){
											foreach($user->result() as $user){
											echo '<option value="'.$user->code.'">'.$user->name.'</option>';
											}}?>
										</datalist>
									</div>
								</div>
								<?php	
									$todayDate = date('d/m/Y');
									$previousDate = date('d/m/Y', strtotime('- 7 days'));
								?>
								<div class="col-sm-5">
									<div class="input-daterange input-group">
										<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control date-inputmask col-sm-5" name="start" id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>"/>
											<div class="input-group-append">
												<span class="input-group-text bg-myvegiz b-0 text-white">TO</span>
											</div>
											<input type="text" class="form-control date-inputmask toDate" name="end" id="toDate" placeholder="dd/mm/yyyy" value="<?= $todayDate ?>"/>
										</div>
									</div>
								</div>
								<div class="card-body">
									<div class="form-group  text-center">
										<button type="button" id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
										<button type="Reset" class="btn btn-dark waves-effect waves-light btn btn-inverse" id="btnClear">Clear</button>
									</div>
								</div>
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
							<table id="datatablePlaced" class="table table-striped table-bordered">
								<thead>
									<tr>
										 <th>Sr. No</th>
										 <th>Code</th>
										 <th>Client Name</th>
										 <th>Area</th>
										 <th>Address</th>
										 <th>Mobile No</th>
										 <th>Order Status</th>
										 <th>Amount</th>
										 <th>Delivery Boy</th>
										 <th>Date</th>
										 <th>Operations</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="col-sm-4 offset-sm-8 mt-1">
							<h4 class="border p-2">Total - <span id="total" class="float-right">0.00</span></h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
	<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Transfer Delivery</h4>
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
<script>
   $( document ).ready(function() {
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
		
		$("#orderStatus option[value=PND]").hide();
		$("#orderStatus option[value=CAN]").hide(); 
		$("#orderStatus option[value=REL]").hide(); 
		   
		var cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode="";
		 
		fromDate = "<?= $previousDate ?>";
		toDate = "<?= $todayDate ?>";
		dataTable("","","","",fromDate,toDate,"","");
		getTotalAmount(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,"","");
		$('#btnClear').click(function () {
			location.reload();
		}); // End Clear Click
			
		function transfer(){
			$('#transfer_change').click(function () {
				//alert('aaaaaaaaaaa');
				var slt = $("#select_boy").val();
				var order_code =$("#order_code").val();
				var areaCode =$("#areaCode").val();
				//alert(slt);
				if(slt===''){
					alert('please select delivery boy');
				}else{ 
					$.ajax({
						url: base_path+"Order/delivery_update",  
						method:"POST",
						data:{'slt':slt,'order_code':order_code,'areaCode':areaCode},
						datatype:"text",
						success: function(data)
						{
							$("#responsive-modal").modal("hide");
							var obj=JSON.parse(data);
							if(obj.status)
							{
								toastr.success(obj.message, 'Transfer', { "progressBar": true });
								dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode);
								getTotalAmount(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode);
							}
							else
							{
								toastr.error(obj.message, 'Transfer', { "progressBar": true });
							}
						}
					});
				}
			});	
		}			
		function getTotalAmount(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode){       
		   $.ajax({
				url:'<?php echo site_url('Order/placedTotal'); ?>',
				method:"get",
				data:{
					'placeList':'1',
					'cityCode':cityCode,
					'orderCode':orderCode,
					'orderStatus':orderStatus,
					'pincode':pincode,
					'fromDate':fromDate,
					'toDate':toDate,
					'areaCode':areaCode,
					'deliveryCode':deliveryCode
				},
				datatype:"text",
				success: function(data) {
					  //  $("#total").text(data) ;
				}
			});
		}
		$('#btnSearch').on('click', function (e){
			orderCode=$('#orderCode').val();
			cityCode = $('#cityCode').val();
			orderStatus=$('#orderStatus').val();
			pincode=$('#pincode').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
			areaCode=$('#areaCode').val();
			deliveryCode=$('#deliveryCode').val(); 
			dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode);	
			getTotalAmount(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode);
		});	
			 
		function dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate,areaCode,deliveryCode)
		{
			$("#total").text("0.00");
			$.fn.DataTable.ext.errMode = 'none';
			if ($.fn.DataTable.isDataTable("#datatablePlaced")) $('#datatablePlaced').DataTable().clear().destroy();
		 
			$('#datatablePlaced').DataTable({  
				//stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"Order/getPlacedOrders",  
					type:"GET",
					data:{	
						'placeList':'1',
						'cityCode':cityCode,
						'orderCode':orderCode,
						'orderStatus':orderStatus,
						'pincode':pincode,
						'fromDate':fromDate,
						'toDate':toDate,
						'areaCode':areaCode,
						'deliveryCode':deliveryCode
					}, 
					complete: function(json) 
					{		 
					//console.log('test1');					
					
					
						$('#total').text((json.responseJSON.total));
						$(".transfer").click(function(){
							var code=$(this).data('seq');
							//alert(code);
							$.ajax({
								url: base_path+"Order/transfer",  
								method:"GET",
								data:{code:code},
								datatype:"text",
								success: function(data)
								{
									$(".modal-body").html(data);
								},
								complete:function(data){
									transfer();
								}
							});
						});
			   
						$(function () {
							$('[data-toggle="tooltip"]').tooltip() //for tooltip 
						})
				
						var status='';
						$(".orderStatus").change(function(){
							if ($(this).is(":checked")) { 
								status=$(this).val();
								var getId=$(this).attr('id');
								var shortStatus=status.substring(0,3);
								var string="";
								if(shortStatus=='RFP'){
									string="Ready For Pickup";	
								}
								else if(shortStatus=='DEL'){
									string="Delivered";	
								} else {
									string="";	
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
									if(e)
									{
										$.ajax({
											url:base_path+'Order/orderOperations',
											method:"POST",
											data:{'status':status},
											datatype:"text",
											success: function(response) {
												var res = JSON.parse(response);
												if(res.status)
												{
													swal({ 
														title: "Completed",
														text: "Successfully Order Status Changed",
														type: "success" 
													},
													function(isConfirm){
														if(isConfirm){
															orderCode=$('#odCode').val();
															orderStatus=$('#orderStatus').val();
															pincode=$('#pincode').val();
															cityCode=$('#cityCode').val(); 
															dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate);
															getTotalAmount(cityCode,orderCode,orderStatus,pincode,fromDate,toDate);
														}
													});									
												}	
												else
												{
													swal('Order Status','Order Status Faield To Change','error');  
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
										$('#'+getId).prop('checked',false);
										swal("Cancelled", "Your Order Reject Request Failed", "error");
										rderCode=$('#odCode').val();
										orderStatus=$('#orderStatus').val();
										pincode=$('#pincode').val();
										fromDate=$('#fromDate').val();
										toDate=$('#toDate').val();
										cityCode= $("#cityCode").val();
										dataTable(cityCode,orderCode,orderStatus,pincode,fromDate,toDate);	
										getTotalAmount(cityCode,orderCode,orderStatus,pincode,fromDate,toDate);
									}
								});
							}
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
		

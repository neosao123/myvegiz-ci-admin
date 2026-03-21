<div class="page-wrapper">        
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Confirm Orders List</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Confirm Order List</li>
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
								
								<div class="col-sm-3 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Order Status :</label> </span>
										<select  class="form-control" id="orderStatus" name="orderStatus">
											<option value="">Select Option</option>
											<?php
											foreach ($orderStatus->result() as $status) {
											echo '<option value="' . $status->statusSName . '">' . $status->statusName . '</option>';
											} ?>
										</select>
									</div>
								</div>
							
									<?php	
									$todayDate = date('d/m/Y');
									$previousDate = date('d/m/Y',strtotime(' - 7 days'));
								?>
								<div class="col-sm-5">
									<div class="input-daterange input-group">
									<span> <label> Search Dates :</label> </span>
										<div class="input-daterange input-group" id="productDateRange">
											<input type="text" class="form-control date-inputmask col-sm-5" name="start"  id="fromDate" placeholder="dd/mm/yyyy" value="<?= $previousDate ?>"/>
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
						<h4 class="card-title">Confirmed Orders List</h4>
						
						<div class="table-responsive">
							<table id="datatableConfirmed" class="table table-striped table-bordered">
								<thead>
									<tr>
										 <th>Sr. No</th>
										<th>Code</th>
										<th>Client Name</th>
										<!--<th>Area</th>-->
										<th>Address</th>
										<th>Mobile No</th>
										<th>Order Status</th>
										<th>Amount</th>
										<th>Order Date</th>
										<th>Delivery Boy</th>
										<th>Operations</th>
									</tr>
								</thead>
							</table>
						</div>
						<!--<div class="col-sm-4 offset-sm-8 mt-1">
							<h4 class="border p-2">Total - <span id="total" class="float-right">0.00</span></h4>
						</div>-->
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
		$("#orderStatus option[value=RJT]").hide();	  
		$("#orderStatus option[value=PLC]").hide();	  
		//$("#orderStatus option[value=PUP]").hide();	
		$("#orderStatus option[value=PUP]").text('On the Way');
		
		var orderCode="",orderStatus,fromDate,toDate="";
		fromDate = "<?= $previousDate ?>";
		toDate = "<?= $todayDate ?>";
		dataTable("","","","",fromDate,toDate,"","");
		$('#btnClear').click(function () {
			// location.reload();
			dataTable("","","","",fromDate,toDate,"","");
		}); // End Clear Click
			
		$('#btnSearch').on('click', function (e){
			orderCode=$('#orderCode').val();
			orderStatus=$('#orderStatus').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
			dataTable(orderCode,orderStatus,fromDate,toDate);	
		});	
			 
		function dataTable(orderCode,orderStatus,fromDate,toDate)
		{
			$("#total").text("0.00");
			$.fn.DataTable.ext.errMode = 'none';
			if ($.fn.DataTable.isDataTable("#datatableConfirmed")) $('#datatableConfirmed').DataTable().clear().destroy();
		 
			$('#datatableConfirmed').DataTable({  
				//stateSave: true,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url:base_path+"index.php/Confirmorder/getPlacedOrders",
					type:"GET",
					data:{	
						'placeList':'1',
						'orderCode':orderCode,
						'orderStatus':orderStatus,
						'fromDate':fromDate,
						'toDate':toDate
					}, 
					complete: function(json) 
					{		   
						$(function () {
							$('[data-toggle="tooltip"]').tooltip() //for tooltip 
						})
					}
				}
			});
		}
   });
   	$( document ).ready(function() {
	//show alerts
		var data='<?php echo $error; ?>';
		// console.log(data);
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
		

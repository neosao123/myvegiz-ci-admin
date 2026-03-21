<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Pending Orders List</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url() . 'index.php/admin/index'; ?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Pending List</li>
						</ol>
					</nav>
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
										<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-myve waves-effect waves-light">Search</button>
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
						<h4 class="card-title">Pending Orders List</h4>
						<div class="table-responsive">
							<table id="datatablePending" class="table table-striped table-bordered">
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
					</div>
				</div>
			</div>
		</div>
	</div>	
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
   $( document ).ready(function() { //nitin 05 DEC 2018
		$("#orderStatus option[value=SHP]").hide();
		$("#orderStatus option[value=RFP]").hide();
		$("#orderStatus option[value=DEL]").hide();
	    $("#orderStatus option[value=PLC]").hide();
		$("#orderStatus option[value=PRE]").hide();
		$("#orderStatus option[value=PUP]").hide();
		$("#orderStatus option[value=REL]").hide();
		$("#orderStatus option[value=RCH]").hide();
		$("#orderStatus option[value=PLC]").text('Pending');
		
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
		$('.btn-inverse').click(function(){
			dataTable();
		});
	    var orderCode,orderStatus,fromDate,toDate="";
		dataTable(orderCode,orderStatus,fromDate,toDate);
	    $('#btnSearch').on('click', function (e){
			orderCode=$('#orderCode').val();
			orderStatus=$('#orderStatus').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
			dataTable(orderCode,orderStatus,fromDate,toDate);	
		});	
		function dataTable(orderCode,orderStatus,fromDate,toDate)
		{
			$.fn.DataTable.ext.errMode = 'none';
			if ($.fn.DataTable.isDataTable("#datatablePending")) {
				$('#datatablePending').DataTable().clear().destroy();
			}
			$('#datatablePending').DataTable({  
				stateSave: false,
				"processing":true,  
				"serverSide":true,  
				"order":[],
				"searching": false,
				"ajax":{  
					url: base_path+"index.php/Pendingorders/getOrderList",
					type:"GET" , 
					data:{
						'call':'call from pending',
						'placeList':0,
						'orderCode':orderCode,
						'orderStatus':orderStatus,
						'fromDate':fromDate,
						'toDate':toDate
					} , 
					"complete": function(response) {			    
						
					}//complete
				}//datatable ajax
			});//datatable call
		} //main datatable function	   
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
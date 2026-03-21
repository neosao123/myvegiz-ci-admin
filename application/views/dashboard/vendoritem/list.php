<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Vendor Item</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Vendor Item List</li>
						</ol>
					</nav>
				</div>
				
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">
					<div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/Food/Vendoritem/add';?>">Create Item</a></div>
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
									
									
									<span> <label for="orderStatus">Menu Category:</label> </span>
										<input type="text"  class="form-control" list="menucategoryList" id="menucategoryCode" name="menucategoryCode" placeholder="Enter Menu Category Here ">
											<datalist id="menucategoryList">
											<?php  if($menucategory){ foreach($menucategory->result() as $m){
											echo'<option value="'.$m->code.'">'.$m->menuCategoryName.'</option>';
											} } ?>
											</datalist>
									
									
										
									</div>
								</div>
								<div class="col-sm-3 mb-3">
									<div class="form-group">
									
									
									<span> <label for="orderStatus">Item:</label> </span>
										<input type="text"  class="form-control" list="itemList" id="itemCode" name="itemCode" placeholder="Enter Menu Item Here ">
											<datalist id="itemList">
											<?php  if($menuitem){ foreach($menuitem->result() as $mi){
											echo'<option value="'.$mi->code.'">'.$mi->itemName.'</option>';
											} } ?>
											</datalist>
									
									
										
									</div>
								</div>
								
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="vender">Vendor:</label> </span>
										<input type="text"  class="form-control" list="vendorList" id="vendorCode" name="vendorCode" placeholder="Enter Vendor Name Here ">
											<datalist id="vendorList">
											<?php if($vendor){ foreach($vendor->result() as $v){
											echo'<option value="'.$v->code.'">'.$v->entityName.'</option>';
											} } ?>
											</datalist>	
									</div>
								</div>
							 
								
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Item Type :</label> </span>
										<select id="itemtype" name="cuisineType" class="form-control" required>
									<option value="">Select Cuisine</option>
									<option value="veg">Veg</option>
									<option value="nonveg">Non - Veg</option>
								</select>
									</div>
								</div>
								
								
								<!--<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="itemtype">Item Type:</label> </span>
										<input type="text"  class="form-control" list="itemtypelist" id="itemtype" name="itemtype" placeholder="Enter Status Here ">
											<datalist id="itemtypelist">
											<?php if($itemtype){ foreach($itemtype->result() as $it){
											echo'<option value="'.$it->cuisineType.'"></option>';
											} } ?>
											</datalist>
									</div>
								</div>-->
								
							
								<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="itemstatus">Item Status:</label> </span>
									
											<select id="itemstatus" name="itemstatus" class="form-control" required>
									<option value="">Select Status</option>
									<option value="1">Active</option>
									<option value="0">InActive</option>
								</select>
									</div>
								</div>
								
									<!--<div class="col-sm-2 mb-3">
									<div class="form-group">
										<span> <label for="orderStatus">Status:</label> </span>
										<input type="text"  class="form-control" list="itemactivestatuslist" id="itemactivestatus" name="itemactivestatus" placeholder="Enter Status Here ">
											<datalist id="itemactivestatuslist">
											<?php if($itemactivestatus){ foreach($itemactivestatus->result() as $ias){
											echo'<option value="'.$ias->isActive.'"></option>';
											} } ?>
											</datalist>
									</div>
								</div>-->
									
									
							
								<!--<div class="col-sm-5">
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
								</div>-->
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
	
		<!--end filter-->
		
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<h4 class="card-title">Vendor Item List</h4>
						<div class="table-responsive">
							<table id="datatableVendor" class="table table-striped table-bordered ">
								<thead>
									<tr>
										<th>Sr no.</th>
										<th>Code</th>
										<th>Item Name</th>
										<th>Vendor Name</th>
										<th>Menu Category</th>
										<th>Status</th>
										<th>Active Status</th>
										<th>Approved Status</th>
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
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Vendor</h4>
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
	
</div>

<script>
		   $( document ).ready(function() {
			    loadTable();
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
			loadTable();
		});
		   var menucategoryCode="",itemCode="",vendorCode="",itemstatus="",itemactivestatus="",itemtype="";
		ForListUrls= base_path+"index.php/Vendoritem/getVendorItemList";  
		loadTable(menucategoryCode,itemCode,vendorCode,itemstatus,itemtype);
	    $('#btnSearch').on('click', function (e){
			menucategoryCode=$('#menucategoryCode').val();
			itemCode=$('#itemCode').val();
			vendorCode=$('#vendorCode').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
			itemstatus=$('#itemstatus').val();
			itemactivestatus=$('#itemactivestatus').val();
			itemtype=$('#itemtype').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
			loadTable(menucategoryCode,itemCode,vendorCode,itemstatus,itemtype);	
		});
			    function loadTable(kmenucategoryCode,kitemCode,kvendorCode,kitemstatus,kitemtype){
					if ($.fn.DataTable.isDataTable("#datatableVendor")) {
					  $('#datatableVendor').DataTable().clear().destroy();
					}
				var dataTable = $('#datatableVendor').DataTable({ 
					stateSave: true,
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": false,
				   "ajax":{ 
				   
						url: base_path+"Food/Vendoritem/getVendorItemList",  
						type:"GET",
						data:{
						'call':'call from pending',						 
						'placeList':0,
						 'menucategoryCode':kmenucategoryCode,
						 'itemCode':kitemCode,
						 'vendorCode':kvendorCode,
						 'itemstatus':kitemstatus,
						 //'itemactivestatus':kitemactivestatus,
						 'itemtype':kitemtype,
						
						
						
					} ,
				 "complete": function(response) {
					
					$(".blue").click(function(){
					 var code=$(this).data('seq');
					 $.ajax(
					 
					 {
							url: base_path+"Food/Vendoritem/view",  
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
				var code=$(this).data('seq');
				//alert(code);
					swal({
						title: "You want to delete vendor item "+code+" ?",
						// text: " Category against Product and stock also deleted.",
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
								url: base_path+"Food/Vendoritem/delete",
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
							swal("Cancelled", "Your vendor item record is safe :)", "error");
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
         toastr.success(obj.message, 'Vendor Item', { "progressBar": true });
      }
      else
      {
		  toastr.error(obj.message, 'Vendor Item', { "progressBar": true });
      }
    }
	//end show alerts
   });

</script>
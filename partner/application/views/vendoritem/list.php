<div class="page-wrapper">
	<div class="page-breadcrumb">
		<div class="row">
			<div class="col-5 align-self-center">
				<h4 class="page-title">Vendor Item</h4>
				<div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/Home/index';?>">Home</a></li>
							<li class="breadcrumb-item active" aria-current="page">Vendor Item List</li>
						</ol>
					</nav>
				</div>
			</div>
			<div class="col-7 align-self-center">
				<div class="d-flex no-block justify-content-end align-items-center">
					<div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/Vendoritem/add';?>">Create Item</a></div>
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
								<div class="col-sm-3 mb-3">
									<div class="form-group">
									    <span> <label for="approvedStatus">Approved Status:</label> </span>
										<select  class="form-control" id="approvedStatus" name="approvedStatus">
											<option value="">Select Option</option>
											<option value="1">Yes</option>
											<option value="0">No</option>
										</select>
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
			<!--end filter-->
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
										<!--<th>Vendor Name</th>-->
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
	    var menucategoryCode="",itemCode="";
		ForListUrls= base_path+"index.php/Vendoritem/getVendorItemList";  
		loadTable(menucategoryCode,itemCode);
	    $('#btnSearch').on('click', function (e){
			menucategoryCode=$('#menucategoryCode').val();
			itemCode=$('#itemCode').val();
			approvedStatus=$('#approvedStatus').val();
		
			loadTable(menucategoryCode,itemCode,approvedStatus);	
		});
				
				
				
			    function loadTable(kmenucategoryCode,kitemCode,kapprovedStatus){
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
						url: base_path+"index.php/Vendoritem/getVendorItemList",  
						type:"GET",  
						data:{
						'call':'call from pending',						 
						'placeList':0,
						'menucategoryCode':kmenucategoryCode,
						'itemCode':kitemCode,
                        'approvedStatus':kapprovedStatus, 						
						
						
					} , 
				        "complete": function(response) { 
        					$(".actionStatus").change(function(){
        					    var code=$(this).data('id'); 
        					    var activeStatus = 0;
        					    var textActive = 'In-Active';
        					    if($(this).is(':checked'))
        					    {
        					        activeStatus = 1;
        					        textActive = 'Active';
        					    } 
            					swal({
            						title: "Do you confirm to change the status of this item "+code+" to "+textActive+" ?", 
            						type: "warning",
            						showCancelButton: !0,
            						confirmButtonColor: "#DD6B55",
            						confirmButtonText: "Yes",
            						cancelButtonText: "No",
            						closeOnConfirm: !1,
            						closeOnCancel: !1
            					}, function(e) {
            						console.log(e);
            						if(e)
            						{
            							$.ajax({
            								url: base_path+"index.php/Vendoritem/updateItemStatus",
            								type: 'POST',
            								data:{
            								  'code':code,'activeStatus':activeStatus
            								},
            								success: function(respose) { 
            								  if(respose)
            								  {
            									   swal({ 
            										  title: "Completed",
            										   text: "Successfully updated the status",
            											type: "success" 
            										  },
            										  function(isConfirm){
            									         if (isConfirm) { 
            									        	loadTable();
            										
            									        }
            									    }); 
            								  }
            								  else
            								  {
            								   swal("Failed", "Item Active Status failed to update", "error");
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
            							swal("Cancelled", "Status shall not be chnaged :)", "info");
            						}
            					});
        					});
            			    //delete
            			     $('.mywarning').on("click", function() {
            				    var code=$(this).data('seq'); 
            					swal({
            						title: "You want to request admin to delete this item "+code+" ?",
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
            								url: base_path+"index.php/Vendoritem/delete",
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
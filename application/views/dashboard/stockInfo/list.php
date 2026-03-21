  

        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Stock information</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Stock informatio</li>
                                </ol>

                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            
                            <!--<div class=""><a class="btn btn-primary" href="<?php echo base_url().'index.php/designation/add';?>">Create Designation</a></div>-->
                        </div>
                    </div>
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
                <!-- basic table -->
				 <div class="row">
				 <div class="col-12">
					 <div class="card">
                            <div class="card-body">
							 <div class="form-row">
                                <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="itemCode">Item Name: </label>
                                        <input type="text"  id="itemCode" name="itemCode" list="itemCodeList" class="form-control">
                                        <datalist id="itemCodeList">                                           
										   <option value="" >select option</option>
                                            <?php
                                          foreach($itemmaster->result() as $item){
                                          echo '<option value="' . $item->code . '">' . $item->name. '</option>';
                                          }
                                          ?>
                                        </datalist>
                                </div>
							 
							 <!-- <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="siteCode">Mine Name: </label>
                                         <input type="text"  id="contractCode" name="contractCode" list="contractCodeList" class="form-control">
                                          <datalist id="contractCodeList">
										  </datalist>
							      </div>-->
								
								
								 <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="siteCode">Site Name: </label>
                                         <input type="text"  id="siteCode" name="siteCode" list="siteCodeList" class="form-control">
                                           <datalist id="siteCodeList">
										   <option value="">select option</option>
										  <?php
                                          foreach($sitemaster->result() as $site){
                                          echo '<option value="' . $site->code . '">' . $site->siteName. '</option>';
                                          }
                                          ?>
                                            
                                          </datalist>
                                         
								   </div>
								   
								   <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="storageCode">Storage Name: </label>
                                         <input type="text"  id="storageCode" name="storageCode" list="storageCodeList" class="form-control">
                                           <datalist id="storageCodeList">
										   <option value="">select option</option>
										  </datalist>
                                         
								   </div>
								    <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="storageSectionCode">Storage Section Name: </label>
                                         <input type="text"  id="storageSectionCode" name="storageSectionCode" list="storageSectionCodeList" class="form-control">
                                           <datalist id="storageSectionCodeList">
										   <option value="">select option</option>
										   </datalist>
                                   </div>
								   
								   <div class="col-sm-4 col-md-2 mb-3">
								    <label for="tableStructure">Table Structure: </label>
								  <div class="btn-group " data-toggle="buttons" role="group">
                                            <label class="btn btn-outline btn-info active">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio1" name="options" value="list" class="custom-control-input" >
                                                    <label class="custom-control-label" for="customRadio1"> <i class="ti-check text-active" aria-hidden="true"></i> List Table</label>
                                                </div>
                                            </label>
                                            <label class="btn btn-outline btn-info">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio2" name="options" value="grid" class="custom-control-input">
                                                    <label class="custom-control-label" for="customRadio2"> <i class="ti-check text-active" aria-hidden="true"></i> Grid Table</label>
                                                </div>
                                            </label>
                                           
                                        </div>
                                    <!--<label for="tblStructure">Table Structures: </label>
                                         <select  id="tblStructure" name="tblStructure"  class="form-control">
                                         <option value="">select option</option>
										 <option value="LINE">Line Table</option>
										 <option value="GRID">Grid Table</option>
										   </select>-->
                                   </div>
								   
								    
                                
                                      <!--<div class="col-sm-4 col-md-2 mb-3">
                                         <label for="locationCode">Location: </label>
                                         <input  type="text" id="locationCode" name="locationCode"  list="locationCodeList" class="form-control">
                                            <datalist id="locationCodeList">
											</datalist>
										</div>-->
										<!--<div class="col-sm-12  col-md-4 mb-3">
									
									 <label for="contractDateRange">Date</label>
										
											 <div class="input-daterange input-group" id="contractDateRange">
												<input type="text" class="form-control date-inputmask" id="start" name="start" placeholder="dd/mm/yyyy" />
												<div class="input-group-append">
												   <span class="input-group-text bg-info b-0 text-white">TO</span>
												</div>
												<input type="text" class="form-control date-inputmask"  id="end" name="end" placeholder="dd/mm/yyyy" />
											 </div>
											  </div>-->
										
										<div class="col-sm-4">
										 <label for="contractDateRange"></label>
									 <div class="form-group   text-center">
										<button type="button" id="btnSearch" name="btnSearch" class="btn btn-info waves-effect waves-light">Search</button>
										<button type="button" class="btn btn-dark waves-effect waves-light" id="clear">Clear</button>
							        </div>
									  </div>
								  
								</div>
								</div>
								
								
							
						
							</div>
						</div>
				  </div><!--filter end-->
				
                <div class="row" id="lineTableData">
                    <div class="col-12" >
                        <div class="card">

                            <div class="card-body">
                                <h4 class="card-title"> Stock Information</h4>
                               <div class="table-responsive">
                                    <table id="datatableStockInfo" class="table table-striped table-bordered ">
                                        <thead>
                                            <tr>
                                               <th>ID</th>
											   <th>Item Code</th>
											  <th>Site Name</th>
											  <th>Storage Name</th>
                                              <th>Storage Section</th>
                                              <th>Stock</th>
                                               <!--<th>Mine Name</th>
											  <th>Status</th>-->
                                              <th>Oprations</th>
                                            </tr>
                                        </thead>
                                    
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				 <div class="row" id="stockInfoGridList">
		        </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
		<!-- sample modal content -->
			<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog">
					<div class="modal-content ">
						<div class="modal-header">
							<h4 class="modal-title"> View Stock Information</h4>
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
					$( '#clear' ).click(function() {
					 
					$( "#stockInfoGridList" ).empty();
					});
					
	var keyitemCode='',keySiteCode='',keyStorageCode='',keyStorageSectionCode='';
	
	
	function clearGlobalVariables()
	{
		keyitemCode='',keySiteCode='',keyStorageCode='',keyStorageSectionCode='';
		
	}
						
					  //search button click
	$('#btnSearch').on('click', function (e){
		$('#stockInfoGridList').DataTable().state.clear();
		structure=$("[name='options']:checked").val()
		
		//structure=$('#options').val();
		keyItemCode=$('#itemCode').val();
		keySiteCode=$('#siteCode').val();
		//alert(keySiteCode);
		keyStorageCode=$('#storageCode').val();
		keyStorageSectionCode=$('#storageSectionCode').val();
		//keyNumber=$('#mineNumber').val();
		//keyMineCode=$('#mineCode').val();
		//alert(keyContractName+" "+keyContractNumber+" "+keyContractProvider+" "+keyContractor+" "+keyMineNumber+" "+keyMineName);
		var fromDate = $('#tenderFromDate').val();
		    var toDate = $('#tenderToDate').val();
			if(fromDate!='' && toDate!='')
			{
		    keyTenderDateStart = moment(fromDate,'DD/MM/YYYY').format("YYYY/MM/DD");
            keyTenderDateEnd = moment(toDate,"DD/MM/YYYY").format("YYYY/MM/DD");
			   if(moment(keyTenderDateStart)>moment(keyTenderDateEnd))
			   {
				 alert('Date Should Be Greater Than From Date');
				 keyTenderDateStart='';
				 keyTenderDateEnd='';
			   }
			}
		   
		if(structure!='grid')
		{
		$('#lineTableData').show();
		$('#stockInfoGridList').hide();
		getDataTable(keyItemCode,keySiteCode,keyStorageCode,keyStorageSectionCode);
		//console.log(getDataTable);
		}
		else{
			$('#lineTableData').hide();
			$('#stockInfoGridList').show();
			 $.ajax({
            url: base_path + 'StockInfo/getGridDataByCondition',
            method: "GET",
            data: {
              'itemCode':keyItemCode,
			  'siteCode':keySiteCode,
			  'storageCode':keyStorageCode,
			  'storageSectionCode':keyStorageSectionCode
            },
            datatype: "text",
            success: function(data) {
				
				  // $("#stockInfoGridList").clear().destroy();
				 
                   $("#stockInfoGridList").html(data); 
                 },
				 complete: function(data){
					 $('.inform').click (function () {
					  var no = $(this).attr('srno');
				  
					var item_Code = $("#itemCode"+no).text();
					var storage_Section = $("#storageSectionCode"+no).val();
					var stock_Code = $("#stock"+no).text();
					//console.log(stock);
					//console.log(storageSection);
					
					$.ajax({
					 url:'<?php echo site_url('StockInfo/view');?>',
					 //url: base_path+"StockInfo/gridView",
					 method:"GET",
					 data:{'itemCode':item_Code,
						'storageSection':storage_Section,
						'stock':stock_Code
						},
					 datatype:"text",
					 success: function(data)
					 {
						//console.log(data);
						$("#modalBody"+no).html(data);
					 },
					 complete: function(data){
						 console.log('completed');
					 }
					 
					});
					 
				
					});
				 }
			 });
			
		}
	});
				 function getDataTable(p_keyItemCode,p_keySiteCode,p_keyStorageCode,p_keyStorageSectionCode)
	    {
					if ($.fn.DataTable.isDataTable("#datatableStockInfo")) {
					  $('#datatableStockInfo').DataTable().clear().destroy();
					}
							   
							   var dataTable = $('#datatableStockInfo').DataTable({  
									"processing":true,  
								   "serverSide":true,  
								   "order":[],
								   "searching": false,
								   "ajax":{  
										url: base_path+"StockInfo/getDatatableDataByCondition",  
										type:"GET",
										data:{'itemCode':p_keyItemCode,
										      'siteCode':p_keySiteCode,
										      'storageCode':p_keyStorageCode,
											  'storageSectionCode':p_keyStorageSectionCode
											  // 'contractCode':p_keyContractCode,
											  // 'mineCode':p_keyMineCode,
											  
											  },
										
								  "complete": function(response) {
									$(".blue").click(function(){
									 var code=$(this).data('seq');
									 var storageSection=$(this).data('storage');
									  var stock=$(this).data('stock');
									 //alert(code);
									 //alert(storageSection);
									// alert(code+" "+storageSection);
									 $.ajax({
											url: base_path+"StockInfo/view",  
											method:"GET",
											data:{'itemCode':code,'storageSection':storageSection,'stock':stock},
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
				
				
					swal({
						title: "Are you sure?",
						text: "You will not be able to recover this imaginary file!",
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes, delete it!",
						cancelButtonText: "No, cancel plx!",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						console.log(e);
						if(e)
						{
							$.ajax({
								url: base_path+"Designation/delete",
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
										//loadTable();
										
									  }
									});
									
								  }
								  else
								  {
								   toastr.success('Record Not Deleted', 'Failed', { "progressBar": true });
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
							swal("Cancelled", "Your imaginary file is safe :)", "error");
						}
					});
				});	
				 }
				}
			});
		}/////
				   
 });
 	   $( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  
	
         toastr.success(obj.message, 'Designation', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Designation', { "progressBar": true });
       
      }
    }
	//end show alerts
   });
				
$('#siteCode').change (function () {
		 
			var site_code = $(this).val();
			$.ajax({
					 url:'<?php echo site_url('common/getStorageCodeBySiteCode');?>',
					 method:"GET",
					 data:{'siteCode':site_code},
					 datatype:"text",
					 success: function(data)
					 {
						//console.log(data);
						 var res = JSON.parse(data);
					   $("#storageCodeList").html(res.storageCode);
					 }
					 
			});
		 
	
		});
		$('#storageCode').change (function () {
		 
			var storage_code = $(this).val();
			$.ajax({
					 url:'<?php echo site_url('common/getStorageSectionByStorageCode');?>',
					 method:"GET",
					 data:{'storageCode':storage_code},
					 datatype:"text",
					 success: function(data)
					 {
						//console.log(data);
						 var res = JSON.parse(data);
					   $("#storageSectionCodeList").html(res);
					 }
					 
			});
		 
	
		});
		
</script>
 
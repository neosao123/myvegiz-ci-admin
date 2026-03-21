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
                        <h4 class="page-title">Production</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Production List</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                      <div class="col-7 align-self-center">
                      <div class="d-flex no-block justify-content-end align-items-center">
                        
                        <div class="m-r-10">
                          <div class=""><a class="btn btn-primary" href="<?php echo base_url().'index.php/Production/add';?>">Create Production</a></div>    
                        <!-- <div class=""><small>LAST MONTH</small>
                            <h4 class="text-info m-b-0 font-medium">$58,256</h4></div>
                    </div> --> 
                </div> 
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
                                    <label for="itemCode">Item Category: </label>
                                        <Select  id="itemCode" name="itemCode" class="form-control">
                                            <option value="" >select option</option>
                                            <?php
                                          foreach($itemsBasedOnCategory->result() as $item){
                                          echo '<option value="' . $item->code . '">' . $item->name. '</option>';
                                          }
                                          ?>
                                        </Select>
                                </div>
							 <div class="col-md-2 col-sm-4 mb-3">
                                    <label for="assetCode">Asset: </label>
                                         <input type="text" id="assetCode" name="assetCode" class="form-control" list="assetCodeList">
                                           <datalist id="assetCodeList">
										   <?php
										   foreach($assetmaster->result() as $asset)
										   {
											   echo "<option value='".$asset->code."'>".$asset->assetName."</option>";
										   }
										   ?>
                                          </datalist>
                                   </div>
							  <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="siteCode">Mine Name: </label>
                                         <input type="text"  id="contractCode" name="contractCode" list="contractCodeList" class="form-control">
                                          <datalist id="contractCodeList">
										  </datalist>
							      </div>
								
								
								 <div class="col-sm-4 col-md-2 mb-3">
                                    <label for="siteCode">Site Name: </label>
                                         <input type="text"  id="siteCode" name="siteCode" list="siteCodeList" class="form-control">
                                           <datalist id="siteCodeList">
										   <option value="">select option</option>
                                            
                                        </datalist>
                                         
								   </div>
                                
                                      <div class="col-sm-4 col-md-2 mb-3">
                                         <label for="locationCode">Location: </label>
                                         <input  type="text" id="locationCode" name="locationCode"  list="locationCodeList" class="form-control">
                                            <datalist id="locationCodeList">
											</datalist>
										</div>
										<div class="col-sm-12  col-md-4 mb-3">
									
									 <label for="contractDateRange">Production Date</label>
										
											 <div class="input-daterange input-group" id="contractDateRange">
												<input type="text" class="form-control date-inputmask" id="start" name="start" placeholder="dd/mm/yyyy" />
												<div class="input-group-append">
												   <span class="input-group-text bg-info b-0 text-white">TO</span>
												</div>
												<input type="text" class="form-control date-inputmask"  id="end" name="end" placeholder="dd/mm/yyyy" />
											 </div>
											  </div>
										
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
				  </div>
				 
				 <div class="row" id="productionEntriesList">
		        </div>
			
		<!-- /.modal 2 -->
					 <script>
		$( document ).ready(function() {
				
			loadEntries();
			function loadEntries()
			{
			 $.ajax({
            url: base_path + 'Production/getLastRecord',
            method: "GET",
            data: {
            },
            datatype: "text",
            success: function(data) {
				
                $("#productionEntriesList").html(data); 
           },
		   complete: function (data){
			   $(".inform").click(function() {
		      var no = ($(this).attr('srno'));
		   
			var production_code = $('#code'+no).val();
			
			
		   
		  
		  $.ajax({
            url: base_path + 'Production/getProductionEntriesNames',
            method: "GET",
            data: {
               productionCode: production_code
            },
            datatype: "text",
            success: function(data) {
				console.log(data);
                  var res = $.parseJSON(data);
                
				 //$("#itemCode").val(res.itemCode);
				 //$("#target").val(res.productionQuantity+ res.uom);
				  
                $("#contractCode"+no).val(res.mineName);
                $("#siteCode"+no).val(res.siteName);
                $("#locationCode"+no).val(res.locationName);
                $("#assetCode"+no).val(res.assetName);
				$("#remark"+no).val(res.remark);
				//$("#productionDate").val(res.remark);
          }
       });
    }); 
	 $(".delete").click(function() {
		 var no = ($(this).attr('srno'));
		 
       var production_code = $('#code'+no).val();
	    var reason_code = $('#reason'+no).val();
      console.log(production_code);
      $.ajax({
            url: base_path + 'Production/delete',
            method: "GET",
            data: {
               productionCode: production_code,reason: reason_code
            },
            datatype: "text",
            success: function(data) {
				console.log(data);
				if(status!='false')
					  {
				       toastr.success('Record  Deleted', 'Deleted', { "progressBar": true });	
                       document.forms["productionSearch"].reset();	
                      loadEntries();				   
					  }	
                  else{
					   toastr.danger('Record Not Deleted', 'Failed', { "progressBar": true });			
				  }					  
				}
       });
    });
	        				 
		   }
       });
		}	
			
	    $("#btnSearch").click(function() {
			//$('#datatableStorage').DataTable().state.clear();
			var item_code = $('#itemCode').val();
			var asset_code = $('#assetCode').val();
			var contractCode_code = $('#contractCode').val();
			var siteCode_code = $('#siteCode').val();
			var location_code = $('#locationCode').val();
			var fromDate = $('#start').val();
		    var toDate = $('#end').val();
			if(fromDate!='' && toDate!='')
			{
		    var keyStartDate = moment(fromDate,'DD/MM/YYYY').format("YYYY/MM/DD");
			
			var keyEndDate = moment(toDate,"DD/MM/YYYY").format("YYYY/MM/DD");
			
			   if(moment(keyStartDate)>moment(keyEndDate))
			   {
				 alert('Date Should Be Greater Than From Date');
				 keyStartDate='';
				 keyEndDate='';
			   }
			}
			
			
			 $.ajax({
            url: base_path + 'Production/getSearchResult',
            method: "GET",
            data: {
               itemCode: item_code,assetCode: asset_code,contractCode: contractCode_code,siteCode: siteCode_code,locationCode: location_code,startDate: keyStartDate, endDate: keyEndDate
			   
            },
            datatype: "text",
            success: function(data) {
				
				//$('#productionEntriesList').clear().destroy();
                   $("#productionEntriesList").html(data); 
                 },
				 complete: function(data){
					 $(".inform").click(function() {
		  
		 var no = ($(this).attr('srno'));

		var production_code = $('#code'+no).val();
		
		
         
      $.ajax({
            url: base_path + 'Production/getProductionEntriesNames',
            method: "GET",
            data: {
               productionCode: production_code
            },
            datatype: "text",
            success: function(data) {
					
                  var res = $.parseJSON(data);
				 
                
				 $("#contractCode"+no).val(res.mineName);
                $("#siteCode"+no).val(res.siteName);
                $("#locationCode"+no).val(res.locationName);
                $("#assetCode"+no).val(res.assetName);
				$("#remark"+no).val(res.remark);
          }
       });
    });
	 $(".delete").click(function() {
		 var no = ($(this).attr('srno'));
       var production_code = $('#code'+no).val();
	    var reason_code = $('#reason'+no).val();
      console.log(production_code);
      $.ajax({
            url: base_path + 'Production/delete',
            method: "GET",
            data: {
               productionCode: production_code,reason: reason_code
            },
            datatype: "text",
            success: function(data) {
				console.log(data);
				if(status!='false')
					  {
				       toastr.success('Record  Deleted', 'Deleted', { "progressBar": true });	
                       document.forms["productionSearch"].reset();					   
					  }	
                  else{
					   toastr.danger('Record Not Deleted', 'Failed', { "progressBar": true });			
				  }					  
				}
       });
    }); 
					  $("#clear").click(function() {
				      loadEntries();
			 });
					 
			   }
		
       });
		});
			
			
			
			
			
	 
		$("#code").change(function() {
       var production_code = $('#code').val();
     
      $.ajax({
            url: base_path + 'Production/getProductionEntries',
            method: "GET",
            data: {
               productionCode: production_code
            },
            datatype: "text",
            success: function(data) {
				console.log(data);
                  var res = $.parseJSON(data);
                
				  $("#itemCode").val(res.itemCode);
				 $("#target").val(res.productionQuantity+ res.uom);
				  
          }
       });
    }); 
		 
			   
			   
		   });
		   $( document ).ready(function() {
	//show alerts
    var data='<?php echo $error; ?>';
    if(data!='')
    {
      var obj=JSON.parse(data);
      if(obj.status)
      {
		  
	
         toastr.success(obj.message, 'Production', { "progressBar": true });
   
      }
      else
      {
		  toastr.error(obj.message, 'Production', { "progressBar": true });
       
      }
    }
	//end show alerts
   });

		</script>
			

						



 <!--============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 align-self-center">
                    <h4 class="page-title">Production</h4>
                    <div class="d-flex align-items-center">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Production</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <div class="col-7 align-self-center">
                  <!--   <div class="d-flex no-block justify-content-end align-items-center">
                        <div class="m-r-10">
                            <div class="lastmonth"></div>
                        </div>
                        <div class=""><small>LAST MONTH</small>
                            <h4 class="text-info m-b-0 font-medium">$58,256</h4></div>
                    </div> --> 
                </div> 
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="container-fluid col-md-8">
            <!-- ============================================================== -->
            <!-- Start Page Content -->
            <!-- ============================================================== -->

            <!-- <div class="col-md-4"> -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Create Production Entry</h4>
						<!--class="needs-validation" novalidate removed class-->
                        <form  method="post" id="myForm" action="<?php echo base_url().'index.php/Production/save';?>" >
                           
							 <div class="row">
							  <div class="col-sm-6">
							 </div>
							
							<label class="col-sm-2 " for="assetCode">Date: </label>
							<div class="col-sm-3"> 
							 <input type="text" class="date-inputmask form-control-line" placeholder="dd/mm/yyyy" name="productionDate" id="productionDate">
							 </div>
							 </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
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
							 <div class="col-md-4 mb-3">
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
							  <div class="col-md-4 mb-3">
                                    <label for="siteCode">Mine Name: </label>
                                         <input type="text"  id="contractCode" name="contractCode" list="contractCodeList" class="form-control">
                                          <datalist id="contractCodeList">
										  </datalist>
							      </div>
								  
								</div>
							   	 <div class="form-row"> 
								 <div class="col-md-4 mb-3">
                                    <label for="siteCode">Site Name: </label>
                                         <input type="text"  id="siteCode" name="siteCode" list="siteCodeList" class="form-control">
                                           <datalist id="siteCodeList">
										   <option value="">select option</option>
                                            
                                        </datalist>
                                         
								
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="locationCode">Location: </label>
                                         <input  type="text" id="locationCode" name="locationCode"  list="locationCodeList" class="form-control">
                                            <datalist id="locationCodeList">
											</datalist>
										</div>
								</div>
                               
							<hr>
							<div class="form-horizontal">
                               <div class="form-group row">
                                            <label for="targetedQuantity" class="col-sm-3 mt-3 col-form-label"><strong>Targetted Quantity:</strong></label>
                                            <div class="col-sm-6 mt-3">
                                                <input type="text" class="form-control" id="targettedQuantity" name="targettedQuantity">
                                        </div>
										<div class="col-sm-3 mt-3">
                                          <input type="text" id="uomOfTq" name="uomOfTq" class="form-control" >
                                          
                                        </div>
                                      
                            
                              </div>
							  
                          </div>
							 <div class="form-horizontal">
                               <div class="form-group row">
                                            <label for="productionQuantity" step=".01" class="col-sm-3 mt-3 col-form-label"><strong>Production Quantity:</strong></label>
                                            <div class="col-sm-6 mt-3">
                                                <input type="text" class="form-control" id="productionQuantity" name="productionQuantity" placeholder="Enter quantity">
                                        </div>
                                       <div class="col-sm-3 mt-3">
                                          <input type="text" id="uom" name="uom" class="form-control" >
                                          
                                        </div>
                            
                              </div>
                          </div>
						  
						   <div class="form-horizontal">
                               <div class="form-group row">
                                            <label for="differenceQuantity"  class="col-sm-3 mt-3 col-form-label"><strong>Difference Quantity:</strong></label>
                                            <div class="col-sm-6 mt-3">
                                                <input type="text" class="form-control" id="differenceQuantity" name="differenceQuantity" >
                                        </div>
                                      
                            
                              </div>
                          </div>
                            			
                               <div class="form-horizontal">
                               <div class="form-group row">
							   <label for="remark" class="col-sm-3 mt-3 col-form-label"><strong>Remark:</strong></label>
                              <div class="col-sm-6 mt-3">
                               <textarea  type="text"id="remark" name="remark" class="form-control" row="2" cols="50" ></textarea>
										
                                </div>                            
							  </div>
							</div>												
                                 
                           
                                            
                            <div class="text-xs-right">
                                <button type="submit" class="btn btn-info" >Submit</button>
                                <button type="Reset" class="btn btn-reset">Reset</button>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End PAge Content -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Right sidebar -->
            <!-- ============================================================== -->
            <!-- .right-sidebar -->
            <!-- ============================================================== -->
            <!-- End Right sidebar -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ==============================================================-->

<script>
$(document).ready(function(){
	
	var date = moment(new Date(), "DD/MM/YYYY");
	 cuurentDate=moment(date).format("DD/MM/YYYY");
	 $('#productionDate').val(cuurentDate);
	  
	   var count = 0; 
window.onload = function () { 
    if (typeof history.pushState === "function") { 
        history.pushState("back", null, null);          
        window.onpopstate = function () { 
            history.pushState('back', null, null);              
            if(count == 1){
			swal({
						title: "Are you sure you want to leave?",
						type: "warning",
						showCancelButton: !0,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Yes,Leave Page!",
						cancelButtonText: "No, cancel please!",
						closeOnConfirm: !1,
						closeOnCancel: !1
					}, function(e) {
						console.log(e);
						if(e)
						{
						window.location.href="http://wolfox.in/wf-rocktech/index.php/Production/listRecords";	
						}
						else
						{
							swal("Cancelled", "Your imaginary file is safe :)", "error");
						}
					});
			}
         }; 
     }
 }  
setTimeout(function(){count = 1;},200); 
	
 // $("#assetTypeName").keyup(function() {   
 
    // var Max_Length = 50;
// var length = $("#assetTypeName").val().length;
// if (length > Max_Length) {
  // $("#assetTypeName").after("<p style='color:red'>the max length of "+Max_Length + " characters is reached, you typed in  " + length + "characters</p>");
 // }

 $("#remark").maxlength({max: 256});
 
 
   //for number textfield	
		$('input[type=number]').on('mousewheel',function(e){ $(this).blur(); });
                 // Disable keyboard scrolling
            $('input[type=number]').on('keydown',function(e) {
                var key = e.charCode || e.keyCode;
             // Disable Up and Down Arrows on Keyboard
                 if(key == 38 || key == 40 ) {
	            e.preventDefault();
                     } 
					 else 
					 {
	                     return;
                     }
          });
 });
 $('#itemCode').change(function()
		{
		   
		    item_code=$('#itemCode').val();
			
		 //  console.log(location_code);
		   $.ajax({
				url:'<?php echo site_url('Production/selectUomThroughItemCode'); ?>',
				method:"GET",
				data:{itemCode:item_code},
				
				datatype:"text",
				success: function(data)
				{ 
				console.log(data);
				var res = $.parseJSON(data);
				$("#uom").val(res.uomName);
				 // $("#locationCodeList").html("<option value='"+ res.workLocationCode + "' selected='true'>"+ res.workLocationName +"</option>");
				 // $("#siteCodeList").html("<option value='"+ res.siteCode + "' selected='true'>"+ res.siteName +"</option>");
				 // $("#contractCodeList").html("<option value='"+ res.contractCode + "' selected='true'>"+ res.mineName +"</option>");
				 
				 
				 }
			});
		});
  $('#assetCode').change(function()
		{
		   
		    asset_code=$('#assetCode').val();
			
		 //  console.log(location_code);
		   $.ajax({
				url:'<?php echo site_url('common/getDataFromAsset'); ?>',
				method:"GET",
				data:{assetCode:asset_code},
				
				datatype:"text",
				success: function(data)
				{ 
				//console.log(data);
				var res = $.parseJSON(data);
				
				 $("#locationCodeList").html("<option value='"+ res.workLocationCode + "' selected='true'>"+ res.workLocationName +"</option>");
				 $("#siteCodeList").html("<option value='"+ res.siteCode + "' selected='true'>"+ res.siteName +"</option>");
				 $("#contractCodeList").html("<option value='"+ res.contractCode + "' selected='true'>"+ res.mineName +"</option>");
				 
				 
				 }
			});
		});
		
		 $('#contractCode').change(function()
		{
		   
		    contract_code=$('#contractCode').val();
			item_code=$('#itemCode').val();
			
		 //  console.log(location_code);
		   $.ajax({
				url:'<?php echo site_url('Production/getTargetedQuantity'); ?>',
				method:"GET",
				data:{contractCode:contract_code,itemCode:item_code},
				
				datatype:"text",
				success: function(data)
				{ 
				
				console.log(data);
				var res = $.parseJSON(data);
				  console.log(res);
				 $("#uomOfTq").val(res.uom);
				 $("#targettedQuantity").val(res.perDayWeight);
				 }
			});
		});
		 $('#productionQuantity').change(function()
		{
			 var targettedQuantity= parseFloat($('#targettedQuantity').val());
			
			var  perDayWeight=parseFloat($('#perDayWeight').val());
			 var productionQuantity='';
			 productionQuantity= parseFloat($('#productionQuantity').val());
			 
			
			
			 if(targettedQuantity> productionQuantity)
			 {
		      var difference =targettedQuantity-productionQuantity;
			 }
			 else{
				  var difference =productionQuantity-targettedQuantity;
			 }
			  
			$("#differenceQuantity").val(difference);
			
			 
		});
 
 ///////old logic//////
 $('#contractCode1').change(function()
		{
		   
		    contract_code=$('#contractCode').val();
			
		 //  console.log(location_code);
		   $.ajax({
				url:'<?php echo site_url('Production/getSiteNames'); ?>',
				method:"GET",
				data:{contractCode:contract_code},
				
				datatype:"text",
				success: function(data)
				{ 
				console.log(data);
				var res = $.parseJSON(data);
				  console.log(res);
				 $("#siteCodeList").html(res[0]);
				 }
			});
		});
		$('#siteCode1').change(function()
		{
		   
		    site_code=$('#siteCode').val();
			
		 //  console.log(location_code);
		   $.ajax({
				url:'<?php echo site_url('Production/getLocationName'); ?>',
				method:"GET",
				data:{siteCode:site_code},
				
				datatype:"text",
				success: function(data)
				{ 
				console.log(data);
				var res = $.parseJSON(data);
				  console.log(res);
				 $("#locationCodeList").html(res[0]);
				 }
			});
		});
 $('#locationCode1').change(function()
		{
		   
		    location_code=$('#locationCode').val();
			
		 //  console.log(location_code);
		   $.ajax({
				url:'<?php echo site_url('Production/getAssets'); ?>',
				method:"GET",
				data:{locationCode:location_code},
				
				datatype:"text",
				success: function(data)
				{ 
				console.log(data);
				
				 var res = $.parseJSON(data);
				 console.log(res);
				 $("#assetCodeList").html(res[0]);
				}
			});
		});
		////////////////// ///////old logic end/////////////////////////////
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
	
	
	
								  
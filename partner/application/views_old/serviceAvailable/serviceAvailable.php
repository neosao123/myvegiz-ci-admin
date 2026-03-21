<div class="page-wrapper">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Service Available</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/Home/index';?>">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Service Available</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="col-7 align-self-center d-none">
                <div class="d-flex no-block justify-content-end align-items-center">
                    <!--<div class=""><a class="btn btn-myve" href="<?php echo base_url().'index.php/Vendoritem/add';?>">Create Item</a></div>-->
                    <span class='btn'>Click</span>
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
        <!-- basic table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Service Available</h4>
                        
                        <div class="table-responsive">
                            <table id="datatableServiceAvaliable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sr no.</th>
                                        <th>Code</th>
                                        <th>Item Name</th>
                                        <th>Menu Category</th> 
                                        <th>Service On/Off</th>
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
<!--sound script start--> 
<script src="<?php echo base_url().'assets/admin/assets/libs/bootstrap-switch/dist/js/bootstrap-switch.min.js';?>"></script>  
<!--sound script end -->
<script>
   
//sound js start
var audioUrl = 'http://www.pachd.com/a/button/button1.wav';

$('.btn').click( () => new Audio(audioUrl).play() ); 
//sound js end
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
	    var menucategoryCode="",itemCode="",fromDate="",toDate="";
		ForListUrls= base_path+"index.php/Serviceavailable/getVendorItemList";  
		loadTable(menucategoryCode,itemCode);
	    $('#btnSearch').on('click', function (e){
			menucategoryCode=$('#menucategoryCode').val();
			itemCode=$('#itemCode').val();
			fromDate=$('#fromDate').val();
			toDate=$('#toDate').val();
		
			loadTable(menucategoryCode,itemCode);	
		});
			 
			    function loadTable(kmenucategoryCode,kitemCode){
					if ($.fn.DataTable.isDataTable("#datatableServiceAvaliable")) {
					  $('#datatableServiceAvaliable').DataTable().clear().destroy();
					}
				var dataTable = $('#datatableServiceAvaliable').DataTable({ 
					stateSave: true,
					"processing":true,  
				   "serverSide":true,  
				   "order":[],
				   "searching": false,
				   "ajax":{  
						url: base_path+"index.php/Serviceavailable/getVendorItemList",  
						type:"GET",  
						data:{
						'call':'call from pending',						 
						'placeList':0,
						'menucategoryCode':kmenucategoryCode,
						'itemCode':kitemCode,
						// 'fromDate':kfromDate,
                         // 'toDate':ktoDate						
						
						
					} ,

                        "complete": function(response) { 
                            // $("input[type='checkbox']").bootstrapSwitch();
                            
                            $(".toggle").bootstrapSwitch({
                                'size': 'mini',
                                'onSwitchChange': function(event, state){
                                    var code = $(this).attr('id');
                                    // var action = $(this).val();
                                    var action = $(this).bootstrapSwitch('state');
                                    // alert(code);
                                    // alert(action);
                                    if(action){
                                        var flag = 1;
                                    }else{
                                        var flag = 0;
                                    }
                                    // return false;
                                    $.ajax({
                                        url: base_path+"index.php/Serviceavailable/chnageService",
                                        type: 'POST',
                                        data:{'code':code,'flag':flag},
                                        success: function(data) {
                                            if(data)
                                            {
                                                
                                            }
                                            else
                                            {
                                                
                                            }
                                        }
                                    });
                                },
                                'AnotherName':'AnotherValue'
                            });
                            // $(".toggle").on('change.bootstrapSwitch', function(e) {
                                // console.log(e.target.checked);
                            // });
                            
                            // $('.toggle').on('switch-change', function () {
                                // console.log("inside switchchange");
                            // });

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
                                            url: base_path+"index.php/Serviceavailable/updateItemStatus",
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

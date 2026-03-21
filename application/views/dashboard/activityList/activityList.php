        <style>
			.blue{}
		</style>
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
                        <h4 class="page-title">Activity</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Activity List</li>
                                </ol>
                            </nav>
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
			
				<div class="card">
				<div class="card-body">
			     <form>
				<div class="form-row">
				<div class="col-sm-2">
					<div class="form-group">
					   <span> <label>User Code</label> </span>
					   <input type="text"  class="form-control" list="userList" id="code" name="code">
					   <datalist id="userList">
						<?php foreach($report->result() as $rep)
							{ 
							echo "<option value='".$rep->code."'>".$rep->userFname." ".$rep->userMname." ".$rep->userLname."</option>";
								}?> 
						</datalist>
					</div>
				 </div>
				<div class="col-sm-2 mb-3">
								<div class="form-group">
								<span> <label for="fromdate">From Date</label> </span>
							<input type="text"  class="form-control date-inputmask" list="fromDate" id="fromDate" name="fromDate" placeholder="dd/mm/yyyy" required>
									
							</div>
							</div>
							
							<div class="col-sm-2 mb-3">
								<div class="form-group">
								<span> <label for="toDate">To Date</label> </span>
							<input type="text"  class="form-control date-inputmask" list="toDate" id="toDate" name="toDate" placeholder="dd/mm/yyyy" required>
								  <div class="invalid" id="error-id" style="color:red"> </div>		
							</div>
							</div>
							<div class="col-sm-2 mb-3">
										
							</div>
							
					
						
				          <div class="form-group m-t-25">
							<button type="button"  id="btnSearch" name="btnSearch" class="btn btn-info waves-effect waves-light">Search</button>
								<button type="reset" class=" btn btn-inverse">Clear</button>
				           </div>
				   
			        </div>
					
			  </form>
			</div>
		</div>
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Activity List</h4>
                                
                                <div class="table-responsive">
                                    <table id="datatableActivity" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Activity</th>
                                                <th>Date & Time</th>
                                                
                                            </tr>
                                        </thead>
                                       
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->

			<script>
				
$( document ).ready(function() {
	
	$("#btnSearch").click(function(e)
		   {
			   var fDate = $("#fromDate").val();
			   var tDate = $("#toDate").val();
			   
			   if(fDate=="" && tDate=="")
			   {
				 
				 $('#error-id').html("Dates Are Required");
				e.preventDefault();
			   }
			   setTimeout(function()
             {
                $('#error-id').hide('fast');
            },6000
			 );
					   
                 
             });
		   
	var keyCode,keyFromDate,keyToDate;
			   getDataTable(keyCode,keyFromDate,keyToDate);
				$('#btnSearch').on('click', function (e)
				{
				keyFromDate=$('#fromDate').val();
				keyFromDate=moment(keyFromDate, "DD/MM/YYYY");
				keyFromDate=moment(keyFromDate).format("YYYY-MM-DD");
	
				
				keyToDate=$('#toDate').val();
				keyToDate=moment(keyToDate, "DD/MM/YYYY");
				keyToDate=moment(keyToDate).format("YYYY-MM-DD");
		
				
		        keyCode=$('#code').val();			
			  //  alert(keyCode);
				getDataTable(keyCode,keyFromDate,keyToDate);
	            });
			   
	         //  loadTable();
			   function getDataTable(p_keyCode,p_keyFromDate,p_keyToDate)
			   {
					if ($.fn.DataTable.isDataTable("#datatableActivity")) 
					{
					  $('#datatableActivity').DataTable().clear().destroy();
					}
	
	var dataTable = $('#datatableActivity').DataTable({  
		  "processing":true,  
           "serverSide":true,  
           "order":[],
		   "searching": false,
           "ajax":{  
                 url: base_path+"Activity/getActivityList",  
				  data:{
						
						'addID':p_keyCode,
						'fromDate':p_keyFromDate,
						'toDate':p_keyToDate
						},
                type:"GET" ,  
 
		 }
								  
	   });
     }
	});

</script>


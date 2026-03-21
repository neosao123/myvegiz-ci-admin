                
	<script>
		var base_url= '<?= base_url()?>';
	</script>
	
 <link href="<?php echo base_url().'assets/admin/assets/normalize.css';?>" rel="stylesheet">

 
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
            <h4 class="page-title">Over Time Payments</h4>
            <div class="d-flex align-items-center">
               <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                     <li class="breadcrumb-item active" aria-current="page">Over Time Payments  List</li>
                  </ol>
               </nav>
            </div>
         </div>
         <!--<div class="col-7 align-self-center">
            <div class="d-flex no-block justify-content-end align-items-center">
               <div class=""><a class="btn btn-primary" href="<?php echo base_url().'index.php/contract/add';?>">Add Contract</a></div>
            </div>
         </div>-->
      </div>
   </div>
   <!-- ============================================================== -->
   <!-- End Bread crumb and right sidebar toggle -->
   <!-- ============================================================== -->
   <!-- ============================================================== -->
   <!-- Container fluid  -->
   <!-- ============================================================== -->
   <div id="load-container" class="container-fluid">
      <!-- ============================================================== -->
      <!-- Start Page Content -->
      <!-- ============================================================== -->
      <!-- basic table -->
      <div class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-body">
					     
					<div class="card-body">
						<div class="col-7 align-self-center">
                               	<h3 class="card-title"> Generate Over Time Payments</h3>					
						</div>
						<div class="row">
							<div class="col-sm-3">
									<label for="contract" class="text-right control-label col-form-label">Contract :</label>
									
										<input type="text" class="form-control" list="contractCodeList" id="contract" placeholder="Select Contract" name="contract">

										<datalist id="contractCodeList">
										 <?php
										 foreach($contract->result() as $con)
										 {
										echo "<option value='".$con->code."'>".$con->contractName."</option>";
										 }
										 ?>
										 </datalist>
							</div>
							<!--<div class="col-sm-3">
								<label for="employeeCode"  class="text-right control-label col-form-label">Employee :</label> 
								 <select class="custom-select form-control" id="employeeCode" name="employeeCode">
                                                    <option value="" readonly>Select Option..</option>
                                                </select>  
								 <div class="invalid-feedback">
                                        Select Employee...!
									</div>
							</div>-->
							<div class="col-sm-3">
								<label for="month" class="text-right control-label col-form-label">Month :</label>
								<select id="month" name="month" class="form-control required">
									<option selected value='01'>Janaury</option>
									<option value='02'>February</option>
									<option value='03'>March</option>
									<option value='04'>April</option>
									<option value='05'>May</option>
									<option value='06'>June</option>
									<option value='07'>July</option>
									<option value='08'>August</option>
									<option value='09'>September</option>
									<option value='10'>October</option>
									<option value='11'>November</option>
									<option value='12'>December</option>
								</select>
								<script>
								   var cmonth = "<?php echo date('m'); ?>";
								   $("#month").val(cmonth);
								   
								</script>
							</div>
							<div class="col-sm-3">
								<label for="year" class="text-right control-label col-form-label">Year :</label>
								<input type="text" class="form-control" value="<?=$cyear=date('Y')?>" list="yearList" id="year" name="year">
								<datalist id="yearList">
									<?php
										echo $cyear=date('Y');
										for($i=2005;$i<=$cyear;$i++)
										{
											echo '<option value='.$i.'>'.$i.'</option>';
										}
									?>
								 </datalist>
							</div>
						</div>
							
							
					</div>
					
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Over Time Payment Generated List</h4>
				 
					  
				 <button type="button" class="btn btn-success  text-white pull-right" id="print" disabled>Print</button>
				 <button type="button" class="btn btn-success  text-white pull-right" id="sendByMail" disabled>Send By Mail</button>
                
                  <div class="table-responsive printableArea">
					
					 <table id="datatablePayments" class="table table-striped table-bordered" border="1" style="border-collapse:collapse;width:100%;"  >
                        <thead>
						<tr >
						  <th>Sr. No</th>
						  <th>Code</th>
						  <th>Employee Name</th>
						  <th>Designation</th>
						  <th>Basic Salary</th>
						  <th>Total Pay Amount</th>
						  <th>Sign</th>
					   </tr>
                        </thead>
                  </table>
				  
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="loadermodal"></div>
</div>
<!-- ============================================================== -->

<!-- End Container fluid  -->
<!-- ============================================================== -->
<!-- sample modal content -->
			<div id="responsive-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">View Contract</h4>
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
			<!--<img id ="printLogo" alt="" src="<?php //echo base_url().'assets/images/logo/logo.png';?>" width="85px" style="position-->
			<!-- /.modal -->
<!-- PNotify -->


	<script>
	
		$('document').ready(function() {
	   
			loadDataTable(); // Call DataTable
			
			var container = $("#load-container");
			var codeList=[];
			
			var contractName='';
		   
			var totalRows=0;
	   
			$("#print").click(function() {
			
				if(totalRows==0)
				{
					$("#print").attr('disabled','disabled');
				}
				else
				{
		  
					var month=$('#month option:selected').text();
					var year=$('#year').val();
					var date= month+"-"+year;
					var header1=contractName+" Over Time PAY LIST FOR "+date;
			  
					$('#datatablePayments').printThis({
						header: "<h3><div id='logo'  style='float:left;'><img src='http://wolfox.in/wf-rocktech/assets/admin/assets/extra-libs/printThis/logo.png' width='150' height='100' alt='rocktech-logo'></div> <div style='text-align:center;' class='title'><h3>ROCKTECH ENGINEERS </h3></div> <div class='heading' style='text-align:center;'><h4>"+header1+"</h4></div> </h3>",
				   
						footer: "<div class='print-footer'><span class='hrDept' style='margin-left:100px;font-size:20px';><b>HR. Dept</b></span> <span class='acDept' style='margin-left:320px;font-size:20px';><b>Cheked By(AC Dept)</b></span>   <span class='hoManger' style='margin-left:320px;font-size:20px';><b>H.O.MANAGER</b></span> <span class='sign' style='margin-left:300px;font-size:20px';><b>Authorised Signatory</b></span></div>",
						importCSS: false,
						loadCSS: base_url+"assets/admin/assets/extra-libs/printThis/printThis.css",			
					});
		
					$.ajax({  
						url: base_path+"PaymentReports/getActivityDetails",
						data:{
							'year':year,
							'month':month,
							'message':'otp',
							'contractName':contractName
						},
						
						type:"GET",
						
						complete: function(response) {
							
						}
							
					}); // End Ajax	 
				}	 
		 
			}); // End Print Click
			
		
			$("#sendByMail").click(function() {
			
				container.addClass("loading");
				var month=$("#month").val();
				var year=$("#year").val();
				var date= year+'-'+month + '-00';
			
				if(totalRows==0)
				{
					$("#sendByMail").attr('disabled','disabled');
				}
				else
				{
					$.ajax({
						url: base_path+"PaymentReports/sendMailToAll",  
						method:"POST",
						data:{
							date:date,
							codeList:codeList,
							contractCode:$("#contract").val()
							
						},
						
						datatype:"text",
						
						success: function(data)
						{ 
							container.removeClass("loading");
							
							if(data!='')
							{
							  var obj=JSON.parse(data);
							  
							  if(obj.status)
							  {
								  toastrMsg(obj.message,true,'Salary Slip Mail');
								
							  }
							  else
							  {
								  toastrMsg(obj.message,false,'Salary Slip Mail');
							   
							  }
							  
							}
						}
						
					}); // End Ajax
				}	
				
			}); // End Send Mail Click

	
			$("#contract").on('input', function () {
				var val = this.value;
				
				if($('#contractCodeList option').filter(function(){
					
					if(val==this.value)
					{
						contractName=this.text;
					}
					
					return this.value === val;
					
				}).length) {
					
					container.addClass("loading");
					loadDataTable();
				}
				 
			}); // End Contract input

			
			$('#month').on('change', function (e) {
				
				container.addClass("loading");
				loadDataTable();
				
			}); // End Month Change
			
	
			$("#year").on('input', function () {
				
				var val = this.value;
				
				if($('#yearList option').filter(function(){
					
					return this.value === val; 
				
				}).length) {
				
					container.addClass("loading");
					loadDataTable();
				}
				
			}); // End Year input
			

			function loadDataTable()
			{
				var month=$("#month").val();
				var year=$("#year").val();
				var contractCode=$("#contract").val();
				
		
				if ($.fn.DataTable.isDataTable("#datatablePayments")) {
					$('#datatablePayments').DataTable().clear().destroy();
				}
			
				$('#datatablePayments').DataTable({
					
					dom: "Bfrtip",
					fixedHeader: !0,
					responsive: !0,
					paging: false,
					searching: false,
					
					"columnDefs": [
						{
							"targets": [ 1 ],
							"visible": false,
							"searchable": false
						}
					],
					
					ajax:{  
						url: base_path+"PaymentReports/getOTPaymentReportList",
						data:{
							'month':month,
							'year':year,
							'contractCode':contractCode
						},
						
						type:"GET",
			   
						complete: function(response) {
							
							
							codeList=[];
							
							console.log(response);
							
							for(var i=0;i<response.responseJSON.data.length;i++)
							{
								codeList.push(response.responseJSON.data[i][1]);
							}
				  
							totalRows=response['responseJSON']['recordsTotal'];


							if(totalRows>0)
							{
								$("#print").removeAttr('disabled');
								$("#sendByMail").removeAttr('disabled');
							}
							else
							{
								$("#print").attr('disabled','disabled');
								$("#sendByMail").attr('disabled','disabled');
							} 
				  
							container.removeClass("loading");
							
						} // End Complete
						
					} // End Ajax
		   
				}); // End DataTable
				
			} // End loadDataTable 
	
	
			$('#contract').change(function(){
				
				var contract_code = $(this).val();
				
				$.ajax({
					url:base_path+"AttendanceEntry/getemployeeFromContract",
					method:"GET",
					data:{contractCode:contract_code},
					datatype:"text",
					success: function(data)
					{
						$('#employeeCode').html(data);
					}
				});
				
			}); // End Contract Change
			
			
			function toastrMsg(msg,msgStatus,msgTitle)
			{
				if (msg != '') {
					
					if (msgStatus) {
						toastr.success(msg, msgTitle, {
							"progressBar": true
						});

					} else {
						toastr.error(msg, msgTitle, {
							"progressBar": true
						});

					}
				}
				
			} // End Toastr Msg Function
			
			   
		}); // End Ready

		
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
	<script src="<?php echo base_url().'assets/admin/assets/extra-libs/printThis/printThis.js';?>"></script>
	
	
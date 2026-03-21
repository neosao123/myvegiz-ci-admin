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
            <h4 class="page-title">Payments</h4>
            <div class="d-flex align-items-center">
               <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
                     <li class="breadcrumb-item active" aria-current="page"> Payments  List</li>
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
	  <form>
      <div class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-body">
					<div class="card-body">
						<div class="col-7 align-self-center">
                               	<h3 class="card-title"> Generate Payments</h3>					
						</div>
						
						<div class="row">
							<div class="col-sm-3">
									<label for="contract" class="text-right control-label col-form-label">Contract :</label>
									
										<input type="text" class="form-control check" list="contractCodeList" id="contract" placeholder="Select Contract" name="contract">

										<datalist id="contractCodeList">
										 <?php
										 foreach($contract->result() as $con)
										 {
											echo "<option value='".$con->code."'>".$con->contractName."</option>";
										 }
										 ?>
										 </datalist>
							</div>
							
							<div class="col-sm-3">
								<label for="month" class="text-right control-label col-form-label">Month :</label>
								<select id="month" name="month" class="form-control required check">
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
								<input type="text" class="form-control check" value="<?=$cyear=date('Y')?>" list="yearList" id="year" name="year">
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
							<div class="col-sm-3">
							<label for="btnGenerateSalary" class="text-right control-label col-form-label">Generate Salary</label>
							<button type="button" id="btnGenerateSalary" class="btn btn-info" disabled>Generate Salary</a></button>
							</div>
						</div>
							
							
					</div>
						<hr>
						
               </div>
            </div>
         </div>
      </div>
	  
	  <div class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-body">
					      
					<div class="card-body">
						<div class="col-7 align-self-center">
                               	<h3 class="card-title"> Search Employee Payment</h3>			
						</div>
						<div class="row">
							<div class="col-sm-3">
								<label for="employeeCode"  class="text-right control-label col-form-label">Employee :</label> 
								 <select class="custom-select form-control" id="employeeCode" name="employeeCode">
                                                    <option value="" readonly>Select Option</option>
                                                </select>  
								 <div class="invalid-feedback">
                                        Select Employee...!
									</div>
							</div>
						</div>
							
							
					</div>
						<hr>
					
               </div>
            </div>
         </div>
      </div>
	  
	  </form>
	  
      <div class="row">
         <div class="col-12">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title"> Attendance Entry List</h4>
                
                  <div class="table-responsive">
                     <table id="datatablePayments" class="table table-striped table-bordered ">
                        <thead>
                           <tr>
                              <th>Sr. No</th>
							  <th>Employee Code</th>
                              <th>Employee No</th>
                              <th>Employee Name</th>
							  <th>Working Days</th>
							  <th>Basic Sal</th>
							  <th>HRA</th>
							  <th>Conv.Allowance</th>
							  <th>Canteen</th>
							  <th>ESI</th>
							  <th>Advance</th>
							  <th>Fine</th>
							  <th>Gross Salary</th>
							  <th>Total Deduction</th>
							  <th>Net Salary</th>
							  <th>Corrected Salary</th>
							  <th>Operation</th>
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
<!-- PNotify -->
<script src="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.js';?>"></script>
<script src="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.buttons.js';?>"></script>
<script src="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.nonblock.js';?>"></script>
<script src="<?php echo base_url().'assets/admin/assets/bootstrap-confirm-delete.js';?>"></script>


	<script>
	
		$('document').ready(function() {
			
			var container = $("#load-container");
			
			loadDataTable(); // Call DataTable

			$("#contract").on('input', function() {
				
				var val = this.value;
				
				if ($('#contractCodeList option').filter(function() {
						return this.value === val;
					}).length) {
						
					container.addClass("loading");
					loadDataTable();
				}
				
			}); // End Contract On input

			$('#month').on('change', function(e) {
				
				container.addClass("loading");
				loadDataTable();
				
			}); // End Month Change

			
			$("#year").on('input', function() {
				
				var val = this.value;
				if ($('#yearList option').filter(function() {
						return this.value === val;
					}).length) {
					
					container.addClass("loading");
					loadDataTable();
				}
				
			}); // End Year On input
			

			$('#employeeCode').on('change', function(e) {
				
				container.addClass("loading");
				loadDataTable();
				
			}); // End Employee Change

			
			function loadDataTable() {
				
				var month = $("#month").val();
				var year = $("#year").val();
				var contractCode = $("#contract").val();
				var empCode = $("#employeeCode").val();
				
				if (month != '' && year != '' && contractCode != '') {
					if ($.fn.DataTable.isDataTable("#datatablePayments")) {
						$('#datatablePayments').DataTable().clear().destroy();
					}

					$('#datatablePayments').DataTable({
						dom: "Bfrtip",
						fixedHeader: !0,
						responsive: !0,
						ajax: {
							url: base_path + "Payments/getPaymentsList",
							data: {
								'empCode': empCode,
								'month': month,
								'year': year,
								'contractCode': contractCode
							},
							
							type: "GET",
							
							complete: function(response) {
								console.log(response);
								console.log();
								if (response.responseJSON.extraData['tableDataStatus']) {
									$("#btnGenerateSalary").prop('disabled', true);
									$('html, body').animate({
										scrollTop: $("#datatablePayments").offset().top
									}, 700);
								} else {
									$("#btnGenerateSalary").prop('disabled', false);
								}
								
								container.removeClass("loading");

							} // End Complete Function
							
						} // End Ajax

					}); // End DataTable
					
				} // End if Condition
				 
			} // End Load DataTable
              
			  
			  // $("#year,#contract").change(function()
			  // {
			  $('#employeeCode').click(function (e) 
				{
		
				 var month = $("#month").val();
				var year = $("#year").val();
				var contract_Code = $("#contract").val();
				//alert(contractCode);
				if(contract_Code!='' && month!='' && year!='')
			      {
					  $.ajax({   
					url: base_path + "AttendanceEntry/getemployeeFromContract",
					method: "GET",
					data: {
						contractCode: contract_Code
					},
					datatype: "text",
					success: function(data) {
						$('#employeeCode').html(data);
					}
					
				}); // End Ajax
					
						
				}
				
			// Get Employee List From Contractcode
			else
			{
				alert('Select Contract , Month And Year');
						e.preventDefault ();
			  //$('#contract').change(function() {
				//alert('hii');
				//var contract_code = $(this).val();
				
			
			//}); // End Contract Change
			
		  }//else End
		  
	   });//emp change
	   
	//});//End Change

			
			$('#btnGenerateSalary').click(function() {
				generatePayment();

			}); // End Generate Salary Click

			
			function generatePayment() {
				
				var month = $("#month").val();
				var year = $("#year").val();
				var contractCode = $("#contract").val();

				$.ajax({
					url: base_path + "Payments/generatePayment",
					method: "POST",
					data: {
						'month': month,
						'year': year,
						'contractCode': contractCode
					},
					datatype: "text",
					success: function(data) {
						
						var obj = JSON.parse(data);
						if (obj.status) {
							
							toastrMsg(obj.message,true,'Salary');
							
							container.addClass("loading");
							loadDataTable();
							
						} else {
							toastrMsg(obj.message,false,'Attendance');
						}
					}
					
				}); // End Ajax
				
			} // End Generate Payment Function
			
			
			
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
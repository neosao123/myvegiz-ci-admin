	<link href="<?php echo base_url().'assets/admin/assets/normalize.css';?>" rel="stylesheet">

	<div class="page-wrapper">
   
		<div class="page-breadcrumb">
			<div class="row">
				<div class="col-5 align-self-center">
					<h4 class="page-title">Over Time Payments</h4>
					<div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
								<li class="breadcrumb-item active" aria-current="page"> Over Time Payments  List</li>
							</ol>
						</nav>
					</div>
				</div>
			</div>
		</div>
   
   
		<div id="load-container" class="container-fluid">
   
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<div class="card-body">
						
							<div class="col-7 align-self-center">
								<h3 class="card-title"> Generate Over Time Payments</h3>	
							</div>
						
							<hr/>
						
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
			
			<div class="loadermodal"></div>
			
		</div>
		
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-body">
						<form>
                          
							<div class="card-body">
								<div class="col-7 align-self-center">
										<h3 class="card-title"> Search Employee Payment</h3>			
								</div>
							
								<hr/>
							
								<div class="row">
								
								<div class="col-sm-3">
									<label for="employeeCode"  class="text-right control-label col-form-label">Employee :</label> 
									
									<select class="custom-select form-control" id="employeeCode" name="employeeCode">
														
										<option value="" readonly>Select Option..</option>
									
									</select>  
									
									<div class="invalid-feedback">
										Select Employee...!
									</div>
									
								</div>
								
							</div>
							
						</div>
						
						<hr/>						
							
					</form>
				</div>
			</div>
		</div>
	</div>
  
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<h4 class="card-title"> Over Time Payment List</h4>
                
					<div class="table-responsive">
						<table id="datatablePayments" class="table table-striped table-bordered ">
							<thead>
								<tr>
								
									<th>Sr. No</th>
									<th>Employee Code</th>
									<th>Employee No</th>
									<th>Employee Name</th>
									<th>OT Working Days</th>
									<th>OT Salary</th>

								</tr>
							</thead>
						</table>
					</div>
					
				</div>
			</div>
		</div>
	</div>



	<script src="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.buttons.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/extra-libs/pnotify/dist/pnotify.nonblock.js';?>"></script>
	<script src="<?php echo base_url().'assets/admin/assets/bootstrap-confirm-delete.js';?>"></script>



	<script>
	
		$('document').ready(function() {
			
			var container = $("#load-container");
			
			var msg='',msgStatus=false,msgTitle='Salary';
			
			loadDataTable(); // Call DataTable

			$("#contract").on('input', function() {
				
				$('#employeeCode').removeAttr('disabled');
				
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
				
				if($('#contract').val()=='' && $("#year").val().length==4)
				{
					//alert('Please Check Contract');
					toastrMsg('Please Check Contract',false,'Alert');
				}
				else
				{
					$('#employeeCode').val('');
					$('#employeeCode').attr('disabled','disabled');
					
					if($("#year").val().length==4)
					{
						$('#loaderSearch').show();
						
						var val = this.value;
						
						$('#employeeCode').removeAttr('disabled');
					}
					
					if ($('#yearList option').filter(function() {
								return this.value === val;
							}).length) {
								
							container.addClass("loading");
							loadDataTable();
						}
						
						
				}
				
			}); // End Year On input

			$('#employeeCode').click(function(){
				
				if($("#year").val().length!=4)
				{
					$('#employeeCode').attr('disabled','disabled');
					//alert('Please Check Contract And Year');
					toastrMsg('Please Check Contract And Year',false,'Alert');
				}
				
			});  // End Employee Click
			
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
							url: base_path + "Payments/getOTPaymentsList",
							data: {
								'empCode': empCode,
								'month': month,
								'year': year,
								'contractCode': contractCode
							},
							
							type: "GET",
							
							complete: function(response) {
								
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
              
			 
			
			// Get Employee List From Contractcode
		
			$('#contract').change(function() {
			
				$('#employeeCode').removeAttr('disabled');
				
				var contract_code = $(this).val();
				
				$.ajax({   
					url: base_path + "AttendanceEntry/getemployeeFromContract",
					method: "GET",
					data: {
						contractCode: contract_code
					},
					datatype: "text",
					success: function(data) {
						
						$('#employeeCode').html(data);

					}
					
				}); // End Ajax
			
			}); // End Contract Change
		
			
			$('#btnGenerateSalary').click(function() {
				generatePayment();

			}); // End Generate Salary Click

			
			function generatePayment() {
				
				container.addClass("loading");
				
				var month = $("#month").val();
				var year = $("#year").val();
				var contractCode = $("#contract").val();

				$.ajax({
					url: base_path + "Payments/generateOTPayment",
					method: "POST",
					data: {
						'month': month,
						'year': year,
						'contractCode': contractCode
					},
					datatype: "text",
					success: function(data) {
						
						container.removeClass("loading");
						
						var obj = JSON.parse(data);
						if (obj.status) {
							toastrMsg(obj.message,obj.status,'Salary'); // Call Toastr Msg Function
							loadDataTable();
						} else {
							
							toastrMsg(obj.message,obj.status,'Salary'); // Call Toastr Msg Function
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
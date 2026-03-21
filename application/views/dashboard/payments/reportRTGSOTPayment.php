	
	<script>
		var base_url= '<?= base_url()?>';
	</script>
	
	<link href="<?php echo base_url().'assets/admin/assets/normalize.css';?>" rel="stylesheet">

	<div class="page-wrapper">

		<div class="page-breadcrumb">
		   <div class="row">
			  <div class="col-5 align-self-center">
				 <h4 class="page-title">RTGS Over Time Report</h4>
				 <div class="d-flex align-items-center">
					<nav aria-label="breadcrumb">
					   <ol class="breadcrumb">
						  <li class="breadcrumb-item"><a href="<?php echo base_url().'index.php/admin/index';?>">Home</a></li>
						  <li class="breadcrumb-item active" aria-current="page"> RTGS Over Time Report List</li>
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

		<div id="load-container" class="container-fluid">
		   
		   <div class="row">
			  <div class="col-12">
				 <div class="card">
					<div class="card-body">
					   <div class="card-body">
						  <div class="col-7 align-self-center">
							 <h3 class="card-title"> Generate RTGS Over Time Report</h3>
						  </div>
						  
						  <hr/>
						  
						  <div class="row">
							 <div class="col-sm-3">
								<label for="bankIfscCode" class="text-right control-label col-form-label">Bank Name :</label>
								<input type="text" class="form-control" list="bankIfscCodeList" id="bankIfscCode" name="bankIfscCode" placeholder="Select Bank" >
								<datalist id="bankIfscCodeList">
								   <?php
									  foreach($bank->result() as $row)
									  {
									  echo "<option value='".$row->bankIfscCode."'>".$row->bankName."</option>";
									  }
									  ?>
								</datalist>
							 </div>
							 
							 <div class="col-sm-3">
								<label for="contract" class="text-right control-label col-form-label">Contract :</label>
								<input type="text" class="form-control" list="contractCodeList" id="contract" name="contract" placeholder="Select Contract" >
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
						  </div> 
					   </div>
					   
					    <hr/>
					   
					</div>
				 </div>
			  </div>
		   </div>
		   <div class="row">
			  <div class="col-12">
				 <div class="card">
					<div class="card-body">
					   <h4 class="card-title"> RTGS Over Time Report Generated List</h4>
					   <button type="button" class="btn btn-success  text-white pull-right" id="print" disabled>Print</button>
					   <div class="table-responsive printableArea">
						  <table id="datatablePayments" class="table table-striped table-bordered" border="1" style="border-collapse:collapse;width:100%;"  >
							 <thead>
								<tr>
								   <th>Sr. No</th>
								   <th>Employee Name</th>
								   <th>Designation</th>
								   <th>Basic Salary</th>
								   <th>Total Pay Amount</th>
								</tr>
							 </thead>
						  </table>
					   </div>
					</div>
				 </div>
			  </div>
		   </div>
		</div>
		
		<div class="loadermodal"></div>
		
	</div>

	
	<script>
	
		$('document').ready(function() {
			
			var container = $("#load-container");
			
			var contractName='';
		   
			var totalRows=0;
			
			//$('#printLogo').hide();

			$("#print").click(function() {
				
				//$('#printLogo').show();
				
				if(totalRows==0)
				{
					$("#print").attr('disabled','disabled');
				}
				else
				{

					var month=$('#month option:selected').text();
					var year=$('#year').val();
					var date= month+"-"+year;
					var header1=contractName+" RTGS OVER TIME REPORT FOR "+date;
					
					$('#datatablePayments').printThis({
						header: "<div id='logo'></div> <div class='title' style='text-align:center';><h3>ROCKTECH ENGINEERS </h3></div> <div class='heading'style='text-align:center';><h4>"+header1+"</h4></div>  ",
									   
						importCSS: false,
						loadCSS: base_url+"assets/admin/assets/extra-libs/printThis/printThis.css",			
					});
					
					$.ajax({  
						url: base_path+"PaymentReports/getActivityDetails",
						data:{
							'year':year,
							'month':month,
							'message':'rtgsotp',
							'contractName':contractName
						},
						
						type:"GET",
						
						complete: function(response) {
							
						}
							
					}); // End Ajax 
				}
				
			}); // End Print Click
			
			
			loadDataTable(); // Table Load Function
			
			$("#bankIfscCode").on('input', function () {
				var val = this.value;
				if($('#bankIfscCodeList option').filter(function(){
					return this.value === val;        
				}).length) {
					
					container.addClass("loading");
					loadDataTable();
				}
			});
			
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
			});

			$('#month').on('change', function (e) {
				
				container.addClass("loading");
				loadDataTable();
				
			});
			
			$("#year").on('input', function () {
				var val = this.value;
				if($('#yearList option').filter(function(){
					return this.value === val;        
				}).length) {
					
					container.addClass("loading");
					loadDataTable();
				}
			});

			function loadDataTable()
			{
				var bankIfscCode=$("#bankIfscCode").val();
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
					
				   ajax:{  
						url: base_path+"PaymentReports/getRTGSOTReportList",
						data:{
							'bankIfscCode':bankIfscCode,
							'month':month,
							'year':year,
							'contractCode':contractCode
						},
						
						type:"GET",
					   
						complete: function(response) {
						   
							totalRows=response['responseJSON']['recordsTotal']; 
					 
							if(totalRows>0)
							{
							 $("#print").removeAttr('disabled');
							}
							else
							{
							  $("#print").attr('disabled','disabled');
							} 
							
							container.removeClass("loading");
						   
						} // End Complete
				   }
				   
				}); // End DataTable
				
			} // End loadDataTable
				   
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
	
	
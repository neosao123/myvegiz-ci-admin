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
					 <h4 class="page-title">Change In Salary Details</h4>
					 <div class="d-flex align-items-center">
						<nav aria-label="breadcrumb">
						   <ol class="breadcrumb">
							  <li class="breadcrumb-item"><a href="#">Home</a></li>
							  <li class="breadcrumb-item active" aria-current="page">Employee Salary Details</li>
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
   <!-- ============================================================== -->
   <!-- Start Page Content -->
   <!-- ============================================================== -->
   <div class="card">
      <div class="card-body">
		 <hr>
		
          <h4 class="card-title bg-success col-3 text-white rounded">Employee Information</h4>
          
         <form method="post" id="myForm" action="<?php echo base_url().'index.php/Payments/saveEdit';?>">
         <hr>		
						
						<input type="hidden" value="<?php echo $date;?>" id="salaryDate" name="salaryDate" readonly>
						<input type="hidden" value="<?php echo $paymentInfo[0]->id;?>" id="empId" name="empId" readonly>
						<div class="row">
							<div class="col-md-4">
									<div class="form-group">
										<label for="firstName">Employee Code :</label>
										<input type="text" class="form-control-line" value="<?php echo $employeeData[0]->code;?>"  id="employeeCode" name="employeeCode" readonly>
										</div>
							</div>
							<div class="col-md-4">
									<div class="form-group">
										<label for="empToken">Employee Number :</label>
										<input type="text" class="form-control-line" value="<?php echo $employeeData[0]->empToken;?>"  id="empToken" name="empToken" readonly>
										</div>
							</div>
							
							<div class="col-md-4">
									<div class="form-group">
										<label for="salaryYear">Salary Month Year :</label>
										<input type="text" class="form-control-line" value="<?php echo $date;?>"  id="salaryYear" name="salaryYear" readonly>
										</div>
							</div>
						</div>
						<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="firstName">Employee Name :</label>
                                <input type="text" class="form-control-line" value="<?php echo $employeeData[0]->firstName. ' '.$employeeData[0]->middleName.' '.$employeeData[0]->lastName;?>" id="employeeName" name="employeeName" readonly>
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="middleName">Contract Name:</label>
                                <input type="text" class="form-control-line" value="<?php echo $employeeData[0]->joinvalue2;?>" id="contract" name="contract" readonly>
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="middleName">Employee Designation:</label>
                                <input type="text" class="form-control-line" value="<?php echo $employeeData[0]->joinvalue1;?>"  id="designation" name="designation" disabled>
							</div>
                        </div>
						</div>
						
						
						<div class="row">
							<div class="col-md-4">
									<div class="form-group">
										<label for="BS">Basic Salary :</label>
										<input type="number" min="0" class="form-control calculate_salary" value="<?=$paymentInfo[0]->basicSalary;?>" readonly > </div>
							</div>
						
						</div>
						
            
            
			 </div>
			</div>
			<!-----------------------------Basic Salary ROW----------------------------->
			 <div class="card">
               <div class="card-body">
			<h4 class="card-title  bg-success col-3 text-white">Employee Allowances</h4>
			<hr>
					<!-----------------------------FIRST ROW----------------------------->
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="HRA">House Rent Allowance(HRA+DA) :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="HRA" name="HRA" value="<?=$paymentInfo[0]->houseRentAllowance;?>" readonly > 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="CA">Conveyance Allowances :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="CA" name="CA" value="<?=$paymentInfo[0]->conveyanceAllowance;?>" readonly> 
							</div>
                        </div>
                        <div class="col-md-4">
							<div class="form-group">
                                <label for="MA">Medical Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="MA" name="MA" value="<?=$paymentInfo[0]->medicalAllowance;?>" readonly> 
							</div>
                        </div>
                        
					</div>
					<!-----------------------------SECOND ROW----------------------------->
					<div class="row">
						
						
						<div class="col-md-4">
							<div class="form-group">
                                <label for="ERA">EDU REMB Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="ERA" name="ERA" value="<?=$paymentInfo[0]->eduRembAllowance;?>" readonly> 
							</div>
                        </div>
                        <div class="col-md-4">
							<div class="form-group">
                                <label for="TTA">Telephone + Travelling Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="TTA" name="TTA" value="<?=$paymentInfo[0]->telephoneTravellingAllowance;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="OA">Other Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="OA" name="OA" value="<?=$paymentInfo[0]->otherAllowance;?>" readonly> 
							</div>
                        </div>
                        
					</div>
					<!-----------------------------THIRD ROW----------------------------->
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="HSA">Hazardous Site Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="HSA" name="HSA" value="<?=$paymentInfo[0]->HazardousSiteAllowance;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="FA">Fooding/Canteen Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="FA" name="FA" value="<?=$paymentInfo[0]->foodingAllowance;?>" readonly> 
							</div>
                        </div>
						
						<div class="col-md-4">
							<div class="form-group">
                                <label for="SA">Special Allowance :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="SA" name="SA" value="<?=$paymentInfo[0]->specialAllowance;?>" readonly> 
							</div>
                        </div>
						 
                        </div>
                      </div>  
					</div>
			<!-----------------------------END ROW----------------------------->
			<div class="card">
               <div class="card-body">
			<hr>
			<h4 class="card-title  bg-success col-3 text-white">Employee Deduction</h4>
			<hr>
					<!-----------------------------FIRST ROW----------------------------->
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="PF">Povident Fund (PF) :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="PF" name="PF" value="<?=$paymentInfo[0]->providentFund;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="ESI">Employee State Insurance (ESI) :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="ESI" name="ESI" value="<?=$paymentInfo[0]->empStateInsurance;?>" readonly> 
							</div>
                        </div>
                        <div class="col-md-4">
							<div class="form-group">
                                <label for="PT">Professional Tax (PT) :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="PT" name="PT" value="<?=$paymentInfo[0]->professionalTax;?>" readonly> 
							</div>
                        </div>
                        
					</div>
					<!-----------------------------SECOND ROW----------------------------->
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="CD">Canteen/Fooding Deduction:</label>
                                <input type="number" min="0" class="form-control" id="CD" name="CD" value="<?=$paymentInfo[0]->foodingDeduction;?>" oninput='calEarnings()'> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="FD">Fine Deduction :</label>
                                <input type="number" min="0" class="form-control" id="FD" name="FD" value="<?=$paymentInfo[0]->fineDeduction;?>" oninput='calEarnings()'> 
							</div>
                        </div>
                        <div class="col-md-4">
							<div class="form-group">
                                <label for="OD">Other Deduction :</label>
                                <input type="number" min="0" class="form-control" id="OD" name="OD" value="<?=$paymentInfo[0]->otherDeduction;?>" readonly> 
							</div>
                        </div>
                        
					 </div>					
					</div>
				</div>
					<!-----------------------------END ROW----------------------------->
					
				<div class="card">
               <div class="card-body">
			<h4 class="card-title  bg-success col-3 text-white">Advance</h4>
			<hr>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="Advance">Advance EMI Amount:</label>
                                <input type="number" min="0" class="form-control " id="advance" name="advance" value="<?=$paymentInfo[0]->advance;?>" readonly>
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="installmentPaid">No.of Installments Paid:</label>
                                <input type="number" min="0" class="form-control" id="installmentPaid" name="installmentPaid" value="0" readonly> 
							</div>
                        </div>
                        
					 </div>
					
					
					</div>
				</div>
					<!-----------------------------END ROW----------------------------->
			<div class="card">
               <div class="card-body">
			<hr>
			<h4 class="card-title  bg-success col-3 text-white">Employee Total Salary :</h4>
			<hr>
					<!-----------------------------FIRST ROW----------------------------->
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
                                <label for="empGS">Working Days Of Month:</label>
                                <input type="number" min="0" class="form-control " id="workingDays" name="workingDays" value="<?=$paymentInfo[0]->workingMonthDays;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="empNS">Worked Days:</label>
                                <input type="number" min="0" class="form-control " id="workedDays" name="workedDays" value="<?=$paymentInfo[0]->totalWorkingDays;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="empGS">Payment Type:</label>
                                <select class="form-control " id="paymentType" name="paymentType"> 
							    <option value="">select Option</option>
								<option value="cash">Cash</option>
								<option value="bank">Bank</option>
								</select>
							</div>
                        </div>
					</div>
					<div class="row">
		
						<div class="col-md-4">
							<div class="form-group">
                                <label for="empTD">Total Duduction:</label>
                                <input type="number" min="0" class="form-control " id="empTD" name="empTD" value="<?=$paymentInfo[0]->finalTotalDeduction;?>" readonly> 
								<input type="hidden" min="0" class="form-control " id="hempTD" name="hempTD" value="<?=$paymentInfo[0]->finalTotalDeduction;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="empGS">Gross Salary :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="empGS" name="empGS" value="<?=$paymentInfo[0]->grossSalary;?>" readonly> 
							</div>
                        </div>
						<div class="col-md-4">
							<div class="form-group">
                                <label for="empNS">Net Salary :</label>
                                <input type="number" min="0" class="form-control calculate_salary" id="empNS" name="empNS" value="<?=$paymentInfo[0]->netSalary;?>" readonly> 
								<input type="hidden" min="0" class="form-control calculate_salary" id="hempNS" name="hempNS" value="<?=$paymentInfo[0]->netSalary;?>" readonly> 
							</div>
                        </div>
					</div>
					
					<div class="row">
						
                        <div class="col-md-4">
							<div class="form-group">
                                <label for="payableSalary">Total Payable Salary :</label>
                                <input type="number" min="0" class="form-control " id="payableSalary" name="payableSalary" value="<?=$paymentInfo[0]->finalPayableAmount;?>"> 
							</div>
                        </div>
						<div class="col-md-8">
							<div class="form-group">
                                <label for="holidayRemark"> Comment: </label>
                                <textarea type="text" id="comment" name="comment" class="form-control is-maxlength" row="2" cols="50"><?=$paymentInfo[0]->actionComment;?></textarea>
							</div>
                        </div>
					</div>
					
					  
					<!-----------------------------END ROW----------------------------->
			<hr>
			
			
            <div class="text-center">
               <div class="text-xs-center">
                  <button type="submit" class="btn btn-success">Submit</button>
               </div>
            </div>
         </form>
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



<!--<script src="<?php //echo base_url().'assets/admin/assets/extra-libs/jquery.repeater/dff.js';?>"></script>-->
<script>
	function calEarnings()
		{
		
			var cd = $("#CD").val();
			var fd = $("#FD").val();
			var od = $("#OD").val();
			var pi = $("#PI").val();
			var pf = $("#PF").val();
			
			var esi = $('#ESI').val();
			
			
			
			if(cd=='')
			{
				$("#CD").val('0');
			}
			if(fd=='')
			{
				$("#FD").val('0');
			}
			
			
			var cdfd=parseInt($("#CD").val())+parseInt($("#FD").val());
			var deduction=parseInt($("#hempTD").val())+cdfd;
			var currentNetSalary=$('#hempNS').val();
			
			//var totalDeduction=(parseInt($("#PF").val())+parseInt($("#ESI").val())+parseInt($("#PT").val())+parseInt($("#CD").val())+parseInt($("#FD").val())+parseInt($("#OD").val()));
			//alert(totalDeduction);
			if(currentNetSalary<cdfd)
			{
				alert("Deduction will not greater than Salary!");
				$("#CD").val('0');
				$("#FD").val('0');
			}
			else
			{
				$("#empTD").val(deduction);
				var netSal=currentNetSalary-cdfd;
				$('#empNS').val(netSal);
				$('#payableSalary').val(netSal);
			}
			
		}
		
</script>
<script>


    $(document).ready(function () {
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
						window.location.href="http://wolfox.in/wf-rocktech/index.php/payroll/listRecords";	
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
 });
	

</script>


